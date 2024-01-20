<?php

namespace si_Email_Writer\apiCalls;

use si_Email_Writer\Google\AccessToken as GoogleAccessToken;
use si_Email_Writer\OpenAI\AccessToken as OpenAIAccessToken;

/**
 * This class makes a CURL call
 */
class ApiAdapter
{
    /**
     * Creates a curl call with data provided
     * @param string $method e.g. POST
     * @param string $url to be appended after standard API URL e.g. contacts/import
     * @param bool $newToken If the request is for new accessToken
     * @param string $userID id of the logged in user to get the access token
     * @param string $type google, openai, anthropic etc
     * @param string $post_fields If method is PATCH, add data payload
     * @param string $mimeType mimeType of file, only used while uploading a file
     * @param string $jsonEncode Encode the post fields of a regular call. If we need to upload the contents of a file, don't json encode its contents
     * @param string $parseResponse Parse the response of a regular call, if downloading a file don't parse the response
     * @return array $response Response from the CURL call
     */
    public static function call($method, $url, $newToken = false, $userID = '', $post_fields = '', $type = "google", $mimeType = '', $jsonEncode = true, $parseResponse = true)
    {
        $curl = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 180,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method)
        ];

        if ($newToken)
            $options[CURLOPT_HTTPHEADER] = array(
                "Content-Type:  application/x-www-form-urlencoded"
            );
        else {
            if (!$userID) {
                global $current_user;
                $userID = $current_user->id;
            }
            if ($type == 'google')
                $access_token = GoogleAccessToken::getToken($userID);
            else if ($type == 'openai')
                $access_token = OpenAIAccessToken::getToken($userID);
            if (!$access_token) return ['error' => 'access token not found for user with id: ' . $userID];

            $options[CURLOPT_HTTPHEADER] = array(
                "Authorization: Bearer " . $access_token,
                "accept: application/json",
                "content-type: application/json",
            );
        }
        if ($mimeType)
            $options[CURLOPT_HTTPHEADER][2] = "Content-Type: " . $mimeType;

        if (strtoupper($method) == 'PATCH' or strtoupper($method) == 'POST') {
            if ($jsonEncode)
                $options[CURLOPT_POSTFIELDS] = json_encode($post_fields);
            else
                $options[CURLOPT_POSTFIELDS] = $post_fields;
        }
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        // Check if any error occurred
        if (curl_errno($curl))
            $GLOBALS['log']->fatal('Curl error: ' . curl_error($curl));
        curl_close($curl);
        if (strtoupper($method) == 'DELETE')
            return;
        if (!$parseResponse) {
            $parsed = json_decode($response, 1);
            if (isset($parsed['error'])) {
                return $parsed;
            }
            return $response;
        }
        return self::parseResponse($url, $response);
    }

    public static function printCurlRequest($options)
    {
        $curlCommand = 'curl ';
        foreach ($options as $option => $value) {
            if ($option === CURLOPT_URL) {
                $curlCommand .= "'$value' ";
            } elseif ($option === CURLOPT_CUSTOMREQUEST) {
                $curlCommand .= "-X $value ";
            } elseif ($option === CURLOPT_POSTFIELDS) {
                $curlCommand .= "-d '$value' ";
            } elseif ($option === CURLOPT_HTTPHEADER) {
                foreach ($value as $header) {
                    $curlCommand .= "-H '$header' ";
                }
            }
        }
        $GLOBALS['log']->fatal("Curl Request: $curlCommand\n");
    }

    /**
     * Decodes json response for centralized error handling
     * @param string $url for logging purposes
     * @param string $response original response from Google in json format
     * @return array $response parsed response
     */
    public static function parseResponse($url, $response)
    {
        $parsed = json_decode($response, 1);
        if (count($parsed) == 0 || gettype($parsed) == 'NULL' || isset($parsed['error']))
            $GLOBALS['log']->fatal("Error from Google (URL: " . $url . "): " . $response);
        return $parsed;
    }
}
