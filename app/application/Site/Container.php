<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Site;

use Akeeba\Engine\Postproc\Connector\S3v4\Connector as S3Connector;
use Akeeba\Engine\Postproc\Connector\S3v4\Configuration as S3Config;
use Site\Application\Configuration;

/**
 * Class Container
 * @package Site
 *
 * @property-read S3Connector $s3 Amazon S3 connector
 */
class Container extends \Awf\Container\Container
{
	public function __construct(array $values = [])
	{
		$classParts = explode('\\', __CLASS__);
		$defaults   = [
			'application_name'     => reset($classParts),
			'appConfig'            => function (Container $c) {
				return new Configuration($c);
			},
			'basePath'             => function (Container $c) {
				static $basePath = null;

				if (is_null($basePath))
				{
					$basePath = APATH_APPROOT . '/' . ucfirst($c->application_name);
				}

				return $basePath;
			},
			'session_segment_name' => function () use ($values) {
				$installationId = 'default';

				if (function_exists('base64_encode'))
				{
					$installationId = base64_encode(__DIR__);
				}

				if (function_exists('md5'))
				{
					$installationId = md5(__DIR__);
				}

				if (function_exists('sha1'))
				{
					$installationId = sha1(__DIR__);
				}

				return $values['application_name'] . '_' . $installationId;
			},
			's3'                   => function (Container $c) {
				if (!defined('AKEEBAENGINE'))
				{
					define('AKEEBAENGINE', 1);
				}

				$access        = $this->appConfig->get('s3.access');
				$secret        = $this->appConfig->get('s3.secret');
				$region        = $this->appConfig->get('s3.region');
				$configuration = new S3Config($access, $secret, 'v4', $region);

				return new S3Connector($configuration);
			},
		];

		$values = array_merge($defaults, $values);

		parent::__construct($values);
	}
}