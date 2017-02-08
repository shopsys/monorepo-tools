(function ($) {

	SS6 = window.SS6 || {};
	SS6.search = SS6.search || {};
	SS6.search.autocomplete = SS6.search.autocomplete || {};

	var options = {
		minLength: 3,
		requestDelay: 200
	};

	var $input = null;
	var $searchAutocompleteResults = null;
	var requestTimer = null;
	var resultExists = false;
	var searchDataCache = {};

	SS6.search.autocomplete.init = function () {
		$input = $('#js-search-autocomplete-input');
		$searchAutocompleteResults = $('#js-search-autocomplete-results');

		$input.bind('keyup paste', SS6.search.autocomplete.onInputChange);
		$input.bind('focus', function () {
			if (resultExists) {
				$searchAutocompleteResults.show();
			}
		});

		$(document).click(SS6.search.onDocumentClickHideAutocompleteResults);
	};

	SS6.search.autocomplete.onInputChange = function(event) {
		clearTimeout(requestTimer);

		// on "paste" event the $input.val() is not updated with new value yet,
		// therefore call of search() method is scheduled for later
		requestTimer = setTimeout(SS6.search.autocomplete.search, options.requestDelay);

		// do not propagate change events
		// (except "paste" event that must be propagated otherwise the value is not pasted)
		if (event.type !== 'paste') {
			return false;
		}
	};

	SS6.search.onDocumentClickHideAutocompleteResults = function (event) {
		var $autocompleteElements = $input.add($searchAutocompleteResults);
		if (resultExists && $(event.target).closest($autocompleteElements).length === 0) {
			$searchAutocompleteResults.hide();
		}
	};

	SS6.search.autocomplete.search = function () {
		var searchText = $input.val();

		if (searchText.length >= options.minLength) {
			if (searchDataCache[searchText] !== undefined) {
				SS6.search.autocomplete.showResult(searchDataCache[searchText]);
			} else {
				SS6.search.autocomplete.searchRequest(searchText);
			}
		} else {
			resultExists = false;
			$searchAutocompleteResults.hide();
		}
	};

	SS6.search.autocomplete.searchRequest = function (searchText) {
		SS6.ajaxPendingCall('SS6.search.autocomplete.searchRequest', {
			loaderElement: null,
			url: $input.data('autocomplete-url'),
			type: 'post',
			dataType: 'html',
			data: {
				searchText: searchText
			},
			success: function (responseHtml) {
				searchDataCache[searchText] = responseHtml;
				SS6.search.autocomplete.showResult(responseHtml);
			}
		});
	};

	SS6.search.autocomplete.showResult = function(responseHtml) {
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
		SS6.search.autocomplete.init();
	});

})(jQuery);

