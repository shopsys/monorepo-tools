(function ($) {

	SS6 = window.SS6 || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-tooltip[title]').tooltip();
	});

})(jQuery);