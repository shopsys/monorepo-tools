(function ($) {

	Shopsys = window.Shopsys || {};

	Shopsys.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-tooltip[title]').tooltip();
	});

})(jQuery);