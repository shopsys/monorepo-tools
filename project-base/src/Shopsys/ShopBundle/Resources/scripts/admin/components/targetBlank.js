(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.targetBlank = Shopsys.targetBlank || {};

    Shopsys.targetBlank.init = function ($container) {
        $container.filterAllNodes('a[target="_blank"]').each(Shopsys.targetBlank.bind);
    };

    Shopsys.targetBlank.bind = function () {
        $(this).on('click', function() {
            var href = $(this).attr('href');
            window.open(href);
            return false;
        });
    };

    Shopsys.register.registerCallback(Shopsys.targetBlank.init);

})(jQuery);
