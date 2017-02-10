(function ($){

    Shopsys = Shopsys || {};
    Shopsys.categoryTree = Shopsys.categoryTree || {};
    Shopsys.categoryTree.sorting = Shopsys.categoryTree.sorting || {};

    Shopsys.register.registerCallback(function ($container) {
        var $rootTree = $container.filterAllNodes('#js-category-tree-sorting > .js-category-tree-items');
        var $saveButton = $container.filterAllNodes('#js-category-tree-sorting-save-button');
        
        if ($rootTree.length > 0 && $saveButton.length > 0) {
            var sorting = new Shopsys.categoryTree.sorting.constructor($rootTree, $saveButton);
            sorting.init();
        }
    });

    Shopsys.categoryTree.sorting.constructor = function ($rootTree, $saveButton) {
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

            Shopsys.ajax({
                url: self.$saveButton.data('category-save-order-url'),
                type: 'post',
                data: {
                    categoriesOrderingData: self.getCategoriesOrderingData()
                },
                success: function () {
                    self.$saveButton.addClass('btn--disabled');
                    Shopsys.formChangeInfo.removeInfo();
                    Shopsys.window({
                        content: Shopsys.translator.trans('Order saved.')
                    });
                },
                error: function () {
                    Shopsys.window({
                        content: Shopsys.translator.trans('There was an error while saving. The order isn\'t saved.')
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
            Shopsys.formChangeInfo.showInfo();
        };
    };

})(jQuery);
