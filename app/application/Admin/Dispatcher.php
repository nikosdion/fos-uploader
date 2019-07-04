<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */


namespace Admin;


class Dispatcher extends \Awf\Dispatcher\Dispatcher
{
	public function __construct(?Container $container = null)
	{
		// The default backend view is 'events'
		$this->defaultView = 'events';

		parent::__construct($container);
	}

}