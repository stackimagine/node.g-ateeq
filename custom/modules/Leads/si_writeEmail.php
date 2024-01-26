<?php

require_once 'custom/include/si_Email_Writer/autoload.php';

use si_Email_Writer\Helpers\PrepareEmail;

try {
    global $current_user;
    $result = PrepareEmail::writeEmail(ucfirst($_REQUEST['module']), $_REQUEST['id']);

    if (isset($result['error']) && $result['error']) {
        $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $error['error']);
    }

    echo json_encode($result);
} catch (Exception $ex) {
    $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
}

