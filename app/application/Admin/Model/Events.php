<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */


namespace Admin\Model;


use Admin\Container;
use Awf\Date\Date;
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
 * @property   string $publish_up
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

	public function check(): Events
	{
		parent::check();

		if (empty($this->publish_up))
		{
			$this->publish_up = null;
		}

		return $this;
	}

	/**
	 * Automatically publish events based on their publish date when getting a collection of objects
	 *
	 * @param   array  $items
	 */
	protected function onAfterGetItemsArray(array &$items): void
	{
		if (empty($items))
		{
			return;
		}

		$nullDate = $this->dbo->getNullDate();
		$now      = time();

		/**
		 * @var   int    $idx
		 * @var   Events $item
		 */
		foreach ($items as $idx => &$item)
		{
			if (empty($item->publish_up) || ($item->publish_up === $nullDate))
			{
				continue;
			}

			$publishUp = new Date($item->publish_up);

			if ($publishUp->toUnix() > $now)
			{
				continue;
			}

			/** @var static $record */
			$item->save([
				'enabled' => 1,
			]);
		}
	}
}
