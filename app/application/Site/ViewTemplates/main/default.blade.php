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

<header  style="background-image:url('/media/images/events/{{{ $this->event->image }}}')">
    <div class="fos-image">
        <h1>
            {{{ $this->event->title }}}
        </h1>
        <div class="triangle"><img src="/media/images/logo.svg" alt="Fos Photography" width="50"/></div>
    </div>
</header>
<section class="main">
	<h3>
		@lang('SITE_MAIN_HEADER')
		<a href="#"><span>@lang('SITE_MAIN_HEADER_READMORE')</span></a>
	</h3>
	<hr />
	<form action="@route('index.php?view=main&task=setinfo')"
		  method="POST" class="">

		<div>
			<label for="fullname">
				@lang('SITE_MAIN_NAME_LABEL')
			</label>
			<input type="text" name="fullname" id="fullname" value="{{{ $this->fullname }}}">
			<a href="#"><span class="icon"><img src="/media/images/help.svg" style="border-bottom:1px solid #000;"/></span></a>
		</div>
		<p class="help-text">
			@lang('SITE_MAIN_NAME_HELP')
		</p>
		<div class="checkbox">
			<input type="checkbox" name="agreetotos" id="agreetotos" {{ $this->accept ? 'checked="checked"' : '' }}>
			<label for="agreetotos">
				@sprintf('SITE_MAIN_TOS_LABEL', 'tos.html', 'privacy.html')
			</label>
		</div>
		<div>
			<button type="submit">
				@lang('SITE_MAIN_BTN_SUBMIT')
				<span class="icon"><img src="/media/images/forward.svg"/></span>
			</button>
		</div>
	</form>
</section>

