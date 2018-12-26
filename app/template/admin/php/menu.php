<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

use Awf\Document\Document;
use Awf\Document\Menu\Item;
use Awf\Text\Text;

function _fos_template_renderSubmenu(Document $app, Item $root)
{
	$enabled = $app->getMenu()->isEnabled('main');

	$children = $root->getChildren();

	if (empty($children))
	{
		return;
	}

	/** @var Item $item */
	foreach ($children as $item):
		$class = $item->isActive() ? 'class="active"' : '';
		$link = $item->getUrl();

		if (!$enabled)
		{
			$class = 'class="disabled"';
			$link = '#';
		}
	?>
		<a href="<?php echo $link ?>"><?php echo Text::_($item->getTitle()) ?></a>
		<?php
        // We never had nested submenus, so we completely skipped this feature in FEF :)
        // _solo_template_renderSubmenu($app, $item);
        ?>
	<?php
	endforeach;

}
