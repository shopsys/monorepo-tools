(function ($){
	$(document).ready(function () {
		var $productEditForm = $('form[name="product_edit"]');
		$productEditForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];

				if ($('input[name="product_edit[productData][priceCalculationType]"]:checked').val() === 'manual') {
					groups.push('manualPriceCalculation');
				}

				return groups;
			}
		});
		var $productForm = $('#product_edit_productData');
		$productForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];

				if ($('input[name="product_edit[productData][priceCalculationType]"]:checked').val() === 'auto') {
					groups.push('autoPriceCalculation');
				}

				return groups;
			}
		});
	});
})(jQuery);
