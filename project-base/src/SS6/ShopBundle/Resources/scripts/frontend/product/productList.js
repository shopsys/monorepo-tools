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

	SS6.productList.AjaxLoading = function () {
		var $loadMoreButton = $('.js-load-more-button');
		var $loadMoreSpinner = $('.js-load-more-spinner');
		var $currentProductList = $('.js-product-list');

		var totalCount = $loadMoreButton.data('total-count');
		var pageSize = $loadMoreButton.data('page-size');
		var page = $loadMoreButton.data('page');
		var nextPage = page + 1;

		this.init = function () {
			updateAjaxPaginationStatus();
			$loadMoreButton.on('click', onClickLoadMoreButton);
		};

		var onClickLoadMoreButton = function () {
			$(this).hide();
			$loadMoreSpinner.show();
			$.ajax({
				type: 'POST',
				url: document.location,
				data: {page: nextPage},
				success: function (data) {
					var $response = $($.parseHTML(data));
					var nextProducts = $response.find('>li');
					$currentProductList.append(nextProducts);
					$loadMoreSpinner.hide();
					page = nextPage;
					nextPage++;
					updateAjaxPaginationStatus();
				}
			});
		};

		var updateAjaxPaginationStatus = function () {
			var remaining = totalCount - page * pageSize;
			if (remaining > 0 && remaining < pageSize) {
				$loadMoreButton.val(SS6.translator.trans('Načíst dalších %remaining% zboží', {'%remaining%': remaining})).show();
			}
			if (remaining > pageSize) {
				$loadMoreButton.val(SS6.translator.trans('Načíst dalších %remaining% zboží', {'%remaining%': pageSize})).show();
			}
			if (remaining <= 0) {
				$loadMoreButton.hide();
			}
		};
	};

	$(document).ready(function () {
		SS6.productList.init();
		var ajaxLoading = new SS6.productList.AjaxLoading();
		ajaxLoading.init();
	});

})(jQuery);
