<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd & Fos Photography
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
 * @property   int     $event_id
 * @property   string  $title
 * @property   string  $shortcode
 * @property   string  $folder
 * @property   int     $published
 * @property   string  $image
 * @property   string  $notes
 *
 * @method   title(string $title)
 * @method   shortcode(string $shortcode)
 * @method   folder(string $folder)
 * @method   image(string $image)
 * @method   notes(string $notes)
 */
class Events extends DataModel
{
	public function __construct(Container $container = null)
	{
		$this->tableName = '#__events';

		parent::__construct($container);

		$this->addBehaviour('Filters');
	}
}