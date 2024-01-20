<?php
$hook_array['before_save'][] = array(1, 'Associate with Account based on Linkedin', 'custom/include/si_Email_Writer/si_Email_Writer_hook.php', 'si_Email_WriterHook', 'linkAccountToLead');

$hook_array['before_save'][] = array(1, 'Set status when bio is added', 'custom/include/si_Email_Writer/si_Email_Writer_hook.php', 'si_Email_WriterHook', 'setBioStatus');
