<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 * This file is based on original work by Akeeba Ltd and is provided to FOS Photography under license.
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
			'download'	=> false,
		);
	}
} 
