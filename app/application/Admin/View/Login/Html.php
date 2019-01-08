<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\View\Login;

use Admin\View\CommonMediaTrait;
use Awf\Mvc\View;

class Html extends View
{
	use CommonMediaTrait;

	/**
	 * Executes before displaying the "main" task (initial requirements check page)
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		// Present the login in a plain page, no headers, menus, etc.
		$this->container->input->set('tmpl', 'component');

		$this->username  = $this->container->segment->getFlash('auth_username');
		$this->password  = $this->container->segment->getFlash('auth_password');

		return true;
	}
}
