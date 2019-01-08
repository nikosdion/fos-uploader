<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */


namespace Admin\Model;


use Admin\Container;
use Awf\Mvc\DataModel;

/**
 * Class Events
 * @package Admin\Model
 *
 * @property   int    $event_id
 * @property   string $title
 * @property   string $shortcode
 * @property   string $folder
 * @property   int    $enabled
 * @property   string $image
 * @property   string $redirect
 * @property   string $notes
 *
 * @method  $this  title(string $title)
 * @method  $this  shortcode(string $shortcode)
 * @method  $this  folder(string $folder)
 * @method  $this  enabled(int $published)
 * @method  $this  image(string $image)
 * @method  $this  redirect(string $image)
 * @method  $this  notes(string $notes)
 */
class Events extends DataModel
{
	public function __construct(Container $container = null)
	{
		$this->tableName        = '#__events';
		$this->idFieldName      = 'event_id';
		$this->fieldsSkipChecks = ['event_id'];

		parent::__construct($container);

		$this->addBehaviour('Filters');
	}
}