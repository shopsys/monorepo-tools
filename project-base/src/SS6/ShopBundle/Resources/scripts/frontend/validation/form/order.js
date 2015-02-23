(function ($){
	$(document).ready(function () {

		var $transportAndPaymentForm = $('#transportAndPayment');
		$transportAndPaymentForm.jsFormValidator({
			callbacks: {
				validateTransportPaymentRelation: function () {
					// validation combination of transport and payment is in ../../order.js
				}
			}
		});

		var $orderPersonalInfoForm = $('form[name="orderPersonalInfo"]');
		$orderPersonalInfoForm.jsFormValidator({
			'groups': function () {

				var groups = [SS6.constant('\\SS6\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
				if ($orderPersonalInfoForm.find('#orderPersonalInfo_deliveryAddressFilled').is(':checked')) {
					groups.push(SS6.constant('\\SS6\\ShopBundle\\Form\\Front\\Customer\\DeliveryAddressFormType::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS'));
				}
				if ($orderPersonalInfoForm.find('#orderPersonalInfo_companyCustomer').is(':checked')) {
					groups.push(SS6.constant('\\SS6\\ShopBundle\\Form\\Front\\Customer\\BillingAddressFormType::VALIDATION_GROUP_COMPANY_CUSTOMER'));
				}

				return groups;
			}
		});

	});
})(jQuery);
