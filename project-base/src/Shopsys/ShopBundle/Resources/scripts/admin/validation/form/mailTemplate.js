(function ($) {
    $(document).ready(function () {

        $('#js-mail-templates').find('.js-mail-template').each(function () {
            var self = this;
            var sendMailId = $(this).attr('id') + '_sendMail';

            $(this).jsFormValidator({
                'groups': function () {

                    var groups = [Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
                    if ($(self).find('#' + sendMailId).is(':checked')) {
                        groups.push(Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Mail\\MailTemplateFormType::VALIDATION_GROUP_SEND_MAIL'));
                    }

                    return groups;
                }
            });
        });

    });
})(jQuery);
