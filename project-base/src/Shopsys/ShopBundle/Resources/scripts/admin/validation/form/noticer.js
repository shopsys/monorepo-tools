(function ($){
	$(document).ready(function () {
		var $advertForm = $('form[name="advert_form"]');

		$.fn.initAdvertType = function(){
			var $checkedType = $(this).find('input[name="advert_form[type]"]:checked').val();
			$(this).find('.js-advert-type-content').hide();
			$(this).find('.js-advert-type-content[data-type=' + $checkedType + ']').show();
		};

		$advertForm.initAdvertType();
		$advertForm.find('input[name="advert_form[type]"]').on('change',function(){
			$advertForm.initAdvertType();
		});

		$advertForm.jsFormValidator({
			'groups': function () {
				var groups = [Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];

				if ($('input[name="advert[type]"]:checked').val() === Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Advert\\Advert::TYPE_CODE')) {
					groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Advert\\AdvertFormType::VALIDATION_GROUP_TYPE_CODE'));
				} else if ($('input[name="advert[type]"]:checked').val() === Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Advert\\Advert::TYPE_IMAGE')) {
					groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Advert\\AdvertFormType::VALIDATION_GROUP_TYPE_IMAGE'));
				}
				return groups;
			}
		});
	});
})(jQuery);
