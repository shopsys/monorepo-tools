(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};
	SS6.productList.AjaxMoreLoader = SS6.productList.AjaxMoreLoader || {};

	SS6.productList.AjaxMoreLoader = function () {
		var self = this;
		var $loadMoreButton;
		var $loadMoreSpinner;
		var $currentProductList;
		var $paginationToItemSpan;

		var totalCount;
		var pageSize;
		var page;
		var paginationToItem;

		this.init = function () {
			$loadMoreButton = $('.js-load-more-button');
			$loadMoreSpinner = $('.js-load-more-spinner');
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
			$loadMoreSpinner.show();
			$.ajax({
				type: 'GET',
				url: document.location,
				data: {page: page + 1},
				success: function (data) {
					var $response = $($.parseHTML(data));
					var $nextProducts = $response.find('.js-product-list > li');
					$currentProductList.append($nextProducts);
					$loadMoreSpinner.hide();
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
				'[1,4]Načíst další %loadNextCount% zboží|[5,Inf]Načíst dalších %loadNextCount% zboží',
				loadNextCount,
				{'%loadNextCount%': loadNextCount}
			);

			$loadMoreButton
				.val(buttonText)
				.toggle(remaining > 0);
		};

	};

})(jQuery);
