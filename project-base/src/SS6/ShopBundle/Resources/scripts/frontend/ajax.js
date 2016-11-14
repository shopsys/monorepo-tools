(function ($) {

	SS6 = window.SS6 || {};

	var ajaxPendingCalls = {};

	SS6.ajax = function (options) {
		var loaderOverlayTimeout;
		var defaults = {
			loaderElement: 'body',
			loaderMessage: '',
			overlayDelay: 200,
			error: showDefaultError,
			complete: function () {}
		};
		options = $.extend(defaults, options);
		var userCompleteCallback = options.complete;
		var $loaderOverlay = SS6.loaderOverlay.getLoaderOverlay(options.loaderMessage, options.loaderElement);
		var userErrorCallback = options.error;

		options.complete = function (jqXHR, textStatus) {
			userCompleteCallback.apply(this, [jqXHR, textStatus]);
			clearTimeout(loaderOverlayTimeout);
			$loaderOverlay.remove();
			$(options.loaderElement).removeClass('in-overlay');
		};

		options.error = function (jqXHR) {
			// on FireFox abort ajax request, but request was probably successful
			if (jqXHR.status !== 0) {
				userErrorCallback.apply(this, [jqXHR]);
			}
		};

		loaderOverlayTimeout = setTimeout(function () {
			SS6.loaderOverlay.showLoaderOverlay(options.loaderElement, $loaderOverlay);
		}, options.overlayDelay);
		$.ajax(options);
	};

	var showDefaultError = function () {
		SS6.window({
			content: SS6.translator.trans('Nastala chyba, zkuste to, prosím, znovu.')
		});
	};

	/**
	 * Calls ajax with provided options. If ajax call with the same name is already running, the new ajax call is created as pending.
	 * After completion of the ajax call only last pending call with the same name is called.
	 * @param {string} pendingCallName
	 * @param {object} options
	 */
	SS6.ajaxPendingCall = function (pendingCallName, options) {
		if (typeof pendingCallName !== 'string') {
			throw 'Ajax queued call must have name!';
		}
		var userCompleteCallback = options.hasOwnProperty('complete') ? options.complete : null;

		options.complete = function (jqXHR, textStatus) {
			if (userCompleteCallback !== null) {
				userCompleteCallback.apply(this, [jqXHR, textStatus]);
			}

			if (ajaxPendingCalls.hasOwnProperty(pendingCallName) === true) {
				if (ajaxPendingCalls[pendingCallName].isPending === true) {
					ajaxPendingCalls[pendingCallName].isPending = false;
					SS6.ajax(ajaxPendingCalls[pendingCallName].options);
				} else {
					delete ajaxPendingCalls[pendingCallName];
				}
			}
		};

		var callImmediately = ajaxPendingCalls.hasOwnProperty(pendingCallName) === false;

		ajaxPendingCalls[pendingCallName] = {
			isPending: true,
			options: options
		};

		if (callImmediately) {
			ajaxPendingCalls[pendingCallName].isPending = false;
			SS6.ajax(options);
		}
	};

})(jQuery);