/**
 * @package     Solo
 * @copyright   Copyright (c)2014-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

if (typeof akeeba === 'undefined')
{
	var akeeba = {};
}

if (typeof akeeba.System === 'undefined')
{
	akeeba.System              = {};
	akeeba.System.params       = {
		AjaxURL:               '',
		errorCallback:         akeeba.System.modalErrorHandler,
		password:              '',
		errorDialogId:         'errorDialog',
		errorDialogMessageId:  'errorDialogPre'
	};
	akeeba.System.translations = [];
	akeeba.System.modalDialog  = null;
}

/**
 * Submit a form, setting a specific task
 *
 * @param   formId  The ID of the form to submit
 * @param   task    (optional) The task to set
 */
akeeba.System.submitForm = function (formId, task)
{
	if (!(typeof(task) === 'undefined'))
	{
		document.querySelector('#' + formId + ' input[name=\'task\']')
			.setAttribute('value', task);
	}

	document.querySelector('#' + formId).submit();
};

/**
 * An extremely simple error handler, dumping error messages to screen
 *
 * @param  error  The error message string
 */
akeeba.System.defaultErrorHandler = function (error)
{
	if ((error == null) || (typeof error == 'undefined'))
	{
		return;
	}

	alert("An error has occurred\n" + error);
};

/**
 * An error handler based on Bootstrap modal. It requires you to set up a modal dialog div with id errorDialog
 *
 * @param  error  The error message string
 */
akeeba.System.modalErrorHandler = function (error)
{
	var dialog_element =
			document.getElementById(akeeba.System.params.errorDialogId);

	var errorContent = error;

	if (dialog_element != null)
	{
		document.getElementById(akeeba.System.params.errorDialogMessageId).innerHTML = error;

		errorContent = dialog_element.innerHTML;
	}

	akeeba.Modal.open({
		content: errorContent,
		width:   '80%'
	});
};

akeeba.System.params.errorCallback = akeeba.System.modalErrorHandler;

/**
 * Performs an AJAX request and returns the parsed JSON output.
 * akeeba.System.params.AjaxURL is used as the AJAX proxy URL.
 * If there is no errorCallback, the global akeeba.System.params.errorCallback is used.
 *
 * @param  data             An object with the query data, e.g. a serialized form
 * @param  successCallback  A function accepting a single object parameter, called on success
 * @param  errorCallback    A function accepting a single string parameter, called on failure
 * @param  useCaching       Should we use the cache?
 * @param  timeout          Timeout before cancelling the request (default 60s)
 */
akeeba.System.doAjax = function (data, successCallback, errorCallback, useCaching, timeout)
{
	if (useCaching == null)
	{
		useCaching = true;
	}

	// We always want to burst the cache
	var now                = new Date().getTime() / 1000;
	var s                  = parseInt(now, 10);
	data._cacheBustingJunk = Math.round((now - s) * 1000) / 1000;

	if (timeout == null)
	{
		timeout = 600000;
	}

	var structure =
			{
				type:    "POST",
				url:     akeeba.System.params.AjaxURL,
				cache:   false,
				data:    data,
				timeout: timeout,
				success: function (msg)
						 {
							 // Initialize
							 var junk    = null;
							 var message = "";

							 // Get rid of junk before the data
							 var valid_pos = msg.indexOf('###');

							 if (valid_pos == -1)
							 {
								 // Valid data not found in the response
								 msg = akeeba.System.sanitizeErrorMessage(msg);
								 msg = 'Invalid AJAX data: ' + msg;

								 if (errorCallback == null)
								 {
									 if (akeeba.System.params.errorCallback != null)
									 {
										 akeeba.System.params.errorCallback(msg);
									 }
								 }
								 else
								 {
									 errorCallback(msg);
								 }

								 return;
							 }
							 else if (valid_pos != 0)
							 {
								 // Data is prefixed with junk
								 junk    = msg.substr(0, valid_pos);
								 message = msg.substr(valid_pos);
							 }
							 else
							 {
								 message = msg;
							 }

							 message = message.substr(3); // Remove triple hash in the beginning

							 // Get of rid of junk after the data
							 valid_pos = message.lastIndexOf('###');
							 message   = message.substr(0, valid_pos); // Remove triple hash in the end

							 try
							 {
								 var data = JSON.parse(message);
							 }
							 catch (err)
							 {
								 message = akeeba.System.sanitizeErrorMessage(message);
								 msg     = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";

								 if (errorCallback == null)
								 {
									 if (akeeba.System.params.errorCallback != null)
									 {
										 akeeba.System.params.errorCallback(msg);
									 }
								 }
								 else
								 {
									 errorCallback(msg);
								 }

								 return;
							 }

							 // Call the callback function
							 successCallback(data);
						 },
				error:   function (Request, textStatus, errorThrown)
						 {
							 var text    = Request.responseText ? Request.responseText : '';
							 var message = '<strong>AJAX Loading Error</strong><br/>HTTP Status: ' + Request.status +
								 ' (' + Request.statusText + ')<br/>';

							 message = message + 'Internal status: ' + textStatus + '<br/>';
							 message = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
							 message = message + 'Raw server response:<br/>' + akeeba.System.sanitizeErrorMessage(text);

							 if (errorCallback == null)
							 {
								 if (akeeba.System.params.errorCallback != null)
								 {
									 akeeba.System.params.errorCallback(message);
								 }
							 }
							 else
							 {
								 errorCallback(message);
							 }
						 }
			};

	if (useCaching)
	{
		akeeba.Ajax.enqueue(structure);
	}
	else
	{
		akeeba.Ajax.ajax(structure);
	}
};

