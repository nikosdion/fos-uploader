<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\Controller;

use Awf\Container\Container;
use Awf\Mvc\Controller;
use Site\Controller\ControllerTraits\RequireShortcode;

class Thankyou extends Controller
{
	use RequireShortcode;

	public function __construct(Container $container = null)
	{
		$this->modelName = 'main';

		parent::__construct($container);
	}

	public function onBeforeMain()
	{
		$event = $this->getEvent();
		$view  = $this->getView();
		$view->setModel('event', $event);

		return true;
	}
}