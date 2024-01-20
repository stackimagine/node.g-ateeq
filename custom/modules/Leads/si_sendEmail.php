<?php

require_once 'custom/include/si_Email_Writer/autoload.php';

use si_Email_Writer\Helpers\HandleSending;

try {
    $response = HandleSending::send($_REQUEST['id'], ucfirst($_REQUEST['module']));
    echo json_encode($response);
} catch (Exception $ex) {
    $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
}

function sendError($error)
{
    $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $error);
    echo json_encode(['error' => $error]);
}
