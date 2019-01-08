<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\Model;

use Awf\Mvc\Model;

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
		// Get the currently valid shortcodes from the database
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select([
				$db->qn('shortcode')
			])->from($db->qn('#__events'))
			->where($db->qn('enabled') . ' = ' . $db->q('1'));
		$validCodes = $db->setQuery($query)->loadColumn(0);

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
		// Get the expired shortcodes from the database
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select([
				$db->qn('shortcode')
			])->from($db->qn('#__events'))
			->where($db->qn('enabled') . ' = ' . $db->q('0'));
		$expiredCodes = $db->setQuery($query)->loadColumn(0);

		// Cast codes to lowercase for safe comparison
		$expiredCodes = array_map('strtolower', $expiredCodes);
		$code         = strtolower($code);

		return in_array($code, $expiredCodes);
	}

	/**
	 * Get the Amazon S3 folder name for this project
	 *
	 * @param   string  $code  The project code
	 *
	 * @return  string  The S3 folder name for the project
	 */
	public function getProjectFolderName(string $code): string
	{
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select([
				$db->qn('folder')
			])->from($db->qn('#__events'))
			->where($db->qn('shortcode') . ' = ' . $db->q(trim($code)));
		return $db->setQuery($query)->loadResult(0);
	}

	public function getFolderNameFromName(string $name)
	{
		// Remove any '-' from the string they will be used as concatenator
		$name = str_replace('-', ' ', $name);

		// Lowercase and trim
		$name = trim(mb_strtolower($name));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$name = preg_replace(array('/\s+/', '/[^\p{L}\-_]/u'), array('-', ''), $name);

		// Limit length
		if (strlen($name) > 100)
		{
			$name = substr($name, 0, 100);
		}

		return $name;
	}
}