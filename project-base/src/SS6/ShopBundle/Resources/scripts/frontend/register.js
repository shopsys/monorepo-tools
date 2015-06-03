(function ($) {

	SS6 = window.SS6 || {};
	SS6.register = SS6.register || {};

	var callbacks = [];

	SS6.register.registerCallback = function (callback) {
		callbacks.push(callback);
	};

	SS6.register.registerNewContent = function ($container) {
		for (var i in callbacks) {
			callbacks[i]($container);
		}
	};

	$(document).ready(function () {
		SS6.register.registerNewContent($('body'));
	});

})(jQuery);
