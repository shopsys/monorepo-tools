(function ($) {

	SS6 = window.SS6 || {};
	SS6.product = SS6.product || {};
	SS6.product.accessories = SS6.product.accessories || {};

	SS6.product.accessories.init = function () {
		var $accessoriesContainer = $('.js-accessories');
		var prototype = $accessoriesContainer.data('prototype');
		var $addButton = $('#js-accessory-add');

		var accessoryCollection = new SS6.product.AccessoryCollection(
			$accessoriesContainer,
			prototype,
			$addButton
		);
		accessoryCollection.init();
	};

	SS6.product.AccessoryCollection = function ($itemsContainer, itemPrototype, $addButton) {
		var self = this;

		var nextIndex = 0;

		this.init = function() {
			$addButton.click(self.addAccessory);

			$itemsContainer.find('.js-accessory').each(function () {
				var accessory = new SS6.product.Accessory($(this));
				accessory.init();

				var accessoryIndex = accessory.getIndex();
				if (accessoryIndex >= nextIndex) {
					nextIndex = accessoryIndex + 1;
				}
			});
		};

		this.addAccessory = function() {
			var itemHtml = itemPrototype.replace(/__name__/g, nextIndex);
			var $item = $($.parseHTML(itemHtml));
			$item.data('index', nextIndex);
			nextIndex++;

			$itemsContainer.append($item);

			var accessory = new SS6.product.Accessory($item);
			accessory.init();

			return false;
		};
	};

	SS6.product.Accessory = function ($element) {
		var self = this;

		this.init = function() {
			$element.find('.js-accessory-remove').click(function () {
				$(this).closest('.js-accessory').remove();

				return false;
			});
		};

		this.getIndex = function () {
			var id = $element.find('input[type=hidden]:first').attr('id');
			var index = id.slice('product_edit_productData_accessories_'.length);
			return parseInt(index);
		};
	};

	$(document).ready(function () {
		SS6.product.accessories.init();
	});

})(jQuery);
