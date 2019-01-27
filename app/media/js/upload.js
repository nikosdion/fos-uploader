/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

if (typeof akeeba === "undefined")
{
	akeeba = {};
}

if (typeof akeeba.Upload === "undefined")
{
	akeeba.Upload = {
		totalFiles:           0,
		totalSize:            0,
		files:                {},
		processingSelections: 0
	};
}

/**
 * Update the selected images UI
 */
akeeba.Upload.updateUI = function () {
	function bytesToSize(bytes)
	{
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

		if (bytes === 0)
		{
			return '0 Byte';
		}

		var i = parseInt(String(Math.floor(Math.log(bytes) / Math.log(1024))));
		return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
	}


	if (akeeba.Upload.totalFiles === 0)
	{
		document.getElementById("previewContainer").style.display = "none";
        document.getElementById("selected").style.display = "none";
        document.getElementById("uploadPrompt").style.display = "flex";

		return;
	}

	if (akeeba.Upload.processingSelections)
	{
		document.getElementById('uploadButton').style.display = 'none';
        document.getElementById('processingButton').style.display = 'block';
        document.getElementById('uploadPrompt').style.display = 'none';
	}
	else
	{
		document.getElementById('uploadButton').style.display = 'block';
        document.getElementById('processingButton').style.display = 'none';
        document.getElementById('uploadPrompt').style.display = 'none';
	}

	document.getElementById("previewContainer").style.display = "flex";
    document.getElementById("selected").style.display = "block";

	document.getElementById("numFiles").innerText  = akeeba.Upload.totalFiles;
	document.getElementById("totalSize").innerText = bytesToSize(akeeba.Upload.totalSize);
};

akeeba.Upload.sendStatsToSession = function (successCallback) {
	var data = {
		files: akeeba.Upload.totalFiles,
		size: akeeba.Upload.totalSize
	};

	akeeba.System.params.AjaxURL = '/upload/stats';
	akeeba.System.doAjax(data, function (result) {
		if (result === false)
		{
			console.log("Failed to send statistics to the session");

			return;
		}

		console.log("Successfully sent statistics to the session");

		akeeba.Ajax.triggerCallbacks(successCallback);
	}, function (msg) {
		akeeba.Upload.setUIDisplayState(true);

		// FIXME Better error handling
		akeeba.System.modalErrorHandler(msg);
	});
};

/**
 * Handles any changes to the image file selection INPUT. This is used to create thumbnails and add images to the upload
 * queue.
 *
 * @param  {FileList} files     The list of files returned from the INPUT
 * @param  {Element}  appendTo  The DIV to append the thumbnais to
 */
akeeba.Upload.handleFiles = function (files, appendTo) {
	for (var i = 0; i < files.length; i++)
	{
		var file = files[i];

		// Prevent duplicates
		var found = false;

		for (var propName in akeeba.Upload.files)
		{
			if (!akeeba.Upload.files.hasOwnProperty(propName))
			{
				continue;
			}

			var someFile = akeeba.Upload.files[propName];

			if (someFile.name !== file.name)
			{
				continue;
			}

			if (someFile.size !== file.size)
			{
				continue;
			}

			if (someFile.lastModified !== file.lastModified)
			{
				continue;
			}

			found = true;

			break;
		}

		if (found)
		{
			continue;
		}

		akeeba.Upload.processingSelections++;
		akeeba.Upload.getFileThumb(file, appendTo);
	}
};

