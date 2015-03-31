(function ($) {

	SS6 = window.SS6 || {};
	SS6.fixedBar = SS6.fixedBar || {};

	SS6.fixedBar.onSymfonyToolbarShow = function () {
		$('.window-fixed-bar').addClass('window-fixed-bar--developer-mode');
	};

	SS6.fixedBar.onSymfonyToolbarHide = function () {
		$('.window-fixed-bar').removeClass('window-fixed-bar--developer-mode');
	};

	$(document).ready(function () {
		SS6.symfonyToolbarSupport.registerOnToolbarShow(SS6.fixedBar.onSymfonyToolbarShow);
		SS6.symfonyToolbarSupport.registerOnToolbarHide(SS6.fixedBar.onSymfonyToolbarHide);
	});

})(jQuery);