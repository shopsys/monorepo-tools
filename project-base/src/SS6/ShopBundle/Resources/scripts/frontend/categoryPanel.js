(function ($) {

	SS6 = window.SS6 || {};
	SS6.categoryPanel = SS6.categoryPanel || {};

	SS6.categoryPanel.init = function ($container) {
		$container.filterAllNodes('.js-category-collapse-control')
			.on('click', onCategoryCollapseControlClick);
	};

	function onCategoryCollapseControlClick(event) {
		event.stopPropagation();
		event.preventDefault();

		var $categoryCollapseControl = $(this);
		var $categoryItem = $categoryCollapseControl.closest('.js-category-item');
		var $categoryList = $categoryItem.find('.js-category-list').first();
		var isOpen = $categoryCollapseControl.hasClass('active');

		if (isOpen) {
			$categoryList.slideUp('fast');
		} else if ($categoryList.length > 0) {
			$categoryList.slideDown('fast');
		} else {
			loadCategoryItemContent($categoryItem, $categoryCollapseControl.data('url'))
		}

		$categoryCollapseControl.toggleClass('active', !isOpen);
	}

	function loadCategoryItemContent($categoryItem, url) {
		SS6.ajax({
			loaderElement: $categoryItem,
			url: url,
			dataType: 'html',
			success: function (data) {
				var $categoryListPlaceholder = $categoryItem.find('.js-category-list-placeholder');
				var $categoryList = $($.parseHTML(data));

				$categoryListPlaceholder.replaceWith($categoryList);
				$categoryList.hide().slideDown('fast');

				SS6.register.registerNewContent($categoryList);
			}
		});
	}

	SS6.register.registerCallback(SS6.categoryPanel.init);

})(jQuery);
