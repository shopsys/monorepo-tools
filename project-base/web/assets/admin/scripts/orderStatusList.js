(function ($) {

	SS6 = window.SS6 || {};
	SS6.orderStatusList = SS6.orderStatusList || {};

	SS6.orderStatusList.init = function () {
		$('.js-order-status-list-no-delete').tooltip();
	};

	$(document).ready(function () {
		SS6.orderStatusList.init();
	});

})(jQuery);
