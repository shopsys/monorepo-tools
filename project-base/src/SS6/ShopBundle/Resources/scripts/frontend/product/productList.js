(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};

	SS6.productList.init = function () {
		$('.js-productListOrderingMode').change(function () {
			var cookieName = $(this).data('cookie-name');

			$.cookie(cookieName, $(this).val(), { path: '/' });

			location.reload(true);
		});
	};

	SS6.productList.AjaxMoreLoader = function () {
		var $loadMoreButton = $('.js-load-more-button');
		var $loadMoreSpinner = $('.js-load-more-spinner');
		var $currentProductList = $('.js-product-list');
		var $paginationToItemSpan = $('.js-pagination-to-item');

		var totalCount = $loadMoreButton.data('total-count');
		var pageSize = $loadMoreButton.data('page-size');
		var page = $loadMoreButton.data('page');
		var paginationToItem = $loadMoreButton.data('pagination-to-item');

		this.init = function () {
			updateLoadMoreButton();
			$loadMoreButton.on('click', onClickLoadMoreButton);
		};

		var onClickLoadMoreButton = function () {
			$(this).hide();
			$loadMoreSpinner.show();
			$.ajax({
				type: 'POST',
				url: document.location,
				data: {page: page + 1},
				success: function (data) {
					var $response = $($.parseHTML(data));
					var $nextProducts = $response.find('>li');
					$currentProductList.append($nextProducts);
					$loadMoreSpinner.hide();
					page++;
					paginationToItem += $nextProducts.length;
					$paginationToItemSpan.text(paginationToItem);
					updateLoadMoreButton();

					// TODO: temporal solution, US-537 should fix this
					$nextProducts.find('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
				}
			});
		};

		var updateLoadMoreButton = function () {
			var remaining = totalCount - page * pageSize;
			if (remaining > 0 && remaining < pageSize) {
				$loadMoreButton.val(SS6.translator.trans('Načíst dalších %remaining% zboží', {'%remaining%': remaining})).show();
			} else if (remaining > pageSize) {
				$loadMoreButton.val(SS6.translator.trans('Načíst dalších %remaining% zboží', {'%remaining%': pageSize})).show();
			} else if (remaining <= 0) {
				$loadMoreButton.hide();
			}
		};
	};

	$(document).ready(function () {
		SS6.productList.init();
		var ajaxMoreLoader = new SS6.productList.AjaxMoreLoader();
		ajaxMoreLoader.init();
	});

})(jQuery);
