(function ($) {

	SS6 = window.SS6 || {};
	SS6.categoryDescription = SS6.categoryDescription || {};

	SS6.categoryDescription.init = function () {
		var $description = $('.js-category-description');
		var $loadMoreButton = $('.js-category-description-load-more');
		var descriptionHeight = $description.height();

		if (descriptionHeight > 32) {
			$loadMoreButton.show();
			$description.addClass('shortened-category-description');
		}

		$loadMoreButton.click(function () {
			$description.removeClass('shortened-category-description');
			$loadMoreButton.closest('.js-category-description-load-more').hide();
		});
	};

	$(document).ready(function () {
		SS6.categoryDescription.init();
	});

})(jQuery);

