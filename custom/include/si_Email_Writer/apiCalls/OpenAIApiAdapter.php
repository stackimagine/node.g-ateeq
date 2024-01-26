<?php

namespace si_Email_Writer\apiCalls;

use si_Email_Writer\apiCalls\ApiAdapter;
use si_Email_Writer\Sugar\Helpers\DBHelper;

/**
 * This class is responsible for making CURL calls to OpenAI GPT API
 */
class OpenAIApiAdapter extends ApiAdapter
{
    public static $baseURL = 'https://api.openai.com/v1/chat/completions';

    public static function firstEmail($name, $personDesc = '', $companyDesc = '', $userId = '', $prompt_id = null)
    {
        $prompt = self::getPrompt("description", $prompt_id, $userId);
        $prompt['description'] .= "The simpler your language is, the better.\nWrite like you’re writing for a 9 year old.\nIf a word doesn’t add value, remove it.\nKeep it short, 8 to 10 words in a sentence max.\nMake use of new lines instead of writing in a single paragraph.\nRemove the “,”.\nWrite short email: 5-6 sentences max\n[Observation] - 1 sentence\n[Problem] - 1 sentence\n[Credibility] - 1 sentence\n[Solution] - 1 sentence\n[CTA] - 1 sentence\nIf you manage to discover something genuinely personal and positive to express, conclude your email with a Post Script (PS) regarding that matter.\nBe clear + Be concise\nEmail content should be most suitable according to the following criteria\nDemographic information: age, gender, location, income level, industry,\nPsychographic information: The interests, values, attitudes, and lifestyle preferences\nPain points and challenges: Effectively address your target issues.\nGoals and aspirations: Position your offerings as solutions\nValue proposition alignment: With the prospects\' needs and preferences\nThe subject cannot be greater than two words.\nFrequently use new lines in the email body.\nRespond with an actual email (no placeholders) in JSON format (use subject and body as keys for the json). Do not add signature, I\'ll add signature myself.\nAVOID THE FOLLOWING PATTERNS/WORDS\n• Virtual coffee e-meet\n• Quick call\n• Jump on a 15-minute chat?\n• Pick your brain\n• I’d love to network....\n• Evaluate synergies\n• I’m impressed by your profile.\n• I don’t want to waste your time....\n• I know you’re busy, but....\n• Happy {{Weekday name}}!\n• Hope you’re well\n• To see if we’re a fit\n• Just touching base\n• Quick question\n• I’d love to...\n• I’ll be brief\n• Just wondering\n• Just checking in\n• Just following up\n• Just circling back\n• Did you get my last email?\n• We work with companies like {{companyName}}\n• My name is ____ & I’m with ____\n• Who from {{companyName}} Is a better person to connect with??\n• Let’s connect\n• Popping on top of your inbox\n• Sorry to be persistant";

        $systemPrompt = $prompt['description'];
        if (!$systemPrompt) {
            return ['error' => 'Prompt not found, <a href = "index.php?module=si_Email_Writer&action=EditView"> create one here </a>'];
        }
        $userPrompt = 'Name of the person: ' . $name . '\n';
        $userPrompt .= $personDesc ? 'Bio of the person: ' . $personDesc . '\n' : '';
        $userPrompt .= $companyDesc ? 'The rest of the information is about the person\'s company\n' . $companyDesc . '\n' : '';
        $body = [
            "model" => $prompt['large_language_model'] ? $prompt['large_language_model'] : 'gpt-3.5-turbo',
            "messages" => [
                [
                    "role" => "system",
                    "content" => $systemPrompt
                ],
                [
                    "role" => "user",
                    "content" => $userPrompt
                ]
            ]
        ];
        $response = ApiAdapter::call(
            'POST',
            self::$baseURL,
            false,
            '',
            $body,
            'openai'
        );
        $parsedData = self::parseEmail($response);
        if (is_array($parsedData)) {
            foreach ($parsedData as $key => $element) {
                $parsedData[$key] = str_replace(['<4SpacesHere>', '<backslashTHere>', '<backslashRHere>', '<lineBreakHere>'], ['&nbsp;&nbsp;&nbsp;&nbsp;', '&emsp;', '<br>', '<br>'], $parsedData[$key]);
                $parsedData[$key] = preg_replace('/,/', ', ', $parsedData[$key]);
            }
        }
        return $parsedData;
    }

