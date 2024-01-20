<?php
$dashletData['si_email_writerDashlet']['searchFields'] = [
  'name' => [
    'default' => '',
  ],
  'timezone' => [
    'default' => '',
  ],
  'require_approval' => [
    'default' => '',
  ],
  'followup_require_approval' => [
    'default' => '',
  ],
  'campaign_days' => [
    'default' => '',
  ],
  'email_frequency' => [
    'default' => '',
  ],
  'date_modified' => [
    'default' => '',
  ],
  'assigned_user_id' => [
    'default' => '',
  ],
];

$dashletData['si_email_writerDashlet']['columns'] = [
  'name' => [
    'width' => '40%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ],
  'date_modified' => [
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'name' => 'date_modified',
    'default' => false,
  ],
  'large_language_model' => [
    'type' => 'enum',
    'default' => false,
    'studio' => 'visible',
    'label' => 'LBL_LARGE_LANGUAGE_MODEL',
    'width' => '10%',
    'name' => 'large_language_model',
  ],
  'timezone' => [
    'type' => 'enum',
    'default' => false,
    'studio' => 'visible',
    'label' => 'LBL_TIMEZONE',
    'width' => '10%',
    'name' => 'timezone',
  ],
  'assigned_user_name' => [
    'width' => '8%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'name' => 'assigned_user_name',
    'default' => false,
  ],
];
