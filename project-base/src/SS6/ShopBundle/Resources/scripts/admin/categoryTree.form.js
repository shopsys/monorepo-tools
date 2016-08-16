(function ($){

	SS6 = SS6 || {};
	SS6.categoryTree = SS6.categoryTree || {};
	SS6.categoryTree.Form = SS6.categoryTree.Form || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-category-tree-form').each(function () {
			var categoryTreeForm = new SS6.categoryTree.Form($(this));
			categoryTreeForm.init();
		});
	});

	SS6.categoryTree.Form = function ($tree) {
		var self = this;

		$tree.find('> .js-category-tree-form-children-container > .js-category-tree-form-item').each(function () {
			var categoryTreeItem = new SS6.categoryTree.FormItem($(this), null);
			categoryTreeItem.init();
		});

		this.init = function () {

		};
	};

})(jQuery);
