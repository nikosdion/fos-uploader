<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\Controller;

use Awf\Mvc\Controller;
use Awf\Text\Text;
use Site\Controller\ControllerTraits\RequireShortcode;

class Main extends Controller
{
	use RequireShortcode;

	/**
	 * Runs when executing a task. Used to verify that a valid shortcode exists.
	 *
	 * @param   string $task
	 *
	 * @return  bool|null
	 *
	 * @throws  \Exception
	 */
	public function execute($task): ?bool
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

			$redirection = $this->getRedirection();

			if (!empty($redirection))
			{
				$redirectURL = $redirection;
				$msg         = null;
				$msgType     = null;
			}

			$this->setRedirect($redirectURL, $msg, $msgType);

			return true;
		}

		return parent::execute($task);
	}

	/**
	 * Form handler for the name and agree to TOS fields. Always redirects.
	 *
	 * @return  bool
	 */
	public function setinfo(): bool
	{
		$name    = trim($this->input->post->getString('fullname', ''));
		$agree   = $this->input->post->get('agreetotos', false) == 'on';
		$session = $this->container->segment;
		$valid   = true;

		$session->set('name', $name);
		$session->set('agree', $agree);

		if (empty($name))
		{
			$valid = false;
			$msg   = Text::_('SITE_ERR_NAME_EMPTY');
			$this->container->application->enqueueMessage($msg, 'error');
		}

		if (!$agree)
		{
			$valid = false;
			$msg   = Text::_('SITE_ERR_TOS_ACCEPT');
			$this->container->application->enqueueMessage($msg, 'error');
		}

		$redirectURL = $this->container->router->route('index.php?view=upload');

		if (!$valid)
		{
			$redirectURL = $this->container->router->route('index.php?view=main');
		}

		$this->setRedirect($redirectURL);

		return true;
	}
}