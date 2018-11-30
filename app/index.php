<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

// Include the autoloader
/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require_once __DIR__ . '/../vendor/autoload.php';

// Load the platform defines
require_once __DIR__ . '/defines.php';

// Should I enable debug?
if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
}

// Add our app to the autoloader, if it's not already set
$autoloader->addPsr4('Site', APATH_BASE);

try
{
	// Create the container if it doesn't already exist
	if (!isset($container))
	{
		$container = new Site\Container(array(
			'application_name'	=> 'Site'
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
catch (Exception $exc)
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
