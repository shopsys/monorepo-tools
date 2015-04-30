(function($) {

$(document).ready(function() {
	$('.js-datePicker').each(function() {
		$(this).datepicker({
			'dateFormat': SS6.constant('\\SS6\\ShopBundle\\Form\\DatePickerType::FORMAT_JS')
		});
	});
});

})(jQuery);
