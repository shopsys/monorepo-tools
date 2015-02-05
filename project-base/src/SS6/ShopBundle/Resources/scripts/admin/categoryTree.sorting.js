(function ($){

	SS6 = SS6 || {};
	SS6.categoryTree = SS6.categoryTree || {};
	SS6.categoryTree.sorting = SS6.categoryTree.sorting || {};

	SS6.categoryTree.sorting.init = function () {
		$('#js-category-tree-sorting > .js-category-tree-sorting-items').nestedSortable({
			listType: 'ul',
			handle: '.js-category-tree-sorting-item-handle',
			items: '.js-category-tree-sorting-item',
			placeholder: 'js-category-tree-sorting-placeholder',
			toleranceElement: '> .js-category-tree-sorting-item-line',
			forcePlaceholderSize: true,
			helper:	'clone',
			opacity: 0.6,
			revert: 100
		});
	};

	$(document).ready(function () {
		SS6.categoryTree.sorting.init();
	});

})(jQuery);
