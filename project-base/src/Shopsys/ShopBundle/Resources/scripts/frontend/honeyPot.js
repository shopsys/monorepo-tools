(function ($) {

	SS6 = window.SS6 || {};
	SS6.honeyPot = SS6.honeyPot || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-honey').hide();
	});

})(jQuery);