/**
 * Sanitize a message before displaying it in an error dialog. Some servers return an HTML page with DOM modifying
 * JavaScript when they block the backup script for any reason (usually with a 5xx HTTP error code). Displaying the
 * raw response in the error dialog has the side-effect of killing our backup resumption JavaScript or even completely
 * destroy the page, making backup restart impossible.
 *
 * @param {string} msg The message to sanitize
 *
 * @returns {string}
 */
akeeba.System.sanitizeErrorMessage = function (msg)
{
	/**
	if (msg.indexOf("<script") > -1)
	{
		msg = "(HTML containing script tags)";
	}
	/**/

	return msg;
};


/**
 * Performs an AJAX request to the restoration script (restore.php).
 *
 * @param  data             An object with the query data, e.g. a serialized form
 * @param  successCallback  A function accepting a single object parameter, called on success
 * @param  errorCallback    A function accepting a single string parameter, called on failure
 * @param  useCaching       Should we use the cache?
 * @param  timeout          Timeout before cancelling the request (default 60s)
 *
 * @return
 */
akeeba.System.doEncryptedAjax = function (data, successCallback, errorCallback, useCaching, timeout)
{
	if (useCaching == null)
	{
		useCaching = true;
	}

	// We always want to burst the cache
	var now                = new Date().getTime() / 1000;
	var s                  = parseInt(now, 10);
	data._cacheBustingJunk = Math.round((now - s) * 1000) / 1000;

	if (timeout == null)
	{
		timeout = 600000;
	}

	var json = JSON.stringify(data);
	var post_data = {json: json};

    if (akeeba.System.params.password.length > 0)
    {
        post_data.password = akeeba.System.params.password;
    }

	var structure =
			{
				type:    "POST",
				url:     akeeba.System.params.AjaxURL,
				cache:   false,
				data:    post_data,
				timeout: timeout,
				success: function (msg)
						 {
							 // Initialize
							 var junk    = null;
							 var message = "";

							 // Get rid of junk before the data
							 var valid_pos = msg.indexOf('###');

							 if (valid_pos == -1)
							 {
								 // Valid data not found in the response
								 msg = 'Invalid AJAX data: ' + msg;

								 if (errorCallback == null)
								 {
									 if (akeeba.System.params.errorCallback != null)
									 {
										 akeeba.System.params.errorCallback(msg);
									 }
								 }
								 else
								 {
									 errorCallback(msg);
								 }

								 return;
							 }
							 else if (valid_pos != 0)
							 {
								 // Data is prefixed with junk
								 junk    = msg.substr(0, valid_pos);
								 message = msg.substr(valid_pos);
							 }
							 else
							 {
								 message = msg;
							 }

							 message = message.substr(3); // Remove triple hash in the beginning

							 // Get of rid of junk after the data
							 valid_pos = message.lastIndexOf('###');
							 message   = message.substr(0, valid_pos); // Remove triple hash in the end

							 try
							 {
								 var data = JSON.parse(message);
							 }
							 catch (err)
							 {
								 errorMessage = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";

								 if (errorCallback == null)
								 {
									 if (akeeba.System.params.errorCallback != null)
									 {
										 akeeba.System.params.errorCallback(errorMessage);
									 }
								 }
								 else
								 {
									 errorCallback(errorMessage);
								 }
								 return;
							 }

							 // Call the callback function
							 successCallback(data);
						 },
				error:   function (Request, textStatus, errorThrown)
						 {
							 var message = 'AJAX Loading Error: ' + textStatus;

							 if (errorCallback == null)
							 {
								 if (akeeba.System.params.errorCallback != null)
								 {
									 akeeba.System.params.errorCallback(message);
								 }
							 }
							 else
							 {
								 errorCallback(message);
							 }
						 }
			};

	if (useCaching)
	{
		akeeba.Ajax.enqueue(structure);
	}
	else
	{
        akeeba.Ajax.ajax(structure);
	}
};

