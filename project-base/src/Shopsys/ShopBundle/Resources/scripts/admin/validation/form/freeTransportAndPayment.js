(function ($){
	$(document).ready(function () {

		$('.js-free-transport-and-payment-price-limit').each(function () {
			var $priceLimitForm = $(this);
			$priceLimitForm.jsFormValidator({
				'groups': function () {

					var groups = [SS6.constant('\\SS6\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
					if ($priceLimitForm.find('.js-free-transport-and-payment-price-limit-enabled').is(':checked')) {
						groups.push(SS6.constant('\\SS6\\ShopBundle\\Form\\Admin\\TransportAndPayment\\FreeTransportAndPaymentPriceLimitsFormType::VALIDATION_GROUP_PRICE_LIMIT_ENABLED'));
					}

					return groups;
				}
			});
		});

	});
})(jQuery);
