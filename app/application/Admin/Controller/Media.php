<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */


namespace Admin\Controller;


use Admin\Model\Media as MediaModel;
use Admin\View\Media\Html as MediaView;
use Awf\Mvc\Controller;
use Awf\Utils\Path;

class Media extends Controller
{
	public function main()
	{
		/** @var MediaModel $model */
		$model = $this->getModel();
		/** @var MediaView $view */
		$view = $this->getView();

		$view->files = $model->getImages();

		$this->display();
	}

	public function thumb()
	{
		$file = $this->input->getPath('file', null);

		if (empty($file))
		{
			$this->container->application->close();
		}

		$filePath = MediaModel::mediaPath . '/' . Path::clean($file);

		header('Content-Type: image/png');

		/** @var MediaModel $model */
		$model = $this->getModel();
		echo $model->getResizedImageData($filePath, 120);
	}

	public function upload()
	{
		/** @var MediaModel $model */
		$model       = $this->getModel();
		$redirectURL = $this->container->router->route('index.php?view=media&task=browse');

		try
		{
			$model->handleUpload($_FILES['file']);
			$this->setRedirect($redirectURL);
		}
		catch (\RuntimeException $e)
		{
			$this->setRedirect($redirectURL, $e->getMessage(), 'error');
		}
	}
}
