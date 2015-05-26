(function ($) {

	SS6 = window.SS6 || {};
	SS6.productPicker = SS6.productPicker || {};

	var generatorIdCounter = 0;

	SS6.productPicker.init = function () {
		$('.js-product-picker-container').each(function () {
			var $removeButton = $(this).find('.js-product-picker-remove-button');
			$removeButton.toggle($(this).find('.js-product-picker-label').val() !== $(this).data('placeholder'));
		});
		$('.js-product-picker-remove-button').click(function () {
			var $productPickerContainer = $(this).closest('.js-product-picker-container');
			var $input = $productPickerContainer.find('.js-product-picker-input');
			var placeholder = $productPickerContainer.data('placeholder');
			SS6.productPicker.selectProduct($input.attr('id'), '', placeholder);

			return false;
		});
	};

	SS6.productPicker.makePicker = function (pickerButton) {
		var $pickerButton = $(pickerButton);
		var $pickerContainer = $pickerButton.closest('.js-product-picker-container');
		var $pickerInput = $pickerContainer.find('.js-product-picker-input');

		var inputId = getOrGenerateElementId($pickerInput);

		$.magnificPopup.open({
			items: {src: $pickerButton.data('product-picker-url').replace('__input_id__', inputId)},
			type: 'iframe',
			closeOnBgClick: false
		});
	};

	SS6.productPicker.selectProduct = function (parentPickerInputId, productId, productName) {
		var $parentPickerInput = $(window.parent.document).find('#' + parentPickerInputId);
		var $parentPickerContainer = $parentPickerInput.closest('.js-product-picker-container');
		var $parentPickerLabel = $parentPickerContainer.find('.js-product-picker-label');
		var $parentPickerRemoveButton = $parentPickerContainer.find('.js-product-picker-remove-button');

		$parentPickerRemoveButton.toggle(productId !== '');
		$parentPickerInput.val(productId);
		$parentPickerLabel.val(productName);
		$.magnificPopup.close();
	};

	var getOrGenerateElementId = function ($element) {
		var elementId = $element.attr('id');
		if (elementId === undefined) {
			elementId = 'js-product-picker-generator-id-' + (generatorIdCounter++);
			$element.attr('id', elementId);
		}

		return elementId;
	};

	$(document).ready(function () {
		SS6.productPicker.init();
	});

})(jQuery);