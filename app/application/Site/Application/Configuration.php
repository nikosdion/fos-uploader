<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */


namespace Site\Application;


use Awf\Utils\Phpfunc;
use Symfony\Component\Dotenv\Dotenv;

class Configuration extends \Awf\Application\Configuration
{
	/**
	 * Maps .env keys to internal configuration values
	 */
	protected const configMap = [
		// Database configuration
		'DB_DRIVER'     => 'dbdriver',
		'DB_HOST'       => 'dbhost',
		'DB_USER'       => 'dbuser',
		'DB_PASS'       => 'dbpass',
		'DB_NAME'       => 'dbname',
		'DB_PREFIX'     => 'prefix',
		'DB_SELECT'     => 'dbselect',

		// Filesystem configuration
		'FS_DRIVER'     => 'fs.driver',
		'FS_HOST'       => 'fs.host',
		'FS_PORT'       => 'fs.port',
		'FS_USER'       => 'fs.username',
		'FS_PASS'       => 'fs.password',
		'FS_DIR'        => 'fs.dir',
		'FS_SSL'        => 'fs.ssl',
		'FS_PASSIVE'    => 'fs.passive',

		// Date / time
		'TIMEZONE'      => 'timezone',

		// Mailer
		'SMTP_HOST'     => 'mail.smtphost',
		'SMTP_AUTH'     => 'mail.smtpauth',
		'SMTP_USER'     => 'mail.smtpuser',
		'SMTP_PASS'     => 'mail.smtppass',
		'SMTP_SECURE'   => 'mail.smtpsecure',
		'SMTP_PORT'     => 'mail.smtpport',
		'MAIL_FROM'     => 'mail.mailfrom',
		'MAIL_FROMNAME' => 'mail.fromname',
		'MAIL_METHOD'   => 'mail.mailer',
		'MAIL_ENABLE'   => 'mail.online',

		// Routing
		'URL_BASE'      => 'base_url',
		'LIVE_SITE'     => 'live_site',

		// User management
		'USER_TABLE'     => 'user_table',
		'USER_CLASS'     => 'user_class',

		// Amazon S3
		'S3_ACCESS' => 's3.access',
		'S3_SECRET' => 's3.secret',
		'S3_REGION' => 's3.region',
		'S3_BUCKET' => 's3.bucket',
		'S3_PREFIX' => 's3.prefix',
	];

	/**
	 * Loads the configuration off a .env file
	 *
	 * @param   string|array   $filePath The path to the .env files (optional)
	 * @param   Phpfunc        $phpfunc  Ignored in this implementation
	 *
	 * @return  void
	 */
	public function loadConfiguration($filePath = null, Phpfunc $phpfunc = null)
	{
		if (empty($filePath))
		{
			$filePath = $this->getDefaultPath();
		}

		// Reset the class
		$this->data = new \stdClass();

		if (!is_array($filePath))
		{
			$filePath = [$filePath];
		}

		$temp = [];

		foreach ($filePath as $someFile)
		{
			if (@is_file($someFile) && @is_readable($someFile))
			{
				$temp[] = $someFile;
			}
		}

		// No files...?
		if (empty($temp))
		{
			return;
		}

		$dotenv = new Dotenv();
		call_user_func_array([$dotenv, 'load'], $temp);

		foreach (self::configMap as $envKey => $regKey)
		{
			$v = getenv($envKey);

			// If the .env variable is not set just ignore it
			if ($v === false)
			{
				continue;
			}

			$this->set($regKey, $v);
		}
	}

	/**
	 * Save the application configuration
	 *
	 * @param   string $filePath The path to the JSON file (optional)
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When saving fails
	 */
	public function saveConfiguration($filePath = null)
	{
		$fileData = '';

		foreach (self::configMap as $envKey => $regKey)
		{
			$v = $this->get($regKey, null);

			if (is_null($v))
			{
				continue;
			}

			if (is_bool($v))
			{
				$v = $v ? 1 : 0;
			}

			$fileData .= "$envKey=$v\n";
		}

		$res = $this->container->fileSystem->write($filePath, $fileData);

		if (!$res)
		{
			throw new \RuntimeException('Can not save ' . $filePath, 500);
		}
	}


	/**
	 * Returns the default configuration file path. If it's not specified, it defines it based on the built-in
	 * conventions.
	 *
	 * @return string
	 */
	public function getDefaultPath()
	{
		if (empty($this->defaultPath))
		{
			$this->defaultPath = $this->container->basePath . '/config/.env';
		}

		return $this->defaultPath;
	}
}