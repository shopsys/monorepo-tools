(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.search = Shopsys.search || {};
    Shopsys.search.autocomplete = Shopsys.search.autocomplete || {};

    var options = {
        minLength: 3,
        requestDelay: 200
    };

    var $input = null;
    var $searchAutocompleteResults = null;
    var requestTimer = null;
    var resultExists = false;
    var searchDataCache = {};

    Shopsys.search.autocomplete.init = function () {
        $input = $('#js-search-autocomplete-input');
        $searchAutocompleteResults = $('#js-search-autocomplete-results');

        $input.bind('keyup paste', Shopsys.search.autocomplete.onInputChange);
        $input.bind('focus', function () {
            if (resultExists) {
                $searchAutocompleteResults.show();
            }
        });

        $(document).click(Shopsys.search.onDocumentClickHideAutocompleteResults);
    };

    Shopsys.search.autocomplete.onInputChange = function (event) {
        clearTimeout(requestTimer);

        // on "paste" event the $input.val() is not updated with new value yet,
        // therefore call of search() method is scheduled for later
        requestTimer = setTimeout(Shopsys.search.autocomplete.search, options.requestDelay);

        // do not propagate change events
        // (except "paste" event that must be propagated otherwise the value is not pasted)
        if (event.type !== 'paste') {
            return false;
        }
    };

    Shopsys.search.onDocumentClickHideAutocompleteResults = function (event) {
        var $autocompleteElements = $input.add($searchAutocompleteResults);
        if (resultExists && $(event.target).closest($autocompleteElements).length === 0) {
            $searchAutocompleteResults.hide();
        }
    };

    Shopsys.search.autocomplete.search = function () {
        var searchText = $input.val();

        if (searchText.length >= options.minLength) {
            if (searchDataCache[searchText] !== undefined) {
                Shopsys.search.autocomplete.showResult(searchDataCache[searchText]);
            } else {
                Shopsys.search.autocomplete.searchRequest(searchText);
            }
        } else {
            resultExists = false;
            $searchAutocompleteResults.hide();
        }
    };

    Shopsys.search.autocomplete.searchRequest = function (searchText) {
        Shopsys.ajaxPendingCall('Shopsys.search.autocomplete.searchRequest', {
            loaderElement: null,
            url: $input.data('autocomplete-url'),
            type: 'post',
            dataType: 'html',
            data: {
                searchText: searchText
            },
            success: function (responseHtml) {
                searchDataCache[searchText] = responseHtml;
                Shopsys.search.autocomplete.showResult(responseHtml);
            }
        });
    };

    Shopsys.search.autocomplete.showResult = function (responseHtml) {
        var $response = $($.parseHTML(responseHtml));

        resultExists = $response.find('li').length > 0;

        if (resultExists) {
            $searchAutocompleteResults.show();
        } else {
            $searchAutocompleteResults.hide();
        }

        $searchAutocompleteResults.html(responseHtml);
    };

    $(document).ready(function () {
        Shopsys.search.autocomplete.init();
    });

})(jQuery);
