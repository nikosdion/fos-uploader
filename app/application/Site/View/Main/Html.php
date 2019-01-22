<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\View\Main;

use Admin\Model\Events;

class Html extends \Awf\Mvc\View
{
	/** @var string The full name of the user */
	public $fullname = '';

	/** @var bool Has the user accepted the terms of service and privacy policy? */
	public $accept = false;

	/** @var Events Event information */
	public $event = null;

	/**
	 * Runs before showing the page where you enter your name and agree to TOS.
	 *
	 * @return  bool
	 */
	public function onBeforeMain(?string &$tpl = null): bool
	{
		$session        = $this->container->segment;
		$this->fullname = $session->get('name', '');
		$this->accept   = $session->get('agree', false);
		$this->event    = $this->getModel('event');

		$tpl = null;

		return true;
	}
}