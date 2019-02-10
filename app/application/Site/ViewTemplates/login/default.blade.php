<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

/** @var  $this  \Site\View\Login\Html */
?>
<div class="page-home">
	<header>
		<div class="fos-image">
			<div><img src="/media/images/logo.svg" alt="Fos Photography" width="150" /></div>
			<h1>@lang('SITE_LOGIN_HEADER')</h1>
		</div>
	</header>
	<section class="main">
		<form action="@route('index.php?view=main')"
			  method="GET" class="">
			<!-- p>@lang('SITE_LOGIN_LBL_ENTER_SHORTCODE')</p -->
			<div class="">
				<label for="shortcode"  aria-label="shortcode">@lang('SITE_LOGIN_SHORTCODE_LABEL')</label>
				<input type="text" name="shortcode" id="shortcode" value="{{{ $this->shortcode }}}" placeholder="@lang('SITE_LOGIN_INPUT_PLACEHOLDER')">
			</div>
			<div class="">
				<button type="submit" class="">
					@lang('SITE_LOGIN_BTN_SUBMIT_LABEL')
				</button>
			</div>
		</form>
	</section>
</div>