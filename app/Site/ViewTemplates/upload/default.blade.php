<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

?>
@js('media://js/system.js')
@js('media://js/ajax.js')
@js('media://js/modal.js')
@js('media://js/upload.js')

<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--stretch">
	<div class="akeeba-panel--teal" id="uploadWrapper">
		<header class="akeeba-block-header">
			<h1>
				@lang('SITE_UPLOAD_HEAD_CHOOSE_PHOTOS')
			</h1>
		</header>
		<div class="akeeba-form-group">
			<label for="images">
				@lang('SITE_UPLOAD_LBL_CHOOSE_PHOTOS')
			</label>
			<input type="file" multiple name="images" id="images"
				   accept=".jpg,.jpeg.,.gif,.png,.mov,.mp4"
				   onchange="akeeba.Upload.handleFiles(this.files, document.getElementById('thumbnails'))"
			>
			<p class="akeeba-help-text">
				@lang('SITE_UPLOAD_HELP_CHOOSE_PHOTOS')
			</p>
		</div>
	</div>

	<div class="akeeba-panel--info" id="previewContainer" style="display: none;">
		<header class="akeeba-block-header">
			<h3>
				@lang('SITE_UPLOAD_HEAD_SELECTED_IMAGES')
			</h3>
		</header>

		<p>
			@lang('SITE_UPLOAD_LBL_TOTAL')&nbsp;
			<span id="numFiles"></span>
			@lang('SITE_UPLOAD_LBL_ITEMS'),&nbsp;
			<span id="totalSize"></span>
		</p>

		<p id="uploadButton">
			<button
					type="submit" class="akeeba-btn--green--big"
					onclick="akeeba.Upload.uploadAllFiles(); return false;"
			>
				@lang('SITE_UPLOAD_BTN_UPLOAD')
			</button>
		</p>


		<div id="thumbnails" class="akeeba-grid--small">

		</div>
	</div>
</div>
