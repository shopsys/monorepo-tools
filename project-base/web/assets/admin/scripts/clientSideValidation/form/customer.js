(function ($){
	$(document).ready(function () {
		var $customerForm = $('#customer_deliveryAddressData');
		$customerForm.jsFormValidator({
			'groups': function () {
				
				var groups = ['Default'];
				if ($customerForm.find('#customer_deliveryAddressData_addressFilled').is(':checked')) {
					groups.push('differentDeliveryAddress');
				}
				
				return groups;
			}
		});
	});
})(jQuery);