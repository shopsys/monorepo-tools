(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes("[data-counter='counterup']").counterUp({
            delay: 10,
            time: 1000
        });
    });

})(jQuery);
