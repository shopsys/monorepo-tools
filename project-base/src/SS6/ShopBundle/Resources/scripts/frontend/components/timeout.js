(function() {

	SS6 = window.SS6 || {};
	SS6.timeout = window.SS6.timeout || {};

	var timeouts = {};

	/**
	 * @param {string} timeoutName
	 * @param {callback} callback
	 * @param {int} timeoutMilliseconds
	 * @returns {void}
	 */
	SS6.timeout.setTimeoutAndClearPrevious = function(timeoutName, callback, timeoutMilliseconds) {
		if (typeof timeoutName !== 'string') {
			throw 'Timeout must have name!';
		}

		if (timeouts.hasOwnProperty(timeoutName) === true) {
			clearTimeout(timeouts[timeoutName]);
		}

		timeouts[timeoutName] = setTimeout(callback, timeoutMilliseconds);
	};

})();