(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-color-picker').minicolors({
            theme: 'bootstrap'
        });
    });

})(jQuery);