/**
 * Toggles the check state of a group of boxes
 *
 * Checkboxes must have an id attribute in the form cb0, cb1...
 *
 * Based on Joomla.checkAll of Joomla! 3.
 *
 * @param    checkbox  The number of box to 'check', for a checkbox element
 * @param    stub      An alternative field name
 */
akeeba.System.checkAll = function (checkbox, stub)
{
	if (!stub)
	{
		stub = 'cb';
	}

	if (checkbox.form)
	{
		var c = 0;

		for (var i = 0, n = checkbox.form.elements.length; i < n; i++)
		{
			var e = checkbox.form.elements[i];

			if (e.type == checkbox.type)
			{
				if ((stub && e.id.indexOf(stub) == 0) || !stub)
				{
					e.checked = checkbox.checked;
					c += (e.checked == true ? 1 : 0);
				}
			}
		}

		if (checkbox.form.boxchecked)
		{
			checkbox.form.boxchecked.value = c;
		}

		return true;
	}

	return false;
};

/**
 * Update the boxchecked field of a form when a checkbox is checked. Based on Joomla.isChecked of Joomla! 3.
 *
 * @param   isItChecked  The checkbox
 * @param   form         The form to update
 *
 * @return  void
 */
akeeba.System.isChecked = function (isItChecked, form)
{
	if (typeof(form) === 'undefined')
	{
		form = document.getElementById('adminForm');
	}

	if (isItChecked == true)
	{
		form.boxchecked.value++;
	}
	else
	{
		form.boxchecked.value--;
	}
};

/**
 * Applies the ordering preference on administration tables. Based on Joomla.tableOrdering of Joomla! 3.
 *
 * @param   order  string Which field to order by
 * @param   dir    string The direction (ASC or DESC)
 * @param   task   string The task to apply
 * @param   form   Element The form to use
 */
akeeba.System.tableOrdering = function (order, dir, task, form)
{
	if (typeof(form) === 'undefined')
	{
		form = document.getElementById('adminForm');
	}

	form.filter_order.value     = order;
	form.filter_order_Dir.value = dir;

	akeeba.System.submitForm(form.getAttribute("id"), task)
};

/**
 * Creates a modal box based on the data object. The keys to this object are:
 * - title          The title of the modal dialog, skip to not create a title
 * - body           The body content of the dialog. Not applicable if href is defined
 * - href           A URL to open in an IFrame inside the body
 * - iFrameHeight   The height of the IFrame, applicable if href is set
 * - iFrameWidth    The width of the IFrame, applicable if href is set
 * - OkLabel        The label of the OK (primary) button
 * - CancelLabel    The label of the Cancel button
 * - OkHandler      Run this when the OK button is pressed, before closing the modal
 * - CancelHandler  Run this when the Cancel button is pressed, after closing the modal
 * - showButtons    Set to false to not show the buttons
 *
 * Alternatively you can pass a reference to an element. In this case we expect that the rel attribute of the element
 * contains a JSON-encoded string of the data object.
 *
 * @param   data  The configuration data (see above)
 */