/**
 * Get a thumbnail IMG for a file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getFileThumb = function (file, appendTo) {
	if (file.type.match("image"))
	{
		akeeba.Upload.getPhotoThumb(file, appendTo);

		return true;
	}

	if (file.type.match("video"))
	{
		akeeba.Upload.getVideoThumb(file, appendTo);

		return true;
	}

	return false;
};

/**
 * Append a thumbnail to the selected images and enqueue the file
 *
 * @param  {string}  url       The preview image URL, generated by our JS code
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.appendThumb = function (url, file, appendTo) {
	// Add the file to the queue and update the stats
	var uuid                  = akeeba.System.uuid();
	akeeba.Upload.files[uuid] = file;
	akeeba.Upload.totalSize += file.size;
	akeeba.Upload.totalFiles += 1;

	// Create a containing DIV
	var div = document.createElement("div");
	akeeba.System.data.set(div, "uuid", uuid);
	div.className = 'thumbContainer';

	// Create an X button
	var removeBtn       = document.createElement("img");
	removeBtn.src       = '/media/images/modal-close.png';
	removeBtn.className = 'thumbRemove';

	// Create a progress bar
	var elProgress           = document.createElement("div");
	elProgress.className     = 'thumbProgress';
	elProgress.style.display = 'none';

	var elProgressFill         = document.createElement('div');
	elProgressFill.className   = 'thumbProgressFill';
	elProgressFill.style.width = '0';
	elProgress.appendChild(elProgressFill);

	// Create the thumbnail IMG
	var img       = document.createElement("img");
	img.src       = url;
	img.className = 'thumbPreview';

	div.appendChild(removeBtn);
	div.appendChild(elProgress);
	div.appendChild(img);
	appendTo.appendChild(div);

	// Mark one file as processed
	akeeba.Upload.processingSelections--;

	if (akeeba.Upload.processingSelections < 0)
	{
		akeeba.Upload.processingSelections = 0;
	}

	// Finally, update the UI
	akeeba.Upload.updateUI();

	// Remove file on clicking its preview
	akeeba.System.addEventListener(removeBtn, "click", function () {
		// Get a reference to the containing DIV
		var elContainer = this.parentNode;

		// Remove this file's size from the running total
		var uuid = akeeba.System.data.get(elContainer, "uuid");
		var file = akeeba.Upload.files[uuid];

		akeeba.Upload.totalSize -= file.size;
		akeeba.Upload.totalFiles -= 1;

		// Remove the file information
		delete akeeba.Upload.files[uuid];

		// Remove the thumbnail
		elContainer.parentNode.removeChild(elContainer);

		// Update the status counters
		akeeba.Upload.updateUI();
	});
};

/**
 * Get a thumbnail IMG for an image file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getPhotoThumb = function (file, appendTo) {
	var fileReader = new FileReader();

	fileReader.onload = function () {
		akeeba.Upload.appendThumb(String(fileReader.result), file, appendTo);
	};
	fileReader.readAsDataURL(file);
};

/**
 * Get a thumbnail IMG for a video file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getVideoThumb = function (file, appendTo) {
	var fileReader = new FileReader();

	fileReader.onload = function () {
		var blob  = new Blob([fileReader.result], {type: file.type});
		var url   = URL.createObjectURL(blob);
		var video = document.createElement("video");

		var timeupdate = function () {
			if (snapImage())
			{
				video.removeEventListener("timeupdate", timeupdate);
				video.pause();
			}
		};

		video.addEventListener("loadeddata", function () {
			if (snapImage())
			{
				video.removeEventListener("timeupdate", timeupdate);
			}
		});

		var snapImage = function () {
			var canvas    = document.createElement("canvas");
			canvas.width  = video.videoWidth;
			canvas.height = video.videoHeight;
			canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
			var image   = canvas.toDataURL();
			var success = image.length > 100000;

			if (success)
			{
				akeeba.Upload.appendThumb(image, file, appendTo);
				URL.revokeObjectURL(url);
			}

			return success;
		};

		video.addEventListener("timeupdate", timeupdate);
		video.preload = "metadata";
		video.src     = url;

		// Load video in Safari / IE11
		video.muted       = true;
		video.playsInline = true;
		video.play();
	};

	fileReader.readAsArrayBuffer(file);
};

/**
 * Hide/show the upload UI: upload form, Upload button and the remove buttons on all images
 *
 * @param   {boolean}  displayed
 */
