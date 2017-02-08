(function ($) {

	SS6 = window.SS6 || {};
	SS6.register = SS6.register || {};

	SS6.register.CALL_PRIORITY_NORMAL = 500;
	SS6.register.CALL_PRIORITY_HIGH = 300;

	var callbackQueue = [];

	SS6.register.registerCallback = function (callback, callPriority) {
		if (callPriority === undefined) {
			callPriority = SS6.register.CALL_PRIORITY_NORMAL;
		}

		callbackQueue.push({
			callback: callback,
			callPriority: callPriority
		});
	};

	SS6.register.registerNewContent = function ($container) {
		callbackQueue.sort(function (a, b) {
			return a.callPriority - b.callPriority;
		});

		for (var i in callbackQueue) {
			callbackQueue[i].callback($container);
		}
	};

	$(document).ready(function () {
		SS6.register.registerNewContent($('body'));
	});

})(jQuery);
