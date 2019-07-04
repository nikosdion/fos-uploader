<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Admin\Application;

use Awf\User\Privilege;

class UserPrivileges extends Privilege
{
	public function __construct()
	{
		$this->name = 'fos';
		// Set up the privilege names and their default values
		$this->privileges = array(
			'create'	=> false,
			'configure'	=> false,
			'system'	=> false,
		);
	}
} 
