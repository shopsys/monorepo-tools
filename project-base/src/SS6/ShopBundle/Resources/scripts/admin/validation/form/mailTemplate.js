(function ($){
	$(document).ready(function () {

		SS6 = window.SS6 || {};

		$('#js-mail-templates').find('.js-mail-template').each(function () {
			var self = this;
			var sendMailId = $(this).attr('id') + '_sendMail';

			$(this).jsFormValidator({
				'groups': function () {

					var groups = ['Default'];
					if ($(self).find('#' + sendMailId).is(':checked')) {
						groups.push(SS6.constant('\\SS6\\ShopBundle\\Form\\Admin\\Mail\\MailTemplateFormType::VALIDATION_GROUP_SEND_MAIL'));
					}

					return groups;
				}
			});
		});

	});
})(jQuery);
