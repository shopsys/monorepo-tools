(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};
	SS6.order.items = SS6.order.items || {};

	SS6.order.items.init = function () {
		$('#js-order-items').on('click', '.js-order-item-remove', SS6.order.items.onRemoveItemClick);
		$('#js-order-item-add').on('click', SS6.order.items.onAddItemClick);
		SS6.order.items.refreshCount($('#js-order-items'));
	};

	SS6.order.items.onRemoveItemClick = function(event) {
		if (!$(this).hasClass('link-disabled')) {
			var $item = $(this).closest('.js-order-item');
			var itemName = $("<textarea/>").text($item.find('.js-order-item-name').val()).html();

			SS6.window({
				content: 'Opravdu chcete odebrat z objednávky položku "<i>' + itemName + '</i>"?',
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

		SS6.validation.removeItemFromCollection('#order_items', index);
		$item.remove();

		SS6.order.items.refreshCount($collection);
	};

	SS6.order.items.addItem = function($collection) {
		var prototype = $collection.data('prototype');
		var index = $collection.data('index');
		var itemIndex = 'new_' + index;

		var item = prototype.replace(/__name__/g, itemIndex);
		var $item = $(item);
		$item.data('index', itemIndex);

		$collection.append($item);
		SS6.validation.addNewItemToCollection('#order_items', itemIndex);

		$collection.data('index', index + 1);

		SS6.order.items.refreshCount($collection);
	};

	SS6.order.items.refreshCount = function($collection) {
		var $items = $collection.find('.js-order-item');
		if ($items.size() === 1) {
			$items.find('.js-order-item-remove')
				.addClass('link-disabled')
				.tooltip({
					title: 'Objednávka musí obsahovat alespoň jednu položku',
					placement: 'bottom'
				});
		} else {
			$items.find('.js-order-item-remove')
				.removeClass('link-disabled')
				.tooltip('destroy');
		}
	};

	$(document).ready(function () {
		SS6.order.items.init();
	});

})(jQuery);
