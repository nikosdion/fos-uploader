<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site\Controller;

use Awf\Mvc\Controller;
use Site\Controller\ControllerTraits\RequireShortcode;

class Main extends Controller
{
	use RequireShortcode;

	public function execute($task)
	{
		if (!$this->isValidShortcode())
		{
			$redirectURL = $this->container->router->route('index.php?view=login');

			if ($this->isExpiredShortcode())
			{
				$redirectURL = $this->container->router->route('index.php?view=expired');
			}

			$this->setRedirect($redirectURL);

			return true;
		}

		return parent::execute($task);
	}

}