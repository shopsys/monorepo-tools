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
	var resultExists = false;
	var searchDataCache = {};

	SS6.search.autocomplete.init = function () {
		$input = $('#js-search-autocomplete-input');
		$label = $('#js-search-autocomplete-label');
		$list = $('#js-search-autocomplete-list');
		$listItemTemplate = $($.parseHTML($list.data('item-template')));

		$input.bind('keyup paste', SS6.search.autocomplete.onInputChange);
		$input.bind('focus', function () {
			if (resultExists) {
				$list.show();
			}
		});

		$(document).click(function(event) {
			if($(event.target).closest('#js-search-autocomplete').length === 0) {
				$list.hide();
			}
		});
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
			$list.hide();
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
		resultExists = true;
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

