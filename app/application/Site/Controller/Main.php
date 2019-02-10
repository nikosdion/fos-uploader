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
		$shortCode = $this->getShortcode();

		if (!$this->isValidShortcode())
		{
			// Default is no error message (because we have an empty shortcode the first time someone visits the site)
			$msg     = null;
			$msgType = null;

			// If the shortcode is non-empty we have to show an error message
			if (!empty($shortCode))
			{
				$msg     = Text::_('SITE_ERR_SHORTCODE_NOTEXISTS');
				$msgType = 'error';
			}

			// Default redirection URL is the login page
			$redirectURL = $this->container->router->route('index.php?view=login');

			// If it's an expired shortcode we have to show a different error message
			if ($this->isExpiredShortcode())
			{
				$msg     = Text::_('SITE_ERR_SHORTCODE_EXPIRED');
				$msgType = 'info';
			}

			// Expired events may define their own redirection, outside of the uploader app.
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

		// Checks complete. Set the event and show the welcome page.
		$event = $this->getEvent();
		$view  = $this->getView();
		$view->setModel('event', $event);

		return parent::execute($task);
	}

	/**
	 * Runs before the main event. If we do not have an explicitly set event code we redirect the user back to the login
	 * page, allowing them to log into a different event if necessary.
	 *
	 * The event code comes through the shortcode URL parameter. This is either provided explicitly in the URL or
	 * through a SEF URL (e.g. /foobar). The SEF URL to URL parameter mapping happens in Application::loadRoutes(), in
	 * the programmatic (callback) rule we add to the bottom of the SEF URL parser.
	 *
	 * @return  bool
	 */
	public function onBeforeDefault(): bool
	{
		$requestCode = $this->input->getString('shortcode', '');

		if (empty($requestCode))
		{
			$redirectURL = $this->container->router->route('index.php?view=login');

			$this->setRedirect($redirectURL);

			return true;
		}

		return true;
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