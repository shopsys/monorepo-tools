(function ($) {

	SS6 = window.SS6 || {};
	SS6.productsPicker = SS6.productsPicker || {};

	SS6.productsPicker.instances = [];

	SS6.productsPicker.close = function () {
		$.magnificPopup.close();
	};

	SS6.productsPicker.addProduct = function (addButton, instanceId, productId, productName) {
		var $addButton = $(addButton);
		if (!$addButton.data('already-selected')) {
			var productsPicker = window.parent.SS6.productsPicker.instances[instanceId];
			productsPicker.addProduct(productId, productName);
			$addButton
				.data('already-selected', true)
				.addClass('cursor-default btn-success')
				.find('.js-products-picker-icon').removeClass('fa-plus').addClass('fa-check').end()
				.find('.js-products-picker-label').text(SS6.translator.trans('Přidáno'));
		}
	};

	SS6.productsPicker.init = function () {
		$('.js-products-picker').each(function () {
			var productsPicker = new SS6.productsPicker.ProductsPicker($(this));
			productsPicker.init();
		});
	};

	SS6.productsPicker.ProductsPicker = function ($productsPicker) {
		var self = this;
		var instanceId = SS6.productsPicker.instances.length;
		SS6.productsPicker.instances[instanceId] = this;

		var $addButton = $productsPicker.find('.js-products-picker-button-add');
		var $itemsContainer = $productsPicker.find('.js-products-picker-items');
		var nextIndex = 0;

		this.init = function () {
			$addButton.click(openProductsPicker);
			$itemsContainer.find('.js-products-picker-item').each(function () {
				initItem($(this));
			});
		};

		this.addProduct = function (productId, productName) {
			var itemHtml = $productsPicker.data('products-picker-prototype').replace(/__name__/g, 'new_' + nextIndex);
			nextIndex++;
			var $item = $($.parseHTML(itemHtml));
			$item.find('.js-products-picker-item-product-name').text(productName);
			$item.find('.js-products-picker-item-input').val(productId);
			$itemsContainer.append($item);
			initItem($item);
			SS6.formChangeInfo.showInfo();
		};

		var initItem = function ($item) {
			$item.find('.js-products-picker-item-button-delete').click(function () {
				$item.remove();
				SS6.formChangeInfo.showInfo();
			});
		};

		var openProductsPicker = function () {
			$.magnificPopup.open({
				items: {src: $productsPicker.data('products-picker-url').replace('__js_instance_id__', instanceId)},
				type: 'iframe',
				closeOnBgClick: false
			});

			return false;
		};
	};

	$(document).ready(function () {
		SS6.productsPicker.init();
	});

})(jQuery);