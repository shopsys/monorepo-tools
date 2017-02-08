(function ($){
	$(document).ready(function () {
		var $productEditForm = $('form[name="product_edit_form"]');
		$productEditForm.jsFormValidator({
			'groups': function () {

				var groups = [SS6.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];

				if ($('input[name="product_edit_form[productData][priceCalculationType]"]:checked').val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_MANUAL')) {
					groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Product\\ProductEditFormType::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION'));
				}

				return groups;
			}
		});
		var $productForm = $('#product_edit_form_productData');
		$productForm.jsFormValidator({
			'groups': function () {

				var groups = [SS6.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];

				if ($('input[name="product_edit_form[productData][usingStock]"]:checked').val() === '1') {
					groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Product\\ProductFormType::VALIDATION_GROUP_USING_STOCK'));
					if($('select[name="product_edit_form[productData][outOfStockAction]"]').val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY')) {
						groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Product\\ProductFormType::VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY'));
					}
				} else {
					groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Product\\ProductFormType::VALIDATION_GROUP_NOT_USING_STOCK'));
				}

				if ($('input[name="product_edit_form[productData][priceCalculationType]"]:checked').val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::PRICE_CALCULATION_TYPE_AUTO')) {
					groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Product\\ProductFormType::VALIDATION_GROUP_AUTO_PRICE_CALCULATION'));
				}

				return groups;
			}
		});
	});
})(jQuery);
