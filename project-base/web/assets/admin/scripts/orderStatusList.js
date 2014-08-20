(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.orderStatusList = $.fn.SS6.orderStatusList || {};

	$.fn.SS6.orderStatusList.init = function () {
		$('.js-order-status-list-no-delete').tooltip();
	};

	$(document).ready(function () {
		$.fn.SS6.orderStatusList.init();
	});

})(jQuery);
