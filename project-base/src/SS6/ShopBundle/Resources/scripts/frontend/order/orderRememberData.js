(function ($) {

	SS6 = window.SS6 || {};
	SS6.orderRememberData = SS6.orderRememberData || {};

	SS6.orderRememberData.delayedSaveDataTimer = null;
	SS6.orderRememberData.delayedSaveDataDelay = 200;

	SS6.orderRememberData.init = function ($container) {
		$container.filterAllNodes('#js-order-form input, #js-order-form select, #js-order-form textarea')
			.bind('change.orderRememberData', SS6.orderRememberData.saveData);

		$container.filterAllNodes('#js-order-form input, #js-order-form textarea')
			.bind('keyup.orderRememberData', SS6.orderRememberData.delayedSaveData);
	};

	SS6.orderRememberData.delayedSaveData = function() {
		var $this = $(this);
		clearTimeout(SS6.orderRememberData.delayedSaveDataTimer);
		SS6.orderRememberData.delayedSaveDataTimer = setTimeout(function () {
			$this.trigger('change.orderRememberData');
		}, SS6.orderRememberData.delayedSaveDataDelay);
	};

	SS6.orderRememberData.saveData = function() {
		clearTimeout(SS6.orderRememberData.delayedSaveDataTimer);
		var $orderForm = $('#js-order-form');
		SS6.ajaxClearAndEnqueue('SS6.orderRememberData.saveData', {
			type: "POST",
			url: $orderForm.data('ajax-save-url'),
			data: $orderForm.serialize(),
			loaderElement: null
		});
	};

	SS6.register.registerCallback(SS6.orderRememberData.init);

})(jQuery);
