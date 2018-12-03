if (typeof akeeba === "undefined")
{
	akeeba = {};
}

if (typeof akeeba.Upload === "undefined")
{
	akeeba.Upload = {
		totalFiles: 0,
		totalSize: 0,
		files: {}
	};
}

/**
 * Update the selected images UI
 */
akeeba.Upload.updateUI = function ()
{
	if (akeeba.Upload.totalFiles === 0)
	{
		document.getElementById("previewContainer").style.display = "none";

		return;
	}

	document.getElementById("previewContainer").style.display = "block";

	document.getElementById("numFiles").innerText  = akeeba.Upload.totalFiles;
	document.getElementById("totalSize").innerText = akeeba.Upload.totalSize;

};

/**
 * Handles any changes to the image file selection INPUT. This is used to create thumbnails and add images to the upload
 * queue.
 *
 * @param  {FileList} files     The list of files returned from the INPUT
 * @param  {Element}  appendTo  The DIV to append the thumbnais to
 */
akeeba.Upload.handleFiles = function (files, appendTo)
{
	var i = 0;

	for (i = 0; i < files.length; i++)
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

		akeeba.Upload.getFileThumb(file, appendTo);
	}
};

/**
 * Get a thumbnail IMG for a file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getFileThumb = function (file, appendTo)
{
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
akeeba.Upload.appendThumb = function (url, file, appendTo)
{
	// Add the file to the queue and update the stats
	var uuid                  = akeeba.System.uuid();
	akeeba.Upload.files[uuid] = file;
	akeeba.Upload.totalSize += file.size;
	akeeba.Upload.totalFiles += 1;

	// Create the thumbnail
	var img            = document.createElement("img");
	img.src            = url;
	img.style.maxWidth = "120";
	akeeba.System.data.set(img, "uuid", uuid);
	appendTo.appendChild(img);

	// Finally, update the UI
	akeeba.Upload.updateUI();

	// Remove file on clicking its preview
	akeeba.System.addEventListener(img, "click", function (event) {
		// Remove this file's size from the running total
		var uuid = akeeba.System.data.get(this, "uuid");
		var file = akeeba.Upload.files[uuid];

		akeeba.Upload.totalSize -= file.size;
		akeeba.Upload.totalFiles -= 1;

		delete akeeba.Upload.files[uuid];

		this.parentNode.removeChild(this);

		akeeba.Upload.updateUI();
	});
};

/**
 * Get a thumbnail IMG for an image file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getPhotoThumb = function (file, appendTo)
{
	var fileReader = new FileReader();

	fileReader.onload = function () {
		akeeba.Upload.appendThumb(fileReader.result, file, appendTo);
	};
	fileReader.readAsDataURL(file);
};

/**
 * Get a thumbnail IMG for a video file and append it to the appendTo element
 *
 * @param  {File}    file      The file to process
 * @param  {Element} appendTo  The DIV to append the thumbnail to
 */
akeeba.Upload.getVideoThumb = function (file, appendTo)
{
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
				var img            = document.createElement("img");
				img.src            = image;
				img.style.maxWidth = "120";
				akeeba.System.data.set(img, "file", file);
				appendTo.appendChild(img);
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