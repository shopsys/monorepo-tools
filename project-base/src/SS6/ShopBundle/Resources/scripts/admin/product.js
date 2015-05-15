(function ($) {

	SS6 = window.SS6 || {};
	SS6.product = SS6.product || {};

	SS6.product.init = function () {
		var usingStockSelection = $('#product_edit_productData_usingStock input[type="radio"]');
		var $outOfStockActionSelection = $('select[name="product_edit[productData][outOfStockAction]"]');

		usingStockSelection.change(function () {
			SS6.product.toggleIsUsingStock($(this).val() === '1');
		});

		$outOfStockActionSelection.change(function () {
			SS6.product.toggleIsUsingAlternateAvailability($(this).val() === SS6.constant('\\SS6\\ShopBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE'));
		});

		SS6.product.toggleIsUsingStock(usingStockSelection.filter(':checked').val() === '1');
		SS6.product.toggleIsUsingAlternateAvailability($outOfStockActionSelection.val() === SS6.constant('\\SS6\\ShopBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE'));
	};

	SS6.product.toggleIsUsingStock = function (isUsingStock) {
		$('.js-product-using-stock').toggle(isUsingStock);
		$('.js-product-not-using-stock').toggle(!isUsingStock);
	};

	SS6.product.toggleIsUsingAlternateAvailability = function (isUsingStockAndAlternateAvailability) {
		$('.js-product-using-stock-and-alternate-availability').toggle(isUsingStockAndAlternateAvailability);
	};

	$(document).ready(function () {
		SS6.product.init();
	});

})(jQuery);
