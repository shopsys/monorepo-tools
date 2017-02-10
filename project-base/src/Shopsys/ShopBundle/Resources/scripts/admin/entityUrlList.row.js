(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.entityUrls = Shopsys.entityUrls || {};

	Shopsys.entityUrls.Row = function ($row) {
		var $label = $row.find('.js-entity-url-list-row-label');
		var $checkbox = $row.find('.js-entity-url-list-row-checkbox');
		var $deleteBlock = $row.find('.js-entity-url-list-row-delete-block');
		var $deleteBlockButton = $deleteBlock.find('.js-entity-url-list-row-delete-block-button');
		var $revertBlock = $row.find('.js-entity-url-list-row-revert-block');
		var $revertBlockButton = $revertBlock.find('.js-entity-url-list-row-revert-block-button');
		var $radio = $row.find('.js-entity-url-list-select-main');
		var $mainDeleteInfo = $row.find('.js-entity-url-list-info-main-delete');
		var $deleteRevertWrapper = $row.find('.js-entity-url-list-row-delete-revert-wrapper');

		this.init = function () {
			$deleteBlockButton.click(function () {
				markAsDeleted(true);
				Shopsys.formChangeInfo.showInfo();
				return false;
			});

			$revertBlockButton.click(function () {
				markAsDeleted(false);
				Shopsys.formChangeInfo.showInfo();
				return false;
			});

			$radio.change(function () {
				var $allRadioButtons = $radio.closest('table').find('.js-entity-url-list-select-main');
				$allRadioButtons.each(function () {
					updateMain($(this));
				});
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
			var $row = radio.closest('.js-entity-url-list-friendly-url');
			var isMain = radio.is(':checked');
			var $mainDeleteInfo = $row.find('.js-entity-url-list-info-main-delete');
			var $deleteRevertWrapper = $row.find('.js-entity-url-list-row-delete-revert-wrapper');
			$deleteRevertWrapper.toggle(!isMain);
			$mainDeleteInfo.toggle(isMain);
		};
	};

	Shopsys.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-entity-url-list-friendly-url').each(function () {
			var entityUrlsRow = new Shopsys.entityUrls.Row($(this));
			entityUrlsRow.init();
		});
	});

})(jQuery);
