<?php

$manifest = [
  0 => [
    'acceptable_sugar_versions' => [
      'exact_matches' => [],
      'regex_matches' => ['6\.5\.[0-9]$'],
    ],
  ],
  1 => [
    'acceptable_sugar_flavors' => [
      0 => 'CE',
      1 => 'PRO',
      2 => 'ENT',
    ],
  ],
  'readme' => '',
  'key' => 'si',
  'author' => 'StackImagine',
  'description' => 'Send personalized cold emails through ChatGPT',
  'icon' => '',
  'is_uninstallable' => true,
  'name' => 'StackImagine Email Writer',
  'id' => 'StackImagine_Email_Writer',
  'published_date' => '2024-01-14 20:14:03',
  'type' => 'module',
  'version' => 2,
  'remove_tables' => 'prompt',
];

$installdefs = [
  'id' => 'StackImagine_Email_Writer_v2',
  'beans' => [
    0 =>
    [
      'module' => 'si_Email_Writer',
      'class' => 'si_Email_Writer',
      'path' => 'modules/si_Email_Writer/si_Email_Writer.php',
      'tab' => true,
    ],
  ],
  'layoutdefs' => [],
  'relationships' => [],
  'image_dir' => '<basepath>/custom/themes/default',
  'copy' => [
    [
      'from' => '<basepath>/modules/si_Email_Writer',
      'to' => 'modules/si_Email_Writer',
    ],
    [
      'from' => '<basepath>/custom',
      'to' => 'custom',
    ],
  ],
  'language' => [
    [
      'from' => '<basepath>/custom/Extension/application/Ext/Language/en_us.StackImagineEmailWriter.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ],
  ],
  'post_uninstall' => [
    '<basepath>/scripts/post_uninstall.php',
  ],
  'pre_uninstall' => [
    '<basepath>/scripts/pre_uninstall.php',
  ],
];
