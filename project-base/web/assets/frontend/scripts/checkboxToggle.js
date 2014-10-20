(function ($) {

	SS6 = window.SS6 || {};
	SS6.checkboxToggle = SS6.checkboxToggle || {};

	var containerIdDataAttribute = 'checkbox-toggle-container-id';

	SS6.checkboxToggle.init = function () {
		$('.checkbox-toggle').on('change', SS6.checkboxToggle.onChange);

		$('.checkbox-toggle').each(function () {
			var containerId = $(this).data(containerIdDataAttribute);

			if ($(this).is(':checked')) {
				$('#' + containerId).show();
			} else {
				$('#' + containerId).hide();
			}
		});
	};

	SS6.checkboxToggle.onChange = function (event) {
		var containerId = $(this).data(containerIdDataAttribute);

		if ($(this).is(':checked')) {
			$('#' + containerId).slideDown('fast');
		} else {
			$('#' + containerId).slideUp('fast');
		}
	};

	$(document).ready(function () {
		SS6.checkboxToggle.init();
	});

})(jQuery);
