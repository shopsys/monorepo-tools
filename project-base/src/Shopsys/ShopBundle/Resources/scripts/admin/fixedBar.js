(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.fixedBar = Shopsys.fixedBar || {};

    Shopsys.fixedBar.onSymfonyToolbarShow = function () {
        $('.window-fixed-bar').addClass('window-fixed-bar--developer-mode');
    };

    Shopsys.fixedBar.onSymfonyToolbarHide = function () {
        $('.window-fixed-bar').removeClass('window-fixed-bar--developer-mode');
    };

    $(document).ready(function () {
        Shopsys.symfonyToolbarSupport.registerOnToolbarShow(Shopsys.fixedBar.onSymfonyToolbarShow);
        Shopsys.symfonyToolbarSupport.registerOnToolbarHide(Shopsys.fixedBar.onSymfonyToolbarHide);
    });

})(jQuery);
