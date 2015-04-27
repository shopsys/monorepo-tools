(function ($) {

	SS6 = window.SS6 || {};
	SS6.productsPicker = SS6.productsPicker || {};

	SS6.productsPicker.instances = [];

	SS6.productsPicker.close = function () {
		$.magnificPopup.close();
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
		var productItems = [];
		var nextIndex = 0;

		this.init = function () {
			$addButton.click(openProductsPickerWindow);
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

		this.hasProduct = function (productId) {
			for (var key in productItems) {
				if (productItems[key].find('.js-products-picker-item-input:first').val() === productId.toString()) {
					return true;
				}
			}

			return false;
		};

		var initItem = function ($item) {
			var index = productItems.length;
			productItems[index] = $item;

			$item.find('.js-products-picker-item-button-delete').click(function () {
				delete productItems[index];
				$item.remove();
				SS6.formChangeInfo.showInfo();
			});
		};

		var openProductsPickerWindow = function () {
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