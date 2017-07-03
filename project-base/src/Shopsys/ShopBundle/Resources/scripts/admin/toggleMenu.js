(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.toggleMenu = Shopsys.toggleMenu || {};

    Shopsys.toggleMenu.Toggler = function ($toggleMenu, $links) {
        this.init = function () {
            $toggleMenu.click(function (event) {
                var $activeToggleMenus = $('.navig__item__sub:visible');
                $activeToggleMenus.hide();
                $links.toggle();
                event.stopPropagation();
            });
            $links.click(function (event) {
                event.stopPropagation();
            });
        };

        $(document).on('click', function () {
            $links.hide();
        });
    };

    $(document).ready(function () {
        var settingMenuToggler = new Shopsys.toggleMenu.Toggler($('#js-setting-menu'), $('#js-setting-menu-links'));
        settingMenuToggler.init();
        var adminMenuToggler = new Shopsys.toggleMenu.Toggler($('#js-account-menu'), $('#js-account-menu-links'));
        adminMenuToggler.init();
    });

})(jQuery);