<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin;

use Akeeba\Engine\Postproc\Connector\S3v4\Connector as S3Connector;
use Akeeba\Engine\Postproc\Connector\S3v4\Configuration as S3Config;
use Admin\Application\Configuration;

/**
 * Class Container
 * @package Site
 *
 * @property-read S3Connector $s3 Amazon S3 connector
 */
class Container extends \Awf\Container\Container
{
	public function __construct(array $values = array())
	{
		if (!isset($values['application_name']))
		{
			$values['application_name'] = 'Admin';
		}

		if (!isset($values['appConfig']))
		{
			$values['appConfig'] = function (Container $c)
			{
				return new Configuration($c);
			};
		}

		if (!isset($values['session_segment_name']))
		{
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

			$values['session_segment_name'] = $values['application_name'] . '_' . $installationId;
		}

		parent::__construct($values);

		if (!isset($this['s3']))
		{
			if (!defined('AKEEBAENGINE'))
			{
				define('AKEEBAENGINE', 1);
			}

			$this['s3'] = function (Container $c) {
				$access        = $this->appConfig->get('s3.access');
				$secret        = $this->appConfig->get('s3.secret');
				$region        = $this->appConfig->get('s3.region');
				$configuration = new S3Config($access, $secret, 'v4', $region);

				return new S3Connector($configuration);
			};
		}
	}
}