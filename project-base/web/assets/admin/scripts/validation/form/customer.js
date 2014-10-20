(function ($){
	$(document).ready(function () {
		var $customerDeliveryAddressForm = $('#customer_deliveryAddressData');
		$customerDeliveryAddressForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];
				if ($customerDeliveryAddressForm.find('#customer_deliveryAddressData_addressFilled').is(':checked')) {
					groups.push('differentDeliveryAddress');
				}

				return groups;
			}
		});
		var $customerBillingAddressForm = $('#customer_billingAddressData');
		$customerBillingAddressForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];
				if ($customerBillingAddressForm.find('#customer_billingAddressData_companyCustomer').is(':checked')) {
					groups.push('companyCustomer');
				}

				return groups;
			}
		});

	});
})(jQuery);
