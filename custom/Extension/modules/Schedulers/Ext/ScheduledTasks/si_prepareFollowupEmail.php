<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

$job_strings[] = 'si_prepareFollowupEmails';

/**
 * This function writes email(s) for leads who have not been contacted so far
 * @return bool  true if syncing is successful, false otherwise
 * @access public
 */
function si_prepareFollowupEmails()
{
    $file = 'custom/include/si_Email_Writer/autoload.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        $GLOBALS['log']->fatal('File ' . $file . ' NOT Found');
        return false;
    }
    return si_Email_Writer\Helpers\PrepareEmail::writeFollowups('Leads');
}
