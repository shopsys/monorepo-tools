(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.customerEdit = $.fn.SS6.customerEdit || {};
	
	$.fn.SS6.customerEdit.init = function () {
		$('#js-company-customer')
			.on('change', $.fn.SS6.customerEdit.onCompanyCustomerChange)
			.change();
		$('#js-delivery-address-filled')
			.on('change', $.fn.SS6.customerEdit.onDeliveryAddressFilledChange)
			.change();
	};
	
	$.fn.SS6.customerEdit.onCompanyCustomerChange = function (event) {
		if ($(this).is(':checked')) {
			$('#js-company-fields').show();
		} else {
			$('#js-company-fields').hide();
		}
	};
	
	$.fn.SS6.customerEdit.onDeliveryAddressFilledChange = function (event) {
		if ($(this).is(':checked')) {
			$('#js-delivery-address-fields').show();
		} else {
			$('#js-delivery-address-fields').hide();
		}
	};
	
	$(document).ready(function () {
		$.fn.SS6.customerEdit.init();
	});
	
})(jQuery);
