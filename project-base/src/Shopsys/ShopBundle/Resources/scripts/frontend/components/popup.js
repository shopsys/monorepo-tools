(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-popup-image').magnificPopup({
            type: 'image'
        });
    });

})(jQuery);
