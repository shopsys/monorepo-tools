(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.settingMenu = Shopsys.settingMenu || {};

	Shopsys.settingMenu.Toggler = function ($settingMenu, $links) {
		this.init = function () {
			$settingMenu.click(function (event) {
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
		var settingMenuToggler = new Shopsys.settingMenu.Toggler($('#js-setting-menu'), $('#js-setting-menu-links'));
		settingMenuToggler.init();
	});

})(jQuery);