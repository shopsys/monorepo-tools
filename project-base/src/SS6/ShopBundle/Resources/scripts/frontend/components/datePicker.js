(function($) {

	SS6 = window.SS6 || {};

	var datePicker = function ($container) {
		$container.filterAllNodes('.js-date-picker').each(function() {
			// Loads regional settings for current locale
			var options = $.datepicker.regional[global.locale] || $.datepicker.regional[''];

			// Date format is fixed so that it is understood by back-end
			options.dateFormat = SS6.constant('\\SS6\\ShopBundle\\Form\\DatePickerType::FORMAT_JS');

			$(this).datepicker(options);
		});
	};

	SS6.register.registerCallback(datePicker);

})(jQuery);
