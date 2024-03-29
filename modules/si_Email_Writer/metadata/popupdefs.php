<?php

$popupMeta = [
  'moduleMain' => 'si_Email_Writer',
  'varName' => 'si_Email_Writer',
  'orderBy' => 'si_Email_Writer.name',
  'whereClauses' => [
    'name' => 'si_Email_Writer.name',
    'large_language_model' => 'si_Email_Writer.large_language_model',
    'timezone' => 'si_Email_Writer.timezone',
    'require_approval' => 'si_Email_Writer.require_approval',
    'followup_require_approval' => 'si_Email_Writer.followup_require_approval',
    'campaign_days' => 'si_Email_Writer.campaign_days',
    'email_frequency' => 'si_Email_Writer.email_frequency',
    'start_time' => 'si_Email_Writer.start_time',
    'end_time' => 'si_Email_Writer.end_time',
    'assigned_user_id' => 'si_Email_Writer.assigned_user_id',
  ],
  'searchInputs' => [
    'name' => 'name',
    'large_language_model' => 'large_language_model',
    'timezone' => 'timezone',
    'require_approval' => 'require_approval',
    'followup_require_approval' => 'followup_require_approval',
    'campaign_days' => 'campaign_days',
    'email_frequency' => 'email_frequency',
    'start_time' => 'start_time',
    'end_time' => 'end_time',
    'assigned_user_id' => 'assigned_user_id',
  ],
  'searchdefs' => [
    'name' => [
      'name' => 'name',
      'width' => '10%',
    ],
    'large_language_model' => [
      'type' => 'enum',
      'studio' => 'visible',
      'label' => 'LBL_LARGE_LANGUAGE_MODEL',
      'width' => '10%',
      'name' => 'large_language_model',
    ],
    'timezone' => [
      'type' => 'enum',
      'label' => 'LBL_TIMEZONE',
      'width' => '10%',
      'name' => 'timezone',
    ],
    'require_approval' => [
      'type' => 'enum',
      'label' => 'LBL_REQUIRE_APPROVAL',
      'width' => '10%',
      'name' => 'require_approval',
    ],
    'followup_require_approval' => [
      'type' => 'enum',
      'label' => 'LBL_FOLLOWUP_REQUIRE_APPROVAL',
      'width' => '10%',
      'name' => 'followup_require_approval',
    ],
    'campaign_days' => [
      'type' => 'multienum',
      'label' => 'LBL_CAMPAIGN_DAYS',
      'width' => '10%',
      'name' => 'campaign_days',
    ],
    'email_frequency' => [
      'type' => 'int',
      'label' => 'LBL_EMAIL_FREQUENCY',
      'width' => '10%',
      'name' => 'email_frequency',
    ],
    'assigned_user_id' => [
      'name' => 'assigned_user_id',
      'label' => 'LBL_ASSIGNED_TO',
      'type' => 'enum',
      'function' => [
        'name' => 'get_user_array',
        'params' => [
          false,
        ],
      ],
      'width' => '10%',
    ],
  ],
  'listviewdefs' => [
    'NAME' => [
      'width' => '32%',
      'label' => 'LBL_NAME',
      'default' => true,
      'link' => true,
      'name' => 'name',
    ],
    'LARGE_LANGUAGE_MODEL' => [
      'type' => 'enum',
      'default' => true,
      'studio' => 'visible',
      'label' => 'LBL_LARGE_LANGUAGE_MODEL',
      'width' => '10%',
    ],
    'REQUIRE_APPROVAL' => [
      'type' => 'enum',
      'default' => true,
      'label' => 'LBL_REQUIRE_APPROVAL',
      'width' => '10%',
    ],
    'FOLLOWUP_REQUIRE_APPROVAL' => [
      'type' => 'enum',
      'default' => true,
      'label' => 'LBL_FOLLOWUP_REQUIRE_APPROVAL',
      'width' => '10%',
    ],
    'TIMEZONE' => [
      'type' => 'enum',
      'default' => true,
      'label' => 'LBL_TIMEZONE',
      'width' => '10%',
    ],
    'CAMPAIGN_DAYS' => [
      'type' => 'multienum',
      'default' => true,
      'label' => 'LBL_CAMPAIGN_DAYS',
      'width' => '10%',
    ],
    'EMAIL_FREQUENCY' => [
      'type' => 'int',
      'default' => true,
      'label' => 'LBL_EMAIL_FREQUENCY',
      'width' => '10%',
    ],
    'ASSIGNED_USER_NAME' => [
      'width' => '9%',
      'label' => 'LBL_ASSIGNED_TO_NAME',
      'module' => 'Employees',
      'id' => 'ASSIGNED_USER_ID',
      'default' => true,
      'name' => 'assigned_user_name',
    ],
  ],
];
