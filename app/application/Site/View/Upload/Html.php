<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Site\View\Upload;

use Admin\Model\Events;

class Html extends \Awf\Mvc\View
{
	/** @var Events Event information */
	public $event = null;

	/**
	 * Runs before showing the Thank You page
	 *
	 * @return  bool
	 */
	public function onBeforeMain(?string &$tpl = null): bool
	{
		$this->event = $this->getModel('event');

		$tpl = null;

		return true;
	}
}