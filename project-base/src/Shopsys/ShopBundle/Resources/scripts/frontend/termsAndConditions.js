(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.termsAndConditions = Shopsys.termsAndConditions || {};


    Shopsys.termsAndConditions.init = function () {
        $('#js-terms-and-conditions-print').on('click', function () {
            window.frames['js-terms-and-conditions-frame'].print();
        });
    };

    $(document).ready(function () {
        Shopsys.termsAndConditions.init();
    });

})(jQuery);
