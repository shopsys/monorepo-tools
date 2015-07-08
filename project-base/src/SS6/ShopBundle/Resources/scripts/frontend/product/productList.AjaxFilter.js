(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxFilter = SS6.productList.AjaxFilter || {};

	SS6.productList.AjaxFilter = function (ajaxMoreLoader) {
		var $productsWithControls = $('.js-product-list-ajax-filter-products-with-controls');
		var $productFilterForm = $('form[name="productFilter_form"]');
		var $showResultsButton = $('.js-product-filter-show-result-button');
		var $resetFilterButton = $('.js-product-filter-reset-button');
		var requestTimer = null;
		var requestDelay = 1000;

		this.init = function () {
			$productFilterForm.change(function () {
				clearTimeout(requestTimer);
				requestTimer = setTimeout(submitFormWithAjax, requestDelay);
				history.replaceState({}, '', SS6.url.getBaseUrl() + '?' + $productFilterForm.serialize());
			});

			$showResultsButton.click(function () {
				var $productList = $('.js-product-list');
				$('html, body').animate({ scrollTop: $productList.offset().top }, 'slow');
				return false;
			});

			$resetFilterButton.click(function () {
				$productFilterForm
					.find(':radio, :checkbox').removeAttr('checked').end()
					.find('textarea, :text, select').val('');
				var resetUrl = $(this).attr('href');
				history.replaceState({}, '', resetUrl);
				submitFormWithAjax();
				return false;
			});

			updateFiltersDisabled();
		};

		var showProducts = function ($wrappedData) {
			var $productsHtml = $wrappedData.find('.js-product-list-ajax-filter-products-with-controls');
			$productsWithControls.html($productsHtml);
			$productsWithControls.show();
			$productsWithControls.removeClass('js-disable');
			ajaxMoreLoader.reInit();
			SS6.register.registerNewContent($productsWithControls);
		};

		var updateFiltersCounts = function ($wrappedData) {
			var $existingCountElements = $('.js-product-filter-count');
			var $newCountElements = $wrappedData.find('.js-product-filter-count');

			$newCountElements.each(function () {
				var $newCountElement = $(this);

				var $existingCountElement = $existingCountElements
					.filter('[data-form-id="' + $newCountElement.data('form-id') + '"]');

				$existingCountElement.html($newCountElement.html());
			});
		};

		var updateFiltersDisabled = function () {
			$('.js-product-filter-count').each(function () {
				var $countElement = $(this);

				var $label = $countElement.closest('label');
				var $formElement = $('#' + $countElement.data('form-id'));

				if (willFilterZeroProducts($countElement)) {
					if (!$formElement.is(':checked')) {
						$label.addClass('js-disable');
						$formElement.prop('disabled', true);
					}
				} else {
					$label.removeClass('js-disable');
					$formElement.prop('disabled', false);
				}
			});
		};

		var willFilterZeroProducts = function ($countElement) {
			return $countElement.html().indexOf('(0)') !== -1;
		};

		var submitFormWithAjax = function () {
			$productsWithControls.addClass('js-disable');
			$('.js-product-list-ajax-filter-loading').show();
			$.ajax({
				url: SS6.url.getBaseUrl(),
				data: $productFilterForm.serialize(),
				success: function (data) {
					var $wrappedData = $($.parseHTML('<div>' + data + '</div>'));

					showProducts($wrappedData);
					updateFiltersCounts($wrappedData);
					updateFiltersDisabled();
					$('.js-product-list-ajax-filter-loading').hide();
				}
			});
		};

	};

})(jQuery);
