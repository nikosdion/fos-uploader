<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

/** @var \Awf\Document\Document $this */

include __DIR__ . '/php/menu.php';
$this->outputHTTPHeaders();
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php // Favicons size reference: https://github.com/audreyr/favicon-cheat-sheet ?>
	<link rel="shortcut icon" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/favicon.ico">
	<link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-152.png">
	<meta name="msapplication-TileColor" content="#FFFFFF">
	<meta name="msapplication-TileImage" content="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-144.png">
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-152.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-144.png">
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-120.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-72.png">
	<link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-57.png">
	<link rel="icon" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/fos-32.png" sizes="32x32">

	<title><?php echo \Awf\Text\Text::_('ADMIN_APP_TITLE') ?></title>

<?php include __DIR__ . '/php/head.php' ?>

</head>
<body class="akeeba-renderer-fef">
<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
    <header class="akeeba-navbar">
        <div class="akeeba-maxwidth akeeba-flex">
            <!-- Branding -->
            <div class="akeeba-nav-logo">
                <a href="<?php echo $this->getContainer()->router->route('index.php') ?>">
                    <span class="aklogo-fos"></span>
                    <span class="akeeba-product-name">
                        <?php echo \Awf\Text\Text::_('ADMIN_APP_TITLE_SHORT') ?>
                    </span>
                </a>
                <a href="#" class="akeeba-menu-button akeeba-hidden-desktop akeeba-hidden-tablet"
                   title="<?php echo \Awf\Text\Text::_('ADMIN_COMMON_TOGGLENAV') ?>"><span class="akion-navicon-round"></span></a>
            </div>
            <!-- Navigation -->

            <nav>
	            <?php _fos_template_renderSubmenu($this, $this->getMenu()->getMenuItems('main'), 'nav navbar-nav'); ?>
	            <?php if (\Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getId()): ?>
                <a href="<?php echo $this->getContainer()->router->route('index.php?view=login&task=logout') ?>" class="akeeba-btn--grey hasTooltip" title="<?php echo \Awf\Text\Text::sprintf('ADMIN_LOGIN_LBL_LOGOUT', \Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getUsername()) ?>" data-toggle="tooltip" data-placement="bottom">
                    <span class="akion-log-out"></span>
                    <span class="hidden-lg hidden-md hidden-sm"><?php echo \Awf\Text\Text::sprintf('ADMIN_LOGIN_LBL_LOGOUT', \Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getUsername()) ?></span>
                </a>
	            <?php endif; ?>
            </nav>
        </div>
    </header>

    <?php include __DIR__ . '/php/toolbar.php' ?>
<div class="akeeba-maxwidth">
<?php endif; ?>

<?php include __DIR__ . '/php/messages.php' ?>
<?php echo $this->getBuffer() ?>

<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
</div>
<footer id="akeeba-footer">
    <div class="akeeba-maxwidth">
        <p class="muted credit">
            Copyright &copy;2018 &ndash; <?php echo date('Y') ?> <a href="https://www.akeeba.com">Akeeba Ltd</a>. All legal rights reserved.
        </p>
	    <?php if (defined('AKEEBADEBUG')): ?>
            <p class="small">
                    Page creation <?php echo sprintf('%0.3f', \Awf\Application\Application::getInstance()->getTimeElapsed()) ?> sec
                    &bull;
                    Peak memory usage <?php echo sprintf('%0.1f', memory_get_peak_usage() / 1048576) ?> Mb
            </p>
	    <?php endif; ?>
    </div>
</footer>
<?php endif; ?>

<script type="text/javascript">
	akeeba.System.documentReady(function(){
		for (var i = 0; i < akeeba.loadScripts.length; i++)
		{
			akeeba.loadScripts[i]();
		}
	});
</script>
</body>
</html>
