<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site\Controller;

use Awf\Mvc\Controller;
use Awf\Text\Text;
use Site\Controller\ControllerTraits\RequireShortcode;

class Main extends Controller
{
	use RequireShortcode;

	public function execute($task)
	{
		if (!$this->isValidShortcode())
		{
			$msg     = Text::_('SITE_ERR_SHORTCODE_NOTEXISTS');
			$msgType = 'error';

			$redirectURL = $this->container->router->route('index.php?view=login');

			if ($this->isExpiredShortcode())
			{
				$msg     = Text::_('SITE_ERR_SHORTCODE_EXPIRED');
				$msgType = 'info';
			}

			$this->setRedirect($redirectURL, $msg, $msgType);

			return true;
		}

		return parent::execute($task);
	}

}