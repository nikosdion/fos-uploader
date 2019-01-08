<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\Controller;


use Awf\Text\Text;

trait ACLTrait
{
	protected $aclChecks = [
		'events' => [
			'add'       => ['create'],
			'delete'    => ['create'],
			'publish'   => ['create'],
			'unpublish' => ['create'],
			'*'         => ['configure'],
		],
		'users'  => [
			'*' => ['system'],
		],
	];

	/**
	 * Executes a given controller task. The onBefore<task> and onAfter<task>
	 * methods are called automatically if they exist.
	 *
	 * @param   string $task The task to execute, e.g. "browse"
	 *
	 * @return  null|bool  False on execution failure
	 *
	 * @throws  \Exception  When the task is not found
	 */
	public function execute($task)
	{
		$view = $this->input->getCmd('view', 'main');

		$this->aclCheck($view, $task);

		return parent::execute($task);
	}

	/**
	 * Performs automatic access control checks
	 *
	 * @param   string $view The view being accessed
	 * @param   string $task The task being accessed
	 *
	 * @throws \RuntimeException
	 */
	protected function aclCheck($view, $task)
	{
		$view = strtolower($view);
		$task = strtolower($task);

		if (!isset($this->aclChecks[$view]))
		{
			return;
		}

		if (!isset($this->aclChecks[$view][$task]))
		{
			if (!isset($this->aclChecks[$view]['*']))
			{
				return;
			}

			$requiredPrivileges = $this->aclChecks[$view]['*'];
		}
		else
		{
			$requiredPrivileges = $this->aclChecks[$view][$task];
		}

		$user = $this->container->userManager->getUser();

		foreach ($requiredPrivileges as $privilege)
		{
			if (!$user->getPrivilege('fos.' . $privilege))
			{
				throw new \RuntimeException(Text::_('AWF_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
			}
		}
	}
}