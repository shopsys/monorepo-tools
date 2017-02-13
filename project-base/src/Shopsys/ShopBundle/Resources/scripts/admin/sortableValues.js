(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.sortableValues = Shopsys.sortableValues || {};

    Shopsys.sortableValues.init = function ($container) {
        $container.filterAllNodes('.js-sortable-values-item-add').click(Shopsys.sortableValues.addItemButtonClick);
        $container.filterAllNodes('.js-sortable-values-item-remove').click(Shopsys.sortableValues.removeItemButtonClick);

        $container.filterAllNodes('.js-sortable-values-items').sortable({
            items: '.js-sortable-values-item',
            handle: '.js-sortable-values-item-handle'
        });
    };

    Shopsys.sortableValues.addItemButtonClick = function () {
        var $button = $(this);
        var $container = $button.closest('.js-sortable-values-container');
        var $option = $container.find('.js-sortable-values-input :selected');

        if ($option.val()) {
            var $item = Shopsys.sortableValues.createItem($button.data('item-template'), $option.val(), $option.text());

            $container.find('.js-sortable-values-items').prepend($item);
            Shopsys.register.registerNewContent($item);

            Shopsys.sortableValues.disableOption($option);
        }
    };

    Shopsys.sortableValues.createItem = function (html, value, label) {
        html = html.replace(/%value%/g, Shopsys.escape.escapeHtml(value));
        html = html.replace(/%label%/g, Shopsys.escape.escapeHtml(label));

        return $($.parseHTML(html));
    };

    Shopsys.sortableValues.disableOption = function ($option) {
        var $select = $option.closest('.js-sortable-values-input');

        $option.prop('disabled', true);
        $select.val('');
    };

    Shopsys.sortableValues.removeItemButtonClick = function () {
        var $item = $(this).closest('.js-sortable-values-item');

        Shopsys.sortableValues.enableOptionOfItem($item);

        $item.remove();
    };

    Shopsys.sortableValues.enableOptionOfItem = function ($item) {
        var $container = $item.closest('.js-sortable-values-container');
        var $input = $item.find('.js-sortable-values-item-input');
        var $option = $container.find('.js-sortable-values-input [value="' + $input.val() + '"]');

        $option.prop('disabled', false);
    };

    Shopsys.register.registerCallback(Shopsys.sortableValues.init);

})(jQuery);