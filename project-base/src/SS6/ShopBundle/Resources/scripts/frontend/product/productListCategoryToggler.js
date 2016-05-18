(function ($) {

	SS6 = window.SS6 || {};
	SS6.categoryDescription = SS6.categoryDescription || {};

	SS6.categoryDescription.init = function () {
		var $description = $('.js-category-description');
		var $loadMoreButton = $('.js-category-description-load-more');
		var descriptionHeight = $description.height();

		if (descriptionHeight > 32) {
			$loadMoreButton.show();
			$description.addClass('box-list__description__text--small');
		}

		$loadMoreButton.click(function () {
			$description.removeClass('box-list__description__text--small');
			$loadMoreButton.closest('.js-category-description-load-more').hide();
		});
	};

	$(document).ready(function () {
		SS6.categoryDescription.init();
	});

})(jQuery);

