(function($) {

	Shopsys = window.Shopsys || {};

	var datePicker = function ($container) {
		$container.filterAllNodes('.js-date-picker').each(function() {
			// Loads regional settings for current locale
			var options = $.datepicker.regional[global.locale] || $.datepicker.regional[''];

			// Date format is fixed so that it is understood by back-end
			options.dateFormat = Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\DatePickerType::FORMAT_JS');

			$(this).datepicker(options);
		});
	};

	Shopsys.register.registerCallback(datePicker);

})(jQuery);
