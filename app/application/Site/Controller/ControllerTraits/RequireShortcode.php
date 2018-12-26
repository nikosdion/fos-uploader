<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */


namespace Site\Controller\ControllerTraits;

use Awf\Mvc\Model;
use Site\Model\Main;

/**
 * Controller Trait for requiring a valid shortcode
 *
 * @package Site\Controller\ControllerTraits
 */
trait RequireShortcode
{
	protected function isValidShortcode(): bool
	{
		$code = $this->getShortcode();

		if (empty($code))
		{
			return false;
		}

		/** @var Main $model */
		$model = Model::getInstance('Site', 'Main');

		if ($model->isValidShortcode($code))
		{
			return true;
		}

		return false;
	}

	protected function isExpiredShortcode(): bool
	{
		$code = $this->getShortcode();

		if (empty($code))
		{
			return false;
		}

		/** @var Main $model */
		$model = Model::getInstance('Site', 'Main');

		if ($model->isExpiredShortcode($code))
		{
			return true;
		}

		return false;

	}

	protected function getShortcode(): string
	{
		$requestCode = $this->input->getString('shortcode', '');

		if ($requestCode)
		{
			return $requestCode;
		}

		return $this->container->segment->get('shortcode', '');
	}
}