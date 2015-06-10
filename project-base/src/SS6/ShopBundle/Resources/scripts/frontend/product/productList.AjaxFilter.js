(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxFilter = SS6.productList.AjaxFilter || {};

	SS6.productList.AjaxFilter = function (ajaxMoreLoader) {
		var $productsWithControls = $('.js-product-list-ajax-filter-products-with-controls');
		var $productFilterForm = $('form[name="productFilter_form"]');
		var $showResultsButton = $('.js-product-filter-show-result-button');
		var $resetFilterButton = $('.js-product-filter-reset-button');

		this.init = function () {
			$productFilterForm.change(function () {
				$productsWithControls.addClass('js-disable');
				$('.js-product-list-ajax-filter-loading').show();
				history.replaceState({}, '', SS6.url.getBaseUrl() + '?' + $productFilterForm.serialize());
				submitFormWithAjax($productFilterForm.serialize());
			});

			$showResultsButton.click(function () {
				var $productList = $('.js-product-list');
				$('html, body').animate({ scrollTop: $productList.offset().top }, 'slow');
				return false;
			});

			$resetFilterButton.click(function () {
				$productsWithControls.addClass('js-disable');
				$('.js-product-list-ajax-filter-loading').show();
				$productFilterForm
					.find(':radio, :checkbox').removeAttr('checked').end()
					.find('textarea, :text, select').val('');
				var resetUrl = $(this).attr('href');
				history.replaceState({}, '', resetUrl);
				submitFormWithAjax();
				return false;
			});
		};

		var submitFormWithAjax = function (submitData) {
			$.ajax({
				url: document.location,
				data: submitData,
				success: function (data) {
					$productsWithControls.html(data);
					$productsWithControls.show();
					$productsWithControls.removeClass('js-disable');
					$('.js-product-list-ajax-filter-loading').hide();
					ajaxMoreLoader.reInit();
					SS6.register.registerNewContent($productsWithControls);
				}
			});
		};

	};

})(jQuery);
