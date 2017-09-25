(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productsPicker = Shopsys.productsPicker || {};

    Shopsys.productsPicker.instances = [];

    Shopsys.productsPicker.close = function () {
        $.magnificPopup.close();
    };

    Shopsys.productsPicker.init = function ($container) {
        $container.filterAllNodes('.js-products-picker').each(function () {
            var productsPicker = new Shopsys.productsPicker.ProductsPicker($(this));
            productsPicker.init();
        });
    };

    Shopsys.productsPicker.ProductsPicker = function ($productsPicker) {
        var instanceId = Shopsys.productsPicker.instances.length;
        Shopsys.productsPicker.instances[instanceId] = this;

        var $header = $productsPicker.find('.js-products-picker-header');
        var $addButton = $productsPicker.find('.js-products-picker-button-add');
        var $itemsContainer = $productsPicker.find('.js-products-picker-items');
        var productItems = [];
        var mainProductId = $productsPicker.data('products-picker-main-product-id');

        this.init = function () {
            $addButton.click(openProductsPickerWindow);
            $itemsContainer.find('.js-products-picker-item').each(function () {
                initItem($(this));
            });
            $itemsContainer.sortable({
                items: '.js-products-picker-item',
                handle: '.js-products-picker-item-handle',
                update: updateOrdering
            });
        };

        this.addProduct = function (productId, productName) {
            var nextIndex = $itemsContainer.find('.js-products-picker-item').length;
            var itemHtml = $productsPicker.data('products-picker-prototype').replace(/__name__/g, nextIndex);
            var $item = $($.parseHTML(itemHtml));
            $item.find('.js-products-picker-item-product-name').text(productName);
            $item.find('.js-products-picker-item-input').val(productId);
            $itemsContainer.append($item);
            initItem($item);
            updateHeader();
            Shopsys.formChangeInfo.showInfo();
        };

        this.removeItemByProductId = function (productId) {
            var $item = findProductItemByProductId(productId);
            removeItem($item);
        };

        this.hasProduct = function (productId) {
            return findProductItemIndex(productId) !== null;
        };

        this.isMainProduct = function (productId) {
            return mainProductId !== '' && mainProductId === productId;
        };

        var initItem = function ($item) {
            productItems.push($item);

            $item.find('.js-products-picker-item-button-delete').click(function () {
                removeItem($item);
            });
        };

        var openProductsPickerWindow = function () {
            $.magnificPopup.open({
                items: {src: $productsPicker.data('products-picker-url').replace('__js_instance_id__', instanceId)},
                type: 'iframe',
                closeOnBgClick: true
            });

            return false;
        };

        var reIndex = function () {
            $itemsContainer.find('.js-products-picker-item-input').each(function (index) {
                var name = $(this).attr('name');
                var newName = name.substr(0, name.lastIndexOf('[') + 1) + index + ']';
                $(this).attr('name', newName);
            });
        };

        var removeItem = function ($item) {
            var productId = $item.find('.js-products-picker-item-input:first').val();
            delete productItems[findProductItemIndex(productId)];
            var productItem = findProductItemIndex(productId);
            var newProductItems = [];
            for (var key in productItems) {
                if (productItems[key] !== productItem) {
                    newProductItems.push(productItems[key]);
                }
            }
            productItems = newProductItems;

            $item.remove();
            reIndex();
            updateHeader();
            Shopsys.formChangeInfo.showInfo();
        };

        var findProductItemIndex = function (productId) {
            for (var key in productItems) {
                if (productItems[key].find('.js-products-picker-item-input:first').val() === productId.toString()) {
                    return key;
                }
            }

            return null;
        };

        var updateOrdering = function () {
            reIndex();
            Shopsys.formChangeInfo.showInfo();
        };

        var updateHeader = function () {
            $header.toggle(productItems.length !== 0);
        };

        var findProductItemByProductId = function (productId) {
            return productItems[findProductItemIndex(productId)];
        };
    };

    Shopsys.register.registerCallback(Shopsys.productsPicker.init);

})(jQuery);
