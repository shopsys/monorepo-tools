(function ($) {
    $(document).ready(function () {

        $('.js-free-transport-and-payment-price-limit').each(function () {
            var $priceLimitForm = $(this);
            $priceLimitForm.jsFormValidator({
                'groups': function () {

                    var groups = [Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
                    if ($priceLimitForm.find('.js-free-transport-and-payment-price-limit-enabled').is(':checked')) {
                        groups.push(Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\Admin\\TransportAndPayment\\FreeTransportAndPaymentPriceLimitsFormType::VALIDATION_GROUP_PRICE_LIMIT_ENABLED'));
                    }

                    return groups;
                }
            });
        });

    });
})(jQuery);
