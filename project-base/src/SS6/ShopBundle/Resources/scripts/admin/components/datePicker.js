(function($) {

$(document).ready(function() {
	$('.js-datePicker').each(function() {
		$(this).datepicker({
			'dateFormat': 'dd.mm.yy'
		});
	});
});

})(jQuery);
