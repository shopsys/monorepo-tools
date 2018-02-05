(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.legalConditions = Shopsys.legalConditions || {};

    Shopsys.legalConditions.init = function () {
        $('#js-terms-and-conditions-print').on('click', function () {
            window.frames['js-terms-and-conditions-frame'].print();
        });
    };

    $(document).ready(function () {
        Shopsys.legalConditions.init();
    });

})(jQuery);
