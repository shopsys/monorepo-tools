(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.productsPicker = Shopsys.productsPicker || {};
	Shopsys.productsPicker.window = Shopsys.productsPicker.window || {};

	Shopsys.productsPicker.window.init = function () {
		$('.js-products-picker-window-add-product').each(function () {
			Shopsys.productsPicker.window.initAddButton($(this));
		});
	};

	Shopsys.productsPicker.window.initAddButton = function ($addButton) {
		var productsPicker = window.parent.Shopsys.productsPicker.instances[$addButton.data('product-picker-instance-id')];
		var productId = $addButton.data('product-picker-product-id');

		if (productsPicker.isMainProduct(productId)) {
			Shopsys.productsPicker.window.markAddButtonAsDeny($addButton);
		} else if (productsPicker.hasProduct(productId)) {
			Shopsys.productsPicker.window.markAddButtonAsAdded($addButton);
		} else {
			$addButton.bind('click.addProduct', Shopsys.productsPicker.window.onClickAddButton);
		}
	};

	Shopsys.productsPicker.window.onClickAddButton = function () {
		var productsPicker = window.parent.Shopsys.productsPicker.instances[$(this).data('product-picker-instance-id')];
		Shopsys.productsPicker.window.markAddButtonAsAdded($(this));
		$(this).unbind('click.addProduct');
		productsPicker.addProduct(
			$(this).data('product-picker-product-id'),
			$(this).data('product-picker-product-name')
		);

		return false;
	};

	Shopsys.productsPicker.window.onClickOnAddedButton = function ($addButton, originalLabelText, originalIconText) {
		var productsPicker = window.parent.Shopsys.productsPicker.instances[$addButton.data('product-picker-instance-id')];
		Shopsys.productsPicker.window.unmarkAddButtonAsAdded($addButton, originalLabelText, originalIconText);
		$addButton.unbind('click.removeProduct');
		productsPicker.removeItemByProductId($addButton.data('product-picker-product-id'));

		return false;
	};

	Shopsys.productsPicker.window.markAddButtonAsAdded = function ($addButton) {
		var originalLabelText = $addButton.find('.js-products-picker-label').text();
		var originalIconText = $addButton.find('.js-products-picker-icon').text();
		$addButton
			.addClass('cursor-auto btn--success').removeClass('btn--plus btn--light')
			.find('.js-products-picker-label').text(Shopsys.translator.trans('Added')).end()
			.find('.js-products-picker-icon').addClass('svg svg-checked').empty().end()
			.bind('click.removeProduct', function () {
				Shopsys.productsPicker.window.onClickOnAddedButton($addButton, originalLabelText, originalIconText);
			})
			.click(function () {
				return false;
			});
	};

	Shopsys.productsPicker.window.unmarkAddButtonAsAdded = function ($addButton, originalLabelText, originalIconText) {
		$addButton
			.addClass('btn--plus btn--light').removeClass('cursor-auto btn--success')
			.find('.js-products-picker-label').text(originalLabelText).end()
			.find('.js-products-picker-icon').removeClass('svg svg-checked').text(originalIconText).end()
			.bind('click.addProduct', Shopsys.productsPicker.window.onClickAddButton)
			.click(function () {
				return false;
			});
	};

	Shopsys.productsPicker.window.markAddButtonAsDeny = function ($addButton) {
		$addButton
			.addClass('cursor-help')
			.tooltip({
				title: Shopsys.translator.trans('Not possible to assign product to itself'),
				placement: 'left'
			})
			.find('.js-products-picker-label').text(Shopsys.translator.trans('Unable to add'))
			.find('.js-products-picker-icon').removeClass('svg-circle-plus in-icon in-icon--add').addClass('svg-circle-remove in-icon in-icon--denied').end()
			.click(function () {
				return false;
			});
	};

	$(document).ready(function () {
		Shopsys.productsPicker.window.init();
	});

})(jQuery);
