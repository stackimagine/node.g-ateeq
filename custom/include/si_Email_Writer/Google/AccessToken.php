<?php

namespace si_Email_Writer\Google;

use si_Email_Writer\apiCalls\OAuthApiAdapter;
use si_Email_Writer\Sugar\Helpers\UpdateBean;

require_once 'custom/include/si_Email_Writer/Helpers/si_Time.php';
require_once 'custom/include/si_Email_Writer/lib/si_GoogleOauthHandler.php';

/**
 * This class is responsible for getting Access Token from Google Client
 */
class AccessToken
{
    /**
     * This function gets the Access Token
     * @param string $id User ID of the logged in User
     * @return string access_token is returned
     */
    public static function getToken($userID)
    {
        $creds = \si_GoogleOauthHandler::getStoredCredentials($userID);
        if (!$creds)
            return null;

        $currentTime = \si_Time::getCurrent();
        $date = date_create($currentTime);
        $currentTimeinSeconds = date_format($date, "U");
        if ($currentTimeinSeconds > $creds['expires_in']) {
            $newCredentials = OAuthApiAdapter::refreshAccessToken($creds['refresh_token'], $userID);
            UpdateBean::update('users', $newCredentials);
            $creds['access_token'] = $newCredentials[0]['si_google_access_code'];
            $creds['expires_in'] = $newCredentials[0]['si_google_auth_expires_in'];
        }
        return $creds['access_token'];
    }
}
