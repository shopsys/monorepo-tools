(function ($) {

	SS6 = window.SS6 || {};
	SS6.productPicker = SS6.productPicker || {};

	SS6.productPicker.instances = [];

	SS6.productPicker.ProductPicker = function ($pickerButton, onSelectProductCallback) {
		var self = this;
		var instanceId = SS6.productPicker.instances.length;
		SS6.productPicker.instances[instanceId] = this;

		var $container = $pickerButton.closest('.js-product-picker-container');
		var $input = $container.find('.js-product-picker-input');
		var $label = $container.find('.js-product-picker-label');
		var $removeButton = $container.find('.js-product-picker-remove-button');

		this.init = function () {

			$pickerButton.click(makePicker);
			$removeButton.toggle($label.val() !== $container.data('placeholder'));

			$removeButton.click(function () {
				self.selectProduct('', $container.data('placeholder'));
				return false;
			});
		};

		this.onSelectProduct = function(productId, productName) {
			if (onSelectProductCallback !== undefined) {
				onSelectProductCallback(productId, productName);
			} else {
				this.selectProduct(productId, productName)
			}
		};

		this.selectProduct = function (productId, productName) {
			$input.val(productId);
			$label.val(productName);
			$removeButton.toggle(productId !== '');
		};

		var makePicker = function (event) {
			$.magnificPopup.open({
				items: {src: $pickerButton.data('product-picker-url').replace('__instance_id__', instanceId)},
				type: 'iframe',
				closeOnBgClick: true
			});

			event.preventDefault();
		};
	};

	SS6.productPicker.onClickSelectProduct = function (instanceId, productId, productName) {
		window.parent.SS6.productPicker.instances[instanceId].onSelectProduct(productId, productName);
		$.magnificPopup.close();
	};

	SS6.productPicker.init = function ($container) {
		$container.filterAllNodes('.js-product-picker-create-picker-button').each(function () {
			var productPicker = new SS6.productPicker.ProductPicker($(this));
			productPicker.init();
		});
	};

	SS6.register.registerCallback(SS6.productPicker.init);

})(jQuery);