akeeba.System.modal = function (data)
{
	try
	{
		if (typeof(data.rel) !== 'undefined')
		{
			rel  = data.rel;
			data = JSON.parse(rel);
		}
	}
	catch (e)
	{
	}

	// Outer modal markup
	var modalWrapper = document.createElement('div');
	modalBody.setAttribute('tabindex', '-1');
	modalBody.setAttribute('role', 'dialog');
	modalBody.setAttribute('aria-hidden', 'true');

	var modalDialog = document.createElement('div');
	modalDialog.className = 'modal-dialog';
	modalWrapper.appendChild(modalDialog);

	var modalContent = document.createElement('div');
	modalContent.className = 'modal-content';
	modalDialog.appendChild(modalContent);

	// Modal Header
	if (typeof(data.title) !== 'undefined')
	{
		var modalHeader = document.createElement('div');
		modalHeader.className = 'modal-header';
		modalContent.appendChild(modalHeader);

		var headerCloseButton = document.createElement('button');
		headerCloseButton.className = 'close';
		headerCloseButton.innerHTML = '&times;';
		modalHeader.appendChild(headerCloseButton);

		var modalHeaderTitle = document.createElement('h4');
		modalHeaderTitle.className = 'modal-title';
		modalHeaderTitle.innerHTML = data.title;
		modalHeader.appendChild(modalHeaderTitle);

		// Assign events
		if (typeof(data.CancelHandler) !== 'undefined')
		{
			akeeba.System.addEventListener(headerCloseButton, 'click', function (e)
			{
				var callback = data.CancelHandler;

				akeeba.System.modalDialog.close();

				callback(modalWrapper);
				e.preventDefault();
			});
		}
	}

	// Modal body
	var modalBody = document.createElement('div');
	modalBody.className = 'modal-body';
	modalContent.appendChild(modalBody);

	if (typeof(data.href) === 'undefined')
	{
		// HTML body
		modalBody.innerHTML = data.body;
	}
	else if (data.href.substr(0, 1) == '#')
	{
		// Inherited content
		var inheritedElement = window.document.querySelector(data.href);

		while (inheritedElement.childNodes.length > 0)
		{
			modalBody.appendChild(inheritedElement.childNodes[0]);
		}
	}
	else
	{
		// IFrame

		var iFrame               = document.createElement('iframe');
		iFrame.src               = data.href;
		iFrame.width             = '100%';
		iFrame.height            = 400;
		iFrame.frameborder       = 0;
		iFrame.allowtransparency = true;
		modalBody.appendChild(iFrame);

		if (typeof(data.iFrameHeight) !== 'undefined')
		{
			iFrame.height = data.iFrameHeight;
		}

		if (typeof(data.iFrameWidth) !== 'undefined')
		{
			iFrame.width = data.iFrameWidth;
		}
	}

	// Should I show the buttons?
	var showButtons = true;

	if (typeof(data.showButtons) !== 'undefined')
	{
		showButtons = data.showButtons;
	}

	// Modal buttons
	if (showButtons)
	{
		// Create the modal footer
		var modalFooter       = document.createElement('div');
		modalFooter.className = 'modal-footer';
		modalContent.appendChild(modalFooter);

		// Get the button labels
		var okLabel     = akeeba.System.translations['UI-MODAL-OK'];
		var cancelLabel = akeeba.System.translations['UI-MODAL-CANCEL'];

		if (typeof(data.OkLabel) !== 'undefined')
		{
			okLabel = data.OkLabel;
		}

		if (typeof(data.CancelLabel) !== 'undefined')
		{
			cancelLabel = data.CancelLabel;
		}

		// Create buttons
		var cancelButton = document.createElement('button');
		cancelButton.className = 'btn btn-default';
		cancelButton.setAttribute('type', 'button');
		cancelButton.innerHTML = cancelLabel;
		modalFooter.appendChild(cancelLabel);

		var okButton = document.createElement('button');
		okButton.className = 'btn btn-primary';
		okButton.setAttribute('type', 'button');
		okButton.innerHTML = okLabel;
		modalFooter.appendChild(okButton);

		// Assign handlers
		if (typeof(data.CancelHandler) !== 'undefined')
		{
			akeeba.System.addEventListener(cancelButton, 'click', function (e)
			{
				var callback = data.CancelHandler;
				akeeba.System.modalDialog.close();
				callback(modalWrapper);
				e.preventDefault();
			});
		}

		if (typeof(data.OkHandler) !== 'undefined')
		{
			akeeba.System.addEventListener(okButton, 'click', function (e)
			{
				var callback = data.OkHandler;
				akeeba.System.modalDialog.close();
				callback(modalWrapper);
				e.preventDefault();
			});
		}
		else
		{
			akeeba.System.addEventListener(okButton, 'click', function (e)
			{
				akeeba.System.modalDialog.close();
				e.preventDefault();
			});
		}

		// Hide unnecessary buttons
		if (okLabel.trim() == '')
		{
			okButton.style.display = 'none';
		}

		if (cancelLabel.trim() == '')
		{
			cancelButton.style.display = 'none';
		}
	}

	// Show modal
	akeeba.System.modalDialog = akeeba.Modal.open({
		inherit: modalWrapper,
		width:   '450',
		height:  '280'
	});
};

