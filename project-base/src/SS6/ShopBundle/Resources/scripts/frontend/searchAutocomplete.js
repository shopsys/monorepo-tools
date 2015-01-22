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

		$(document).click(function(event) {
			if(!$(event.target).closest('#js-search-autocomplete').length) {
				$list.hide();
			}
		});
	};

	SS6.search.autocomplete.onInputChange = function(event) {
		clearTimeout(requestTimer);
		// $input.val() is not modified on paste event, value.length will check in makeRequest() after delay
		requestTimer = setTimeout(SS6.search.autocomplete.makeRequest, options.requestDelay);

		if ($input.val().length < options.minLength) {
			existResult = false;
			$list.hide();
		}
		if (event.type !== 'paste') {
			return false;
		}
	};

	SS6.search.autocomplete.makeRequest = function () {
		if ($input.val().length >= options.minLength) {
			$.ajax({
				url: $input.data('autocomplete-url'),
				type: 'post',
				dataType: 'json',
				data: {
					searchText: $input.val()
				},
				success: SS6.search.autocomplete.showResult
			});
		}
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

