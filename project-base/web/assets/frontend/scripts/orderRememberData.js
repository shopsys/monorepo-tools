(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.orderRememberData = $.fn.SS6.orderRememberData || {};
	
	$.fn.SS6.orderRememberData.delayedSaveDataTimer = null;
	$.fn.SS6.orderRememberData.delayedSaveDataDelay = 200;
	
	$.fn.SS6.orderRememberData.init = function () {
		$('#js-order-form input, #js-order-form select, #js-order-form textarea')
			.bind('change.orderRememberData', $.fn.SS6.orderRememberData.saveData);
			
		$('#js-order-form input, #js-order-form textarea')
			.bind('keyup.orderRememberData', $.fn.SS6.orderRememberData.delayedSaveData);
	};
	
	$.fn.SS6.orderRememberData.delayedSaveData = function() {
		var $this = $(this);
		clearTimeout($.fn.SS6.orderRememberData.delayedSaveDataTimer);
		$.fn.SS6.orderRememberData.delayedSaveDataTimer = setTimeout(function () {
			$this.trigger('change.orderRememberData');
		}, $.fn.SS6.orderRememberData.delayedSaveDataDelay);
	};
	
	$.fn.SS6.orderRememberData.saveData = function(event) {
		clearTimeout($.fn.SS6.orderRememberData.delayedSaveDataTimer);
		$.ajax({
			type: "POST",
			url: $('#js-order-form').data('ajax-save-url'),
			data: $('#js-order-form').serialize()
		});
	};
	
	$(document).ready(function () {
		$.fn.SS6.orderRememberData.init();
	});
	
})(jQuery);
