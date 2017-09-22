(function () {

    Shopsys = window.Shopsys || {};
    Shopsys.timeout = window.Shopsys.timeout || {};

    var timeouts = {};

    /**
     * @param {string} timeoutName
     * @param {callback} callback
     * @param {int} timeoutMilliseconds
     * @returns {void}
     */
    Shopsys.timeout.setTimeoutAndClearPrevious = function (timeoutName, callback, timeoutMilliseconds) {
        if (typeof timeoutName !== 'string') {
            throw new Error('Timeout must have name!');
        }

        if (timeouts.hasOwnProperty(timeoutName) === true) {
            clearTimeout(timeouts[timeoutName]);
        }

        timeouts[timeoutName] = setTimeout(callback, timeoutMilliseconds);
    };

})();
