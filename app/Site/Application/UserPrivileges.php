<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

namespace Site\Application;

use Awf\User\Privilege;

class UserPrivileges extends Privilege
{
	public function __construct()
	{
		$this->name = 'frontend';

		// Set up the privilege names and their default values
		$this->privileges = array(
			'upload'	=> false,
		);
	}
} 
