(function ($) {

	SS6 = window.SS6 || {};
	SS6.bestsellingProducts = SS6.bestsellingProducts || {};

	SS6.bestsellingProducts.init = function () {
		$('.js-bestsellingProductsLoadMore').click(function () {
			var $loadMoreButton = $(this);
			var $loadMoreItems = $loadMoreButton.closest('.js-bestsellingProducts').find('.js-bestsellingProduct');
			$loadMoreItems.slideDown('fast');
			$loadMoreButton.closest('.js-bestsellingProductsLoadMoreContainer').slideUp('fast');
		});
	};

	$(document).ready(function () {
		SS6.bestsellingProducts.init();
	});

})(jQuery);
