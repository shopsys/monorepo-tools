(function ($) {

	SS6 = window.SS6 || {};
	SS6.search = SS6.search || {};
	SS6.search.autocomplete = SS6.search.autocomplete || {};

	var options = {
		minLength: 3,
		requestDelay: 200
	};

	var $input = null;
	var $label = null;
	var $list = null;
	var $listItemTemplate = null;
	var requestTimer = null;
	var existResult = false;
	var searchDataCache = {};

	SS6.search.autocomplete.init = function () {
		$input = $('#js-search-autocomplete-input');
		$label = $('#js-search-autocomplete-label');
		$list = $('#js-search-autocomplete-list');
		$listItemTemplate = $($.parseHTML($list.data('item-template')));

		$input.bind('keyup paste', SS6.search.autocomplete.onInputChange);
		$input.bind('focus', function () {
			if (existResult) {
				$list.show();
			}
		});

		$input.closest('form').submit(function () {
			return false;
		});

		$(document).click(function(event) {
			if(!$(event.target).closest('#js-search-autocomplete').length) {
				$list.hide();
			}
		});
	};

	SS6.search.autocomplete.onInputChange = function(event) {
		clearTimeout(requestTimer);
		// $input.val() is not modified on paste event, value.length will check in search() after delay
		requestTimer = setTimeout(SS6.search.autocomplete.search, options.requestDelay);

		if ($input.val().length < options.minLength) {
			existResult = false;
			$list.hide();
		}
		if (event.type !== 'paste') {
			return false;
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
		}
	};

	SS6.search.autocomplete.searchRequest = function (searchText) {
		$.ajax({
			url: $input.data('autocomplete-url'),
			type: 'post',
			dataType: 'json',
			data: {
				searchText: searchText
			},
			success: function (responseData) {
				searchDataCache[searchText] = responseData;
				SS6.search.autocomplete.showResult(responseData);
			}
		});
	};

	SS6.search.autocomplete.showResult = function(responseData) {
		existResult = true;
		$label.text(responseData.label);

		$list.find('.js-search-autocomplete-item').remove();
		$.each(responseData.products, function (key, productData) {
			var $listItem = $listItemTemplate.clone();
			$listItem.find('.js-search-autocomplete-item-label').text(productData.name);
			$listItem.find('.js-search-autocomplete-item-link').attr('href', productData.url);
			$listItem.find('.js-search-autocomplete-item-image').attr('src', productData.imageUrl);

			$listItem.appendTo($list);
		});

		$list.show();
	};

	$(document).ready(function () {
		SS6.search.autocomplete.init();
	});

})(jQuery);

