(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};
	SS6.order.items = SS6.order.items || {};

	SS6.order.items.init = function () {
		$('#js-order-items').on('click', '.js-order-item-remove', SS6.order.items.onRemoveItemClick);
		$('#js-order-item-add').on('click', SS6.order.items.onAddItemClick);
		SS6.order.items.refreshCount($('#js-order-items'));

		var productPicker = new SS6.productPicker.ProductPicker($('#js-order-item-add-product'), SS6.order.items.addProduct);
		productPicker.init();
	};

	SS6.order.items.onRemoveItemClick = function(event) {
		if (!$(this).hasClass('text-disabled')) {
			var $item = $(this).closest('.js-order-item');
			var $itemNameElement = $item.find('.js-order-item-name');
			var itemName = SS6.escape.escapeHtml($itemNameElement.val());

			SS6.window({
				content: 'Opravdu chcete odebrat z objednávky položku "<i>' + itemName + '</i>"?',
				buttonCancel: true,
				buttonContinue: true,
				eventContinue: function () {
					SS6.order.items.removeItem($item);
				}
			});
		}
		event.preventDefault();
	};

	SS6.order.items.onAddItemClick = function(event) {
		var $collection = $(this).closest('table').find('#js-order-items');

		SS6.order.items.addItem($collection);
		event.preventDefault();
	};

	SS6.order.items.removeItem = function($item) {
		var $collection = $item.closest('#js-order-items');
		var index = $item.data('index');

		SS6.validation.removeItemFromCollection('#order_form_items', index);
		$item.remove();

		SS6.order.items.refreshCount($collection);
		SS6.formChangeInfo.showInfo();
	};

	SS6.order.items.getNewIndex = function($collection) {
		var maxIndex = 0;

		$collection.find('.js-order-item').each(function () {
			var indexStr = $(this).data('index').toString();
			if (indexStr.indexOf(SS6.constant('\\SS6\\ShopBundle\\Model\\Order\\OrderData::NEW_ITEM_PREFIX')) === 0) {
				var index = parseInt(indexStr.slice(4));
				if (index > maxIndex) {
					maxIndex = index;
				}
			}
		});

		return SS6.constant('\\SS6\\ShopBundle\\Model\\Order\\OrderData::NEW_ITEM_PREFIX') + (maxIndex + 1);
	};

	SS6.order.items.addItem = function($collection) {
		var prototype = $collection.data('prototype');
		var index = SS6.order.items.getNewIndex($collection);

		var item = prototype.replace(/__name__/g, index);
		var $item = $($.parseHTML(item));
		$item.data('index', index);

		$collection.append($item);
		SS6.validation.addNewItemToCollection('#order_form_items', index);

		SS6.order.items.refreshCount($collection);
		SS6.formChangeInfo.showInfo();
	};

	SS6.order.items.addProduct = function(productId, productName) {
		var $collection = $('#js-order-items');
		SS6.ajax({
			url: $collection.data('order-product-add-url'),
			method: 'POST',
			data: {
				productId: productId
			},
			success: function(data) {
				var $data = $($.parseHTML(data));

				var $orderItem = $data.filter('.js-order-item');
				var index = $orderItem.data('index');

				$collection.append($orderItem);
				$($('#order_form_items')).jsFormValidator('addPrototype', index);

				SS6.order.items.refreshCount($collection);

				SS6.window({content: SS6.translator.trans('Zboží bylo uloženo do objednávky')});
			},
			error: function() {
				SS6.window({content: SS6.translator.trans('Zboží se nepodařilo vložit')});
			}
		});
	};

	SS6.order.items.refreshCount = function($collection) {
		var $items = $collection.find('.js-order-item');
		if ($items.length === 1) {
			$items.find('.js-order-item-remove')
				.addClass('text-disabled')
				.tooltip({
					title: 'Objednávka musí obsahovat alespoň jednu položku',
					placement: 'bottom'
				});
		} else {
			$items.find('.js-order-item-remove')
				.removeClass('text-disabled')
				.tooltip('destroy');
		}
	};

	$(document).ready(function () {
		SS6.order.items.init();
	});

})(jQuery);
