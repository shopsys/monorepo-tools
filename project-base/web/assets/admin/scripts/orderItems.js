(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};
	SS6.order.items = SS6.order.items || {};

	SS6.order.items.init = function () {
		$('#js-order-items').on('click', '.js-order-item-remove', SS6.order.items.onRemoveItemClick);
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

	SS6.order.items.removeItem = function($item) {
		var $collection = $item.closest('#js-order-items');
		$item.remove();
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
