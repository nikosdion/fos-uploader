<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site;

use Site\Application\Configuration;

class Container extends \Awf\Container\Container
{
	public function __construct(array $values = array())
	{
		if (!isset($values['application_name']))
		{
			$values['application_name'] = 'Site';
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
	}
}