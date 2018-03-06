(function ($) {
    $(document).ready(function () {

        var $orderForm = $('form[name="order_form"]');
        $orderForm.jsFormValidator({
            'groups': function () {

                var groups = [Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
                if (!$orderForm.find('#order_form_deliveryAddressSameAsBillingAddress').is(':checked')) {
                    groups.push(Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\Admin\\Order\\OrderFormType::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS'));
                }

                return groups;
            }
        });

    });
})(jQuery);