    public static function followupEmail($conversation, $name, $personDesc = '', $companyDesc = '', $userId = '1', $prompt_id = null)
    {
        $prompt = self::getPrompt("followup_prompt", $prompt_id, $userId);
        $systemPrompt = $prompt['followup_prompt'];
        if (!$systemPrompt) {
            return ['error' => 'Prompt not found, <a href = "index.php?module=si_Email_Writer&action=EditView"> create one here </a>'];
        }
        $systemPrompt .= "Remember, I'll use your output directly in my mail without modifying it, so DO NOT ADD ANYTHING ELSE that cannot be copy pasted as it is to the followup email.\nThe simpler your language is, the better.\nWrite like you’re writing for a 9 year old.\nIf a word doesn’t add value, remove it.\nKeep it short, 8 to 10 words in a sentence max.\nMake use of new lines instead of writing in a single paragraph.\nRemove the “,”.\nAVOID THE FOLLOWING PATTERNS/WORDS\n• Virtual coffee e-meet\n• Quick call\n• Jump on a 15-minute chat?\n• Pick your brain\n• I’d love to network....\n• Evaluate synergies\n• I’m impressed by your profile.\n• I don’t want to waste your time....\n• I know you’re busy, but....\n• Happy {{Weekday name}}!\n• Hope you’re well\n• To see if we’re a fit\n• Just touching base\n• Quick question\n• I’d love to...\n• I’ll be brief\n• Just wondering\n• Just checking in\n• Just following up\n• Just circling back\n• Did you get my last email?\n• We work with companies like {{companyName}}\n• My name is ____ & I’m with ____\n• Who from {{companyName}} Is a better person to connect with??\n• Let’s connect\n• Popping on top of your inbox\n• Sorry to be persistant";

        $userPrompt = 'Conversation history: ' . $conversation . '\n';
        $userPrompt = 'Name of the person: ' . $name . '\n';
        $userPrompt .= $personDesc ? 'Bio of the person: ' . $personDesc . '\n' : '';
        $userPrompt .= $companyDesc ? 'The rest of the information is about the person\'s company\n' . $companyDesc . '\n' : '';
        $body = [
            "model" => $prompt[0]['large_language_model'] ? $prompt['large_language_model'] : 'gpt-3.5-turbo',
            "messages" => [
                [
                    "role" => "system",
                    "content" => $systemPrompt
                ],
                [
                    "role" => "user",
                    "content" => $userPrompt
                ]
            ]
        ];
        $response = ApiAdapter::call(
            'POST',
            self::$baseURL,
            false,
            '',
            $body,
            'openai'
        );
        $message = isset($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : '';
        $message = ltrim($message, '```json');
        $message = trim($message, '```');
        $message = trim($message);
        $parsedMessage = json_decode(json_encode($message), 1);
        $res = $parsedMessage ? $parsedMessage : ($message ? $message : $response);
        return ['body' => $res];
    }

    private static function parseEmail($response)
    {
        $message = isset($response['choices'][0]['message']['content']) ? $response['choices'][0]['message']['content'] : '';

        if (isset($message['body']))
            return $message;
        // Remove optional ```json``` and trim whitespace
        $jsonString = trim(preg_replace('/^```json|```$/i', '', $message));
        // If the content starts with `json` within backticks, remove it
        $jsonString = preg_replace('/^```json/i', '', $jsonString);

        // Decode the JSON string
        $parsedData = json_decode($jsonString, true);
        // Check if the decoding was successful and 'body' is present
        if ($parsedData !== null && isset($parsedData['body'])) {
            return $parsedData;
        }

        // Remove unnecessary backslashes
        $jsonString = str_replace("\\\\", "\\", $jsonString);
        // Replace escaped newlines with actual newlines
        $jsonString = str_replace("\\n", "\n", $jsonString);
        // Remove extra whitespaces around JSON elements
        $pattern = '/(?<=[{}])\s+|(?<=["\'])\s+|\s+(?=[{}])|\s+(?=["\'])|(?<=,)\s+(?="body)/s';
        $jsonString = preg_replace($pattern, '', $jsonString);
        $jsonString = preg_replace('/,\s+/', ',', $jsonString);

        // Decode the JSON string
        $parsedData = json_decode($jsonString, true);
        // Check if the decoding was successful and 'body' is present
        if ($parsedData !== null && isset($parsedData['body'])) {
            return $parsedData;
        }

        // replace whitespaces with placeholders
        $jsonString = str_replace(["\n", "\r", "\t", "    ", '\n'], ['<lineBreakHere>', '<backslashRHere>', '<backslashTHere>', '<4SpacesHere>', '<lineBreakHere>'], $jsonString);
        // Decode the JSON string again
        $parsedData = json_decode($jsonString, true);

        // Check if the decoding was successful and 'body' is present
        if ($parsedData !== null && isset($parsedData['body'])) {
            return $parsedData;
        }
        // Attempt decoding with stripslashes if the previous attempts fail
        $parsedData = json_decode(stripslashes($jsonString), true);

        // Check if the decoding was successful and 'body' is present
        if ($parsedData !== null && isset($parsedData['body'])) {
            return $parsedData;
        }
        $GLOBALS['log']->fatal("SI Email Writer: failed to parse the email json from the API response");
        $GLOBALS['log']->fatal(print_r($response, 1));
        // Return the original body if 'body' is not found
        return ['body' => $jsonString];
    }

    private static function getPrompt($type = "followup_prompt", $prompt_id = null, $userId = '1')
    {
        if ($prompt_id) {
            $prompt = DBHelper::select('si_Email_Writer', [$type, 'large_language_model'], [
                'id' => ['=', $prompt_id],
            ]);
        } else {
            $prompt = DBHelper::select('si_Email_Writer', [$type, 'large_language_model'], [
                'assigned_user_id' => ['=', $userId],
                'deleted' => ['=', '0']
            ], 'date_modified DESC');
        }
        return $prompt[0];
    }
}

