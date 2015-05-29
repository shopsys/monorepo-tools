(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxFilter = SS6.productList.AjaxFilter || {};

	SS6.productList.AjaxFilter = function (ajaxMoreLoader) {
		var $productsWithControls = $('.js-product-list-ajax-filter-products-with-controls');
		var $productFilterForm = $('form[name="productFilter"]');
		var $showResultsButton = $('.js-product-filter-show-result-button');
		var $resetFilterButton = $('.js-product-filter-reset-button');

		this.init = function () {
			$productFilterForm.change(function () {
				$productsWithControls.addClass('js-disable');
				submitFormWithAjax($productFilterForm.serialize());
			});

			$showResultsButton.click(function () {
				var $productList = $('.js-product-list');
				$('html, body').animate({ scrollTop: $productList.offset().top }, 'slow');
				return false;
			});

			$resetFilterButton.click(function () {
				$productsWithControls.addClass('js-disable');
				$productFilterForm[0].reset();
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
					ajaxMoreLoader.reInit();
					// TODO: temporal solution, US-537 should fix this
					$productsWithControls.find('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
					SS6.productList.init();
				}
			});
		};

	};

})(jQuery);
