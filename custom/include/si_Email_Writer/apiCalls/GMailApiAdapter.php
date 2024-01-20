<?php

namespace si_Email_Writer\apiCalls;

use si_Email_Writer\apiCalls\ApiAdapter;

/**
 * This class is responsible for making CURL calls to Google Mail API
 */
class GMailApiAdapter extends ApiAdapter
{
    public static $baseURL = 'https://gmail.googleapis.com/gmail/v1/users/';

    /**
     * This function creates a URL for the API call to Google Mail API
     * @param string $requestType method of request
     * @param array $fields fields to attach in the URL
     * @return string $url
     */
    private static function makeRequestURL($requestType, $fields = '', $userId = 'me')
    {
        $url = self::$baseURL;

        switch ($requestType) {
            case 'listMessages':
                $url .= $userId . '/messages/';
                break;
            case 'getMessage':
                $url .= $userId . '/messages/' . $fields['messageId'] . '/';
                break;
            case 'sendEmail':
                $url .= $userId . '/messages/send/';
                break;
            default:
                $url = '';
                $GLOBALS['log']->fatal('$requestType is required');
        }

        $GLOBALS['log']->debug("si_Email_Writer Mail URL for $requestType: " . $url);
        return $url;
    }

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
     * This function sends an email
     * @param string $from email address of the sender
     * @param string $fromName name of the sender
     * @param string $to email address of the recipient
     * @param string $message email to be sent in HTML or plain text
     * @param string $signature email signature
     * @return string $response
     */
    public static function sendEmail($from, $fromName, $to, $subject, $message, $signature = '')
    {
        $url = self::makeRequestURL('sendEmail');
        $message = nl2br(trim($message));
        $rawMessage = "From: $fromName<$from>\r\n" .
            "To: $to\r\n" .
            "Subject: $subject\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/html; charset=utf-8\r\n" .
            "\r\n" .
            "$message";
        if (!empty($signature)) $rawMessage .= "<br><br><div style='color: #888;'>$signature</div>";
        // Create the base64 encoded email message
        $base64Message = base64_encode($rawMessage);

        $data = [
            'raw' => $base64Message,
        ];

        $response = ApiAdapter::call('POST', $url, false, '', $data);

        return $response;
    }
}
