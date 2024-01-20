<?php

namespace si_Email_Writer\apiCalls;

/**
 * This class is responsible for making CURL calls related to the Authentication
 */
class OAuthApiAdapter extends ApiAdapter
{
    /**
     * Creates a URL for the API call related to OAuth 2.
     *
     * @param string $requestType The method of request.
     * @param array $fields Fields to attach in the URL.
     * @return string The generated URL.
     */
    private static function makeRequestURL($requestType, $fields = [])
    {
        global $sugar_config;
        switch ($requestType) {

            case 'authorize':
                $url = 'https://accounts.google.com/o/oauth2/v2/auth';
                $url .= '?response_type=code&access_type=offline';
                $url .= '&client_id=' . $sugar_config['GOOGLE']['CLIENT_ID'];
                $url .= '&redirect_uri=' . urlencode($sugar_config['GOOGLE']['REDIRECT_URI']);
                $url .= '&state=' . urlencode($fields['state']);
                $url .= '&scope=' . implode('%20', $fields['scopes']);
                $url .= '&user_id=' . $fields['si_gmail_id'];
                $url .= '&prompt=consent';
                $url .= '&flowName=GeneralOAuthFlow';
                return $url;

            case 'authenticate':
                $url = 'https://oauth2.googleapis.com/token';
                $url .= '?client_id=' . $sugar_config['GOOGLE']['CLIENT_ID'];
                $url .= '&client_secret=' . $sugar_config['GOOGLE']['CLIENT_SECRET'];
                $url .= '&code=' . $fields['code'];
                $url .= '&redirect_uri=' . $sugar_config['GOOGLE']['REDIRECT_URI'];
                $url .= '&grant_type=authorization_code';
                return $url;

            case 'refreshAccessToken':
                $url = 'https://oauth2.googleapis.com/token';
                $url .= '?client_id=' . $sugar_config['GOOGLE']['CLIENT_ID'];
                $url .= '&client_secret=' . $sugar_config['GOOGLE']['CLIENT_SECRET'];
                $url .= '&refresh_token=' . $fields['refreshToken'];
                $url .= '&grant_type=refresh_token';
                return $url;
        }
    }

    /**
     * Returns a URL for the authentication.
     *
     * This function just returns a string URL and doesn't interact with Google APIs.
     *
     * @return string $url
     */
    public static function authorize()
    {
        global $sugar_config, $current_user;
        $scopes = $sugar_config['GOOGLE']['SCOPES'];
        $state = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $delimiters = ['/index.php', '/#', '/legacy'];
        foreach ($delimiters as $delimiter) {
            $length = strpos($state, $delimiter);
            if ($length !== false)
                $state = substr($state, 0, $length);
        }
        if (version_compare($sugar_config['suitecrm_version'], '8', '<'))
            $state .= "/index.php?module=Users&action=si_GoogleOauth";
        else
            $state .= "/#/Users/si_GoogleOauth";

        return self::makeRequestURL('authorize', ['scopes' => $scopes, 'state' => $state, 'si_gmail_id' => $current_user->si_gmail_id]);
    }

    /**
     * gets access and refresh tokens against a code after a user authenticates
     * @param string $code one-time-use token fetched after authentication
     * @return string $response
     */
    public static function authenticate($code, $userID)
    {
        $url = self::makeRequestURL('authenticate', ['code' => $code]);
        $response = ApiAdapter::call('POST', $url, true);
        return self::prepareResponse($response, $userID);
    }

    /**
     * gets access and refresh tokens against a code after a user authenticates
     * @param string $refreshToken Refresh token
     * @return string $response
     */
    public static function refreshAccessToken($refreshToken, $userID)
    {

        $url = self::makeRequestURL('refreshAccessToken', ['refreshToken' => $refreshToken]);
        $response = ApiAdapter::call('POST', $url, true);
        return self::prepareResponse($response, $userID);
    }

    /**
     * Prepares the API response.
     *
     * @param array $response The API response.
     * @param string $userID The user ID.
     * @return array|null The prepared response.
     */
    public static function prepareResponse($response, $userID)
    {
        if (!empty($response['access_token'])) {
            $TokenExpiryTime = \si_Time::getTokenExpiryTime($response['expires_in']);
            $newCredentials = [[
                'id' => $userID,
                'si_google_access_code' => $response['access_token'],
                'si_google_auth_expires_in' => $TokenExpiryTime,
            ]];
            if ($response['refresh_token'])
                $newCredentials[0]['si_google_refresh_code'] = $response['refresh_token'];
            return $newCredentials;
        } else {
            $GLOBALS['log']->fatal("Error in getting access token: " . print_r($response, 1));
        }
    }
}
