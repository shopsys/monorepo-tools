(function ($) {

	SS6 = window.SS6 || {};
	SS6.checkboxToggle = SS6.checkboxToggle || {};

	var containerIdDataAttribute = 'checkbox-toggle-container-id';

	SS6.checkboxToggle.init = function () {
		var $checkboxToggles = $('.js-checkbox-toggle');

		$checkboxToggles.on('change', SS6.checkboxToggle.onChange);

		$checkboxToggles.each(function () {
			var $checkboxToggle = $(this);
			var containerId = $checkboxToggle.data(containerIdDataAttribute);

			var show = $checkboxToggle.is(':checked');
			if ($checkboxToggle.hasClass('js-checkbox-toggle--inverted')) {
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
		var $checkboxToggle = $(this);
		var containerId = $checkboxToggle.data(containerIdDataAttribute);

		var show = $checkboxToggle.is(':checked');
		if ($checkboxToggle.hasClass('js-checkbox-toggle--inverted')) {
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
