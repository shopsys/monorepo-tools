(function ($) {

	SS6 = window.SS6 || {};
	SS6.parameters = SS6.parameters || {};
	
	SS6.parameters.init = function () {
		$('.js-parameters').on('click', '.js-parameters-item-remove', function (event) {
			var $collection = $(this).closest('.js-parameters');
		
			var $item = $(this).closest('.js-parameters-item');
			var index = $item.data('index');
			$($('#product_parameters')).jsFormValidator('delPrototype', index);
			$item.remove();
			
			SS6.parameters.refreshCount($collection);
			
			event.preventDefault();
		});
		
		$('.js-parameters-item-add').on('click', function (event) {
			var $collection = $(this).prev('table').find('.js-parameters');
			var index = $collection.data('index');
			
			var prototype = $collection.data('prototype');
			var item = prototype
				.replace(/__name__label__/g, index)
				.replace(/__name__/g, index);
			var $item = $(item);
			$item.data('index', index);
			
			$collection.data('index', index + 1);
			
			$collection.append($item);
			
			SS6.parameters.refreshCount($collection);
			
			event.preventDefault();

			$($('#product_parameters')).jsFormValidator('addPrototype', index);
		});
		
		SS6.parameters.refreshCount($('.js-parameters'));
	};
	
	SS6.parameters.refreshCount = function($collection) {
		if ($collection.find('.js-parameters-item').size() === 0) {
			$collection.find('.js-parameters-empty-item').show();
		} else {
			$collection.find('.js-parameters-empty-item').hide();
		}
	};
	
	$(document).ready(function () {
		SS6.parameters.init();
	});
	
})(jQuery);
