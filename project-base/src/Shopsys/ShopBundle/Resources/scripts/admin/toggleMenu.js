(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.toggleMenu = Shopsys.toggleMenu || {};

    Shopsys.toggleMenu.ToggleMenu = function ($toggleMenu) {
        this.init = function () {
            var $items = $toggleMenu.filterAllNodes('.js-toggle-menu-item');

            $items.click(function (event) {
                hideAllSubmenus();

                $(this).filterAllNodes('.js-toggle-menu-submenu').show();
                $(this).addClass('open');

                event.stopPropagation();
            });

            $(document).on('click', function () {
                hideAllSubmenus();
            });

            var hideAllSubmenus = function () {
                $items.filterAllNodes('.js-toggle-menu-submenu').hide();
                $items.removeClass('open');
            };
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-toggle-menu').each(function () {
            var toggleMenu = new Shopsys.toggleMenu.ToggleMenu($(this));
            toggleMenu.init();
        });
    });

})(jQuery);