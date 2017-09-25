(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.generatorToggle = Shopsys.generatorToggle || {};

    Shopsys.generatorToggle.init = function () {
        $('.js-generator-title input[type=checkbox]').on('change', Shopsys.generatorToggle.onChange);

        $('.js-generator-title input[type=checkbox]').each(function () {
            var $container = $(this).closest('.js-generator').find('.js-generator-form');

            if ($(this).is(':checked')) {
                $container.show();
            } else {
                $container.hide();
            }
        });
    };

    Shopsys.generatorToggle.onChange = function (event) {
        var $container = $(this).closest('.js-generator').find('.js-generator-form');

        if ($(this).is(':checked')) {
            $container.slideDown('fast');
        } else {
            $container.slideUp('fast');
        }
    };

    $(document).ready(function () {
        Shopsys.generatorToggle.init();
    });

})(jQuery);
