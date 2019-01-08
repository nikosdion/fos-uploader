<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin;

use Akeeba\Engine\Postproc\Connector\S3v4\Connector as S3Connector;
use Akeeba\Engine\Postproc\Connector\S3v4\Configuration as S3Config;
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