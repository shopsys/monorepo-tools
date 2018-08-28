(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productInputPrice = Shopsys.productInputPrice || {};

    Shopsys.productInputPrice.init = function () {
        var $priceCalculationTypeSelection = $('#product_form_pricesGroup_priceCalculationType input[type="radio"]');
        $priceCalculationTypeSelection.change(function () {
            Shopsys.productInputPrice.showInputByPriceCalculationType($(this).val() === Shopsys.constant('\\Shopsys\\FrameworkBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_AUTO'));
        });
        Shopsys.productInputPrice.showInputByPriceCalculationType($priceCalculationTypeSelection.filter(':checked').val() === Shopsys.constant('\\Shopsys\\FrameworkBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_AUTO'));
    };

    Shopsys.productInputPrice.showInputByPriceCalculationType = function (isPriceCalculationTypeAuto) {
        $('.js-input-price-type-auto').toggle(isPriceCalculationTypeAuto);
        $('.js-input-price-type-manual').toggle(!isPriceCalculationTypeAuto);
    };

    $(document).ready(function () {
        Shopsys.productInputPrice.init();
    });

})(jQuery);
