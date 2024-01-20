<?php

require_once 'custom/include/si_Email_Writer/autoload.php';

use si_Email_Writer\apiCalls\OAuthApiAdapter;
use si_Email_Writer\Sugar\Helpers\UpdateBean;

try {
	global $sugar_config, $current_user;
	if (isset($_GET['code'])) {
		$newCredentials = OAuthApiAdapter::authenticate($_GET['code'], $current_user->id);
		$GLOBALS['log']->debug('File: ' . __FILE__ . ', Line# ' . __LINE__ . ' ', $newCredentials);
		UpdateBean::update('Users', $newCredentials);

		if (!empty($newCredentials) && !empty($current_user->si_google_refresh_code)) {
			//show message authentication done and redirect user where you want
			echo "Already authenticated...</a>";
			SugarApplication::redirect("index.php?module=Users&action=DetailView&record=" . $current_user->id);
		} else {
			//show message authentication fail and redirect user to auth url so that auth code can be grabbed once again
			echo "Error occurred please <a href='$authUrl'>try again</a>";
		}
	} else {
		if (isset($_GET['error'])) {
			echo "Error occurred please <a href='$authUrl'>try again</a>";
		} else {
			//Request authorization
			$GLOBALS['log']->fatal('File: ' . __FILE__ . ', Line# ' . __LINE__ . ' ' . "redirecting to ....");
			$authUrl = OAuthApiAdapter::authorize();
			echo '<script type="text/javascript"> top.window.location.href="' . $authUrl . '";</script>'; // SuiteCRM 8 Compatible
			exit;
		}
	}
} catch (Exception $ex) {
	if (strpos($ex->getMessage(), 'invalid_grant') !== false) {
		echo "Already, you have authorized.";
		SugarApplication::redirect("index.php");
	} else {
		echo "Error occurred, try later on...";
	}
	$GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
}
