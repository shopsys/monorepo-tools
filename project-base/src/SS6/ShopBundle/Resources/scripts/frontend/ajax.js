(function ($) {

	SS6 = window.SS6 || {};

	SS6.ajax = function (options) {
		var loaderOverlayTimeout;
		var defaults = {
			loaderElement: 'body',
			loaderMessage: '',
			error: showDefaultError,
			complete: function () {}
		};
		var options = $.extend(defaults, options);
		var userCompleteCallback = options.complete;
		var $loaderOverlay = getLoaderOverlay(options.loaderMessage);

		options.complete = function (jqXHR, textStatus) {
			userCompleteCallback.apply(this, [jqXHR, textStatus]);
			clearTimeout(loaderOverlayTimeout);
			$loaderOverlay.remove();
		};

		loaderOverlayTimeout = setTimeout(function () {
			showLoaderOverlay(options.loaderElement, $loaderOverlay);
		}, 200);
		$.ajax(options);
	};

	var getLoaderOverlay = function(loaderMessage) {
		return $($.parseHTML(
			'<div class="js-loader-overlay">' +
				'<div class="js-loader-overlay-spinner">' +
					'<i class="fa fa-spinner fa-spin"></i>' +
					loaderMessage +
				'</div>' +
			'</div>'
		));
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