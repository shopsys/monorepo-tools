(function ($) {

	SS6 = window.SS6 || {};
	SS6.generatorToggle = SS6.generatorToggle || {};


	SS6.generatorToggle.init = function () {
		$('.js-generator-title input[type=checkbox]').on('change', SS6.generatorToggle.onChange);

		$('.js-generator-title input[type=checkbox]').each(function () {
			var $container = $(this).closest('.js-generator').find('.js-generator-form');

			if ($(this).is(':checked')) {
				$container.show();
			} else {
				$container.hide();
			}
		});
	};

	SS6.generatorToggle.onChange = function (event) {
		var $container = $(this).closest('.js-generator').find('.js-generator-form');

		if ($(this).is(':checked')) {
			$container.slideDown('fast');
		} else {
			$container.slideUp('fast');
		}
	};

	$(document).ready(function () {
		SS6.generatorToggle.init();
	});

})(jQuery);
