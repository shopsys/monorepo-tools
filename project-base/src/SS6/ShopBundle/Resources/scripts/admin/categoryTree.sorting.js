(function ($){

	SS6 = SS6 || {};
	SS6.categoryTree = SS6.categoryTree || {};
	SS6.categoryTree.sorting = SS6.categoryTree.sorting || {};

	$(document).ready(function () {
		var sorting = new SS6.categoryTree.sorting.constructor(
			$('#js-category-tree-sorting > .js-category-tree-items'),
			$('#js-category-tree-sorting-save-button')
		);
		sorting.init();
	});

	SS6.categoryTree.sorting.constructor = function ($rootTree, $saveButton) {
		var self = this;
		self.$rootTree = $rootTree;
		self.$saveButton = $saveButton;

		self.init = function () {
			self.$rootTree.nestedSortable({
				listType: 'ul',
				handle: '.js-category-tree-item-handle',
				items: '.js-category-tree-item',
				placeholder: 'js-category-tree-placeholder form-tree__placeholder',
				toleranceElement: '> .js-category-tree-item-line',
				forcePlaceholderSize: true,
				helper:	'clone',
				opacity: 0.6,
				revert: 100,
				change: self.onChange
			});

			$saveButton.click(self.onSaveClick);
		};

		self.onSaveClick = function () {
			if (self.$saveButton.hasClass('btn--disabled')) {
				return;
			}

			SS6.ajax({
				url: self.$saveButton.data('category-save-order-url'),
				type: 'post',
				data: {
					categoriesOrderingData: self.getCategoriesOrderingData()
				},
				success: function () {
					self.$saveButton.addClass('btn--disabled');
					SS6.formChangeInfo.removeInfo();
					SS6.window({
						content: SS6.translator.trans('Pořadí bylo uloženo.')
					});
				},
				error: function () {
					SS6.window({
						content: SS6.translator.trans('Během ukládání došlo k chybě. Pořadí nebylo uloženo.')
					});
				}
			});
		};

		self.getCategoriesOrderingData = function () {
			var data = self.$rootTree.nestedSortable(
				'toArray',
				{
					excludeRoot: true,
					expression: /(js-category-tree-)(\d+)/
				}
			);

			var categoriesOrderingData = [];
			$.each(data, function (key, value) {
				categoriesOrderingData.push({
					categoryId: value.item_id,
					parentId: value.parent_id
				});
			});

			return categoriesOrderingData;
		};

		self.onChange = function () {
			self.$saveButton.removeClass('btn--disabled');
			SS6.formChangeInfo.showInfo();
		};
	};

})(jQuery);
