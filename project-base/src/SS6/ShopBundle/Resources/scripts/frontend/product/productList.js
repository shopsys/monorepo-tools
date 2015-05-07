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

	SS6.productList.ajaxLoadMore = function () {
		$loadMoreButton = $('.js-load-more-button');
		$paginationResultData = $('.js-pagination-result-data');
		$loadMoreSpinner = $('.js-load-more-spinner');

		totalCount = $paginationResultData.data('total-count');
		pageSize = $paginationResultData.data('page-size');
		page = $paginationResultData.data('page');
		isXmlHttpRequest = false;

		SS6.productList.updateAjaxPaginationStatus();

		$loadMoreButton.on('click', function () {
			$(this).hide();
			$loadMoreSpinner.show();
			$.ajax({
				type: 'POST',
				url: document.location,
				data: {page: nextPage},
				success: function (data) {
					var $response = $($.parseHTML(data));
					var $currentProductList = $('.js-product-list');
					var nextProducts = $response.find('>li');
					$currentProductList.append(nextProducts);
					$loadMoreSpinner.hide();
					isXmlHttpRequest = true;
					SS6.productList.updateAjaxPaginationStatus();
				}
			});
		});
	};

	SS6.productList.updateAjaxPaginationStatus = function () {
		if (!isXmlHttpRequest) {
			nextPage = page + 1;
		} else {
			page = nextPage;
			nextPage++;
		}
		remaining = totalCount - page * pageSize;
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

	$(document).ready(function () {
		SS6.productList.init();
		SS6.productList.ajaxLoadMore();
	});

})(jQuery);
