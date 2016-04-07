(function ($) {

	SS6 = window.SS6 || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-popup-image').magnificPopup({
			type: 'image'
		});
	});

})(jQuery);