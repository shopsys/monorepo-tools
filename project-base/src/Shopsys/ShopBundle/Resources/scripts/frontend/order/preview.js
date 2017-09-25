(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.orderPreview = Shopsys.orderPreview || {};

    Shopsys.orderPreview.init = function ($container) {
        $container
            .filterAllNodes('.js-order-transport-input, .js-order-payment-input')
            .change(Shopsys.orderPreview.loadOrderPreview);
    };

    Shopsys.orderPreview.loadOrderPreview = function () {
        var $orderPreview = $('#js-order-preview');
        var $checkedTransport = $('.js-order-transport-input:checked');
        var $checkedPayment = $('.js-order-payment-input:checked');
        var data = {};

        if ($checkedTransport.length > 0) {
            data['transportId'] = $checkedTransport.data('id');
        }
        if ($checkedPayment.length > 0) {
            data['paymentId'] = $checkedPayment.data('id');
        }

        Shopsys.ajaxPendingCall('Shopsys.orderPreview.loadOrderPreview', {
            loaderElement: '#js-order-preview',
            url: $orderPreview.data('url'),
            type: 'get',
            data: data,
            success: function (data) {
                $orderPreview.html(data);
            }
        });
    };

    Shopsys.register.registerCallback(Shopsys.orderPreview.init);

})(jQuery);
