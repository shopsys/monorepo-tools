(function ($) {

	SS6 = window.SS6 || {};
	SS6.product = SS6.product || {};

	SS6.product.init = function () {
		var usingStockSelection = $('#product_edit_productData_usingStock input[type="radio"]');

		usingStockSelection.change(function () {
			SS6.product.toggleIsUsingStock($(this).val() === '1');
		});

		SS6.product.toggleIsUsingStock(usingStockSelection.filter(':checked').val() === '1');
	};

	SS6.product.toggleIsUsingStock = function (isUsingStock) {
		$('.js-product-using-stock').toggle(isUsingStock);
		$('.js-product-not-using-stock').toggle(!isUsingStock);
	};

	$(document).ready(function () {
		SS6.product.init();
	});

})(jQuery);
