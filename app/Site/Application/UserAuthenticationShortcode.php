<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */


namespace Site\Application;

use Awf\Application\Application;
use Awf\User\Authentication;

/**
 * Project shortcode authentication plugin
 *
 * @package  Site\Application
 */
class UserAuthenticationShortcode extends Authentication
{
	public function onAuthentication($params = [])
	{
		// TODO Actually load available event codes from the database
		$shortcodes = ['test', 'demo'];

		$userCode = isset($params['password']) ? $params['password'] : '';
		$allowed  = false;

		// This loop prevents brute forcing of event shortcodes
		foreach ($shortcodes as $code)
		{
			$allowed = $allowed || $this->timingSafeEquals($code, $userCode);
		}

		if (!$allowed)
		{
			return false;
		}

		// Cache the event shortcode in the session
		Application::getInstance('Site')->getContainer()->segment->set('shortcode', $userCode);

		return true;
	}

	/**
	 * A timing safe equals comparison
	 *
	 * To prevent leaking length information, it is important that user input is always used as the second parameter.
	 *
	 * @param   string  $safe  The internal (safe) value to be checked
	 * @param   string  $user  The user submitted (unsafe) value
	 *
	 * @return  boolean  True if the two strings are identical.
	 */
	protected function timingSafeEquals(string $safe, string $user): bool
	{
		// Prevent issues if string length is 0
		$safe .= chr(0);
		$user .= chr(0);

		$safeLen = strlen($safe);
		$userLen = strlen($user);

		// Set the result to the difference between the lengths
		$result = $safeLen - $userLen;

		// Note that we ALWAYS iterate over the user-supplied length
		// This is to prevent leaking length information
		for ($i = 0; $i < $userLen; $i++) {
			// Using % here is a trick to prevent notices
			// It's safe, since if the lengths are different
			// $result is already non-0
			$result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
		}

		// They are only identical strings if $result is exactly 0...
		return $result === 0;
	}

}