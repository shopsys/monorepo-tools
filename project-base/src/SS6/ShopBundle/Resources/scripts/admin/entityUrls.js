(function ($) {

	SS6 = window.SS6 || {};
	SS6.entityUrls = SS6.entityUrls || {};

	SS6.entityUrls.Row = function ($row) {
		var $label = $row.find('.js-entity-urls-row-label');
		var $checkbox = $row.find('.js-entity-urls-row-checkbox');
		var $deleteBlock = $row.find('.js-entity-urls-row-delete-block');
		var $deleteBlockButton = $deleteBlock.find('.js-entity-urls-row-delete-block-button');
		var $revertBlock = $row.find('.js-entity-urls-row-revert-block');
		var $revertBlockButton = $revertBlock.find('.js-entity-urls-row-revert-block-button');

		this.init = function () {
			$deleteBlockButton.click(function () {
				markAsDeleted(true);
				return false;
			});

			$revertBlockButton.click(function () {
				markAsDeleted(false);
				return false;
			});

			markAsDeleted($checkbox.is(':checked'));
		};

		var markAsDeleted = function (toDelete) {
			$checkbox.prop('checked', toDelete);
			$label.toggleClass('text-disabled', toDelete);
			$deleteBlock.toggle(!toDelete);
			$revertBlock.toggle(toDelete);
		}
	};

	$(document).ready(function () {
		$('.js-entity-urls-friendly-url').each(function () {
			var entityUrlsRow = new SS6.entityUrls.Row($(this));
			entityUrlsRow.init();
		});
	});

})(jQuery);
