<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\Controller;

use Awf\Mvc\Controller;

class Login extends Controller
{
	use ACLTrait;

	/**
	 * Database setup task. This is where we ask the user for the database connection details.
	 *
	 * @return  boolean
	 */
	public function login()
	{
		try
		{
			$this->csrfProtection();

			// Get the username and password from the request
			$username = $this->input->get('username', '', 'raw');
			$password = $this->input->get('password', '', 'raw');

			// Try to log in the user
			$manager = $this->container->userManager;
			$manager->loginUser($username, $password);

			// Redirect to the saved return_url or, if none specified, to the application's main page
			$url    = $this->container->segment->getFlash('return_url');
			$router = $this->container->router;

			if (empty($url))
			{
				$url = $router->route('index.php?view=main');
			}

			$this->setRedirect($url);
		}
		catch (\Exception $e)
		{
			$router = $this->container->router;

			// Login failed. Go back to the login page and show the error message
			$this->setRedirect($router->route('index.php?view=login'), $e->getMessage(), 'error');
		}

		return true;
	}

	public function logout()
	{
		$router  = $this->container->router;
		$manager = $this->container->userManager;
		$manager->logoutUser();

		$this->setRedirect($router->route('index.php?view=main'));
	}
} 
