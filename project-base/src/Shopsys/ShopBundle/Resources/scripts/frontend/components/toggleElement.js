(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.toggleElement = Shopsys.toggleElement || {};

    Shopsys.toggleElement.init = function ($container) {
        $container.filterAllNodes('.js-toggle-container .js-toggle-button')
            .bind('click', Shopsys.toggleElement.toggle);
    };

    Shopsys.toggleElement.show = function ($container) {
        var $content = $container.find('.js-toggle-content');

        $container.trigger('showContent.toggleElement');

        $content.slideDown('fast', function() {
            $content.removeClass('display-none');
        });
    };

    Shopsys.toggleElement.hide = function ($container) {
        var $content = $container.find('.js-toggle-content');

        $container.trigger('hideContent.toggleElement');

        $content.slideUp('fast', function() {
            $content.addClass('display-none');
        });
    };

    Shopsys.toggleElement.toggle = function () {
        var $container = $(this).closest('.js-toggle-container');
        var $content = $container.find('.js-toggle-content');
        if ($content.hasClass('display-none')) {
            Shopsys.toggleElement.show($container);
        } else {
            Shopsys.toggleElement.hide($container);
        }
    };

    Shopsys.register.registerCallback(Shopsys.toggleElement.init);

})(jQuery);
