(function ($) {

	SS6 = window.SS6 || {};

	SS6.createConfirmDelete = function (confirmLink) {
		var ConfirmDelete = new SS6.ConfirmDelete(confirmLink, '#window-main-container .window .js-window-content');
		ConfirmDelete.init();
	};

	SS6.ConfirmDelete = function (confirmLink, messageContainerSelector) {
		var self = this;
		var $confirmLink = $(confirmLink);
		var $messageContainer = $(messageContainerSelector);
		var $confirmDeleteForm = $messageContainer.find('.js-confirm-delete-form');
		var $confirmDeleteFormSelect = $confirmDeleteForm.find('.js-confirm-delete-select');
		var $confirmDeleteFormButton = $confirmDeleteForm.find('.btn');
		var $directDeleteLink = $messageContainer.find('.js-confirm-delete-direct-link');

		this.init = function () {
			if ($directDeleteLink.size() !== 0) {
				$directDeleteLink.click(canDeleteDirectly);
			} else {
				$confirmDeleteForm.submit(onConfirmDeleteFormSubmit);
				$confirmDeleteFormSelect.change(refreshSubmitButton);
				refreshSubmitButton();
			}
		};

		var canDeleteDirectly = function () {
			SS6.ajax({
				url: $confirmLink.attr('href'),
				success: function(data) {
					if ($($.parseHTML(data)).find('.js-confirm-delete-direct-link').size() > 0) {
						document.location = $directDeleteLink.attr('href');
					} else {
						$messageContainer.html(data);
						var ConfirmDelete = new SS6.ConfirmDelete(confirmLink, messageContainerSelector);
						ConfirmDelete.init();
					}
				}
			});

			return false;
		};

		var isSelectedNewValue = function () {
			return $confirmDeleteFormSelect.val() !== '';
		};

		var refreshSubmitButton = function () {
			if (isSelectedNewValue()) {
				$confirmDeleteFormButton
					.removeClass('btn--disabled h-cursor-help')
					.tooltip('destroy');
			} else {
				$confirmDeleteFormButton
					.addClass('btn--disabled h-cursor-help')
					.tooltip({
						title: SS6.translator.trans('Nejprve vyberte novou hodnotu'),
						placement: 'right'
					});
			}
		};

		var onConfirmDeleteFormSubmit = function() {
			return isSelectedNewValue();
		};
	};

})(jQuery);
