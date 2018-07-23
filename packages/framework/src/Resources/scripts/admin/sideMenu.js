(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.sideMenu = Shopsys.sideMenu || {};

    Shopsys.sideMenu.SideMenu = function ($sideMenu) {
        this.init = function () {
            var $items = $sideMenu.filterAllNodes('.js-side-menu-item');

            $items.click(function () {
                $(this).filterAllNodes('.js-side-menu-submenu').show();
                $(this).addClass('open');
            });
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-side-menu').each(function () {
            var sideMenu = new Shopsys.sideMenu.SideMenu($(this));
            sideMenu.init();
        });
    });

})(jQuery);
