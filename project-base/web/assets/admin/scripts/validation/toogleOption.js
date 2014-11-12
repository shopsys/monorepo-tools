(function ($) {

	SS6 = window.SS6 || {};
	SS6.toggleOption = SS6.toggleOption || {};

	SS6.toggleOption = function(ele, show) {
		ele.toggle(show);
		if(show) {
			if (ele.parent('span.js-toggleOption').length) {
				ele.unwrap();
			}
		} else {
			if (ele.parent('span.js-toggleOption').length == 0) {
				ele.wrap('<span class="js-toggleOption" style="display: none;" />');
			}
		}
	};
})(jQuery);
