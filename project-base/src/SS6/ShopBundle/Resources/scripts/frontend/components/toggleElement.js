(function ($) {

	SS6 = window.SS6 || {};
	SS6.toggleElement = SS6.toggleElement || {};

	SS6.toggleElement.init = function () {
		$('.js-toggle-container .js-toggle-button').bind('click', SS6.toggleElement.toggle);
	};

	SS6.toggleElement.show = function ($container) {
		var $content = $container.find('.js-toggle-content');

		$container.trigger('showContent.toggleElement');

		$content.slideDown('fast', function() {
			$content.removeClass('display-none');
		});
	};

	SS6.toggleElement.hide = function ($container) {
		var $content = $container.find('.js-toggle-content');

		$container.trigger('hideContent.toggleElement');

		$content.slideUp('fast', function() {
			$content.addClass('display-none');
		});
	};

	SS6.toggleElement.toggle = function () {
		var $container = $(this).closest('.js-toggle-container');
		var $content = $container.find('.js-toggle-content');
		if ($content.hasClass('display-none')) {
			SS6.toggleElement.show($container);
		} else {
			SS6.toggleElement.hide($container);
		}
	};

	$(document).ready(function () {
		SS6.toggleElement.init();
	});

})(jQuery);
