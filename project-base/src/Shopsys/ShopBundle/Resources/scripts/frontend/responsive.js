(function($) {

	Shopsys = window.Shopsys || {};
	Shopsys.responsive = window.Shopsys.responsive || {};

	Shopsys.responsive.XS = 320;
	Shopsys.responsive.SM = 480;
	Shopsys.responsive.MD = 600;
	Shopsys.responsive.LG = 769;
	Shopsys.responsive.VL = 980;
	Shopsys.responsive.XL = 1200;

	var onLayoutChangeListeners = [];
	var lastIsDesktop = null;

	Shopsys.responsive.isDesktopVersion = function() {
		return $(window).width() >= Shopsys.responsive.LG;
	};

	Shopsys.responsive.registerOnLayoutChange = function (callback) {
		onLayoutChangeListeners.push(callback);
	};

	$(window).resize(function() {
		Shopsys.timeout.setTimeoutAndClearPrevious('Shopsys.responsive.window.resize', onWindowResize, 200);
	});

	function onWindowResize() {
		if (lastIsDesktop !== Shopsys.responsive.isDesktopVersion()) {
			$.each(onLayoutChangeListeners, function (index, callback) {
				callback(Shopsys.responsive.isDesktopVersion());
			});

			lastIsDesktop = Shopsys.responsive.isDesktopVersion();
		}
	}

})(jQuery);