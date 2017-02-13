(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.freeTransportAndPayment = Shopsys.freeTransportAndPayment || {};

    Shopsys.freeTransportAndPayment.FreeTransportAndPayment = function ($container) {
        var $checkbox = $container.find('.js-free-transport-and-payment-price-limit-enabled');
        var $input = $container.find('.js-free-transport-and-payment-price-limit-input');

        this.init = function() {
            $checkbox.click(updateInputDisabledAttribute);
            updateInputDisabledAttribute();
        };

        function updateInputDisabledAttribute() {
            if ($checkbox.is(':checked')) {
                $input.removeAttr('disabled');
            } else {
                $input.attr('disabled', 'disabled');
            }
        }
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-free-transport-and-payment-price-limit').each(function () {
            var freeTransportAndPayment = new Shopsys.freeTransportAndPayment.FreeTransportAndPayment($(this));
            freeTransportAndPayment.init();
        });
    });

})(jQuery);
