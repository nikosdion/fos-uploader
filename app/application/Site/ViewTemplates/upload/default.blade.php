<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

?>
@js('media://js/upload.js')

@include('common/error_modal')

<header>
		<h1>
			{{{ $this->event->title }}}
		</h1>
		<div class="triangle"><img src="/media/images/logo.svg" alt="Fos Photography" width="50"/></div>
</header>

<section class="main">
	<h3 class="upload-header">@lang('SITE_UPLOAD_HEAD_CHOOSE_PHOTOS')</h3>
	<div class="" id="uploadWrapper">

		<div id="selected" style="display: none;">
			<strong><span id="numFiles"></span></strong> @lang('SITE_UPLOAD_LBL_TOTAL')&nbsp;
			<label for="images" class="button">
				<strong>+</strong> @lang('SITE_UPLOAD_LBL_ADD_MORE')
			</label>
		</div>
		<div class="photo-upload-box" id="uploadPrompt">
			<label for="images" class="empty-state">
				<img src="/media/images/upload.svg"/> <br />
				@lang('SITE_UPLOAD_LBL_CHOOSE_PHOTOS')
			</label>


			<input type="file" multiple name="images" id="images"
				   accept=".jpg,.jpeg.,.gif,.png,.mov,.mp4"
				   onchange="akeeba.Upload.handleFiles(this.files, document.getElementById('thumbnails'))"
			>
			<!--p class="akeeba-help-text">
				@lang('SITE_UPLOAD_HELP_CHOOSE_PHOTOS')
			</p-->
		</div>

	</div>

	<div id="previewContainer" style="display: none;">
		<div class="thumbnail-wrapper">
			<div id="thumbnails">

			</div>
		</div>
		<button
				type="button" id="processingButton"
				disabled
		>
			@lang('SITE_UPLOAD_BTN_PROCESSING')
		</button>

		<button
					type="submit" id="uploadButton"
					onclick="akeeba.Upload.uploadAllFiles(); return false;"
			>
				@lang('SITE_UPLOAD_BTN_UPLOAD')
				<strong>(<span id="totalSize"></span>)</strong>
			</button>



	</div>
</section>
