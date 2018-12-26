<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site;

use Awf\Autoloader\Autoloader;
use Awf\Database\Installer;
use Awf\Mvc\Model;
use Awf\Session\Exception;
use Composer\Autoload\ClassLoader;

// Should I enable debug?
define('AKEEBADEBUG', 1);

if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
}

/** @var ClassLoader $autoloader */
$autoloader = require_once __DIR__ . '/../vendor/autoload.php';

// Load the platform defines
if (!defined('APATH_BASE'))
{
	require_once __DIR__ . '/../defines.php';
}

// Add our app to the autoloader, if it's not already set
$prefixes = Autoloader::getInstance()->getPrefixes();
if (!array_key_exists('Site\\', $prefixes))
{
	Autoloader::getInstance()->addMap('Site\\', APATH_APPROOT . '/Site');
}

class InstallerApplication extends \Awf\Application\Cli
{
	public function initialise()
	{
		$container     = $this->getContainer();

		// Halt if the configuration does not exist yet
		$configPaths = [
			APATH_ROOT . '/config/.env.ci',
			APATH_ROOT . '/config/.env.dev',
			APATH_ROOT . '/config/.env',
		];
		$hasConfig = false;

		foreach ($configPaths as $path)
		{
			if (@file_exists($path))
			{
				$hasConfig = true;
				break;
			}
		}

		if (!$hasConfig)
		{
			$this->out('Configuration not found; aborting');
			$this->close(254);
		}

		// Load the application's configuration
		$this->container->appConfig->loadConfiguration($configPaths);

		return $this;
	}

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		$db = $this->container->db;

		if (!$db->connected())
		{
			$this->out('You need to configure the database connection first');
			$this->close(253);
		}

		$hasTable = true;

		try
		{
			$usersColumns = $db->getTableColumns('#__users');

			if (empty($usersColumns))
			{
				$hasTable = false;
			}
		}
		catch (Exception $e)
		{
			$hasTable = false;
		}

		if ($hasTable)
		{
			$this->out('Already configured');
			$this->close(252);
		}

		$installer = new Installer($this->container);
		$installer->setXmlDirectory(__DIR__);

		try
		{
			$installer->updateSchema();
		}
		catch (\Exception $e)
		{
			$this->out('Could not install database tables');
			$this->out($e->getMessage());

			if (defined('AKEEBADEBUG'))
			{
				$this->out($e->getFile() . '::' . $e->getLine());
			}
		}

		$this->out('Setting up default users');

		// TODO Set up a default admin user

	}
}

$container = new Container(array(
	'application_name'	=> 'Site'
));
$app = new InstallerApplication($container);
$app->initialise()->execute();
