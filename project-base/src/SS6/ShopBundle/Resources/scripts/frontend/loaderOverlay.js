(function ($) {

	SS6 = window.SS6 || {};
	SS6.loaderOverlay = SS6.loaderOverlay || {};

	SS6.loaderOverlay.getLoaderOverlay = function(loaderMessage, loaderElement) {
		var $loaderOverlay = $($.parseHTML(
			'<div class="in-overlay__in">' +
				'<div class="in-overlay__spinner">' +
					'<span class="in-overlay__spinner__icon"></span>' +
					'<span class="in-overlay__spinner__message">' + loaderMessage + '</span>' +
				'</div>' +
			'</div>'));

		if (loaderElement !== 'body') {
			$loaderOverlay.addClass('in-overlay__in--absolute');
			$loaderOverlay.find('.in-overlay__spinner').addClass('in-overlay__spinner--absolute');
		}

		return $loaderOverlay;
	};

	SS6.loaderOverlay.showLoaderOverlay = function (loaderElement, $loaderOverlay) {
		$(loaderElement)
			.addClass('in-overlay')
			.append($loaderOverlay);
	};

})(jQuery);