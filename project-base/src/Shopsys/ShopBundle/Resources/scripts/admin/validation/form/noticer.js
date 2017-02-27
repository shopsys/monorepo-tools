// Script is named "noticer.js" because scripts named "advert.js" are often blocked by browser (e.g. by AdBlock plugin)

(function ($){
    $(document).ready(function () {
        var $advertForm = $('form[name="advert_form"]');

        var getCheckedType = function () {
            return $advertForm.find('input[name="advert_form[type]"]:checked').val();
        };

        var initAdvertForm = function () {
            $advertForm
                .find('.js-advert-type-content').hide()
                .filter('[data-type=' + getCheckedType() + ']').show();
        };

        $advertForm.find('input[name="advert_form[type]"]').change(initAdvertForm);
        initAdvertForm();

        $advertForm.jsFormValidator({
            'groups': function () {
                var groups = [Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];

                var checkedType = getCheckedType();
                if (checkedType === Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Advert\\Advert::TYPE_CODE')) {
                    groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Advert\\AdvertFormType::VALIDATION_GROUP_TYPE_CODE'));
                } else if (checkedType === Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Advert\\Advert::TYPE_IMAGE')) {
                    groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Advert\\AdvertFormType::VALIDATION_GROUP_TYPE_IMAGE'));
                }

                return groups;
            }
        });
    });
})(jQuery);
