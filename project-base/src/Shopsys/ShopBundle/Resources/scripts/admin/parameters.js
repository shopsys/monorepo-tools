(function ($) {

	SS6 = window.SS6 || {};
	SS6.parameters = SS6.parameters || {};

	SS6.parameters.init = function () {
		$('.js-parameters').on('click', '.js-parameters-item-remove', function (event) {
			var $collection = $(this).closest('.js-parameters');

			var $item = $(this).closest('.js-parameters-item');
			var index = $item.data('index');
			SS6.validation.removeItemFromCollection('#product_edit_form_parameters', index);
			$item.remove();

			SS6.formChangeInfo.showInfo();

			SS6.parameters.refreshCount($collection);

			event.preventDefault();
		});

		$('.js-parameters-item-add').on('click', function () {
			var $collection = $('.js-parameters');
			var index = $collection.data('index');

			var prototype = $collection.data('prototype');
			var item = prototype
				.replace(/__name__label__/g, index)
				.replace(/__name__/g, index);
			var $item = $(item);
			$item.data('index', index);

			$collection.data('index', index + 1);

			$collection.append($item);

			SS6.formChangeInfo.showInfo();
			SS6.parameters.refreshCount($collection);
			SS6.validation.addNewItemToCollection('#product_edit_form_parameters', index);

			return false;
		});

		SS6.parameters.refreshCount($('.js-parameters'));
	};

	SS6.parameters.refreshCount = function($collection) {
		if ($collection.find('.js-parameters-item').length === 0) {
			$collection.find('.js-parameters-empty-item').show();
		} else {
			$collection.find('.js-parameters-empty-item').hide();
		}
	};

	$(document).ready(function () {
		SS6.parameters.init();
	});

})(jQuery);
