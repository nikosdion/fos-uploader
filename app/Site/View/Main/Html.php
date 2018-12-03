<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site\View\Main;

class Html extends \Awf\Mvc\View
{
	/** @var string The full name of the user */
	public $fullname = '';

	/** @var bool Has the user accepted the terms of service and privacy policy? */
	public $accept = false;

	/**
	 * Runs before showing the main page of the application
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