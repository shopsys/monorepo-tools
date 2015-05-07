(function ($) {

	SS6 = window.SS6 || {};
	SS6.bestsellingProductsAdmin = SS6.bestsellingProductsAdmin || {};

	SS6.bestsellingProductsAdmin.init = function () {
		$('.js-bestsellingProductUnassignButton').click(function () {
			var $bestsellingProductContainer = $(this).closest('.js-bestsellingProductContainer');
			var $input = $bestsellingProductContainer.find('.js-product-picker-input');
			SS6.productPicker.selectProduct($input.attr('id'), 0, $input.data('valueUnassigned'));
		});
	};

	$(document).ready(function () {
		SS6.bestsellingProductsAdmin.init();
	});

})(jQuery);