// =============================================================================
//
// =============================================================================

/**
 * Get and set data to elements. Use:
 * akeeba.System.data.set(element, property, value)
 * akeeba.System.data.get(element, property, defaultValue)
 *
 * On modern browsers (minimum IE 11, Chrome 8, FF 6, Opera 11, Safari 6) this will use the data-* attributes of the
 * elements where possible. On old browsers it will use an internal cache and manually apply data-* attributes.
 */
akeeba.System.data = (function ()
{
	var lastId = 0,
		store  = {};

	return {
		set: function (element, property, value)
			 {
				 // IE 11, modern browsers
				 if (element.dataset)
				 {
					 element.dataset[property] = value;

					 if (value == null)
					 {
						 delete element.dataset[property];
					 }

					 return;
				 }

				 // IE 8 to 10, old browsers
				 var id;

				 if (element.myCustomDataTag === undefined)
				 {
					 id                      = lastId++;
					 element.myCustomDataTag = id;
				 }

				 if (typeof(store[id]) == 'undefined')
				 {
					 store[id] = {};
				 }

				 // Store the value in the internal cache...
				 store[id][property] = value;

				 // ...and the DOM

				 // Convert the property to dash-format
				 var dataAttributeName = 'data-' + property.split(/(?=[A-Z])/).join('-').toLowerCase();

				 if (element.setAttribute)
				 {
					 element.setAttribute(dataAttributeName, value);
				 }

				 if (value == null)
				 {
					 // IE 8 throws an exception on "delete"
					 try
					 {
						 delete store[id][property];
						 element.removeAttribute(dataAttributeName);
					 }
					 catch (e)
					 {
						 store[id][property] = null;
					 }
				 }
			 },

		get: function (element, property, defaultValue)
			 {
				 // IE 11, modern browsers
				 if (element.dataset)
				 {
					 if (typeof(element.dataset[property]) == 'undefined')
					 {
						 element.dataset[property] = defaultValue;
					 }

					 return element.dataset[property];
				 }
				 // IE 8 to 10, old browsers

				 if (typeof(defaultValue) == 'undefined')
				 {
					 defaultValue = null;
				 }

				 // Make sure we have an internal storage
				 if (typeof(store[element.myCustomDataTag]) == 'undefined')
				 {
					 store[element.myCustomDataTag] = {};
				 }

				 // Convert the property to dash-format
				 var dataAttributeName = 'data-' + property.split(/(?=[A-Z])/).join('-').toLowerCase();

				 // data-* attributes have precedence
				 if (typeof(element[dataAttributeName]) !== 'undefined')
				 {
					 store[element.myCustomDataTag][property] = element[dataAttributeName];
				 }

				 // No data-* attribute and no stored value? Use the default.
				 if (typeof(store[element.myCustomDataTag][property]) == 'undefined')
				 {
					 this.set(element, property, defaultValue);
				 }

				 // Return the value of the data
				 return store[element.myCustomDataTag][property];
			 }
	};
}());

/**
 * Adds an event listener to an element
 *
 * @param element
 * @param eventName
 * @param listener
 */
akeeba.System.addEventListener = function (element, eventName, listener)
{
	// Allow the passing of an element ID string instead of the DOM elem
	if (typeof element === "string")
	{
		element = document.getElementById(element);
	}

	if (element == null)
	{
		return;
	}

	if (typeof element != 'object')
	{
		return;
	}

	if (!(element instanceof Element))
	{
		return;
	}

	// Handles the listener in a way that returning boolean false will cancel the event propagation
	function listenHandler(e)
	{
		var ret = listener.apply(this, arguments);

		if (ret === false)
		{
			if (e.stopPropagation())
			{
				e.stopPropagation();
			}

			if (e.preventDefault)
			{
				e.preventDefault();
			}
			else
			{
				e.returnValue = false;
			}
		}

		return (ret);
	}

	// Equivalent of listenHandler for IE8
	function attachHandler()
	{
		// Normalize the target of the event –– PhpStorm detects this as an error
		// window.event.target = window.event.srcElement;

		var ret = listener.call(element, window.event);

		if (ret === false)
		{
			window.event.returnValue  = false;
			window.event.cancelBubble = true;
		}

		return (ret);
	}

	if (element.addEventListener)
	{
		element.addEventListener(eventName, listenHandler, false);

		return;
	}

	element.attachEvent("on" + eventName, attachHandler);
};

