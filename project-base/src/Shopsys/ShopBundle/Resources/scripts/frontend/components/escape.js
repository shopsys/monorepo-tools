(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.escape = Shopsys.escape || {};

    Shopsys.escape.escapeHtml = function (string) {
        return $('<textarea/>').text(string).html();
    };

})(jQuery);
