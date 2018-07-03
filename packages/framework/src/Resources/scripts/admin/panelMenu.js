(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.panelMenu = Shopsys.panelMenu || {};

    Shopsys.panelMenu.PanelMenu = function ($panelMenu) {
        this.init = function () {
            var $items = $panelMenu.filterAllNodes('.js-panel-menu-item');

            $items.click(function () {
                $(this).filterAllNodes('.js-panel-menu-submenu').show();
                $(this).addClass('open');
            });
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-panel-menu').each(function () {
            var panelMenu = new Shopsys.panelMenu.PanelMenu($(this));
            panelMenu.init();
        });
    });

})(jQuery);
