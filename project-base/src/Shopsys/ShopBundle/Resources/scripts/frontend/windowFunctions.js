(function ($) {

	SS6 = window.SS6 || {};
	SS6.windowFunctions = SS6.windowFunctions || {};

	SS6.windowFunctions.close = function () {
		$('#js-window').trigger('windowFastClose');
	};

})(jQuery);
