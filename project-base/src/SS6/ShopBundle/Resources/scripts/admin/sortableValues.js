(function ($) {

	SS6 = window.SS6 || {};
	SS6.sortableValues = SS6.sortableValues || {};

	SS6.sortableValues.init = function ($container) {
		$container.filterAllNodes('.js-sortable-values-item-add').click(SS6.sortableValues.addItemButtonClick);
		$container.filterAllNodes('.js-sortable-values-item-remove').click(SS6.sortableValues.removeItemButtonClick);

		$container.filterAllNodes('.js-sortable-values-items').sortable({
			items: '.js-sortable-values-item',
			handle: '.js-sortable-values-item-handle'
		});
	};

	SS6.sortableValues.addItemButtonClick = function () {
		var $button = $(this);
		var $container = $button.closest('.js-sortable-values-container');
		var $option = $container.find('.js-sortable-values-input :selected');

		if ($option.val()) {
			var $item = SS6.sortableValues.createItem($button.data('item-template'), $option.val(), $option.text());

			$container.find('.js-sortable-values-items').prepend($item);
			SS6.register.registerNewContent($item);

			SS6.sortableValues.disableOption($option);
		}
	};

	SS6.sortableValues.createItem = function (html, value, label) {
		html = html.replace(/%value%/g, SS6.escape.escapeHtml(value));
		html = html.replace(/%label%/g, SS6.escape.escapeHtml(label));

		return $($.parseHTML(html));
	};

	SS6.sortableValues.disableOption = function ($option) {
		var $select = $option.closest('.js-sortable-values-input');

		$option.prop('disabled', true);
		$select.val('');
	};

	SS6.sortableValues.removeItemButtonClick = function () {
		var $item = $(this).closest('.js-sortable-values-item');

		SS6.sortableValues.enableOptionOfItem($item);

		$item.remove();
	};

	SS6.sortableValues.enableOptionOfItem = function ($item) {
		var $container = $item.closest('.js-sortable-values-container');
		var $input = $item.find('.js-sortable-values-item-input');
		var $option = $container.find('.js-sortable-values-input [value="' + $input.val() + '"]');

		$option.prop('disabled', false);
	};

	SS6.register.registerCallback(SS6.sortableValues.init);

})(jQuery);