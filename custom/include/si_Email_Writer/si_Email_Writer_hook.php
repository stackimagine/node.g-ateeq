<?php
require_once("custom/include/si_Email_Writer/autoload.php");

use si_Email_Writer\Sugar\Helpers\DBHelper;

class si_Email_WriterHook
{
    /**
     * This function associates a lead with an account based on company linkedin url
     *
     *
     * @param  object  $bean    bean
     * @param  string  $event   sugar status
     * @access public
     */
    function linkAccountToLead($bean, $event)
    {
        if ($bean->si_conversation_history == '') {
            $bean->si_conversation_history = null;
        }

        if (empty($bean->si_company_linkedin_profile) && empty($bean->website)) {
            return false;
        }

        if (!empty($bean->si_company_linkedin_profile))
            $account = DBHelper::select("accounts", "id", ["si_linkedin_profile" => ["=", $bean->si_company_linkedin_profile]]);

        if (empty($account) && !empty($bean->website))
            $account = DBHelper::select("accounts", "id", ["website" => ["=", $bean->website]]);

        if (!empty($account)) {
            $bean->account_id = $account[0]['id'];
            $bean->si_company_linkedin_profile = '';
            $bean->website = '';
        }
    }

    /**
     * This function sets status when the linkedin bio is added in a lead
     *
     *
     * @param  object  $bean    bean
     * @param  string  $event   sugar status
     * @access public
     */
    function setBioStatus($bean, $event)
    {
        if (
            $bean->si_email_status == 'New' && !empty($bean->description)
        ) {
            $bean->si_email_status = 'data_entered';
        }
    }

    function linkGmailAccount($bean, $event)
    {
        global $current_user;
        if ($bean->module_dir == 'Users' && $current_user->id == $bean->id) {
            if (!empty($bean->si_gmail_id) && ($bean->fetched_row['si_gmail_id'] != $bean->si_gmail_id || empty($bean->si_google_refresh_code))) {
                $bean->fetched_row['si_gmail_id'] = $bean->si_gmail_id;
                $GLOBALS['log']->fatal("redirecting to GoogleOauth that will redirect to the login screen...");
                SugarApplication::redirect("index.php?module=Users&action=si_GoogleOauth");
            }
        }
    }
};
