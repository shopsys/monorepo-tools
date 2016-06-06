(function ($) {

	SS6 = window.SS6 || {};
	SS6.cartBox = SS6.cartBox || {};

	SS6.cartBox.init = function ($container) {
		$container.filterAllNodes('#js-cart-box').bind('reload', SS6.cartBox.reload);
	};

	SS6.cartBox.reload = function (event) {

		SS6.ajax({
			loaderElement: '#js-cart-box',
			url: $(this).data('reload-url'),
			type: 'get',
			success: function (data) {
				$('#js-cart-box').replaceWith(data);

				SS6.register.registerNewContent($('#js-cart-box').parent());
			}
		});

		event.preventDefault();
	};

	SS6.register.registerCallback(SS6.cartBox.init);

})(jQuery);

