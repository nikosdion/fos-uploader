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

class Legal extends Controller
{
	public function __construct(Container $container = null)
	{
		$this->modelName = 'main';

		parent::__construct($container);
	}

	public function privacy()
	{
		$this->display();
	}

	public function tos()
	{
		$this->display();
	}
}