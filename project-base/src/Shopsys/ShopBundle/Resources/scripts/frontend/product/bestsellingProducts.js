(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.bestsellingProducts = Shopsys.bestsellingProducts || {};

	Shopsys.bestsellingProducts.init = function () {
		$('.js-bestselling-products-load-more').click(function () {
			var $loadMoreButton = $(this);
			var $loadMoreItems = $loadMoreButton.closest('.js-bestselling-products').find('.js-bestselling-product');
			$loadMoreItems.slideDown('fast');
			$loadMoreButton.closest('.js-bestselling-products-load-more-container').slideUp('fast');
		});
	};

	$(document).ready(function () {
		Shopsys.bestsellingProducts.init();
	});

})(jQuery);
