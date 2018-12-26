<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin;

use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\User\ManagerInterface;
use Exception;

class Application extends \Site\Application
{
	/**
	 * Initialize the application
	 *
	 * @throws Exception
	 */
	public function initialise()
	{
		$isDebug       = defined('AKEEBADEBUG');
		$isDevelopment = strpos(Uri::base(), 'local.web') !== false;

		if (!$isDebug && $isDevelopment)
		{
			define('AKEEBADEBUG', 1);
		}

		// This line must appear before the user manager initializes, or it won't find the users table!
		$this->container->appConfig->set('user_table', '#__users');

		// Let AWF know that the prefix for our system JavaScript is 'akeeba.System.'
		\Awf\Html\Grid::$javascriptPrefix = 'akeeba.System.';

		// If the PHP session save path is not writable we will use the 'session' subdirectory inside our tmp directory
		$this->discoverSessionSavePath();

		// Set up the template (theme) to use
		$this->setTemplate('admin');

		// Load the language files
		$this->loadLanguages();

		// Load the configuration if it's present. IMPORTANT! The **first** .env file to have a certain option wins.
		$configPaths = [
			APATH_ROOT . '/config/.env.ci',
			APATH_ROOT . '/config/.env.dev',
			APATH_ROOT . '/config/.env',
		];

		// Load the application's configuration
		$this->container->appConfig->loadConfiguration($configPaths);

		// Attach the user privileges to the user manager
		$manager = $this->container->userManager;
		$this->attachPrivileges($manager);

		// Show the login page when necessary
		$this->redirectToLogin();

		// Set up the media query key
		$this->setupMediaVersioning();
	}

	/**
	 * Redirect the user to the login page if they are not already logged in
	 *
	 * @return  void
	 */
	private function redirectToLogin(): void
	{
		// Get the view. Necessary to go through $this->getContainer()->input as it may have already changed
		$view = $this->getContainer()->input->getCmd('view', '');

		// Get the user manager
		$manager = $this->container->userManager;

		// Show the login page if there is no logged in user and we're not in the setup or login page already
		// and we're not using the remote (front-end backup), json (remote JSON API) views of the (S)FTP
		// browser views (required by the session task of the setup view).
		if (!in_array($view, [
				'login',
			]) && !$manager->getUser()->getId())
		{
			$return_url = $this->container->segment->getFlash('return_url');

			if (empty($return_url))
			{
				$return_url = Uri::getInstance()->toString();
			}

			$this->container->segment->setFlash('return_url', $return_url);

			$this->getContainer()->input->setData([
				'view' => 'login',
			]);
		}
	}

	/**
	 * Attach the user privileges and authentication plugins
	 *
	 * @param   ManagerInterface $manager
	 *
	 * @return  void
	 */
	private function attachPrivileges(ManagerInterface $manager): void
	{
		$manager->registerPrivilegePlugin('fos', '\\Admin\\Application\\UserPrivileges');
		$manager->registerAuthenticationPlugin('password', '\\Admin\\Application\\UserAuthenticationPassword');
	}
}