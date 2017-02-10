(function ($) {

    Shopsys = window.Shopsys || {};

    var ajaxPendingCalls = {};

    Shopsys.ajax = function (options) {
        var loaderOverlayTimeout;
        var defaults = {
            loaderElement: undefined,
            loaderMessage: undefined,
            overlayDelay: 200,
            error: showDefaultError,
            complete: function () {}
        };
        options = $.extend(defaults, options);
        var userCompleteCallback = options.complete;
        var $loaderOverlay = Shopsys.loaderOverlay.createLoaderOverlay(options.loaderElement, options.loaderMessage);
        var userErrorCallback = options.error;

        options.complete = function (jqXHR, textStatus) {
            userCompleteCallback.apply(this, [jqXHR, textStatus]);
            clearTimeout(loaderOverlayTimeout);
            Shopsys.loaderOverlay.removeLoaderOverlay($loaderOverlay);
        };

        options.error = function (jqXHR) {
            // on FireFox abort ajax request, but request was probably successful
            if (jqXHR.status !== 0) {
                userErrorCallback.apply(this, [jqXHR]);
            }
        };

        loaderOverlayTimeout = setTimeout(function () {
            Shopsys.loaderOverlay.showLoaderOverlay($loaderOverlay);
        }, options.overlayDelay);
        $.ajax(options);
    };

    var showDefaultError = function () {
        Shopsys.window({
            content: Shopsys.translator.trans('Error occurred, try again please.')
        });
    };

    /**
     * Calls ajax with provided options. If ajax call with the same name is already running, the new ajax call is created as pending.
     * After completion of the ajax call only last pending call with the same name is called.
     * @param {string} pendingCallName
     * @param {object} options
     */
    Shopsys.ajaxPendingCall = function (pendingCallName, options) {
        if (typeof pendingCallName !== 'string') {
            throw 'Ajax queued call must have name!';
        }
        var userCompleteCallback = options.hasOwnProperty('complete') ? options.complete : null;

        options.complete = function (jqXHR, textStatus) {
            if (userCompleteCallback !== null) {
                userCompleteCallback.apply(this, [jqXHR, textStatus]);
            }

            if (ajaxPendingCalls.hasOwnProperty(pendingCallName) === true) {
                if (ajaxPendingCalls[pendingCallName].isPending === true) {
                    ajaxPendingCalls[pendingCallName].isPending = false;
                    Shopsys.ajax(ajaxPendingCalls[pendingCallName].options);
                } else {
                    delete ajaxPendingCalls[pendingCallName];
                }
            }
        };

        var callImmediately = ajaxPendingCalls.hasOwnProperty(pendingCallName) === false;

        ajaxPendingCalls[pendingCallName] = {
            isPending: true,
            options: options
        };

        if (callImmediately) {
            ajaxPendingCalls[pendingCallName].isPending = false;
            Shopsys.ajax(options);
        }
    };

})(jQuery);