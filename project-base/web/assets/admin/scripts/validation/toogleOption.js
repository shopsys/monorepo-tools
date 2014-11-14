(function ($) {

	SS6 = window.SS6 || {};
	SS6.toggleOption = SS6.toggleOption || {};

	// IE compatible hiding of select's options
	SS6.toggleOption = function(element, show) {
		element.toggle(show);
		if (show) {
			if (element.parent('span.js-toggleOption').length) {
				element.unwrap();
			}
		} else {
			if (element.parent('span.js-toggleOption').length === 0) {
				element.wrap('<span class="js-toggleOption" style="display: none;" />');
			}
		}
	};
})(jQuery);
