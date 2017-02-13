(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.massAction = Shopsys.massAction || {};

    Shopsys.massAction.init = function ($container) {
        $container.filterAllNodes('#js-mass-action-button').click(function () {
            $('#js-mass-action').toggleClass('active');
        });
    };

    Shopsys.register.registerCallback(Shopsys.massAction.init);

})(jQuery);