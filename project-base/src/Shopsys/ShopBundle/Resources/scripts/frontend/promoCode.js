(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.promoCode = Shopsys.promoCode || {};

    Shopsys.promoCode.PromoCode = function ($container) {
        var $promoCodeSubmitButton = $container.filterAllNodes('#js-promo-code-submit-button');
        var $promoCodeInput = $container.filterAllNodes('#js-promo-code-input');

        this.init = function () {
            $promoCodeSubmitButton.click(applyPromoCode);
            $promoCodeInput.keypress(function (event) {
                if (event.keyCode === Shopsys.keyCodes.ENTER) {
                    applyPromoCode();
                    return false;
                }
            });
        };

        var applyPromoCode = function () {
            var code = $promoCodeInput.val();
            if (code !== '') {
                var data = {};
                data[Shopsys.constant('\\Shopsys\\ShopBundle\\Controller\\Front\\PromoCodeController::PROMO_CODE_PARAMETER')] = code;
                Shopsys.ajax({
                    loaderElement: '#js-promo-code-submit-button',
                    url: $promoCodeInput.data('apply-code-url'),
                    dataType: 'json',
                    method: 'post',
                    data: data,
                    success: onApplyPromoCode
                });
            } else {
                Shopsys.window({
                    content: Shopsys.translator.trans('Please enter discount code.')
                });
            }
        };

        var onApplyPromoCode = function (response) {
            if (response.result === true) {
                document.location = document.location;
            } else {
                Shopsys.window({
                    content: response.message
                });
            }
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        var promoCode = new Shopsys.promoCode.PromoCode($container);
        promoCode.init();
    });

})(jQuery);
