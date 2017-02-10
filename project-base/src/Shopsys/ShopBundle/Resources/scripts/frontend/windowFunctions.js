(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.windowFunctions = Shopsys.windowFunctions || {};

    Shopsys.windowFunctions.close = function () {
        $('#js-window').trigger('windowFastClose');
    };

})(jQuery);
