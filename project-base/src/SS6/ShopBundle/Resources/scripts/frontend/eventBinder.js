(function ($) {

	SS6 = window.SS6 || {};
	SS6.eventBinder = SS6.eventBinder || {};

	var callbacks = [];

	SS6.eventBinder.registerCallback = function (callback) {
		callbacks.push(callback);
	};

	SS6.eventBinder.init = function ($container) {
		for (var i in callbacks) {
			callbacks[i]($container);
		}
	};

	$(document).ready(function () {
		SS6.eventBinder.init($('body'));
	});

})(jQuery);
