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
		var $radio = $row.find('.js-entity-urls-select-main');
		var $mainDeleteInfo = $row.find('.js-entity-urls-info-main-delete');
		var $deleteRevertWrapper = $row.find('.js-entity-urls-row-delete-revert-wrapper');

		this.init = function () {
			$deleteBlockButton.click(function () {
				markAsDeleted(true);
				return false;
			});

			$revertBlockButton.click(function () {
				markAsDeleted(false);
				return false;
			});

			$radio.change(function () {
				var $allRadioButtons = $radio.closest('table').find('.js-entity-urls-select-main');
				$allRadioButtons.each(function () {
					updateMain($(this));
				});
				return false;
			});

			markAsDeleted($checkbox.is(':checked'));
			markAsMain($radio.is(':checked'));
		};

		var markAsDeleted = function (toDelete) {
			$checkbox.prop('checked', toDelete);
			$radio.attr('disabled', toDelete);
			$label.toggleClass('text-disabled', toDelete);
			$deleteBlock.toggle(!toDelete);
			$revertBlock.toggle(toDelete);
		};

		var markAsMain = function (isMain) {
			$deleteRevertWrapper.toggle(!isMain);
			$mainDeleteInfo.toggle(isMain);
		};

		var updateMain = function (radio) {
			var $row = radio.closest('.js-entity-urls-friendly-url');
			var isMain = radio.is(':checked');
			var $mainDeleteInfo = $row.find('.js-entity-urls-info-main-delete');
			var $deleteRevertWrapper = $row.find('.js-entity-urls-row-delete-revert-wrapper');
			$deleteRevertWrapper.toggle(!isMain);
			$mainDeleteInfo.toggle(isMain);
		};
	};

	$(document).ready(function () {
		$('.js-entity-urls-friendly-url').each(function () {
			var entityUrlsRow = new SS6.entityUrls.Row($(this));
			entityUrlsRow.init();
		});
	});

})(jQuery);
