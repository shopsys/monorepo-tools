(function ($) {

	SS6 = window.SS6 || {};
	SS6.productInputPrice = SS6.productInputPrice || {};

	SS6.productInputPrice.init = function () {
		var priceCalculationTypeSelection = $('#product_edit_productData_priceCalculationType input[type="radio"]');
		priceCalculationTypeSelection.change(function(){
			SS6.productInputPrice.showInputByPriceCalculationType($(this).val() === '1');
		});
		SS6.productInputPrice.showInputByPriceCalculationType(priceCalculationTypeSelection.filter(':checked').val() === '1');
	};

	SS6.productInputPrice.showInputByPriceCalculationType = function (isPriceCalculationTypeAuto) {
		$('.js-base-price-line').toggle(isPriceCalculationTypeAuto);
		$('.js-manual-base-price').toggle(!isPriceCalculationTypeAuto);
		$('.js-pricing-group-name').toggle(isPriceCalculationTypeAuto);
	};

	$(document).ready(function () {
		SS6.productInputPrice.init();
	});

})(jQuery);