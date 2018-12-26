<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\View;

use Awf\Mvc\View;
use Awf\Utils\Template;

/**
 * Abstract HTML view class for Akeeba Solo. Used to provide common resource loading.
 */
abstract class Html extends View
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

		Template::addJs('media://js/solo/modal.js', $this->container->application);
		Template::addJs('media://js/solo/ajax.js', $this->container->application);
		Template::addJs('media://js/solo/system.js', $this->container->application);
	}
} 