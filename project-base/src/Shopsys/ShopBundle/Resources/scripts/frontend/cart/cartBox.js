(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.cartBox = Shopsys.cartBox || {};

    Shopsys.cartBox.init = function ($container) {
        $container.filterAllNodes('#js-cart-box').bind('reload', Shopsys.cartBox.reload);
    };

    Shopsys.cartBox.reload = function (event) {

        Shopsys.ajax({
            loaderElement: '#js-cart-box',
            url: $(this).data('reload-url'),
            type: 'get',
            success: function (data) {
                $('#js-cart-box').replaceWith(data);

                Shopsys.register.registerNewContent($('#js-cart-box').parent());
            }
        });

        event.preventDefault();
    };

    Shopsys.register.registerCallback(Shopsys.cartBox.init);

})(jQuery);
