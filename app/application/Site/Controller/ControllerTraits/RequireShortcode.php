<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Site\Controller\ControllerTraits;

use Admin\Container;
use Admin\Model\Events;
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

	protected function getRedirection(): ?string
	{
		$code = $this->getShortcode();

		if (empty($code))
		{
			return null;
		}

		/** @var Main $model */
		$model = Model::getInstance('Site', 'Main');

		return $model->getRedirectionURL($code);

	}

	protected function getShortcode(): string
	{
		$requestCode = $this->input->getString('shortcode', '');

		if ($requestCode)
		{
			$this->container->segment->set('shortcode', $requestCode);

			return $requestCode;
		}

		return $this->container->segment->get('shortcode', '');
	}

	protected function getEvent(?string $code = null): Events
	{
		if (is_null($code))
		{
			$code = $this->getShortcode();
		}

		/** @var Events $model */
		$backendContainer = new Container([
			'db' => $this->container->db
		]);
		$model            = Model::getInstance('Admin', 'Events', $backendContainer);

		return $model->findOrFail([
			'shortcode' => $code
		]);
	}
}