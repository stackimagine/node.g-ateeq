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
        return self::parseEmail($response);
    }

    public static function followupEmail($conversation, $name, $personDesc = '', $companyDesc = '', $userId = '1', $prompt_id = null)
    {
        $prompt = self::getPrompt("followup_prompt", $prompt_id, $userId);
        $systemPrompt = $prompt['followup_prompt'];
        if (!$systemPrompt) {
            return ['error' => 'Prompt not found, <a href = "index.php?module=si_Email_Writer&action=EditView"> create one here </a>'];
        }
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

        $emailContent = trim($message);
        $emailContent = ltrim($emailContent, '```json');
        $emailContent = trim($emailContent, '```');
        $emailContent = trim($emailContent);

        $emailContent2 = json_decode($emailContent, true);
        if (isset($emailContent2['body']))
            return $emailContent2;

        $emailContent2 = json_decode(json_encode($emailContent), true);
        if (isset($emailContent2['body']))
            return $emailContent2;

        $emailContent = str_replace(["\n", "\r", "\t", "    ", '
'], ['<lineBreakHere>', '<backslashRHere>', '<backslashTHere>', '<4SpacesHere>', '<lineBreakHere>'], $emailContent);
        do {
            $emailContent = str_replace(['<lineBreakHere>"body":', '<backslashRHere>"body":', '<backslashTHere>"body":', '<4SpacesHere>"body":'], '"body":', $emailContent, $count);
        } while ($count > 0);

        $emailContent2 = json_decode($emailContent, true);


        if (isset($emailContent2['body'])) {
            return str_replace(['<4SpacesHere>', '<backslashTHere>', '<backslashRHere>', '<lineBreakHere>'], ['&nbsp;&nbsp;&nbsp;&nbsp;', '&emsp;', '<br>', '<br>'], $emailContent2);
        }

        $emailContent = preg_replace('/(",\s*"body":)([^"]*)/', '$1""', $emailContent);
        $emailContent = preg_replace('/(""")/g', '"', $emailContent);
        $jsonEmailContent = str_replace("\n", "<br>", $emailContent);
        $emailContent = json_decode(json_encode($jsonEmailContent), 1);

        $emailContent = str_replace(['<4SpacesHere>', '<backslashTHere>', '<backslashRHere>', '<lineBreakHere>'], ['&nbsp;&nbsp;&nbsp;&nbsp;', '&emsp;', '<br>', '<br>'], $emailContent);

        return isset($emailContent['body'])
            ? $emailContent
            : (
                $emailContent
                ? ["body" => $emailContent]
                : (
                    $message
                    ? ["body" => $message]
                    : ["body" => $response]
                )
            );
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
