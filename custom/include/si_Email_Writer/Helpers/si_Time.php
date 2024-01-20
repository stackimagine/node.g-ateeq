<?php

/**
 * This class is responsible for handling time-related operations.
 */
class si_Time
{
    /**
     * Gets the current time with an additional 3 seconds.
     *
     * @return string Current time in the specified format.
     * @access public
     */
    public static function getCurrent()
    {
        $timedate = $GLOBALS['timedate'];
        // Get the current date and add 3 seconds.
        $currentDateTime = date($timedate->get_db_date_time_format());
        $expiryDateTime = strtotime(date($timedate->get_db_date_time_format(), strtotime($currentDateTime)) . "+03 seconds");

        return gmdate($timedate->get_db_date_time_format(), $expiryDateTime);
    }

    /**
     * Calculates the expiration time of Google OAuth2 Access Token.
     *
     * @param int $TokenExpiresIn Expiration time limit of Google OAuth2 Access Token.
     * @return int Expiration time calculated from the current time.
     * @access public
     */
    public static function getTokenExpiryTime($TokenExpiresIn)
    {
        // Calculate and return the expiry time.
        return strtotime(self::getCurrent()) + $TokenExpiresIn;
    }
}
