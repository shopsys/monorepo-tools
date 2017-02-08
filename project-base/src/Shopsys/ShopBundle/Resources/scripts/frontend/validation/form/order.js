(function ($){
	$(document).ready(function () {

		var $transportAndPaymentForm = $('#transport_and_payment_form');
		$transportAndPaymentForm.jsFormValidator({
			callbacks: {
				validateTransportPaymentRelation: function () {
					// JS validation is not necessary as it is not possible to select
					// an invalid combination of transport and payment.
				}
			}
		});

		var $orderPersonalInfoForm = $('form[name="order_personal_info_form"]');
		$orderPersonalInfoForm.jsFormValidator({
			'groups': function () {

				var groups = [Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
				if ($orderPersonalInfoForm.find('#order_personal_info_form_deliveryAddressFilled').is(':checked')) {
					groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Front\\Customer\\DeliveryAddressFormType::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS'));
				}
				if ($orderPersonalInfoForm.find('#order_personal_info_form_companyCustomer').is(':checked')) {
					groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Front\\Customer\\BillingAddressFormType::VALIDATION_GROUP_COMPANY_CUSTOMER'));
				}

				return groups;
			}
		});

	});
})(jQuery);
