(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.view = Shopsys.view || {};

    Shopsys.view.getBottomOffset = function () {
        var windowFixedBarHeight = $('.js-window-fixed-bar').height() || 0;
        var symfonyBarHeight = $('.sf-toolbar').height() || 0;

        return windowFixedBarHeight + symfonyBarHeight;
    };

})(jQuery);
