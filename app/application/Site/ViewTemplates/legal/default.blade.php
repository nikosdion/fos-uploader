<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

$title = \Awf\Text\Text::_('SITE_LEGAL_' . $this->page);
$title = ($title == 'SITE_LEGAL_' . $this->page) ? \Awf\Text\Text::_('SITE_LEGAL_NOTFOUND') : $title;
?>

<div class="page-home">
	<header>
		<div class="fos-image">
			<div><img src="/media/images/logo.svg" alt="Fos Photography" width="150" /></div>
			<h1>{{{ $title }}}</h1>
		</div>
	</header>
	<section class="main">
		{{ $this->content }}
	</section>
</div>
