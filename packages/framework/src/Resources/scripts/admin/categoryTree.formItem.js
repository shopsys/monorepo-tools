(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.categoryTree = Shopsys.categoryTree || {};
    Shopsys.categoryTree.FormItem = Shopsys.categoryTree.FormItem || {};

    Shopsys.categoryTree.FormItem = function ($item, parent) {
        var self = this;
        self.STATUS_OPENED = 'opened';
        self.STATUS_CLOSED = 'closed';
        self.STATUS_LOADING = 'loading';
        self.STATUS_NONE = 'none';

        var status = null;
        var loaded = null;
        var $statusIcon = $item.find('.js-category-tree-form-item-icon:first');
        var $checkbox = $item.find('.js-category-tree-form-item-checkbox:first');
        self.parent = parent;
        self.children = [];

        var $childrenContainer = $item.find('.js-category-tree-form-children-container:first');

        this.init = function () {
            initChildren();
            initStatus();

            $statusIcon.click(self.statusToggle);
        };

        var initChildren = function () {
            $childrenContainer.find('> .js-category-tree-form-item').each(function () {
                var childItem = new Shopsys.categoryTree.FormItem($(this), self);
                childItem.init();
                self.children.push(childItem);
            });
        };

        var initStatus = function () {
            // status could be set to "opened" by children
            if (status === null) {
                if ($item.data('has-children')) {
                    self.close(false);
                } else {
                    setStatus(self.STATUS_NONE);
                }

                if ($checkbox.is(':checked')) {
                    if (self.parent instanceof Shopsys.categoryTree.FormItem) {
                        self.parent.open(false);
                    }
                }
            }
            if (loaded === null) {
                loaded = self.children.length > 0;
            }
        };

        this.statusToggle = function () {
            if (status === self.STATUS_CLOSED) {
                self.open(true);
            } else if (status === self.STATUS_OPENED) {
                self.close(true);
            }
        };

        this.open = function (animate) {
            if (loaded === false) {
                this.loadChildren();
            } else if (!$childrenContainer.is(':animated')) {
                $childrenContainer.slideDown(animate === true ? 'normal' : 0);
                setStatus(self.STATUS_OPENED);
                if (self.parent instanceof Shopsys.categoryTree.FormItem) {
                    self.parent.open(animate);
                }
            }
        };

        this.loadChildren = function () {
            setStatus(self.STATUS_LOADING);

            Shopsys.ajax({
                loaderElement: $item,
                url: $item.data('load-url'),
                dataType: 'json',
                success: function (data) {
                    loaded = true;

                    $.each(data, function () {
                        var $newItem = createItem(this);
                        $childrenContainer.append($newItem);
                    });
                    initChildren();

                    self.open(true);
                },
                complete: function () {
                    if (status === self.STATUS_LOADING) {
                        setStatus(self.STATUS_CLOSED);
                    }
                }
            });
        };

        var createItem = function (itemData) {
            var $form = $item.closest('.js-category-tree-form');
            var newItemHtml = $form.data('prototype');

            newItemHtml = newItemHtml.replace(/__name__/g, itemData.id);
            newItemHtml = newItemHtml.replace(/__category_name__/g, itemData.categoryName);

            var $newItem = $(newItemHtml);
            $newItem.data('load-url', itemData.loadUrl);
            $newItem.data('has-children', itemData.hasChildren);
            if (itemData.isVisible === false) {
                $newItem.addClass($form.data('hidden-item-class'));
            }

            $newItem.find('.js-category-tree-form-item-checkbox').val(itemData.id);

            return $newItem;
        };

        this.close = function (animate) {
            if (!$childrenContainer.is(':animated')) {
                $childrenContainer.slideUp(animate === true ? 'normal' : 0);
                setStatus(self.STATUS_CLOSED);
            }
        };

        var setStatus = function (newStatus) {
            status = newStatus;
            updateStatusIcon();
        };

        var updateStatusIcon = function () {
            $statusIcon.removeClass('svg svg-circle-plus svg-circle-remove sprite sprite-level cursor-pointer form-tree__item__icon--level');
            switch (status) {
                case self.STATUS_OPENED:
                case self.STATUS_LOADING:
                    $statusIcon.addClass('svg svg-circle-remove cursor-pointer');
                    break;
                case self.STATUS_CLOSED:
                    $statusIcon.addClass('svg svg-circle-plus cursor-pointer');
                    break;
                case self.STATUS_NONE:
                    $statusIcon.addClass('sprite sprite-level form-tree__item__icon--level');
                    break;
            }
        };

    };

})(jQuery);
