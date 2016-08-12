(function ($) {

	SS6 = window.SS6 || {};
	SS6.checkboxToggle = SS6.checkboxToggle || {};

	var containerIdDataAttribute = 'checkbox-toggle-container-id';

	SS6.checkboxToggle.init = function () {
		$('.js-checkbox-toggle').on('change', SS6.checkboxToggle.onChange);

		$('.js-checkbox-toggle').each(function () {
			var containerId = $(this).data(containerIdDataAttribute);

			var show = $(this).is(':checked');
			if ($(this).hasClass('js-checkbox-toggle--inverted')) {
				show = !show;
			}

			if (show) {
				$('#' + containerId).show();
			} else {
				$('#' + containerId).hide();
			}
		});
	};

	SS6.checkboxToggle.onChange = function () {
		var containerId = $(this).data(containerIdDataAttribute);

		var show = $(this).is(':checked');
		if ($(this).hasClass('js-checkbox-toggle--inverted')) {
			show = !show;
		}

		if (show) {
			$('#' + containerId).slideDown('fast');
		} else {
			$('#' + containerId).slideUp('fast');
		}
	};

	$(document).ready(function () {
		SS6.checkboxToggle.init();
	});

})(jQuery);
