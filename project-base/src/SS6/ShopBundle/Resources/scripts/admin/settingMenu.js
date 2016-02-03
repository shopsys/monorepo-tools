(function ($) {

	SS6 = window.SS6 || {};
	SS6.settingMenu = SS6.settingMenu || {};

	SS6.settingMenu.Toggler = function ($settingMenu, $links) {
		this.init = function () {
			$settingMenu.click(function () {
				$links.toggle();
			});
		};
	};

	$(document).ready(function () {
		var settingMenuToggler = new SS6.settingMenu.Toggler($('#js-setting-menu'), $('#js-setting-menu-links'));
		settingMenuToggler.init();
	});

})(jQuery);