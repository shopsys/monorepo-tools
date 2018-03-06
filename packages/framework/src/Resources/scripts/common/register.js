(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.register = Shopsys.register || {};

    Shopsys.register.CALL_PRIORITY_NORMAL = 500;
    Shopsys.register.CALL_PRIORITY_HIGH = 300;

    var callbackQueue = [];

    Shopsys.register.registerCallback = function (callback, callPriority) {
        if (callPriority === undefined) {
            callPriority = Shopsys.register.CALL_PRIORITY_NORMAL;
        }

        callbackQueue.push({
            callback: callback,
            callPriority: callPriority
        });
    };

    Shopsys.register.registerNewContent = function ($container) {
        callbackQueue.sort(function (a, b) {
            return a.callPriority - b.callPriority;
        });

        for (var i in callbackQueue) {
            callbackQueue[i].callback($container);
        }
    };

    $(document).ready(function () {
        Shopsys.register.registerNewContent($('body'));
    });

})(jQuery);
