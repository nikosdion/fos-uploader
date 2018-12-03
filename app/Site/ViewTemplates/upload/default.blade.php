<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

?>
@js('media://js/system.js')
@js('media://js/modal.js')
@js('media://js/upload.js')

<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--stretch">
	<div class="akeeba-panel--teal">
		<header class="akeeba-block-header">
			<h1>Choose photos and videos to upload</h1>
		</header>
		<div class="akeeba-form-group">
			<label for="images">Choose photos and videos to upload</label>
			<input type="file" multiple name="images" id="images"
				   accept=".jpg,.jpeg.,.gif,.png,.mov,.mp4"
				   onchange="akeeba.Upload.handleFiles(this.files, document.getElementById('thumbnails'))"
			>
			<p class="akeeba-help-text">
				Click or tap the button above to select photos and videos from your device. Then click on Upload to send
				the files to us.
			</p>
		</div>
	</div>

	<div class="akeeba-panel--info" id="previewContainer" style="display: none;">
		<header class="akeeba-block-header">
			<h3>Selected images</h3>
		</header>

		<p>
			Total&nbsp;
			<span id="numFiles"></span>
			&nbsp;items,&nbsp;
			<span id="totalSize"></span>&nbsp;bytes
		</p>

		<p id="uploadButton">
			<button type="submit" class="akeeba-btn--green--big">Upload</button>
		</p>


		<div id="thumbnails" class="akeeba-grid--small">

		</div>
	</div>
</div>
