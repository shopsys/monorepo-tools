(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.massActionConfirm = Shopsys.massActionConfirm || {};

    var isConfirmed = false;

    Shopsys.massActionConfirm.init = function ($container) {
        $container.filterAllNodes('.js-mass-action-submit').click(function () {
            var $button = $(this);
            if (!isConfirmed) {
                var action = $('.js-mass-action-value option:selected').text().toLowerCase();
                var selectType = $('.js-mass-action-select-type').val();
                var count;
                switch (selectType) {
                    case Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Product\\MassAction\\ProductMassActionData::SELECT_TYPE_CHECKED'):
                        count = $('.js-grid-mass-action-select-row:checked').length;
                        break;
                    case Shopsys.constant('\\Shopsys\\ShopBundle\\Model\\Product\\MassAction\\ProductMassActionData::SELECT_TYPE_ALL_RESULTS'):
                        count = $('.js-grid').data('total-count');
                        break;
                }
                Shopsys.window({
                    content: Shopsys.translator.trans('Do you really want to %action% %count% product?', {'%action%': action, '%count%': count }),
                    buttonCancel: true,
                    buttonContinue: true,
                    eventContinue: function () {
                        isConfirmed = true;
                        $button.trigger('click');
                    }
                });

                return false;
            }
        });

    };

    Shopsys.register.registerCallback(Shopsys.massActionConfirm.init);

})(jQuery);