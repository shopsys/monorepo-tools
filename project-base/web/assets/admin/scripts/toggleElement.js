(function ($) {

	SS6 = window.SS6 || {};
	SS6.toggleElement = SS6.toggleElement || {};

	SS6.toggleElement.init = function () {
		$('.toggle-container .toggle-headline').bind('click', SS6.toggleElement.toggle);
	}

	SS6.toggleElement.toggle = function () {
		var $container = $(this).closest('.toggle-container');
		var $content = $container.find('.toggle-content');
		if ($content.hasClass('toggle-close')) {
		$content.slideDown('fast', function() {
			$content.removeClass('toggle-close');
		});
		} else {
		$content.slideUp('fast', function() {
			$content.addClass('toggle-close');
		});
		}
	};

	$(document).ready(function () {
		SS6.toggleElement.init();
	});

})(jQuery);
