<?php

global $sugar_version;

$admin_option_defs = array();

if (preg_match("/^6.*/", $sugar_version)) {
    $admin_option_defs['Administration']['si_Email_Writer_info'] = array('helpInline', 'LBL_STACKIMAGINE_EMAIL_WRITER_LICENSE_TITLE', 'LBL_STACKIMAGINE_EMAIL_WRITER_LICENSE', './index.php?module=si_Email_Writer&action=license');
} else {
    $admin_option_defs['Administration']['si_Email_Writer_info'] = array('helpInline', 'LBL_STACKIMAGINE_EMAIL_WRITER_LICENSE_TITLE', 'LBL_STACKIMAGINE_EMAIL_WRITER_LICENSE', 'javascript:parent.SUGAR.App.router.navigate("#bwc/index.php?module=si_Email_Writer&action=license", {trigger: true});');
}

$admin_group_header[] = array('LBL_STACKIMAGINE_EMAIL_WRITER_LICENSE', '', false, $admin_option_defs, '');
