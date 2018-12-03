<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */


namespace Site\Model;


use Awf\Mvc\Model;
use Awf\Utils\StringHandling;

class Main extends Model
{
	/**
	 * Does the provided shortcode correspond to a current event?
	 *
	 * @param   string  $code
	 *
	 * @return  bool
	 */
	public function isValidShortcode(string $code): bool
	{
		// TODO Check the database. Get all codes, EXCEPT for expired events
		$validCodes = ['TEST'];

		// Cast codes to lowercase for safe comparison
		$validCodes = array_map('strtolower', $validCodes);
		$code       = strtolower($code);

		return in_array($code, $validCodes);
	}

	/**
	 * Does the provided shortcode correspond to an expired event?
	 *
	 * @param   string  $code
	 *
	 * @return  bool
	 */
	public function isExpiredShortcode(string $code): bool
	{
		// TODO Check the database. Get only the codes for expired events.
		$expiredCodes = ['EXPIRED'];

		// Cast codes to lowercase for safe comparison
		$expiredCodes = array_map('strtolower', $expiredCodes);
		$code         = strtolower($code);

		return in_array($code, $expiredCodes);
	}

	public function getFolderNameFromName(string $name)
	{
		// Remove any '-' from the string they will be used as concatenator
		$name = str_replace('-', ' ', $name);

		// Lowercase and trim
		$name = trim(mb_strtolower($name));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$name = preg_replace(array('/\s+/', '/[^\p{L}-_]/u'), array('-', ''), $name);

		// Limit length
		if (strlen($name) > 100)
		{
			$name = substr($name, 0, 100);
		}

		return $name;
	}
}