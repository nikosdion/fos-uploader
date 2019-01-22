<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin;

use Admin\Application\Configuration;

class Container extends \Site\Container
{
	public function __construct(array $values = [])
	{
		$defaults = [
			'appConfig'            => function (Container $c) {
				return new Configuration($c);
			},
		];

		$values = array_merge($defaults, $values);

		parent::__construct($values);
	}

}