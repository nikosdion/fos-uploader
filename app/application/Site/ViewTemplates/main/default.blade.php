<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

/** @var  \Site\View\Main\Html $this */
?>

<form action="@route('index.php?view=main&task=setinfo')"
	  method="POST" class="akeeba-form--horizontal">
	<div class="akeeba-panel--teal">
		<header class="akeeba-block-header">
			<h1>
				@lang('SITE_MAIN_HEADER')
			</h1>
		</header>
		<div class="akeeba-form-group">
			<label for="fullname">
				@lang('SITE_MAIN_NAME_LABEL')
			</label>
			<input type="text" name="fullname" id="fullname" value="{{{ $this->fullname }}}">
			<p class="akeeba-help-text">
				@lang('SITE_MAIN_NAME_HELP')
			</p>
		</div>
		<div class="akeeba-form-group--checkbox--pull-right">
			<label for="agreetotos">
				<input type="checkbox" name="agreetotos" id="agreetotos" {{ $this->accept ? 'checked="checked"' : '' }}>
				@sprintf('SITE_MAIN_TOS_LABEL', 'tos.html', 'privacy.html')
			</label>
		</div>
		<div class="akeeba-form-group--pull-right">
			<div class="akeeba-form-group--actions">
				<button type="submit" class="akeeba-btn">
					@lang('SITE_MAIN_BTN_SUBMIT')
				</button>
			</div>
		</div>
	</div>
</form>
