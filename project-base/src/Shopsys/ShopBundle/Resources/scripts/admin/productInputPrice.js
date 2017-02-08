(function ($) {

	SS6 = window.SS6 || {};
	SS6.productInputPrice = SS6.productInputPrice || {};

	SS6.productInputPrice.init = function () {
		var $priceCalculationTypeSelection = $('#product_edit_form_productData_priceCalculationType input[type="radio"]');
		$priceCalculationTypeSelection.change(function(){
			SS6.productInputPrice.showInputByPriceCalculationType($(this).val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_AUTO'));
		});
		SS6.productInputPrice.showInputByPriceCalculationType($priceCalculationTypeSelection.filter(':checked').val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_AUTO'));
	};

	SS6.productInputPrice.showInputByPriceCalculationType = function (isPriceCalculationTypeAuto) {
		$('.js-input-price-type-auto').toggle(isPriceCalculationTypeAuto);
		$('.js-input-price-type-manual').toggle(!isPriceCalculationTypeAuto);
	};

	$(document).ready(function () {
		SS6.productInputPrice.init();
	});

})(jQuery);