akeeba.Upload.setUIDisplayState = function (displayed) {
	var display = displayed ? 'inline-block' : 'none';

	document.getElementById('uploadWrapper').style.display = display;
	document.getElementById('uploadButton').style.display  = display;

	var allImages = document.querySelectorAll('#thumbnails div.thumbContainer');

	for (var i = 0; i < allImages.length; i++)
	{
		var elContainer           = allImages[i];
		var elRemoveBtn           = elContainer.querySelectorAll('img.thumbRemove')[0];
		elRemoveBtn.style.display = display;
	}
};

akeeba.Upload.uploadAllFiles = function () {
	// Hide the UI
	akeeba.Upload.setUIDisplayState(false);

	// Set the stats of the files to be uploaded in the session...
	akeeba.Upload.sendStatsToSession(function () {
		akeeba.Upload.uploadNextFile();
	});
};

akeeba.Upload.uploadNextFile = function () {
	// Get the next img
	var allImages   = document.querySelectorAll('#thumbnails div.thumbContainer');
	var elContainer = allImages[0];

	// Get the File object
	var uuid = akeeba.System.data.get(elContainer, 'uuid');
	var file = akeeba.Upload.files[uuid];
	var data = {
		filename: file.name,
		mime:     file.type,
		size:     file.size
	};

	console.log("Getting presigned URL for " + file.name + ' (' + file.type + ')');

	akeeba.System.params.AjaxURL = '/upload/presigned';
	akeeba.System.doAjax(data, function (presignedURL) {
		if (presignedURL === false)
		{
			console.log("Failed to get presigned URL");
			akeeba.Upload.setUIDisplayState(true);
			// FIXME Better error handling
			akeeba.System.modalErrorHandler('The application server cannot communicate with the file storage server.');

			return;
		}

		console.log("Got presigned URL: " + presignedURL);
		setTimeout(function () {
			akeeba.Upload.uploadFile(file, presignedURL, elContainer);
		}, 100);
	}, function (msg) {
		akeeba.Upload.setUIDisplayState(true);
		// FIXME Better error handling
		akeeba.System.modalErrorHandler(msg);
	});
};

/**
 *
 * @param   {File}     file          The File object we're uploading
 * @param   {string}   presignedURL  The presigned URL we're uploading to
 * @param   {Element}  elContainer   The container of the file's thumbnail
 */
akeeba.Upload.uploadFile = function (file, presignedURL, elContainer) {
	var elProgress           = elContainer.querySelectorAll('div.thumbProgress')[0];
	var elPBFill             = elProgress.querySelectorAll('div.thumbProgressFill')[0];
	elProgress.style.display = 'grid';
	elPBFill.style.width     = '0';

	var successCallbackUpload = function (responseText, statusText, xhr) {
		// Update the UI
		akeeba.Upload.totalSize -= file.size;
		akeeba.Upload.totalFiles--;
		//akeeba.Upload.updateUI();

		elContainer.parentElement.removeChild(elContainer);

		// If no more files, redirect
		var allImages = document.querySelectorAll('#thumbnails div.thumbContainer');

		if (allImages.length === 0)
		{
			window.location = '/thankyou';

			return;
		}

		// If I do have more files, call uploadNextFile again
		setTimeout(function () {
			akeeba.Upload.uploadNextFile();
		}, 100);
	};

	var errorCallbackUpload = function (xhr, type) {
		akeeba.Upload.setUIDisplayState(true);
		// TODO Improve error handling
		akeeba.System.modalErrorHandler('An error occurred: ' + type);
	};

	var ajaxStructure = {
		type:        "PUT",
		cache:       false,
		data:        file,
		contentType: file.type,
		timeout:     3600000,
		success:     successCallbackUpload,
		error:       errorCallbackUpload,
		progress:    function (event) {
			if (event.lengthComputable)
			{
				var percent = Math.round((event.loaded / event.total) * 100);
				console.log('Uploaded ' + percent + '%');

				elPBFill.style.width = 105 * percent / 100;
			}
		}
	};

	akeeba.Ajax.ajax(presignedURL, ajaxStructure);
};