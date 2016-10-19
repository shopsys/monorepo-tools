(function ($) {

	SS6 = window.SS6 || {};
	SS6.history = SS6.history || {};

	SS6.history.pushReloadState = function (url, title, stateObject) {
		if (title === undefined) {
			title = '';
		}

		if (stateObject === undefined) {
			stateObject = {};
		}
		stateObject.refreshOnPopstate = true;

		history.pushState(stateObject, title, url);
	};

	$(window).on('popstate', function (event) {
		var state = event.originalEvent.state;
		if (state.hasOwnProperty('refreshOnPopstate') && state.refreshOnPopstate === true) {
			location.reload();
		}
	});

})(jQuery);