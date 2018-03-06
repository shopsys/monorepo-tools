(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.categoryTree = Shopsys.categoryTree || {};
    Shopsys.categoryTree.Form = Shopsys.categoryTree.Form || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-category-tree-form').each(function () {
            var categoryTreeForm = new Shopsys.categoryTree.Form($(this));
            categoryTreeForm.init();
        });
    });

    Shopsys.categoryTree.Form = function ($tree) {
        $tree.find('> .js-category-tree-form-children-container > .js-category-tree-form-item').each(function () {
            var categoryTreeItem = new Shopsys.categoryTree.FormItem($(this), null);
            categoryTreeItem.init();
        });

        this.init = function () {

        };
    };

})(jQuery);
