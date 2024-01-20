<?php

namespace si_Email_Writer;

/**
 * This methods is responsible for loading files
 *
 * @method load
 * @param string $class The fully-qualified class name.
 * @return void
 */
function load($class)
{

    // project-specific namespace prefix
    $prefix = 'si_Email_Writer';

    // base directory for the namespace prefix
    $base_dir = dirname(__FILE__);

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        $GLOBALS['log']->debug('si_Email_Writer - Including Class: ' . $class);
        require $file;
    }
};

$GLOBALS['log']->debug('si_Email_Writer - Initiating Autoloader');
$classes = array(
    // Sugar\Helpers
    'si_Email_Writer\Sugar\Helpers\DBHelper',
    'si_Email_Writer\Sugar\Helpers\UpdateBean',
    'si_Email_Writer\Sugar\Helpers\UpdateJob',
    // apiCalls
    'si_Email_Writer\apiCalls\ApiAdapter',
    'si_Email_Writer\apiCalls\MailApiAdapter',
    'si_Email_Writer\apiCalls\GMailApiAdapter',
    'si_Email_Writer\apiCalls\OAuthApiAdapter',
    'si_Email_Writer\apiCalls\OpenAIApiAdapter',
    // Google
    'si_Email_Writer\Google\AccessToken',
    //OpenAI
    'si_Email_Writer\OpenAI\AccessToken',
    //SMTP
    'si_Email_Writer\SMTP\Send',
    // Helper
    'si_Email_Writer\Helpers\MarkReplyReceived',
    'si_Email_Writer\Helpers\PrepareEmail',
    'si_Email_Writer\Helpers\HandleSending',
);
foreach ($classes as $class) {
    load($class);
}
$GLOBALS['log']->debug('si_Email_Writer - Autoloader Completed');
