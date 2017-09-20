(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productPicker = Shopsys.productPicker || {};

    Shopsys.productPicker.instances = [];

    Shopsys.productPicker.ProductPicker = function ($pickerButton, onSelectProductCallback) {
        var self = this;
        var instanceId = Shopsys.productPicker.instances.length;
        Shopsys.productPicker.instances[instanceId] = this;

        var $container = $pickerButton.closest('.js-product-picker-container');
        var $input = $container.find('.js-product-picker-input');
        var $label = $container.find('.js-product-picker-label');
        var $removeButton = $container.find('.js-product-picker-remove-button');

        this.init = function () {

            $pickerButton.click(makePicker);
            $removeButton.toggle($label.val() !== $container.data('placeholder'));

            $removeButton.click(function () {
                self.selectProduct('', $container.data('placeholder'));
                return false;
            });
        };

        this.onSelectProduct = function(productId, productName) {
            if (onSelectProductCallback !== undefined) {
                onSelectProductCallback(productId, productName);
            } else {
                this.selectProduct(productId, productName);
            }
        };

        this.selectProduct = function (productId, productName) {
            $input.val(productId);
            $label.val(productName);
            $removeButton.toggle(productId !== '');
        };

        var makePicker = function (event) {
            $.magnificPopup.open({
                items: {src: $pickerButton.data('product-picker-url').replace('__instance_id__', instanceId)},
                type: 'iframe',
                closeOnBgClick: true
            });

            event.preventDefault();
        };
    };

    Shopsys.productPicker.onClickSelectProduct = function (instanceId, productId, productName) {
        window.parent.Shopsys.productPicker.instances[instanceId].onSelectProduct(productId, productName);
        $.magnificPopup.close();
    };

    Shopsys.productPicker.init = function ($container) {
        $container.filterAllNodes('.js-product-picker-create-picker-button').each(function () {
            var productPicker = new Shopsys.productPicker.ProductPicker($(this));
            productPicker.init();
        });
    };

    Shopsys.register.registerCallback(Shopsys.productPicker.init);

})(jQuery);