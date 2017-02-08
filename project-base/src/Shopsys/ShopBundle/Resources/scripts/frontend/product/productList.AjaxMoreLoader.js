(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxMoreLoader = SS6.productList.AjaxMoreLoader || {};

	SS6.productList.AjaxMoreLoader = function () {
		var self = this;
		var $loadMoreButton;
		var $currentProductList;
		var $paginationToItemSpan;

		var totalCount;
		var pageSize;
		var page;
		var paginationToItem;

		this.init = function () {
			$loadMoreButton = $('.js-load-more-button');
			$currentProductList = $('.js-product-list');
			$paginationToItemSpan = $('.js-pagination-to-item');

			totalCount = $loadMoreButton.data('total-count');
			pageSize = $loadMoreButton.data('page-size');
			page = $loadMoreButton.data('page');
			paginationToItem = $loadMoreButton.data('pagination-to-item');

			updateLoadMoreButton();
			$loadMoreButton.on('click', onClickLoadMoreButton);
		};

		this.reInit = function () {
			self.init();
		};

		var onClickLoadMoreButton = function () {
			$(this).hide();
			SS6.ajax({
				loaderElement: '.js-product-list-with-paginator',
				type: 'GET',
				url: document.location,
				data: {page: page + 1},
				success: function (data) {
					var $response = $($.parseHTML(data));
					var $nextProducts = $response.find('.js-product-list > li');
					$currentProductList.append($nextProducts);
					page++;
					paginationToItem += $nextProducts.length;
					$paginationToItemSpan.text(paginationToItem);
					updateLoadMoreButton();

					SS6.register.registerNewContent($nextProducts);
				}
			});
		};

		var updateLoadMoreButton = function () {
			var remaining = totalCount - page * pageSize;
			var loadNextCount = remaining >= pageSize ? pageSize : remaining;
			var buttonText = SS6.translator.transChoice(
				'{1}Load next %loadNextCount% product|[2,Inf]Load next %loadNextCount% products',
				loadNextCount,
				{'%loadNextCount%': loadNextCount}
			);

			$loadMoreButton
				.val(buttonText)
				.toggle(remaining > 0);
		};

	};

})(jQuery);
