(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.checkboxToggle = Shopsys.checkboxToggle || {};

	var containerIdDataAttribute = 'checkbox-toggle-container-id';

	Shopsys.checkboxToggle.init = function ($container) {
		var $checkboxToggles = $container.filterAllNodes('.js-checkbox-toggle');

		$checkboxToggles.on('change', Shopsys.checkboxToggle.onChange);

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

	Shopsys.checkboxToggle.onChange = function () {
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

	Shopsys.register.registerCallback(Shopsys.checkboxToggle.init);

})(jQuery);