/**
 * Remove an event listener from an element
 *
 * @param element
 * @param eventName
 * @param listener
 */
akeeba.System.removeEventListener = function (element, eventName, listener)
{
	// Allow the passing of an element ID string instead of the DOM elem
	if (typeof element === "string")
	{
		element = document.getElementById(element);
	}

	if (element == null)
	{
		return;
	}

	if (typeof element != 'object')
	{
		return;
	}

	if (!(element instanceof Element))
	{
		return;
	}

	if (element.removeEventListener)
	{
		element.removeEventListener(eventName, listener);

		return;
	}

	element.detachEvent("on" + eventName, listener);
};

akeeba.System.triggerEvent = function (element, eventName)
{
    if (typeof element == 'undefined') {
        return;
    }

    if (element === null) {
        return;
    }

    // Allow the passing of an element ID string instead of the DOM elem
    if (typeof element === "string") {
        element = document.getElementById(element);
    }

    if (element === null) {
        return;
    }

    if (typeof element != 'object') {
        return;
    }

    if (!(element instanceof Element)) {
        return;
    }

    // Use jQuery and be done with it!
    if (typeof window.jQuery === 'function')
    {
		window.jQuery(element).trigger(eventName);

		return;
    }

    // Internet Explorer way
    if (document.fireEvent && (typeof window.Event == 'undefined')) {
        element.fireEvent('on' + eventName);

        return;
    }

    // This works on Chrome and Edge but not on Firefox. Ugh.
    var event = null;

    event = document.createEvent("Event");
    event.initEvent(eventName, true, true);
    element.dispatchEvent(event);
};

// document.ready equivalent from https://github.com/jfriend00/docReady/blob/master/docready.js
(function (funcName, baseObj)
{
	funcName = funcName || "documentReady";
	baseObj  = baseObj || akeeba.System;

	var readyList                   = [];
	var readyFired                  = false;
	var readyEventHandlersInstalled = false;

	// Call this when the document is ready. This function protects itself against being called more than once.
	function ready()
	{
		if (!readyFired)
		{
			// This must be set to true before we start calling callbacks
			readyFired = true;

			for (var i = 0; i < readyList.length; i++)
			{
				/**
				 * If a callback here happens to add new ready handlers, this function will see that it already
				 * fired and will schedule the callback to run right after this event loop finishes so all handlers
				 * will still execute in order and no new ones will be added to the readyList while we are
				 * processing the list.
				 */
				readyList[i].fn.call(window, readyList[i].ctx);
			}

			// Allow any closures held by these functions to free
			readyList = [];
		}
	}

	/**
	 * Solely for the benefit of Internet Explorer
	 */
	function readyStateChange()
	{
		if (document.readyState === "complete")
		{
			ready();
		}
	}

	/**
	 * This is the one public interface:
	 *
	 * akeeba.System.documentReady(fn, context);
	 *
	 * @param   callback   The callback function to execute when the document is ready.
	 * @param   context    Optional. If present, it will be passed as an argument to the callback.
	 */
	//
	//
	//
	baseObj[funcName] = function (callback, context)
	{
		// If ready() has already fired, then just schedule the callback to fire asynchronously
		if (readyFired)
		{
			setTimeout(function ()
			{
				callback(context);
			}, 1);

			return;
		}

		// Add the function and context to the queue
		readyList.push({fn: callback, ctx: context});

		/**
		 * If the document is already ready, schedule the ready() function to run immediately.
		 *
		 * Note: IE is only safe when the readyState is "complete", other browsers are safe when the readyState is
		 * "interactive"
		 */
		if (document.readyState === "complete" || (!document.attachEvent && document.readyState === "interactive"))
		{
			setTimeout(ready, 1);

			return;
		}

		// If the handlers are already installed just quit
		if (readyEventHandlersInstalled)
		{
			return;
		}

		// We don't have event handlers installed, install them
		readyEventHandlersInstalled = true;

		// -- We have an addEventListener method in the document, this is a modern browser.

		if (document.addEventListener)
		{
			// Prefer using the DOMContentLoaded event
			document.addEventListener("DOMContentLoaded", ready, false);

			// Our backup is the window's "load" event
			window.addEventListener("load", ready, false);

			return;
		}

		// -- Most likely we're stuck with an ancient version of IE

		// Our primary method of activation is the onreadystatechange event
		document.attachEvent("onreadystatechange", readyStateChange);

		// Our backup is the windows's "load" event
		window.attachEvent("onload", ready);
	}
})("documentReady", akeeba.System);

