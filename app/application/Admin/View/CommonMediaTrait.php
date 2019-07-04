<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */


namespace Admin\View;


use Awf\Uri\Uri;
use Awf\Utils\Template;

trait CommonMediaTrait
{
	/**
	 * Overrides the default method to execute and display a template script.
	 * Instead of loadTemplate is uses loadAnyTemplate.
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise an exception.
	 *
	 * @throws  \Exception  When the layout file is not found
	 */
	public function display($tpl = null)
	{
		$this->loadCommonMedia();

		return parent::display($tpl);
	}

	/**
	 * Loads the common media files for our application
	 */
	protected function loadCommonMedia()
	{
		$document  = $this->container->application->getDocument();

		// This only applies to the HTML document type
		if (!($document instanceof \Awf\Document\Html))
		{
			return;
		}

		$baseUrl = Uri::base() . '../media/js/';
		$document->addScript($baseUrl . 'modal.js');
		$document->addScript($baseUrl . 'ajax.js');
		$document->addScript($baseUrl . 'system.js');
	}
}