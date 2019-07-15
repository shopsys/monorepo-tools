(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.sideMenu = Shopsys.sideMenu || {};

    Shopsys.sideMenu.SideMenu = function ($sideMenu) {
        var self = this;
        var $items;

        this.init = function () {
            $items = $sideMenu.filterAllNodes('.js-side-menu-item');

            $items.click(function () {
                $(this).filterAllNodes('.js-side-menu-submenu').show();
                $(this).addClass('open');
            });

            if ($sideMenu.hasClass('js-side-menu-close-after-mouseleave')) {
                self.closeMenusAfterMouseleave(500);
            }
        };

        this.closeMenusAfterMouseleave = function (timoutMilliseconds) {
            var timeoutHandle;
            $sideMenu.hover(
                function () { clearTimeout(timeoutHandle); },
                function () { timeoutHandle = setTimeout(self.closeMenus, timoutMilliseconds); }
            );
        };

        this.closeMenus = function () {
            $items.filterAllNodes('.js-side-menu-submenu').hide();
            $items.removeClass('open');
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-side-menu').each(function () {
            var sideMenu = new Shopsys.sideMenu.SideMenu($(this));
            sideMenu.init();
        });
    });

})(jQuery);
