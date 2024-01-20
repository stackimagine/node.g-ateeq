<?php

namespace si_Email_Writer\Helpers;

use si_Email_Writer\Sugar\Helpers\DBHelper;

/**
 * This class checks for new messages and marks the leads who have replied to not be contacted via automation.
 */
class MarkReplyReceived
{
    public static function run($module = 'Leads')
    {
        $inboundEmails = DBHelper::select('inbound_email', 'id', ['deleted' => ['=', 0], 'status' => ['=', 'Active']]);
        foreach ($inboundEmails as $key => $value) {
            $ie = \BeanFactory::newBean('InboundEmail');
            $ie->retrieve($value['id'], array('disable_row_level_security' => true));
            $hostname = '{' . $ie->server_url . ':' . $ie->port . '/ssl}INBOX';
            $username = $ie->email_user;
            $password = $ie->email_password;

            try {
                $mailbox = imap_open($hostname, $username, $password);
                if ($mailbox === false) {
                    $GLOBALS['log']->fatal($username . ' Cannot connect to mailbox: ' . imap_last_error());
                    continue;
                }

                $since_time = strtotime('-24 hours', time());
                $emails = imap_search($mailbox, 'SINCE "' . date('d-M-Y', $since_time) . '"');

                if (!$emails) {
                    imap_close($mailbox);
                    return false;
                }

                $senders = [];

                foreach ($emails as $email_number) {
                    $header = imap_headerinfo($mailbox, $email_number);
                    $senders[] = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                }
                $senders = array_unique($senders);
                foreach ($senders as $sender) {
                    $leadId = self::getLeadIdByEmail($sender, $module);
                    if (!$leadId)
                        continue;

                    $bean = \BeanFactory::retrieveBean($module, $leadId, array('disable_row_level_security' => true));
                    $bean->si_email_status = 'reply_received';
                    $bean->save();
                }
                imap_close($mailbox);
                return true;
            } catch (\Exception $e) {
                $GLOBALS['log']->fatal($e->getMessage());
                return false;
            }
        }
    }
    public static function getLeadIdByEmail($email, $module = 'Leads')
    {
        $query = "SELECT eabr.bean_id 
                  FROM email_addresses ea 
                  INNER JOIN email_addr_bean_rel eabr 
                  ON eabr.email_address_id = ea.id 
                  AND eabr.bean_module = '?' 
                  AND eabr.deleted = 0 
                  WHERE ea.deleted = 0 
                  AND ea.email_address = '?'";
        $result = $GLOBALS['db']->pQuery($query, [ucfirst($module), $email]);

        $row = $GLOBALS['db']->fetchByAssoc($result);
        return $row['bean_id'];
    }
}
