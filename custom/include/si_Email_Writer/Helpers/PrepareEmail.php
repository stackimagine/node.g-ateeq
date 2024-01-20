<?php

namespace si_Email_Writer\Helpers;

use si_Email_Writer\Sugar\Helpers\DBHelper;
use si_Email_Writer\apiCalls\OpenAIApiAdapter;

require('modules/si_Email_Writer/license/si_Email_WriterOutfittersLicense.php');

/**
 * Class PrepareEmail
 *
 * This class provides functionality for preparing email content.
 */
class PrepareEmail
{
    /**
     * Run the email preparation process.
     *
     * @param string $module             The module to process (default: 'Leads').
     * @param bool   $requireCompleteData Whether to require complete data (default: true).
     */
    public static function firstEmail($module = 'Leads', $requireCompleteData = true)
    {
        $startTime = strtotime('now');
        $mainTable = 'RankedLeads'; //temporary table generated via subquery
        $mainFields = 'id';
        $mainWhere = ['row_num' => ['=', 1]];

        // Define the parameters for the subquery
        $subTable = 'leads';
        $subFields = ['id', 'account_id', 'ROW_NUMBER() OVER (PARTITION BY account_id ORDER BY date_modified) AS row_num'];
        $subWhere = ['si_email_status' => ['=', 'data_entered'], 'description' => ['!=', null], 'description' => ['!=', ''], 'si_email_verified' => ['=', 'Verified']];

        // Run the query using the selectWithSubquery method
        $result = DBHelper::selectWithSubquery($mainTable, $mainFields, $mainWhere, $subTable, $subFields, $subWhere);

        foreach ($result as $key => $value) {
            $written = self::writeEmail($module, $value['id'], 'first');
            $GLOBALS['log']->fatal($written);
            if ((strtotime('now') - $startTime) > 60) {
                break; // Exit the loop if more than a minute has passed
            }
        }
        return true;
    }

    /**
     * Prepare followup emails.
     *
     * @param string $module The module to process (default: 'Leads').
     */
    public static function writeFollowups($module = 'Leads')
    {
        $startTime = strtotime('now');
        $currentTimestampUTC = strtotime(gmdate('Y-m-d H:i:s'));
        $fourDaysAgoUTC = date('Y-m-d H:i:s', strtotime('-4 days', $currentTimestampUTC));

        $result = DBHelper::select(
            strtolower($module),
            'id, si_conversation_history',
            [
                'si_conversation_history' => ['!=', ''],
                'si_email_status' => ['!=', 'reply_received'],
                'si_emailed_at' => ['<', $fourDaysAgoUTC]
            ],
            'si_emailed_at DESC'
        );

        foreach ($result as $key => $value) {
            $written = self::writeEmail($module, $value['id'], 'followup');
            $GLOBALS['log']->fatal($written);
            if ((strtotime('now') - $startTime) > 60) {
                break; // Exit the loop if more than a minute has passed
            }
        }
        return true;
    }

    /**
     * Write an email for a specific record.
     *
     * @param string $module The module of the record.
     * @param int    $id     The ID of the record.
     *
     * @return string 'true' or the reason why it failed.
     */
    public static function writeEmail($module, $id, $emailType = '')
    {
        try {
            $isValidLicense = \si_Email_WriterOutfittersLicense::isValid('si_Email_Writer');
            if ($isValidLicense != true || $isValidLicense != 1) {
                return "Please enter license key <a href='index.php?module=si_Email_Writer&action=license'>here</a>";
            }

            $bean = \BeanFactory::getBean($module, $id, array('disable_row_level_security' => true));

            if (!$bean)
                return 'Record not found';

            $toEmailAddress = $bean->email1 ?? $bean->email2 ?? '';
            if (!$toEmailAddress)
                return ucfirst($module) . ': ' . $id . ': The record does not have an email address';

            if ($bean->si_conversation_history)
                $emailType = 'followup';
            else
                $emailType = 'first';

            $bean->load_relationship('accounts');
            if ($bean->accounts) {
                $relatedAccount = $bean->accounts->get();
            }
            if ($relatedAccount) {
                $account = \BeanFactory::getBean('Accounts', $relatedAccount[0], array('disable_row_level_security' => true));
                if ($account) {
                    if (substr($account->description, 0, 33) === "Their linkedin bio: \nTehcnologies" || substr($account->description, 0, 35) === "Their linkedin bio: <br>Technologies") {
                        $account->description = substr($account->description, 21);
                    }
                    $accountDescription = "Company name: " . $account->name . "\nCompany Description: " . $account->description;
                }
            }

            $bean->load_relationship('si_email_writer_leads_1');
            if ($bean->si_email_writer_leads_1) {
                $relatedPrompt = $bean->si_email_writer_leads_1->get();
                if (isset($relatedPrompt[0])) {
                    $prompt = $relatedPrompt[0];
                }
            }
            // Select the appropriate email sending method based on the email type
            switch ($emailType) {
                case 'followup':
                    $response = OpenAIApiAdapter::followupEmail($bean->si_conversation_history, $bean->first_name . ' ' . $bean->last_name, $bean->description, $account->accountDescription || "", $bean->assigned_user_id, $prompt);
                    break;

                case 'first':
                    $response = OpenAIApiAdapter::firstEmail($bean->first_name . ' ' . $bean->last_name, $bean->description, $accountDescription || "", $bean->assigned_user_id, $prompt);
                    break;

                default:
                    return 'Invalid email type';
            }

            if (isset($response['error']) && $response['error'])
                return $response['error'];


            $emailBody = $response['body'] ? nl2br($response['body']) : (isset($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : '');
            $emailBody = str_replace('<br />', "\n", $emailBody);
            $emailBody = str_replace('<br >', "\n", $emailBody);
            $emailBody = str_replace('<br>', "\n", $emailBody);

            if (isset($response['subject'])) {
                $bean->si_email_subject = $response['subject'];
            }
            $bean->si_email_body = $emailBody;
            $bean->si_email_status = $emailType == 'first' ? 'ready_for_approval' : 'followup_written';
            $bean->save();

            return 'true';
        } catch (\Throwable $th) {
            $GLOBALS['log']->fatal($th->getMessage());
        }
    }
}
