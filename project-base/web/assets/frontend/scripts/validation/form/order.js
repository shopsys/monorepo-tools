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

				var groups = ['Default'];
				if ($orderPersonalInfoForm.find('#orderPersonalInfo_deliveryAddressFilled').is(':checked')) {
					groups.push('differentDeliveryAddress');
				}
				if ($orderPersonalInfoForm.find('#orderPersonalInfo_companyCustomer').is(':checked')) {
					groups.push('companyCustomer');
				}

				return groups;
			}
		});
				
	});
})(jQuery);
