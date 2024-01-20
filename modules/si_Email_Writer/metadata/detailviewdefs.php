<?php
$module_name = 'si_Email_Writer';
$viewdefs[$module_name] = [
  'DetailView' => [
    'templateMeta' => [
      'form' => [
        'buttons' => [
          'EDIT',
          'DUPLICATE',
          'DELETE',
          'FIND_DUPLICATES',
        ],
        'includes' => [
          [
            'file' => 'modules/si_Email_Writer/js/si_Email_Writer.js',
          ],
        ],
      ],
      'maxColumns' => '2',
      'widths' => [
        [
          'label' => '10',
          'field' => '30',
        ],
        [
          'label' => '10',
          'field' => '30',
        ],
      ],
      'useTabs' => false,
      'tabDefs' => [
        'DEFAULT' => [
          'newTab' => false,
          'panelDefault' => 'expanded',
        ],
      ],
      'syncDetailEditViews' => true,
    ],
    'panels' => [
      'default' => [
        [
          'name',
        ],
        [
          'description',
        ],
        [
          [
            'name' => 'api_key',
            'label' => 'LBL_LLM_API_KEY',
          ],
          [
            'name' => 'large_language_model',
            'studio' => 'visible',
            'label' => 'LBL_LARGE_LANGUAGE_MODEL',
          ],
        ],
        [
          'followup_prompt',
        ],
        [
          [
            'name' => 'timezone',
            'studio' => 'visible',
            'label' => 'LBL_TIMEZONE',
          ],
          [
            'name' => 'assigned_user_name',
            'studio' => 'visible',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ],
        ],
        [
          [
            'name' => 'require_approval',
            'studio' => 'visible',
            'label' => 'LBL_REQUIRE_APPROVAL',
          ],
          [
            'name' => 'followup_require_approval',
            'studio' => 'visible',
            'label' => 'LBL_FOLLOWUP_REQUIRE_APPROVAL',
          ],
        ],
        [
          [
            'name' => 'campaign_days',
            'studio' => 'visible',
            'label' => 'LBL_CAMPAIGN_DAYS',
          ],
          [
            'name' => 'email_frequency',
            'studio' => 'visible',
            'label' => 'LBL_EMAIL_FREQUENCY',
          ],
        ],
        [
          [
            'name' => 'start_time',
            'studio' => 'visible',
            'label' => 'LBL_START_TIME',
          ],
          [
            'name' => 'end_time',
            'studio' => 'visible',
            'label' => 'LBL_END_TIME',
          ],
        ],
        [
          [
            'name' => 'outboundemailaccounts_si_email_writer_1_name',
          ],
        ],
      ],
    ],
  ],
];
