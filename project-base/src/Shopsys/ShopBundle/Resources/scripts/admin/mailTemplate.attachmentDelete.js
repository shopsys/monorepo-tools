(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.mailTemplate = Shopsys.mailTemplate || {};

    Shopsys.mailTemplate.AttachmentDelete = function ($attachment) {
        var $deleteButton = $attachment.find('.js-mail-template-attachment-delete-button');
        var $revertButton = $attachment.find('.js-mail-template-attachment-delete-revert-button');
        var $revert = $attachment.find('.js-mail-template-attachment-delete-revert');
        var $checkbox = $attachment.find('.js-mail-template-attachment-delete-checkbox');

        this.init = function () {
            $deleteButton.click(deleteButtonClick);
            $revertButton.click(revertButtonClick);
            updateState();
        };

        var deleteButtonClick = function () {
            $checkbox.prop('checked', true);
            updateState();
        };

        var revertButtonClick = function () {
            $checkbox.prop('checked', false);
            updateState();
        };

        var updateState = function () {
            var isChecked = $checkbox.prop('checked');
            $deleteButton.toggle(!isChecked);
            $revert.toggle(isChecked);
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-mail-template-attachment').each(function () {
            var attachmentDelete = new Shopsys.mailTemplate.AttachmentDelete($(this));
            attachmentDelete.init();
        });
    });

})(jQuery);
