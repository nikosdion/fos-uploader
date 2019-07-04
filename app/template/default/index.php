<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

/** @var \Awf\Document\Document $this */

$this->outputHTTPHeaders();
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php // Favicons size reference: https://github.com/audreyr/favicon-cheat-sheet ?>
    <link rel="shortcut icon" href="<?php echo \Awf\Uri\Uri::base() ?>/media/images/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-144.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-152.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-144.png">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-120.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-72.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-57.png">
    <link rel="icon" href="<?php echo \Awf\Uri\Uri::base() ?>media/images/logo-32.png" sizes="32x32">

	<title><?php echo \Awf\Text\Text::_('SITE_APP_TITLE') ?></title>

<?php include __DIR__ . '/php/head.php' ?>

</head>
<body class="">
<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
<div class="">
<?php endif; ?>

<?php include __DIR__ . '/php/messages.php' ?>
<?php echo $this->getBuffer() ?>

<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
</div>
<footer id="">
    <div class="h">
        <p class="soc">
            <a href="https://www.facebook.com/pg/fosphotographycinematography/posts/"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://www.instagram.com/fos_photography/"><ion-icon name="logo-instagram"></ion-icon></a>
            <a href="https://vimeo.com/user52027932"><ion-icon name="logo-vimeo"></ion-icon></a>
        </p>
        <p class="links">
			<!--<a href="javascript:akeeba.System.modalWhatIsThis();"><?php echo \Awf\Text\Text::_('SITE_FOOTER_EXPLANATION_LINK') ?></a>-->
			<a href="tos"><?php echo \Awf\Text\Text::_('SITE_FOOTER_TERMSOFSERVICE') ?></a>
			<a href="privacy"><?php echo \Awf\Text\Text::_('SITE_FOOTER_PRIVACY') ?></a>
        </p>
        <p class="">
             &copy; 2018 &ndash; <?php echo date('Y') ?> <a href="https://fosproductions.gr/">Fos Productions</a>. <?= Awf\Text\Text::_('SITE_FOOTER_ALLRIGHTSRESERVED') ?>
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

<div id="WhatIsThismodal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="akeeba-renderer-fef">
        <h4 id="errorDialogLabel">
            <?php echo \Awf\Text\Text::_('SITE_EXPLANATION_HEADER') ?>
        </h4>

        <p>
            <?php echo \Awf\Text\Text::_('SITE_EXPLANATION_TEXT') ?>
        </p>
    </div>
</div>

<script type="text/javascript">
	akeeba.System.documentReady(function(){
		for (var i = 0; i < akeeba.loadScripts.length; i++)
		{
			akeeba.loadScripts[i]();
		}
	});
</script>
<script src="https://unpkg.com/ionicons@4.5.1/dist/ionicons.js"></script>
</body>
</html>
