(function ($) {

	SS6 = window.SS6 || {};
	SS6.symfonyToolbarSupport = SS6.symfonyToolbarSupport || {};

	SS6.symfonyToolbarSupport.onToolbarShowCallbacks = [];
	SS6.symfonyToolbarSupport.onToolbarHideCallbacks = [];

	SS6.symfonyToolbarSupport.registerOnToolbarShow = function (callback) {
		SS6.symfonyToolbarSupport.onToolbarShowCallbacks.push(callback);
	};

	SS6.symfonyToolbarSupport.registerOnToolbarHide = function (callback) {
		SS6.symfonyToolbarSupport.onToolbarHideCallbacks.push(callback);
	};

	SS6.symfonyToolbarSupport.notifyOnToolbarShow = function () {
		for (var i in SS6.symfonyToolbarSupport.onToolbarShowCallbacks) {
			var callback = SS6.symfonyToolbarSupport.onToolbarShowCallbacks[i];
			callback.call();
		}
	};

	SS6.symfonyToolbarSupport.notifyOnToolbarHide = function () {
		for (var i in SS6.symfonyToolbarSupport.onToolbarHideCallbacks) {
			var callback = SS6.symfonyToolbarSupport.onToolbarHideCallbacks[i];
			callback.call();
		}
	};

	$(document).ready(function () {
		$('.sf-toolbar').on('click', '[id^="sfMiniToolbar-"] > a', function () {
			SS6.symfonyToolbarSupport.notifyOnToolbarShow();
		});

		$('.sf-toolbar').on('click', '[id^="sfToolbarMainContent-"] > a.hide-button', function () {
			SS6.symfonyToolbarSupport.notifyOnToolbarHide();
		});

		// condition copied from: vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Profiler/toolbar_js.html.twig
		if (typeof Sfjs !== 'undefined' && Sfjs.getPreference('toolbar/displayState') !== 'none') {
			SS6.symfonyToolbarSupport.notifyOnToolbarShow();
		}
	});

})(jQuery);