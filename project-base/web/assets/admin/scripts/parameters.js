(function ($) {

	SS6 = window.SS6 || {};
	SS6.parameters = SS6.parameters || {};
	
	SS6.parameters.init = function () {
		$('.js-parameters').on('click', '.js-parameters-item-remove', function (event) {
			$(this).closest('.js-parameters-item').remove();
			
			event.preventDefault();
		});
		
		$('.js-parameters-item-add').on('click', function (event) {
			var $collection = $(this).prev('table').find('.js-parameters');
			var index = $collection.data('index');
			
			var prototype = $collection.data('prototype');
			var item = prototype
				.replace(/__name__label__/g, index)
				.replace(/__name__/g, index);
			
			$collection.data('index', index + 1);
			
			$collection.append(item);
			
			event.preventDefault();
		});
	};
	
	$(document).ready(function () {
		SS6.parameters.init();
	});
	
})(jQuery);
