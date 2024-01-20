<?php

namespace si_Email_Writer\Helpers;

use si_Email_Writer\apiCalls\MailApiAdapter;
use si_Email_Writer\Sugar\Helpers\DBHelper;

/**
 * Sends an email to a record by getting its details from the database.
 */
class HandleSending
{
    public static function send($recordId, $module = 'Leads', $userId = null)
    {
        try {
            if (!$userId) {
                global $current_user;
                $user = $current_user;
            } else {
                $user = \BeanFactory::getBean(ucfirst('Users'), $userId, array('disable_row_level_security' => true));
            }
            $bean = \BeanFactory::getBean(ucfirst($module), $recordId, array('disable_row_level_security' => true));
            $toEmailAddress = $bean->email1 ?? $bean->email2 ?? '';

            if (!$bean)
                return self::sendError(ucfirst($module) . ': ' . $recordId . ': Record not found');
            if (!$toEmailAddress)
                return self::sendError(ucfirst($module) . ': ' . $recordId . ': The record does not have an email address');
            if (!$bean->si_email_body)
                return self::sendError(ucfirst($module) . ': ' . $recordId . ': Email body is empty');

            $email_type = 'first';
            if ($bean->si_message_id) {
                $email_type = 'followup';
            }

            $senderName = trim($user->first_name . ' ' . $user->last_name);
            $toName = trim($bean->first_name . ' ' . $bean->last_name);
            $messageId = base64_decode(html_entity_decode($bean->si_message_id));

            $bean->load_relationship('si_email_writer_leads_1');
            if ($bean->si_email_writer_leads_1) {
                $relatedPrompt = $bean->si_email_writer_leads_1->get();
                if (isset($relatedPrompt[0])) {
                    $prompt = $relatedPrompt[0];
                    $oe_id = DBHelper::select("outboundemailaccounts_si_email_writer_1_c", "outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida AS oe_id", ["outboundemailaccounts_si_email_writer_1si_email_writer_idb" => ["=", $prompt]]);
                    $oe_id = isset($oe_id[0]["oe_id"]) ? $oe_id[0]["oe_id"] : null;
                }
            }

            $response = MailApiAdapter::sendEmail($toEmailAddress, $toName, $bean->si_email_subject ?? '', $bean->si_email_body, $messageId, $oe_id, $userId);

            if (isset($response['error']) && $response['error'])
                return self::sendError($response['error']);

            $bean->si_message_id = base64_encode($response['message_id']);

            // Update conversation history
            $sentAt = gmdate('Y-m-d H:i:s', strtotime('now'));
            $historyItem = [
                'type' => 'sent',
                'sent_at' => $sentAt,
                'message' => [
                    'from' => $senderName,
                    'subject' => $bean->si_email_subject ?? '',
                    'body' => strip_tags($bean->si_email_body),
                    'thread_id' => $response['thread_id'] ?? null,
                ],
                'read_at' => null,
            ];

            if (!$bean->si_conversation_history) {
                $bean->si_conversation_history = json_encode([$historyItem]);
            } else {
                $history = json_decode(html_entity_decode($bean->si_conversation_history), true);
                $history[] = $historyItem;
                $bean->si_conversation_history = json_encode($history);
            }

            if (isset($response['thread_id']) && $response['thread_id'])
                $bean->si_thread_id = $response['thread_id'];

            $bean->si_email_body = '';
            $bean->si_email_status = $email_type == 'first' ? 'sent' : 'followup_sent';
            $bean->si_followups_counter = $bean->si_followups_counter + 1;
            $bean->si_emailed_at = $sentAt;
            $bean->save();
            return 'success';
        } catch (\Exception $e) {
            $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $e->getMessage());
            return $e->getMessage();
        }
    }

    private static function sendError($error)
    {
        $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $error);
        return json_encode(['error' => $error]);
    }
}
