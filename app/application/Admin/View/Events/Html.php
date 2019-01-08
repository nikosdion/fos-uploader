<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Admin\View\Events;


use Admin\View\CommonMediaTrait;
use Awf\Mvc\DataView\Html as DataHtml;

class Html extends DataHtml
{
	use CommonMediaTrait;

	public function onBeforeBrowse()
	{
		$document = $this->container->application->getDocument();

		// Buttons (new, edit, copy, delete)
		$buttons = array(
			array(
				'title'   => 'ADMIN_BTN_ADD',
				'class'   => 'akeeba-btn--green',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'add\')',
				'icon'    => 'akion-person-add'
			),
			array(
				'title'   => 'ADMIN_BTN_EDIT',
				'class'   => 'akeeba-btn--grey',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'edit\')',
				'icon'    => 'akion-edit'
			),
/*			array(
				'title'   => 'ADMIN_BTN_DELETE',
				'class'   => 'akeeba-btn--red',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'remove\')',
				'icon'    => 'akion-trash-b'
			),*/
			array(
				'title'   => 'ADMIN_BTN_PUBLISH',
				'class'   => 'akeeba-btn--dark',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'publish\')',
				'icon'    => 'akion-checkmark-circled'
			),
			array(
				'title'   => 'ADMIN_BTN_UNPUBLISH',
				'class'   => 'akeeba-btn--red',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'unpublish\')',
				'icon'    => 'akion-android-remove-circle'
			),
		);

		$toolbar = $document->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}

		return parent::onBeforeBrowse();
	}

	protected function onBeforeAdd()
	{
		$this->buttonsForAddEdit();

		return parent::onBeforeAdd();
	}

	protected function onBeforeEdit()
	{
		$this->buttonsForAddEdit();

		return parent::onBeforeEdit();
	}

	protected function buttonsForAddEdit()
	{
		$buttons = array(
			array(
				'title'   => 'ADMIN_BTN_SAVECLOSE',
				'class'   => 'akeeba-btn--green',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'save\')',
				'icon'    => 'akion-checkmark'
			),
			array(
				'title'   => 'ADMIN_BTN_CANCEL',
				'class'   => 'akeeba-btn--orange',
				'onClick' => 'akeeba.System.submitForm(\'adminForm\', \'cancel\')',
				'icon'    => 'akion-close-circled'
			),
		);

		$toolbar = $this->container->application->getDocument()->getToolbar();

		foreach ($buttons as $button)
		{
			$toolbar->addButtonFromDefinition($button);
		}
	}
}