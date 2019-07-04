<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

namespace Site\Controller;


use Akeeba\Engine\Postproc\Connector\S3v4\Acl;
use Akeeba\Engine\Postproc\Connector\S3v4\Input;
use Akeeba\Engine\Postproc\Connector\S3v4\Request as S3Request;
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
		if (empty($task) || ($task == 'default'))
		{
			$task = 'main';
		}

		if (!in_array($task, ['main', 'presigned', 'stats']))
		{
			return false;
		}

		return parent::execute($task);
	}

	/**
	 * Main display task. Displays the upload page UI.
	 *
	 * @return  void
	 */
	public function main()
	{
		if ($this->sanityChecksRequireRedirect())
		{
			return;
		}

		/** @var \Site\Model\Main $model */
		$model      = $this->getModel();
		$name       = $this->container->segment->get('name', '');
		$folderName = $model->getFolderNameFromName($name);
		$this->container->segment->set('foldername', $folderName);

		$event = $this->getEvent();
		$view  = $this->getView();
		$view->setModel('event', $event);

		parent::main();
	}

	/**
	 * Create a presigned upload URL and send it back to the browser JSON-encoded.
	 *
	 * @return  void
	 */
	public function presigned()
	{
		$folderName = $this->container->segment->get('foldername', '');
		$fileName   = $this->input->post->getString('filename', '');
		$mime       = $this->input->post->getString('mime', '');
		$size       = $this->input->post->getInt('size', 0);

		if ($this->sanityChecksRequireRedirect() || empty($folderName) || empty($fileName) || empty($mime))
		{
			$this->redirect = '';
			$this->message  = '';

			@ob_end_clean();
			echo '###' . json_encode(false) . '###';

			$this->container->application->close();

			return;
		}

		// Get the bucket name
		/** @var \Site\Container $container */
		$container = $this->container;
		$bucket    = $container->appConfig->get('s3.bucket', '');

		// Get the project folder in S3
		/** @var \Site\Model\Main $model */
		$model         = $this->getModel();
		$projectCode   = $this->getShortcode();
		$projectFolder = $model->getProjectFolderName($projectCode);

		// Remove path separators from file name for security reasons
		$fileName = str_replace('/', '_', $fileName);

		// Construct the file URI in S3
		$uri = $container->appConfig->get('s3.prefix') . '/' . $projectFolder . '/' . $folderName . '/' . $fileName;

		$s3Config = $container->s3->getConfiguration();
		$uri      = str_replace('%2F', '/', rawurlencode($uri));
		$request  = new S3Request('PUT', $bucket, $uri, $s3Config);
		$fakeData = '';
		$input    = Input::createFromData($fakeData);
		$input->setType($mime);
		$request->setInput($input);
		$input->setSize($size);
		$request->setHeader('Content-Type', $mime);
		$request->setHeader('Content-Length', $size);
		// Do not add that header, it messes up the presigned URL
		//$request->setAmzHeader('x-amz-acl', Acl::ACL_PRIVATE);

		@ob_end_clean();
		echo '###' . json_encode($request->getAuthenticatedURL(60, true)) . '###';

		$this->container->application->close();
	}

	/**
	 * Store the total number and size of files being uploaded to the session
	 *
	 * @return  void
	 */
	public function stats()
	{
		if ($this->sanityChecksRequireRedirect())
		{
			@ob_end_clean();
			echo '###' . json_encode(false) . '###';

			$this->container->application->close();

			return true;
		}

		$numFiles  = $this->input->getInt('files', 0);
		$totalSize = $this->input->getInt('size', 0);

		$segment = $this->container->segment;

		$segment->set('totalfiles', $numFiles);
		$segment->set('totalsize', $totalSize);

		@ob_end_clean();
		echo '###' . json_encode(true) . '###';

		$this->container->application->close();

		return true;

	}

	/**
	 * Checks if all preconditions to start uploading are met. If not, it generates a redirect to the correct page and
	 * returns boolean true. If all preconditions are met it returns boolean false.
	 *
	 * @return  bool
	 */
	protected function sanityChecksRequireRedirect(): bool
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

			$redirection = $this->getRedirection();

			if (!empty($redirection))
			{
				$redirectURL = $redirection;
				$msg         = null;
				$msgType     = null;
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

		return false;
	}
}