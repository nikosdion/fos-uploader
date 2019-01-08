<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

// Include the autoloader
/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require_once(__DIR__ . '/../vendor/autoload.php');

// Load the platform defines
require_once __DIR__ . '/defines.php';

// Should I enable debug?
if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
}

try
{
	// Add our app to the autoloader, if it's not already set
	$autoloader->addPsr4('Admin\\', APATH_APPROOT . '/Admin');
	$autoloader->addPsr4('Site\\', APATH_APPROOT . '/Site');

	// Create the container if it doesn't already exist
	if (!isset($container))
	{
		$container = new Admin\Container(array(
			'application_name'	=> 'Admin'
		));
	}

	// Create the application
	$application = $container->application;

	// Initialise the application
	$application->initialise();

	// Route the URL: parses the URL through routing rules, replacing the data in the app's input
	$application->route();

	// Dispatch the application
	$application->dispatch();

	// Render the output
	$application->render();

	// Clean-up and shut down
	$application->close();
}
catch (Throwable $exc)
{
	$filename = APATH_THEMES . '/system/error.php';

	if (isset($application))
	{
		if ($application instanceof \Awf\Application\Application)
		{
			$template = $application->getTemplate();

			if (file_exists(APATH_THEMES . '/' . $template . '/error.php'))
			{
				$filename = APATH_THEMES . '/' . $template . '/error.php';
			}
		}
	}

	if (!file_exists($filename))
	{
		die($exc->getMessage());
	}

	include $filename;
}
