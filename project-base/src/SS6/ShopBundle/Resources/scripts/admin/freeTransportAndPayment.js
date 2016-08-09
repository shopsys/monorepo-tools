(function ($) {

	SS6 = window.SS6 || {};
	SS6.freeTransportAndPayment = SS6.freeTransportAndPayment || {};

	SS6.freeTransportAndPayment.FreeTransportAndPayment = function ($container) {
		var $checkbox = $container.find('.js-free-transport-and-payment-price-limit-enabled');
		var $input = $container.find('.js-free-transport-and-payment-price-limit-input');

		this.init = function() {
			$checkbox.click(updateInputDisabledAttribute);
			updateInputDisabledAttribute();
		};

		function updateInputDisabledAttribute() {
			if ($checkbox.is(':checked')) {
				$input.removeAttr('disabled');
			} else {
				$input.attr('disabled', 'disabled');
			}
		}
	};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-free-transport-and-payment-price-limit').each(function () {
			var freeTransportAndPayment = new SS6.freeTransportAndPayment.FreeTransportAndPayment($(this));
			freeTransportAndPayment.init();
		});
	});

})(jQuery);
