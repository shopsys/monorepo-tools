(function ($) {

	SS6 = window.SS6 || {};
	SS6.productsPicker = SS6.productsPicker || {};
	SS6.productsPicker.window = SS6.productsPicker.window || {};

	SS6.productsPicker.window.init = function () {
		$('.js-products-picker-window-add-product').each(function () {
			SS6.productsPicker.window.initAddButton($(this));
		});
	};

	SS6.productsPicker.window.initAddButton = function ($addButton) {
		var productsPicker = window.parent.SS6.productsPicker.instances[$addButton.data('product-picker-instance-id')];
		if (productsPicker.hasProduct($addButton.data('product-picker-product-id'))) {
			SS6.productsPicker.window.markAddButtonAsAdded($addButton);
		} else {
			$(this).bind('click.addProduct', function () {
				SS6.productsPicker.window.markAddButtonAsAdded($(this));
				$(this).unbind('click.addProduct');
				productsPicker.addProduct(
					$(this).data('product-picker-product-id'),
					$(this).data('product-picker-product-name')
				);

				return false;
			});
		}
	};

	SS6.productsPicker.window.markAddButtonAsAdded = function ($addButton) {
		$addButton
			.addClass('cursor-default btn-success')
			.find('.js-products-picker-icon').removeClass('fa-plus').addClass('fa-check').end()
			.find('.js-products-picker-label').text(SS6.translator.trans('Přidáno'));
	};

	$(document).ready(function () {
		SS6.productsPicker.window.init();
	});

})(jQuery);