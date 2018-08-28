(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.parameters = Shopsys.parameters || {};

    Shopsys.parameters.init = function () {
        $('.js-parameters').on('click', '.js-parameters-item-remove', function (event) {
            var $collection = $(this).closest('.js-parameters');

            var $item = $(this).closest('.js-parameters-item');
            var index = $item.data('index');
            Shopsys.validation.removeItemFromCollection('#product_form_parameters', index);
            $item.remove();

            Shopsys.formChangeInfo.showInfo();

            Shopsys.parameters.refreshCount($collection);

            event.preventDefault();
        });

        $('.js-parameters-item-add').on('click', function () {
            var $collection = $('.js-parameters');
            var index = $collection.data('index');

            var prototype = $collection.data('prototype');
            var item = prototype
                .replace(/__name__label__/g, index)
                .replace(/__name__/g, index);
            var $item = $($.parseHTML(item));
            $item.data('index', index);

            $collection.data('index', index + 1);

            $collection.append($item);
            Shopsys.register.registerNewContent($item);

            Shopsys.validation.addNewItemToCollection('#product_form_parameters', index);
            Shopsys.formChangeInfo.showInfo();
            Shopsys.parameters.refreshCount($collection);

            return false;
        });

        Shopsys.parameters.refreshCount($('.js-parameters'));
    };

    Shopsys.parameters.refreshCount = function ($collection) {
        if ($collection.find('.js-parameters-item').length === 0) {
            $collection.find('.js-parameters-empty-item').show();
        } else {
            $collection.find('.js-parameters-empty-item').hide();
        }
    };

    $(document).ready(function () {
        Shopsys.parameters.init();
    });

})(jQuery);
