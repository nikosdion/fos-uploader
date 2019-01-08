<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\Model;


use Awf\Container\Container;
use Awf\Mvc\DataModel;
use Awf\Text\Text;

class Users extends DataModel
{
	/**
	 * Public constructor
	 *
	 * @param   Container $container Configuration parameters
	 */
	public function __construct(\Awf\Container\Container $container = null)
	{
		$this->tableName   = '#__users';
		$this->idFieldName = 'id';

		parent::__construct($container);

		$this->addBehaviour('filters');
	}

	/**
	 * Prevent the deletion of the default backup profile
	 *
	 * @param   integer $id The profile ID which is about to be deleted
	 *
	 * @throws  \RuntimeException  When some wise guy tries to delete the default backup profile
	 */
	public function onBeforeDelete($id)
	{
		if ($id == 1)
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_CANNOTDELETEDEFAULT'), 403);
		}
	}

	public function save($data = null, $orderingFilter = '', $ignore = null)
	{
		// Stash the primary key
		$oldPKValue = $this->{$this->idFieldName};

		// Call the onBeforeSave event
		if (method_exists($this, 'onBeforeSave'))
		{
			$this->onBeforeSave($data);
		}

		$this->behavioursDispatcher->trigger('onBeforeSave', [&$this, &$data]);

		// Bind any (optional) data. If no data is provided, the current record data is used
		if (!is_null($data))
		{
			$this->bind($data, $ignore);
		}

		// Is this a new record?
		if (empty($oldPKValue))
		{
			$isNewRecord = true;
		}
		else
		{
			$isNewRecord = $oldPKValue != $this->{$this->idFieldName};
		}

		// Get the user object
		$userManager = $this->container->userManager;
		$user        = $userManager->getUser($this->id);

		// Check the validity of the data
		if (empty($data['username']))
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_NOUSERNAME'), 500);
		}

		if (empty($data['email']))
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_NOEMAIL'), 500);
		}

		if (!empty($data['password']))
		{
			if ($data['password'] != $data['repeatpassword'])
			{
				throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_NOMATCH'), 500);
			}
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__ak_users'))
			->where($db->qn('username') . ' = ' . $db->q($data['username']))
			->where('NOT(' . $db->qn('id') . ' = ' . $db->q($user->getId()) . ')');
		$db->setQuery($query);

		if ($db->loadResult())
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_USERNAMEEXISTS'), 500);
		}

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__ak_users'))
			->where($db->qn('email') . ' = ' . $db->q($data['email']))
			->where('NOT(' . $db->qn('id') . ' = ' . $db->q($user->getId()) . ')');
		$db->setQuery($query);

		if ($db->loadResult())
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_EMAILEXISTS'), 500);
		}

		$permissions = [
			'create'    => in_array($data['permissions']['create'], ['on', 'yes', 'true', '1']),
			'configure' => in_array($data['permissions']['configure'], ['on', 'yes', 'true', '1']),
			'system'    => in_array($data['permissions']['system'], ['on', 'yes', 'true', '1']),
		];

		if (!$permissions['create'] && !$permissions['configure'] && !$permissions['system'])
		{
			throw new \RuntimeException(Text::_('ADMIN_USERS_ERR_NOPERMISSIONS'), 500);
		}

		// Push the new user data
		$user->setEmail($data['email']);
		$user->setName($data['name']);
		$user->setUsername($data['username']);

		if (!empty($data['password']))
		{
			$user->setPassword($data['password']);
		}

		foreach ($permissions as $k => $v)
		{
			$user->setPrivilege('fos.' . $k, $v);
		}

		// Create/update the user
		$userManager->saveUser($user);

		// Finally, call the onAfterSave event
		if (method_exists($this, 'onAfterSave'))
		{
			$this->onAfterSave();
		}

		$this->behavioursDispatcher->trigger('onAfterSave', [&$this]);

		return $this;
	}
}
