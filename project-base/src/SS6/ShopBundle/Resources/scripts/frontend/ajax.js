(function ($) {

	SS6 = window.SS6 || {};

	SS6.ajax = function (options) {
		var loaderOverlayTimeout;
		var defaults = {
			loaderElement: 'body',
			loaderMessage: '',
			overlayDelay: 200,
			error: showDefaultError,
			complete: function () {}
		};
		var options = $.extend(defaults, options);
		var userCompleteCallback = options.complete;
		var $loaderOverlay = getLoaderOverlay(options.loaderMessage, options.loaderElement);
		var userErrorCallback = options.error;

		options.complete = function (jqXHR, textStatus) {
			userCompleteCallback.apply(this, [jqXHR, textStatus]);
			clearTimeout(loaderOverlayTimeout);
			$loaderOverlay.remove();
		};

		options.error = function (jqXHR) {
			// on FireFox abort ajax request, but request was probably successful
			if (jqXHR.status !== 0) {
				userErrorCallback.apply(this, [jqXHR]);
			}
		};

		loaderOverlayTimeout = setTimeout(function () {
			showLoaderOverlay(options.loaderElement, $loaderOverlay);
		}, options.overlayDelay);
		$.ajax(options);
	};

	var getLoaderOverlay = function(loaderMessage, loaderElement) {
		var overlaySpinnerClass = 'js-loader-overlay-spinner';
		if (loaderElement !== 'body') {
			overlaySpinnerClass += '--absolute';
		}

		var $loaderOverlayDiv = $('<div class="js-loader-overlay"></div>');
		var $loaderOverlaySpinnerDiv = $($.parseHTML(
			'<div class="' + overlaySpinnerClass + '">' +
				'<i class="fa fa-spinner fa-lg fa-spin"></i>' +
				loaderMessage +
			'</div>'
		));

		return $loaderOverlayDiv.append($loaderOverlaySpinnerDiv);
	};

	var showLoaderOverlay = function (loaderElement, $loaderOverlay) {
		$(loaderElement)
			.addClass('relative pos-relative')
			.append($loaderOverlay);
	};

	var showDefaultError = function () {
		SS6.window({
			content: SS6.translator.trans('Nastala chyba, zkuste to, prosím, znovu.')
		});
	};

})(jQuery);