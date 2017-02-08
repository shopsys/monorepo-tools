(function ($) {

	SS6 = window.SS6 || {};
	SS6.cartRecalculator = SS6.cartRecalculator || {};

	SS6.cartRecalculator.init = function ($container) {
		function reloadWithDelay(delay) {
			SS6.timeout.setTimeoutAndClearPrevious(
				'cartRecalculator',
				function () {
					SS6.cartRecalculator.reload();
				},
				delay
			);
		}

		// reload content after delay when clicking +/-
		$container.filterAllNodes('.js-cart-item .js-spinbox-plus, .js-cart-item .js-spinbox-minus').click(
			function (event) {
				reloadWithDelay(1000);
				event.preventDefault();
			}
		);

		// reload content after delay after leaving input or pressing ENTER
		// but only if value was changed
		$container.filterAllNodes('.js-cart-item .js-spinbox-input')
			.change(function () {
				$(this).blur(function () {
					reloadWithDelay(1000);
				});
			})
			.keydown(function (event) {
				if (event.keyCode === SS6.keyCodes.ENTER) {
					reloadWithDelay(0);
					event.preventDefault();
				}
			});
	};

	SS6.cartRecalculator.reload = function () {
		var formData = $('.js-cart-form').serializeArray();
		formData.push({
			name: SS6.constant('SS6\\ShopBundle\\Controller\\Front\\CartController::RECALCULATE_ONLY_PARAMETER_NAME'),
			value: 1
		});

		SS6.ajax({
			overlayDelay: 0, // show loader immediately to avoid clicking during AJAX request
			loaderElement: '.js-main-content',
			url: $('.js-cart-form').attr('action'),
			type: 'post',
			data: formData,
			dataType: 'html',
			success: function (html) {
				var $html = $($.parseHTML(html));

				var $mainContent = $html.find('.js-main-content');
				var $cartBox = $html.find('#js-cart-box');

				$('.js-main-content').replaceWith($mainContent);
				$('#js-cart-box').replaceWith($cartBox);

				SS6.register.registerNewContent($mainContent);
				SS6.register.registerNewContent($cartBox);
			}
		});
	};

	SS6.register.registerCallback(SS6.cartRecalculator.init);

})(jQuery);
