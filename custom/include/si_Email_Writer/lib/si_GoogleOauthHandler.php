<?php
require_once 'modules/Users/User.php';

class si_GoogleOauthHandler
{
	public static function getStoredCredentials($user_id)
	{
		global $sugar_config;
		$user = new \User();
		$user->retrieve($user_id, array('disable_row_level_security' => true));
		if (empty($user->id)) {
			$GLOBALS['log']->fatal("User with id " . $user_id . " not found");
			return false;
		}
		if (!empty($user->si_google_refresh_code))
			return [
				'access_token'	=> $user->si_google_access_code,
				'refresh_token'	=> $user->si_google_refresh_code,
				'expires_in'	=> $user->si_google_auth_expires_in,
				'client_id' 	=> $sugar_config['GOOGLE']['CLIENT_ID'],
				'client_secret' => $sugar_config['GOOGLE']['CLIENT_SECRET']
			];

		return false;
	}
}
