<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site;

use Awf\Router\Rule;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\User\ManagerInterface;
use Exception;

class Application extends \Awf\Application\Application
{
	/**
	 * Public constructor
	 *
	 * @param Container $container Configuration parameters
	 *
	 * @return Application
	 */
	public function __construct(Container $container = null)
	{
		// Create or attach the DI container
		if (!is_object($container) || !($container instanceof Container))
		{
			$container = new Container();
		}

		$this->container = $container;

		// Session timeout check
		$this->applySessionTimeout();

		parent::__construct($container);
	}

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

		// Load the application routes
		$this->loadRoutes();

		// Show the login page when necessary
		$this->redirectToLogin();

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
		$routesJSONPath = $this->container->basePath . '/../config/routes.json';
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
		$routesPHPPath = $this->container->basePath . '/../config/routes.php';

		if (!$importedRoutes && @file_exists($routesPHPPath))
		{
			require_once $routesPHPPath;
		}

		// Finally, add our custom route handler which interprets the first section of the path as a shortcode
		$container     = $this->container;
		$shortcodeRule = new Rule([
			'parseCallable' => function ($path) use ($container) {
				if (empty($path))
				{
					return null;
				}

				while (strpos($path, '//') !== false)
				{
					$path = str_replace('//', '/', $path);
				}

				if (empty($path))
				{
					return null;
				}

				$parts = explode('/', $path);

				$container->segment->set('shortcode', $parts[0]);

				/**
				 * At this point, Application::redirectToLogin() has already run and will have set view=login because
				 * there was no shortcode present in the session (since we literally just set the shortcode to the
				 * session!). Therefore we need to modify the effective view. We do so through the return array.
				 */
				return [
					'view' => 'main',
				];
			},
		]);
		$router->addRule($shortcodeRule);
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
	 * @param   string  $path   The custom session save path
	 * @param   boolean $silent Should I suppress all errors?
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

	/**
	 * Redirect the user to the login page if they have not already provided a valid shortcode.
	 *
	 * IMPORTANT: This is called before the Route rules. Therefore, if you are using a shortcode in the URL, e.g.
	 *            https://www.example.com/MYCODE, you will still trigger the rule to set the view to login. However, in
	 *            this case the Route rule will trigger before dispatching the application, resetting the view to Main.
	 *            Keep this in mind when debugging routing issues.
	 *
	 * @return  void
	 */
	private function redirectToLogin(): void
	{
		$requestCode = $this->container->input->getString('shortcode', '');

		if (!empty($requestCode))
		{
			$this->container->segment->set('shortcode', $requestCode);
		}

		$shortCode = $this->container->segment->get('shortcode', '');

		if (empty($shortCode))
		{
			$this->container->input->set('view', 'login');
		}
	}

	public function route($url = null)
	{
		$uri = Uri::getInstance();
		$this->container->router->parse($uri->toString(), false);
	}
}