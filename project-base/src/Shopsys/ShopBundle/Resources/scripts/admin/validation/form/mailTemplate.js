(function ($){
	$(document).ready(function () {

		$('#js-mail-templates').find('.js-mail-template').each(function () {
			var self = this;
			var sendMailId = $(this).attr('id') + '_sendMail';

			$(this).jsFormValidator({
				'groups': function () {

					var groups = [SS6.constant('\\Shopsys\\ShopBundle\\Form\\ValidationGroup::VALIDATION_GROUP_DEFAULT')];
					if ($(self).find('#' + sendMailId).is(':checked')) {
						groups.push(SS6.constant('\\Shopsys\\ShopBundle\\Form\\Admin\\Mail\\MailTemplateFormType::VALIDATION_GROUP_SEND_MAIL'));
					}

					return groups;
				}
			});
		});

	});
})(jQuery);
