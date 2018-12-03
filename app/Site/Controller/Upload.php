<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */


namespace Site\Controller;


use Awf\Container\Container;
use Awf\Mvc\Controller;
use Awf\Text\Text;
use Site\Controller\ControllerTraits\RequireShortcode;

class Upload extends Controller
{
	use RequireShortcode;

	public function __construct(Container $container = null)
	{
		$this->modelName = 'main';

		parent::__construct($container);
	}

	/**
	 * Runs when executing a task. Used to verify that everything is set up correctly.
	 *
	 * @param   string $task
	 *
	 * @return  bool|null
	 *
	 * @throws  \Exception
	 */
	public function execute($task): ?bool
	{
		if (!$this->isValidShortcode())
		{
			$msg     = Text::_('SITE_ERR_SHORTCODE_NOTEXISTS');
			$msgType = 'error';

			$redirectURL = $this->container->router->route('index.php?view=login');

			if ($this->isExpiredShortcode())
			{
				$msg     = Text::_('SITE_ERR_SHORTCODE_EXPIRED');
				$msgType = 'info';
			}

			$this->setRedirect($redirectURL, $msg, $msgType);

			return true;
		}

		$redirectURL = $this->container->router->route('index.php?view=main');
		$name        = $this->container->segment->get('name', '');

		if (empty($name))
		{
			$this->setRedirect($redirectURL, Text::_('SITE_ERR_NAME_EMPTY'), 'error');

			return true;
		}

		$agreeToTos = $this->container->segment->get('agree', '');

		if (!$agreeToTos)
		{
			$this->setRedirect($redirectURL, Text::_('SITE_ERR_TOS_ACCEPT'), 'error');

			return true;
		}

		/** @var \Site\Model\Main $model */
		$model      = $this->getModel();
		$folderName = $model->getFolderNameFromName($name);
		$this->container->segment->set('foldername', $folderName);

		return parent::execute($task);
	}

}