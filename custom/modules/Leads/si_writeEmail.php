<?php

require_once 'custom/include/si_Email_Writer/autoload.php';

use si_Email_Writer\Helpers\PrepareEmail;

try {
    global $current_user;
    $result = PrepareEmail::writeEmail(ucfirst($_REQUEST['module']), $_REQUEST['id']);

    if ($result !== 'true')
        sendError($result);
} catch (Exception $ex) {
    $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
}

function sendError($error)
{
    $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $error);
    echo json_encode(['error' => $error]);
}
