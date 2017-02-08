(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.categoryDescription = Shopsys.categoryDescription || {};

	Shopsys.categoryDescription.init = function () {
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
		Shopsys.categoryDescription.init();
	});

})(jQuery);

