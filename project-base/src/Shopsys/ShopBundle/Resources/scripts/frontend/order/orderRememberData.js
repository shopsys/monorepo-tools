(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.orderRememberData = Shopsys.orderRememberData || {};

    Shopsys.orderRememberData.delayedSaveDataTimer = null;
    Shopsys.orderRememberData.delayedSaveDataDelay = 200;

    Shopsys.orderRememberData.init = function ($container) {
        $container.filterAllNodes('#js-order-form input, #js-order-form select, #js-order-form textarea')
            .bind('change.orderRememberData', Shopsys.orderRememberData.saveData);

        $container.filterAllNodes('#js-order-form input, #js-order-form textarea')
            .bind('keyup.orderRememberData', Shopsys.orderRememberData.delayedSaveData);
    };

    Shopsys.orderRememberData.delayedSaveData = function() {
        var $this = $(this);
        clearTimeout(Shopsys.orderRememberData.delayedSaveDataTimer);
        Shopsys.orderRememberData.delayedSaveDataTimer = setTimeout(function () {
            $this.trigger('change.orderRememberData');
        }, Shopsys.orderRememberData.delayedSaveDataDelay);
    };

    Shopsys.orderRememberData.saveData = function() {
        clearTimeout(Shopsys.orderRememberData.delayedSaveDataTimer);
        var $orderForm = $('#js-order-form');
        Shopsys.ajaxPendingCall('Shopsys.orderRememberData.saveData', {
            type: "POST",
            url: $orderForm.data('ajax-save-url'),
            data: $orderForm.serialize(),
            loaderElement: null
        });
    };

    Shopsys.register.registerCallback(Shopsys.orderRememberData.init);

})(jQuery);
