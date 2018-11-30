<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site;

use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\User\ManagerInterface;
use Exception;

class Application extends \Awf\Application\Application
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

		// Let AWF know that the prefix for our system JavaScript is 'akeeba.System.'
		\Awf\Html\Grid::$javascriptPrefix = 'akeeba.System.';

		// If the PHP session save path is not writable we will use the 'session' subdirectory inside our tmp directory
		$this->discoverSessionSavePath();

		// Set up the template (theme) to use
		$this->setTemplate('default');

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

		// Session timeout check
		$this->applySessionTimeout();

		// Load the application routes
		$this->loadRoutes();

		// Attach the user privileges to the user manager
		$manager = $this->container->userManager;

		$this->attachPrivileges($manager);

		// Show the login page when necessary
		//$this->redirectToLogin();

		// Set up the media query key
		$this->setupMediaVersioning();
	}

	/**
	 * Find a writable PHP session save path
	 *
	 * @return  void
	 * @throws  Exception
	 */
	protected function discoverSessionSavePath(): void
	{
		$sessionPath = $this->container->session->getSavePath();

		if (!@is_dir($sessionPath) || !@is_writable($sessionPath))
		{
			$sessionPath = APATH_BASE . '/tmp/session';
			$this->createOrUpdateSessionPath($sessionPath);
			$this->container->session->setSavePath($sessionPath);
		}
	}

	/**
	 * Loads the language files for the application
	 *
	 * @return void
	 */
	protected function loadLanguages(): void
	{
		// Load the language files
		Text::loadLanguage(null, 'poc', '.ini', false, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'poc', '.ini', false, $this->container->languagePath);
	}

	/**
	 * Apply the session timeout setting.
	 *
	 * @throws  Exception  If we cannot figure out how to create a session ID
	 */
	protected function applySessionTimeout(): void
	{
		// Get the session timeout
		$sessionTimeout = (int) $this->container->appConfig->get('session_timeout', 1440);

		// Get the base URL and set the cookie path
		$uri = new Uri(Uri::base(false, $this->container));

		// Force the cookie timeout to coincide with the session timeout
		if ($sessionTimeout > 0)
		{
			$this->container->session->setCookieParams([
				'lifetime' => $sessionTimeout * 60,
				'path'     => $uri->getPath(),
				'domain'   => $uri->getHost(),
				'secure'   => $uri->getScheme() == 'https',
				'httponly' => true,
			]);
		}

		// Calculate a hash for the current user agent and IP address
		$ip         = \Awf\Utils\Ip::getUserIP();
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$uniqueData = $ip . $user_agent . $this->container->basePath;
		$hash_algos = function_exists('hash_algos') ? hash_algos() : [];

		// Prefer SHA-512...
		if (in_array('sha512', $hash_algos))
		{
			$sessionKey = hash('sha512', $uniqueData, false);
		}
		// ...then SHA-256...
		elseif (in_array('sha256', $hash_algos))
		{
			$sessionKey = hash('sha256', $uniqueData, false);
		}
		// ...then SHA-1...
		elseif (function_exists('sha1'))
		{
			$sessionKey = sha1($uniqueData);
		}
		// ...then MD5...
		elseif (function_exists('md5'))
		{
			$sessionKey = md5($uniqueData);
		}
		// ... CRC32?! ...
		elseif (function_exists('crc32'))
		{
			$sessionKey = crc32($uniqueData);
		}
		// ... base64_encode????! ...
		elseif (function_exists('base64_encode'))
		{
			$sessionKey = base64_encode($uniqueData);
		}
		// ... paint your server a deep blue and toss it in the middle of the ocean where it will never be found!
		else
		{
			throw new \Exception('Your server does not provide any kind of hashing method. Please use a decent host.', 500);
		}

		// Get the current session's key
		$currentSessionKey = $this->container->segment->get('session_key', '');

		// If there is no key, set it
		if (empty($currentSessionKey))
		{
			$this->container->segment->set('session_key', $sessionKey);
		}
		// If there is a key and it doesn't match, trash the session and restart.
		elseif ($currentSessionKey != $sessionKey)
		{
			$this->container->session->destroy();
			$this->redirect($this->container->router->route('index.php'));
		}

		// If the session timeout is 0 or less than 0 there is no limit. Nothing to check.
		if ($sessionTimeout <= 0)
		{
			return;
		}

		// What is the last session timestamp?
		$lastCheck = $this->container->segment->get('session_timestamp', 0);
		$now       = time();

		// If there is a session timestamp make sure it's valid, otherwise trash the session and restart
		if (($lastCheck != 0) && (($now - $lastCheck) > ($sessionTimeout * 60)))
		{
			$this->container->session->destroy();
			$this->redirect($this->container->router->route('index.php'));
		}
		// In any other case, refresh the session timestamp
		else
		{
			$this->container->segment->set('session_timestamp', $now);
		}
	}

	/**
	 * Load the routing setup from the routes.json or routes.php configuration file.
	 *
	 * @return  void
	 */
	protected function loadRoutes(): void
	{
		// Load the routes from JSON, if they are present
		$routesJSONPath = $this->container->basePath . '/config/routes.json';
		$router         = $this->container->router;
		$importedRoutes = false;

		if (@file_exists($routesJSONPath))
		{
			$json = @file_get_contents($routesJSONPath);

			if (!empty($json))
			{
				$router->importRoutes($json);
				$importedRoutes = true;
			}
		}

		// If we could not import routes from routes.json, try loading routes.php
		$routesPHPPath = $this->container->basePath . '/config/routes.php';

		if (!$importedRoutes && @file_exists($routesPHPPath))
		{
			require_once $routesPHPPath;
		}
	}

	/**
	 * Attach custom privileges to the application
	 *
	 * @param   ManagerInterface  $manager
	 *
	 * @return  void
	 */
	protected function attachPrivileges(ManagerInterface $manager): void
	{
		// The only relevant privilege is frontend.upload
		$manager->registerPrivilegePlugin('frontend', '\\Site\\Application\\UserPrivileges');

		// In the frontend we use a lax security model where the event attendees need to supply an event shortcode.
		$manager->registerAuthenticationPlugin('shortcode', '\\Site\\Application\\UserAuthenticationShortcode');
	}

	/**
	 * Setup the media file version query
	 *
	 * @return  void
	 */
	protected function setupMediaVersioning(): void
	{
		$this->getContainer()->mediaQueryKey = md5(microtime(false));

		$isDebug       = defined('AKEEBADEBUG');
		$isDevelopment = strpos(Uri::base(), 'local.web') !== false;

		if (!$isDebug && !$isDevelopment)
		{
			$this->getContainer()->mediaQueryKey = md5(filemtime(__FILE__));
		}
	}

	/**
	 * Creates or updates the custom session save path
	 *
	 * @param   string   $path    The custom session save path
	 * @param   boolean  $silent  Should I suppress all errors?
	 *
	 * @return  void
	 *
	 * @throws  Exception  If $silent is set to false
	 */
	private function createOrUpdateSessionPath(string $path, bool $silent = true): void
	{
		try
		{
			$fs            = $this->container->fileSystem;
			$protectFolder = false;

			if (!@is_dir($path))
			{
				$fs->mkdir($path, 0777);
			}
			elseif (!is_writeable($path))
			{
				$fs->chmod($path, 0777);
				$protectFolder = true;
			}
			else
			{
				if (!@file_exists($path . '/.htaccess'))
				{
					$protectFolder = true;
				}

				if (!@file_exists($path . '/web.config'))
				{
					$protectFolder = true;
				}
			}

			if ($protectFolder)
			{
				$fs->copy($this->container->basePath . '/.htaccess', $path . '/.htaccess');
				$fs->copy($this->container->basePath . '/web.config', $path . '/web.config');

				$fs->chmod($path . '/.htaccess', 0644);
				$fs->chmod($path . '/web.config', 0644);
			}
		}
		catch (\Exception $e)
		{
			if (!$silent)
			{
				throw $e;
			}
		}
	}

}