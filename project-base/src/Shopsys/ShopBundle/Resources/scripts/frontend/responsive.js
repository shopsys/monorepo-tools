(function($) {

	SS6 = window.SS6 || {};
	SS6.responsive = window.SS6.responsive || {};

	SS6.responsive.XS = 320;
	SS6.responsive.SM = 480;
	SS6.responsive.MD = 600;
	SS6.responsive.LG = 769;
	SS6.responsive.VL = 980;
	SS6.responsive.XL = 1200;

	var onLayoutChangeListeners = [];
	var lastIsDesktop = null;

	SS6.responsive.isDesktopVersion = function() {
		return $(window).width() >= SS6.responsive.LG;
	};

	SS6.responsive.registerOnLayoutChange = function (callback) {
		onLayoutChangeListeners.push(callback);
	};

	$(window).resize(function() {
		SS6.timeout.setTimeoutAndClearPrevious('SS6.responsive.window.resize', onWindowResize, 200);
	});

	function onWindowResize() {
		if (lastIsDesktop !== SS6.responsive.isDesktopVersion()) {
			$.each(onLayoutChangeListeners, function (index, callback) {
				callback(SS6.responsive.isDesktopVersion());
			});

			lastIsDesktop = SS6.responsive.isDesktopVersion();
		}
	}

})(jQuery);