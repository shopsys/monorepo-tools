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
		var productId = $addButton.data('product-picker-product-id');

		if (productsPicker.isMainProduct(productId)) {
			SS6.productsPicker.window.markAddButtonAsDeny($addButton);
		} else if (productsPicker.hasProduct(productId)) {
			SS6.productsPicker.window.markAddButtonAsAdded($addButton);
		} else {
			$addButton.bind('click.addProduct', SS6.productsPicker.window.onClickAddButton);
		}
	};

	SS6.productsPicker.window.onClickAddButton = function () {
		var productsPicker = window.parent.SS6.productsPicker.instances[$(this).data('product-picker-instance-id')];
		SS6.productsPicker.window.markAddButtonAsAdded($(this));
		$(this).unbind('click.addProduct');
		productsPicker.addProduct(
			$(this).data('product-picker-product-id'),
			$(this).data('product-picker-product-name')
		);

		return false;
	};

	SS6.productsPicker.window.onClickOnAddedButton = function ($addButton, originalLabelText, originalIconText) {
		var productsPicker = window.parent.SS6.productsPicker.instances[$addButton.data('product-picker-instance-id')];
		SS6.productsPicker.window.unmarkAddButtonAsAdded($addButton, originalLabelText, originalIconText);
		$addButton.unbind('click.removeProduct');
		productsPicker.removeItemByProductId($addButton.data('product-picker-product-id'));

		return false;
	};

	SS6.productsPicker.window.markAddButtonAsAdded = function ($addButton) {
		var originalLabelText = $addButton.find('.js-products-picker-label').text();
		var originalIconText = $addButton.find('.js-products-picker-icon').text();
		$addButton
			.addClass('cursor-auto btn--success').removeClass('btn--plus btn--light')
			.find('.js-products-picker-label').text(SS6.translator.trans('Přidáno')).end()
			.find('.js-products-picker-icon').addClass('svg svg-checked').empty().end()
			.bind('click.removeProduct', function () {
				SS6.productsPicker.window.onClickOnAddedButton($addButton, originalLabelText, originalIconText);
			})
			.click(function () {
				return false;
			});
	};

	SS6.productsPicker.window.unmarkAddButtonAsAdded = function ($addButton, originalLabelText, originalIconText) {
		$addButton
			.addClass('btn--plus btn--light').removeClass('cursor-auto btn--success')
			.find('.js-products-picker-label').text(originalLabelText).end()
			.find('.js-products-picker-icon').removeClass('svg svg-checked').text(originalIconText).end()
			.bind('click.addProduct', SS6.productsPicker.window.onClickAddButton)
			.click(function () {
				return false;
			});
	};

	SS6.productsPicker.window.markAddButtonAsDeny = function ($addButton) {
		$addButton
			.addClass('cursor-help')
			.tooltip({
				title: SS6.translator.trans('Nelze přiřadit produkt sám sobě'),
				placement: 'left'
			})
			.find('.js-products-picker-label').text(SS6.translator.trans('Nelze'))
			.find('.js-products-picker-icon').removeClass('svg-circle-plus in-icon in-icon--add').addClass('svg-circle-remove in-icon in-icon--denied').end()
			.click(function () {
				return false;
			});
	};

	$(document).ready(function () {
		SS6.productsPicker.window.init();
	});

})(jQuery);
