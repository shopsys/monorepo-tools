(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.dropdown = Shopsys.dropdown || {};

    Shopsys.dropdown.DropDown = function ($dropdown) {
        this.init = function () {
            var $items = $dropdown.filterAllNodes('.js-dropdown-item');

            $items.click(function (event) {
                hideAllSubmenus();
                $(this).addClass('open');

                event.stopPropagation();
            });

            $(document).on('click', function () {
                hideAllSubmenus();
            });

            var hideAllSubmenus = function () {
                $items.removeClass('open');
            };
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-dropdown').each(function () {
            var DropDown = new Shopsys.dropdown.DropDown($(this));
            DropDown.init();
        });
    });

})(jQuery);
