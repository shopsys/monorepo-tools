stnw_date_picker = function() {
	var locale = "";
	return{
		initLocale: function() {
			if (global.locale) {
				locale = global.locale;
			}
			else {
				//Set a default locale if the user's one is not managed
				console.error('The locale is missing, default locale will be set (en_EN)');
				locale = "en_EN";
			}
		},
		getLocale: function(length) {
			if (length == 2) {
				return locale.split('_')[0];
			}
			return locale;
		},
		initDatePicker: function() {

			if ($.datepicker.regional[stnw_date_picker.getLocale(4)] != undefined) {
				$.datepicker.setDefaults($.datepicker.regional[stnw_date_picker.getLocale(4)]);
			} else if ($.datepicker.regional[stnw_date_picker.getLocale(2)] != undefined) {
				$.datepicker.setDefaults($.datepicker.regional[stnw_date_picker.getLocale(2) ]);
			} else {
				$.datepicker.setDefaults($.datepicker.regional['']);
			}

			$('.stnw_date_picker').each(function() {


				var id_input = this.id.split('_datepicker')[0];
				var sfInput = $('#' + id_input)[0];
				if (!(sfInput)) {
					console.error('An error has occurred while creating the datepicker');
				}
				$(this).datepicker({
					'yearRange': $(this).data('yearrange'),
					'changeMonth': $(this).data('changemonth'),
					'changeYear': $(this).data('changeyear'),
					'altField': '#' + id_input,
					'altFormat': 'yy-mm-dd',
					'dateFormat': 'dd.mm.yy',
					'minDate': null,
					'maxDate': null
				});

				var dateSf = $.datepicker.parseDate('yy-mm-dd', sfInput.value);

				$(this).datepicker('setDate', dateSf);
				$(this).show();
				$(sfInput).hide();
			});
		}
	}
}();

$(document).ready(function() {
	stnw_date_picker.initLocale();
	stnw_date_picker.initDatePicker();
});
