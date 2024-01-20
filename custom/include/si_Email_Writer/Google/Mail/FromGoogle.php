<?php

namespace si_Email_Writer\Google\Mail;

use si_Email_Writer\Sugar\Helpers\DBHelper;
use si_Email_Writer\apiCalls\GMailApiAdapter;

/**
 * This class is responsible for getting data from Google
 */
class FromGoogle
{
    /**
     * This function gets a list of messages from the authenticated user's mailbox
     * @param string $userID id of the logged in user
     * @return array $res response from the API
     */
    public static function listMessages($userID)
    {
        return GMailApiAdapter::listMessages($userID);
    }

    /**
     * This function gets a specific message by its ID
     * @param string $userID id of the logged in user
     * @param string $messageId ID of the message to retrieve
     * @return array $res response from the API
     */
    public static function getMessage($userID, $messageId)
    {
        return GMailApiAdapter::getMessage($userID, $messageId);
    }
}
