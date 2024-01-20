<?php

namespace si_Email_Writer\apiCalls;

use si_Email_Writer\apiCalls\ApiAdapter;
use si_Email_Writer\Sugar\Helpers\DBHelper;

include_once('include/SugarPHPMailer.php');

/**
 * This class is responsible for making CURL calls to Google Mail API
 */
class MailApiAdapter
{
    /**
     * This function gets a list of messages from the authenticated user's mailbox
     * @param string $userID id of the logged in user
     * @return string $response
     */
    public static function listMessages($userID)
    {
        $url = self::makeRequestURL('listMessages');
        return ApiAdapter::call('GET', $url, false, $userID);
    }

    /**
     * This function gets a specific message by its ID
     * @param string $userID id of the logged in user
     * @param string $messageId ID of the message to retrieve
     * @return string $response
     */
    public static function getMessage($userID, $messageId)
    {
        $url = self::makeRequestURL('getMessage', ['messageId' => $messageId]);
        return ApiAdapter::call('GET', $url, false, $userID);
    }

    /**
     * This function sends an email using SugarPHPMailer
     * @param string $from email address of the sender
     * @param string $fromName name of the sender
     * @param string $to email address of the recipient
     * @param string $subject email subject
     * @param string $message email body in HTML or plain text
     * @param string $signature email signature
     * @return string $response
     */
    public static function sendEmail($to, $toName, $subject, $message, $messageId = null, $oe_id = null, $userId = null)
    {
        $message = nl2br(trim($message));
        if (!$userId) {
            global $current_user;
            $userId = $current_user->id;
        }
        if (!$oe_id) {
            $res = DBHelper::select('outbound_email', 'id', [
                'type' => ['=', 'user'],
                'user_id' => ['=', $userId],
                'deleted' => ['=', '0']
            ], 'date_modified DESC');
            $oe_id = $res[0]['id'];
        }

        $mailoe = new \OutboundEmail();
        $mailoe = $mailoe->retrieve($oe_id, array('disable_row_level_security' => true));
        if (!empty($mailoe->signature)) {
            $message .= "<br><br><div style='color: #888;'>$mailoe->signature</div>";
        }

        $mail = new \SugarPHPMailer(true);
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->AddAddress($to, $toName);
        $mail->From = $mailoe->smtp_from_addr;
        $mail->FromName = $mailoe->smtp_from_name;
        $mail->Subject = $subject;
        $mail->Body_html = from_html($message);
        $mail->Body = wordwrap($message, 900);
        if ($messageId && !empty($messageId)) {
            $mail->addCustomHeader('In-Reply-To',  $messageId);
            $mail->addCustomHeader('References',  $messageId);
        }
        $mail->isHTML(true);
        $mail->isSMTP();
        $mail->Host = $mailoe->mail_smtpserver;
        $mail->SMTPAuth = true;
        $mail->Username = $mailoe->mail_smtpuser;
        $mail->Password = $mailoe->mail_smtppass;
        $mail->SMTPSecure = $mailoe->mail_smtpssl;
        $mail->Port = $mailoe->mail_smtpport;
        $mail->prepForOutbound();

        $response = $mail->send();

        if (!$response) {
            $GLOBALS['log']->fatal("si_Email_Writer ERROR: Mail sending failed!", print_r($response, 1));
            return ['error' => "si_Email_Writer ERROR: Mail sending failed!"];
        }
        return ['message_id' => $mail->getLastMessageID()];
    }
}
