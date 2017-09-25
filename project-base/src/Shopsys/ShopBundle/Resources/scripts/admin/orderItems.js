(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.order = Shopsys.order || {};
    Shopsys.order.items = Shopsys.order.items || {};

    Shopsys.order.items.init = function () {
        $('#js-order-items').on('click', '.js-order-item-remove', Shopsys.order.items.onRemoveItemClick);
        $('#js-order-item-add').on('click', Shopsys.order.items.onAddItemClick);
        Shopsys.order.items.refreshCount($('#js-order-items'));

        var productPicker = new Shopsys.productPicker.ProductPicker($('#js-order-item-add-product'), Shopsys.order.items.addProduct);
        productPicker.init();
    };

    Shopsys.order.items.onRemoveItemClick = function (event) {
        if (!$(this).hasClass('text-disabled')) {
            var $item = $(this).closest('.js-order-item');
            var $itemNameElement = $item.find('.js-order-item-name');
            var itemName = Shopsys.escape.escapeHtml($itemNameElement.val());

            Shopsys.window({
                content: Shopsys.translator.trans('Do you really want to remove item "<i>%itemName%</i>" from the order?', {'%itemName%': itemName}),
                buttonCancel: true,
                buttonContinue: true,
                eventContinue: function () {
                    Shopsys.order.items.removeItem($item);
                }
            });
        }
        event.preventDefault();
    };

    Shopsys.order.items.onAddItemClick = function (event) {
        var $collection = $(this).closest('table').find('#js-order-items');

        Shopsys.order.items.addItem($collection);
        event.preventDefault();
    };

    Shopsys.order.items.removeItem = function ($item) {
        var $collection = $item.closest('#js-order-items');
        var index = $item.data('index');

        Shopsys.validation.removeItemFromCollection('#order_form_items', index);
        $item.remove();

        Shopsys.order.items.refreshCount($collection);
        Shopsys.formChangeInfo.showInfo();
    };

    Shopsys.order.items.getNewIndex = function ($collection) {
        var maxIndex = 0;

        $collection.find('.js-order-item').each(function () {
            var indexStr = $(this).data('index').toString();
            if (indexStr.indexOf(Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Order\\OrderData::NEW_ITEM_PREFIX')) === 0) {
                var index = parseInt(indexStr.slice(4));
                if (index > maxIndex) {
                    maxIndex = index;
                }
            }
        });

        return Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Order\\OrderData::NEW_ITEM_PREFIX') + (maxIndex + 1);
    };

    Shopsys.order.items.addItem = function ($collection) {
        var prototype = $collection.data('prototype');
        var index = Shopsys.order.items.getNewIndex($collection);

        var item = prototype.replace(/__name__/g, index);
        var $item = $($.parseHTML(item));
        $item.data('index', index);

        $collection.append($item);
        Shopsys.validation.addNewItemToCollection('#order_form_items', index);

        Shopsys.order.items.refreshCount($collection);
        Shopsys.formChangeInfo.showInfo();
    };

    Shopsys.order.items.addProduct = function (productId, productName) {
        var $collection = $('#js-order-items');
        Shopsys.ajax({
            url: $collection.data('order-product-add-url'),
            method: 'POST',
            data: {
                productId: productId
            },
            success: function (data) {
                var $data = $($.parseHTML(data));

                var $orderItem = $data.filter('.js-order-item');
                var index = $orderItem.data('index');

                $collection.append($orderItem);
                $($('#order_form_items')).jsFormValidator('addPrototype', index);

                Shopsys.order.items.refreshCount($collection);

                Shopsys.window({content: Shopsys.translator.trans('Product saved in order')});
            },
            error: function () {
                Shopsys.window({content: Shopsys.translator.trans('Unable to add product')});
            }
        });
    };

    Shopsys.order.items.refreshCount = function ($collection) {
        var $items = $collection.find('.js-order-item');
        if ($items.length === 1) {
            $items.find('.js-order-item-remove')
                .addClass('text-disabled')
                .tooltip({
                    title: Shopsys.translator.trans('Order must contain at least one item'),
                    placement: 'bottom'
                });
        } else {
            $items.find('.js-order-item-remove')
                .removeClass('text-disabled')
                .tooltip('destroy');
        }
    };

    $(document).ready(function () {
        Shopsys.order.items.init();
    });

})(jQuery);