akeeba.System.addClass = function (element, newClasses)
{
	if (!element || !element.className)
	{
		return;
	}

	var currentClasses = element.className.split(' ');

	if ((typeof newClasses) == 'string')
	{
		newClasses = newClasses.split(' ');
	}

	currentClasses = array_merge(currentClasses, newClasses);

	element.className = '';

	for (var property in currentClasses)
	{
		if (currentClasses.hasOwnProperty(property))
		{
			element.className += currentClasses[property] + ' ';
		}
	}

	if (element.className.trim)
	{
		element.className = element.className.trim();
	}
};

akeeba.System.removeClass = function (element, oldClasses)
{
	if (!element || !element.className)
	{
		return;
	}

	var currentClasses = element.className.split(' ');

	if ((typeof oldClasses) == 'string')
	{
		oldClasses = oldClasses.split(' ');
	}

	currentClasses = array_diff(currentClasses, oldClasses);

	element.className = '';

	for (property in currentClasses)
	{
		if (currentClasses.hasOwnProperty(property))
		{
			element.className += currentClasses[property] + ' ';
		}
	}

	if (element.className.trim)
	{
		element.className = element.className.trim();
	}
};

akeeba.System.hasClass = function (element, aClass)
{
	if (!element || !element.className)
	{
		return;
	}

	var currentClasses = element.className.split(' ');

	for (i = 0; i < currentClasses.length; i++)
	{
		if (currentClasses[i] == aClass)
		{
			return true;
		}
	}

	return false;
};

akeeba.System.toggleClass = function (element, aClass)
{
	if (akeeba.System.hasClass(element, aClass))
	{
		akeeba.System.removeClass(element, aClass);

		return;
	}

	akeeba.System.addClass(element, aClass);
};

/*!
 Math.uuid.js (v1.4)
 http://www.broofa.com
 mailto:robert@broofa.com

 Copyright (c) 2009 Robert Kieffer
 Dual licensed under the MIT and GPL licenses.

 Usage: Math.uuid()
 */
akeeba.System.uuid = (function ()
{
	// Private array of chars to use
	var CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');

	return function (len, radix)
	{
		var chars = CHARS, uuid = [];
		radix = radix || chars.length;

		if (len)
		{
			// Compact form
			for (var i = 0; i < len; i++)
			{
				uuid[i] = chars[0 | Math.random() * radix];
			}
		}
		else
		{
			// rfc4122, version 4 form
			var r;

			// rfc4122 requires these characters
			uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
			uuid[14] = '4';

			// Fill in random data.  At i==19 set the high bits of clock sequence as
			// per rfc4122, sec. 4.1.5
			for (var i = 0; i < 36; i++)
			{
				if (!uuid[i])
				{
					r = 0 | Math.random() * 16;
					uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
				}
			}
		}

		return uuid.join('');
	};
})();

/**
 * Checks if a varriable is empty. From the php.js library.
 */
function empty(mixed_var)
{
	var key;

	if (mixed_var === "" ||
		mixed_var === 0 ||
		mixed_var === "0" ||
		mixed_var === null ||
		mixed_var === false ||
		typeof mixed_var === 'undefined'
	)
	{
		return true;
	}

	if (typeof mixed_var == 'object')
	{
		for (key in mixed_var)
		{
			return false;
		}
		return true;
	}

	return false;
}

/**
 * Submit a form, setting a specific task
 *
 * @param   formId  The ID of the form to submit
 * @param   task    (optional) The task to set
 */
akeeba.System.submitForm = function (formId, task)
{
	if (!(typeof(task) === 'undefined'))
	{
		document.querySelector('#' + formId + ' input[name=\'task\']')
			.setAttribute('value', task);
	}

	document.querySelector('#' + formId).submit();
};

akeeba.System.modalWhatIsThis = function (error)
{
	var dialog_element = document.getElementById('WhatIsThismodal');

	akeeba.Modal.open({
		content: dialog_element.innerHTML,
		width:   '80%'
	});
};
