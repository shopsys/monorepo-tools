(function($) {

	SS6 = window.SS6 || {};

	var datePicker = function ($container) {
		$container.filterAllNodes('.js-date-picker').each(function() {
			$(this).datepicker({
				'dateFormat': SS6.constant('\\SS6\\ShopBundle\\Form\\DatePickerType::FORMAT_JS')
			});
		});
	};

	SS6.register.registerCallback(datePicker);

})(jQuery);
