(function ($){
	$(document).ready(function () {

		$('.js-free-transport-price-limit').each(function () {
			var $priceLimitForm = $(this);
			$priceLimitForm.jsFormValidator({
				'groups': function () {

					var groups = ['Default'];
					if ($priceLimitForm.find('.js-free-transport-price-limit-enabled').is(':checked')) {
						groups.push(SS6.constant('\\SS6\\ShopBundle\\Form\\Admin\\Transport\\FreeTransportPriceLimitsFormType::VALIDATION_GROUP_PRICE_LIMIT_ENABLED'));
					}

					return groups;
				}
			});
		});

	});
})(jQuery);
