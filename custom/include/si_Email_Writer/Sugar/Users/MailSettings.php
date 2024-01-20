<?php

namespace si_Email_Writer\Sugar\Users;

/**
 * This class is responsible for handling mail synchronization settings
 */
class MailSettings
{
    /**
     * This function saves mail settings in the Users table
     * @param object $gBean GSync Bean
     * @param string $gID User's gmail id
     * @param bool $multi_calendar
     * @param array $mailSettings List of mail settings
     */
    public static function save($gBean, $mailSettings, $gID)
    {
        $updatedMailSettings = [];

        $mailSyncSettings = json_decode(base64_decode(stripslashes(html_entity_decode($gBean->si_mail_sync_settings))), true);

        if (empty($mailSyncSettings)) {
            $mailSyncArr = [];
            foreach ($mailSettings as $mailSetting) {
                $mailSyncArr[] = self::populateArray($mailSetting);
            }
            $gBean->si_mail_sync_settings = base64_encode(json_encode($mailSyncArr));
        } else {
            foreach ($mailSettings as $mailSetting) {
                $mailSettingId = $mailSetting['id'];
                $updatedMailSettings[$mailSettingId] = $mailSettingId;

                if (!isset($mailSyncSettings[$mailSettingId])) {
                    $mailSyncSettings[$mailSettingId] = self::populateArray($mailSetting);
                }
            }

            foreach ($mailSyncSettings as $id => $mailSetting) {
                if (!isset($updatedMailSettings[$id])) {
                    unset($mailSyncSettings[$id]);
                }
            }

            $gBean->si_mail_sync_settings = base64_encode(json_encode(array_values($mailSyncSettings)));
        }

        if (method_exists($gBean, 'save')) {
            $gBean->save();
        } else {
            $GLOBALS['log']->fatal("RTGSync Exception: Failed to save " . $gBean->name . __FILE__ . ":" . __LINE__);
        }
        return;
    }

    /**
     * This function updates the status of a mail setting
     * @param array $mailSetting Mail setting
     * @return array $mailSetting
     */
    public static function updateMailSettingStatus($mailSetting)
    {
        $optParams = null;
        switch ($mailSetting['status']) {
            case 'new':
                $mailSetting['status'] = 'Inprogress';
                break;
            case 'Inprogress':
                if ($mailSetting['pageToken']) {
                    $optParams['pageToken'] = $mailSetting['pageToken'];
                }
                break;
            case 'Completed':
                $optParams['syncToken'] = $mailSetting['syncToken'];
                break;
        }
        return isset($optParams) ? [$mailSetting, $optParams] : [$mailSetting];
    }

    /**
     * This function updates the data of a mail setting
     * @param array $mailSetting Mail setting
     * @param array $messages Messages returned from Google Mail
     * @return array $mailSetting
     */
    public static function updateMailSettingData($mailSetting, $messages)
    {
        if (isset($messages['nextPageToken'])) {
            $mailSetting['pageToken'] = $messages['nextPageToken'];
        } elseif ($mailSetting['status'] == 'Inprogress') {
            $mailSetting['pageToken'] = '';
            $mailSetting['status'] = 'Completed';
        }
        $mailSetting['syncToken'] = $messages['nextSyncToken'] ?: '';
        return $mailSetting;
    }

    /**
     * This function populates mail setting data
     * @param array $mailSettingRecord Mail setting data
     * @return array $mailSetting
     */
    public static function populateArray($mailSettingRecord = null)
    {
        $mailSetting = [
            'id' => isset($mailSettingRecord) ? $mailSettingRecord['id'] : 'primary',
            'mailbox_name' => isset($mailSettingRecord) ? $mailSettingRecord['mailbox_name'] : 'INBOX',
            'status' => 'new',
            'pageToken' => '',
            'syncToken' => '',
        ];
        return $mailSetting;
    }
}
