<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Site\View\Login;

class Html extends \Awf\Mvc\View
{
	public $shortcode = '';

	public function onBeforeMain()
	{
		$this->shortcode = $this->container->segment->get('shortcode', '');

		return true;
	}
}