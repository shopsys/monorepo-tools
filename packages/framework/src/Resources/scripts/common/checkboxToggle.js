(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.checkboxToggle = Shopsys.checkboxToggle || {};

    Shopsys.checkboxToggle.init = function ($container) {
        var $checkboxToggles = $container.filterAllNodes('.js-checkbox-toggle');

        $checkboxToggles.on('change', Shopsys.checkboxToggle.onChange);

        $checkboxToggles.each(function () {
            var $checkboxToggle = $(this);
            var $container = Shopsys.checkboxToggle.findContainer($checkboxToggle);

            var show = $checkboxToggle.is(':checked');
            if ($checkboxToggle.hasClass('js-checkbox-toggle--inverted')) {
                show = !show;
            }

            if (show) {
                $container.show();
            } else {
                $container.hide();
            }
        });
    };

    Shopsys.checkboxToggle.onChange = function () {
        var $checkboxToggle = $(this);
        var $container = Shopsys.checkboxToggle.findContainer($checkboxToggle);

        var show = $checkboxToggle.is(':checked');
        if ($checkboxToggle.hasClass('js-checkbox-toggle--inverted')) {
            show = !show;
        }

        if (show) {
            $container.slideDown('fast');
        } else {
            $container.slideUp('fast');
        }
    };

    Shopsys.checkboxToggle.findContainer = function ($checkboxToggle) {
        if ($checkboxToggle.data('checkbox-toggle-container-id')) {
            return $('#' + $checkboxToggle.data('checkbox-toggle-container-id'));
        }

        return $('.' + $checkboxToggle.data('checkbox-toggle-container-class'));
    };

    Shopsys.register.registerCallback(Shopsys.checkboxToggle.init);

})(jQuery);
