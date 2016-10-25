(function ($) {

	SS6 = window.SS6 || {};

	$(document).ready(function () {
		$('form').each(function () {
			var isFormSubmittingDisabled = false;

			$(this).on('submit', function () {
				if (isFormSubmittingDisabled === true) {
					return false;
				}
				isFormSubmittingDisabled = true;
				setTimeout(
					function () {
						isFormSubmittingDisabled = false;
					},
					200
				);
			});
		});
	});

})(jQuery);