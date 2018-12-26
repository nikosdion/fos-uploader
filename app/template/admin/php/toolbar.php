<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

use Awf\Document\Document;
use Awf\Document\Toolbar\Button;
use Awf\Text\Text;

/** @var Document $this */

$buttons = $this->getToolbar()->getButtons();
$submenu = $this->getMenu()->getMenuItems('submenu')->getChildren();
if (!empty($buttons) || !empty($submenu)): ?>
<header class="akeeba-navbar akeeba-toolbar">
    <div class="akeeba-maxwidth akeeba-flex">
        <div class="akeeba-nav-logo">
	        <?php if ($title = $this->getToolbar()->getTitle()):?>
            <span class="akeeba-toolbar-title"><?php echo \Awf\Text\Text::_($title) ?></span>
	        <?php endif; ?>
            <a href="#" class="akeeba-menu-button akeeba-hidden-desktop akeeba-hidden-tablet"
               title="<?php echo \Awf\Text\Text::_('ADMIN_COMMON_TOGGLENAV') ?>"><span class="akion-navicon-round"></span></a>
        </div>

		<?php if (($buttons = $this->getToolbar()->getButtons()) && count($buttons)):?>
		<nav>
			<?php
			foreach ($buttons as $button):
				/** @var Button $button */
				if ($url = $button->getUrl())
				{
					$type = 'a';
					$action = 'href="' . $url . '"';
				}
				else
				{
					$type = 'button';
					$action = 'onclick="' . $button->getOnClick() . '"';
				}
				?>
				<<?php echo $type . ' ' . $action?> class="akeeba-btn--small <?php echo $button->getClass() ?>" id="<?php echo $button->getId() ?>">
				<?php if ($icon = $button->getIcon()): ?>
				<span class="<?php echo $icon ?>"></span>
			<?php endif; ?>
				<?php echo Text::_($button->getTitle()) ?>
				</<?php echo $type?>>
			<?php endforeach; ?>
		</nav>
		<?php endif; ?>

		<nav>
			<?php _fos_template_renderSubmenu($this, $this->getMenu()->getMenuItems('submenu')); ?>
		</nav>

	</div>
</header>

<?php endif; ?>