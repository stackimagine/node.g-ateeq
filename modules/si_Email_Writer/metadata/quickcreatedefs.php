<?php
$module_name = 'si_Email_Writer';
$viewdefs[$module_name] = [
  'QuickCreate' => [
    'templateMeta' => [
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
    ],
    'panels' => [
      'default' => [
        [
          'name',
          'assigned_user_name',
        ],
        [
          [
            'name' => 'api_key',
            'label' => 'LBL_LLM_API_KEY',
          ],
        ],
        [
          [
            'name' => 'large_language_model',
            'studio' => 'visible',
            'label' => 'LBL_LARGE_LANGUAGE_MODEL',
          ],
        ],
      ],
    ],
  ],
];
