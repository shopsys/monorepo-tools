(function ($){
	$(document).ready(function () {
		var $productEditForm = $('form[name="product_edit"]');
		$productEditForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];

				if($('#product_edit_productData_priceCalculationType_1').is(':checked')) {
					groups.push('manualPriceCalculation');
				}

				return groups;
			}
		});
		var $productForm = $('#product_edit_productData');
		$productForm.jsFormValidator({
			'groups': function () {

				var groups = ['Default'];

				if($('#product_edit_productData_priceCalculationType_0').is(':checked')) {
					groups.push('autoPriceCalculation');
				}

				return groups;
			}
		});
	});
})(jQuery);
