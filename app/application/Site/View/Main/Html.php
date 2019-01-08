<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\View\Main;

class Html extends \Awf\Mvc\View
{
	/** @var string The full name of the user */
	public $fullname = '';

	/** @var bool Has the user accepted the terms of service and privacy policy? */
	public $accept = false;

	/**
	 * Runs before showing the page where you enter your name and agree to TOS.
	 *
	 * @return  bool
	 */
	public function onBeforeMain(?string &$tpl = null): bool
	{
		$session        = $this->container->segment;
		$fullName       = $session->get('name', '');
		$acceptTOS      = $session->get('agree', false);
		$this->fullname = $fullName;
		$this->accept   = $acceptTOS;

		$tpl = null;

		return true;
	}
}