(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.createConfirmDelete = function (confirmLink) {
        var ConfirmDelete = new Shopsys.ConfirmDelete(confirmLink, '#window-main-container .window .js-window-content');
        ConfirmDelete.init();
    };

    Shopsys.ConfirmDelete = function (confirmLink, messageContainerSelector) {
        var $confirmLink = $(confirmLink);
        var $messageContainer = $(messageContainerSelector);
        var $confirmDeleteForm = $messageContainer.find('.js-confirm-delete-form');
        var $confirmDeleteFormSelect = $confirmDeleteForm.find('.js-confirm-delete-select');
        var $confirmDeleteFormButton = $confirmDeleteForm.find('.btn');
        var $directDeleteLink = $messageContainer.find('.js-confirm-delete-direct-link');

        this.init = function () {
            if ($directDeleteLink.length !== 0) {
                $directDeleteLink.click(canDeleteDirectly);
            } else {
                $confirmDeleteForm.submit(onConfirmDeleteFormSubmit);
                $confirmDeleteFormSelect.change(refreshSubmitButton);
                refreshSubmitButton();
            }
        };

        var canDeleteDirectly = function () {
            Shopsys.ajax({
                url: $confirmLink.attr('href'),
                success: function (data) {
                    if ($($.parseHTML(data)).find('.js-confirm-delete-direct-link').length > 0) {
                        document.location = $directDeleteLink.attr('href');
                    } else {
                        $messageContainer.html(data);
                        var ConfirmDelete = new Shopsys.ConfirmDelete(confirmLink, messageContainerSelector);
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
                    .removeClass('btn--disabled cursor-help')
                    .tooltip('destroy');
            } else {
                $confirmDeleteFormButton
                    .addClass('btn--disabled cursor-help')
                    .tooltip({
                        title: Shopsys.translator.trans('Choose new value first'),
                        placement: 'right'
                    });
            }
        };

        var onConfirmDeleteFormSubmit = function () {
            return isSelectedNewValue();
        };
    };

})(jQuery);
