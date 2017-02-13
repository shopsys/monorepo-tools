(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.history = Shopsys.history || {};

    Shopsys.history.pushReloadState = function (url, title, stateObject) {
        var currentState = history.state || {};
        if (!currentState.hasOwnProperty('refreshOnPopstate') || currentState.refreshOnPopstate !== true) {
            currentState.refreshOnPopstate = true;
            history.replaceState(currentState, document.title, location.href);
        }

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