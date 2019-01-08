<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */
?>

<form action="@route('index.php?view=main')"
	  method="GET" class="akeeba-form--inline">
	<div class="akeeba-panel--teal">
		<header class="akeeba-block-header">
			<h1>@lang('SITE_LOGIN_HEADER')</h1>
		</header>
		<p>@lang('SITE_LOGIN_LBL_ENTER_SHORTCODE')</p>
		<div class="akeeba-form-group">
			<label for="shortcode" style="display: none;" aria-label="shortcode">@lang('SITE_LOGIN_SHORTCODE_LABEL')</label>
			<input type="text" name="shortcode" id="shortcode" value="">
		</div>
		<div class="akeeba-form-group--actions">
			<button type="submit" class="akeeba-btn">
				@lang('SITE_LOGIN_BTN_SUBMIT_LABEL')
			</button>
		</div>
	</div>
</form>
