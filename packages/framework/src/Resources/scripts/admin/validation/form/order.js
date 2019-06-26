(function ($) {

    Shopsys.register.registerCallback(function ($container) {
        var $orderForm = $container.filterAllNodes('form[name="order_form"]');
        $orderForm.jsFormValidator({
            'groups': function () {

                var groups = [Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
                if (!$orderForm.find('#order_form_shippingAddressGroup_deliveryAddressSameAsBillingAddress').is(':checked')) {
                    groups.push(Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\Admin\\Order\\OrderFormType::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS'));
                }

                return groups;
            }
        });

        var $orderItemForms = $container.filterAllNodes('.js-order-item-any');
        $orderItemForms.each(function () {
            var $orderItemForm = $(this);

            $orderItemForm.jsFormValidator({
                'groups': function () {

                    var groups = [Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
                    if ($orderItemForm.find('.js-set-prices-manually').is(':checked')) {
                        groups.push(Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\Admin\\Order\\OrderItemFormType::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION'));
                    }

                    return groups;
                }
            });
        });
    });
})(jQuery);
