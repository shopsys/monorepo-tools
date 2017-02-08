(function ($){
	$(document).ready(function () {

		var $orderForm = $('form[name="order_form"]');
		$orderForm.jsFormValidator({
			'groups': function () {

				var groups = [SS6.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
				if (!$orderForm.find('#order_form_deliveryAddressSameAsBillingAddress').is(':checked')) {
					groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Order\\OrderFormType::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS'));
				}

				return groups;
			}
		});

	});
})(jQuery);
