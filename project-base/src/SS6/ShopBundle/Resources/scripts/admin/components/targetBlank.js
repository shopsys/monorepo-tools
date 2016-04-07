(function ($) {

	SS6 = window.SS6 || {};
	SS6.targetBlank = SS6.targetBlank || {};

	SS6.targetBlank.init = function ($container) {
		$container.filterAllNodes('a[target="_blank"]').each(SS6.targetBlank.bind);
	};

	SS6.targetBlank.bind = function () {
		$(this).on('click', function() {
			var href = $(this).attr('href');
			window.open(href);
			return false;
		});
	};

	SS6.register.registerCallback(SS6.targetBlank.init);

})(jQuery);
