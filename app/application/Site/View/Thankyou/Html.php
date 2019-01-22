<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\View\Thankyou;

use Admin\Model\Events;

class Html extends \Awf\Mvc\View
{
	/**
	 * Total number of files uploaded
	 *
	 * @var  int
	 */
	public $totalFiles = 0;

	/**
	 * Total size of files uploaded, in bytes
	 *
	 * @var  int
	 */
	public $totalSize = 0;

	/** @var Events Event information */
	public $event = null;

	public function onBeforeMain(?string &$tpl = null): bool
	{
		$segment = $this->container->segment;

		$this->totalFiles = $segment->get('totalfiles', 0);
		$this->totalSize  = $segment->get('totalsize', 0);
		$this->event      = $this->getModel('event');

		$tpl = null;

		return true;
	}

	public function formatSize(int $size): string
	{
		$units = ['b', 'KB', 'MB', 'GB', 'TB', 'PB'];

		$i       = floor(log($size, 1024));
		$newSize = $size / pow(1024, $i);
		$unit    = $units[$i];

		return sprintf('%0.2f%s', $newSize, $unit);
	}
}