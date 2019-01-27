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

<header>
	<div class="fos-image">
		<h1>
			event title
		</h1>
	</div>
</header>
<section class="main">
	<h3>@lang('SITE_MAIN_HEADER') <span>@lang('SITE_MAIN_HEADER_READMORE')</span></h3>
	<hr />
	<form action="@route('index.php?view=main&task=setinfo')"
		  method="POST" class="">

		<div>
			<label for="fullname">
				@lang('SITE_MAIN_NAME_LABEL')
			</label>
			<input type="text" name="fullname" id="fullname" value="{{{ $this->fullname }}}">

		</div>
		<p>
			@lang('SITE_MAIN_NAME_HELP')
		</p>
		<div>
			<label for="agreetotos">
				<input type="checkbox" name="agreetotos" id="agreetotos" {{ $this->accept ? 'checked="checked"' : '' }}>
				@sprintf('SITE_MAIN_TOS_LABEL', 'tos.html', 'privacy.html')
			</label>
		</div>
		<div>
			<button type="submit">
				@lang('SITE_MAIN_BTN_SUBMIT')
			</button>
		</div>
	</form>
</section>

