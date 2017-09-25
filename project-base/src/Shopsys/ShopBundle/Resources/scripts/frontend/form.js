(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.form = Shopsys.form || {};

    Shopsys.form.disableDoubleSubmit = function ($container) {
        $container.filterAllNodes('form').each(function () {
            var isFormSubmittingDisabled = false;

            $(this).submit(function (event) {
                if (isFormSubmittingDisabled) {
                    event.stopImmediatePropagation();
                    event.preventDefault();
                } else {
                    isFormSubmittingDisabled = true;
                    setTimeout(function () {
                        isFormSubmittingDisabled = false;
                    }, 200);
                }
            });
        });
    };

    Shopsys.register.registerCallback(Shopsys.form.disableDoubleSubmit, Shopsys.register.CALL_PRIORITY_HIGH);

})(jQuery);
