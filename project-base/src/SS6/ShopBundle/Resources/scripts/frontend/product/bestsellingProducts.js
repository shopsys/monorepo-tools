(function ($) {

	SS6 = window.SS6 || {};
	SS6.bestsellingProducts = SS6.bestsellingProducts || {};

	SS6.bestsellingProducts.init = function () {
		$('.js-bestselling-products-load-more').click(function () {
			var $loadMoreButton = $(this);
			var $loadMoreItems = $loadMoreButton.closest('.js-bestselling-products').find('.js-bestselling-product');
			$loadMoreItems.slideDown('fast');
			$loadMoreButton.closest('.js-bestselling-products-load-more-container').slideUp('fast');
		});
	};

	$(document).ready(function () {
		SS6.bestsellingProducts.init();
	});

})(jQuery);
