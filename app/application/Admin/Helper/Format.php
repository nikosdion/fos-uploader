<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */


namespace Admin\Helper;


use Awf\Date\Date;

class Format
{
	/**
	 * Format a date. Default is to format as a GMT date in dd/mm/YYYY HH:MM format.
	 *
	 * @param   string  $date    The date to format.
	 * @param   string  $format  The format to use, default is 'd/m/Y H:i'
	 * @param   bool    $local   Should I display the date in the user's local timezone? Default: no.
	 *
	 * @return  string  The formatted date
	 */
	public static function date($date, $format = null, $local = false)
	{
		$date = new Date($date, 'GMT');

		if (empty($format))
		{
			$format = 'd/m/Y H:i';
		}

		if ($local)
		{
			$tz = new \DateTimeZone('Europe/Athens');

			$date->setTimezone($tz);
		}

		return $date->format($format, $local);
	}

}