(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.symfonyToolbarSupport = Shopsys.symfonyToolbarSupport || {};

	Shopsys.symfonyToolbarSupport.onToolbarShowCallbacks = [];
	Shopsys.symfonyToolbarSupport.onToolbarHideCallbacks = [];

	Shopsys.symfonyToolbarSupport.registerOnToolbarShow = function (callback) {
		Shopsys.symfonyToolbarSupport.onToolbarShowCallbacks.push(callback);
	};

	Shopsys.symfonyToolbarSupport.registerOnToolbarHide = function (callback) {
		Shopsys.symfonyToolbarSupport.onToolbarHideCallbacks.push(callback);
	};

	Shopsys.symfonyToolbarSupport.notifyOnToolbarShow = function () {
		for (var i in Shopsys.symfonyToolbarSupport.onToolbarShowCallbacks) {
			var callback = Shopsys.symfonyToolbarSupport.onToolbarShowCallbacks[i];
			callback.call();
		}
	};

	Shopsys.symfonyToolbarSupport.notifyOnToolbarHide = function () {
		for (var i in Shopsys.symfonyToolbarSupport.onToolbarHideCallbacks) {
			var callback = Shopsys.symfonyToolbarSupport.onToolbarHideCallbacks[i];
			callback.call();
		}
	};

	$(document).ready(function () {
		$('.sf-toolbar').on('click', '[id^="sfMiniToolbar-"] > a', function () {
			Shopsys.symfonyToolbarSupport.notifyOnToolbarShow();
		});

		$('.sf-toolbar').on('click', '[id^="sfToolbarMainContent-"] > a.hide-button', function () {
			Shopsys.symfonyToolbarSupport.notifyOnToolbarHide();
		});

		// condition copied from: vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Profiler/toolbar_js.html.twig
		if (typeof Sfjs !== 'undefined' && Sfjs.getPreference('toolbar/displayState') !== 'none') {
			Shopsys.symfonyToolbarSupport.notifyOnToolbarShow();
		}
	});

})(jQuery);