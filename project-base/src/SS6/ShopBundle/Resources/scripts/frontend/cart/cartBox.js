(function ($) {

	SS6 = window.SS6 || {};
	SS6.cartBox = SS6.cartBox || {};

	SS6.cartBox.init = function () {
		$('#cart-box').bind('reload', SS6.cartBox.reload);
	};

	SS6.cartBox.reload = function (event) {
		var self = $(this);

		$.ajax({
			url: self.data('reload-url'),
			type: 'get',
			success: function (data) {
				$('#cart-box').replaceWith(data);

				// TODO: temporal solution, should be fixed in US-537
				$('#cart-box').bind('reload', SS6.cartBox.reload);
			}
		});

		event.preventDefault();
	};

	$(document).ready(function () {
		SS6.cartBox.init();
	});

})(jQuery);

