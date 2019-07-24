(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('select').select2({
            minimumResultsForSearch: 5
        });
    });

})(jQuery);
