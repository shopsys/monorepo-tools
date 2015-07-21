(function ($) {

	SS6 = window.SS6 || {};
	SS6.domainIcon = SS6.domainIcon || {};

	var domainIcon = function ($container) {
		$container.find('.js-edit-domain-icon').click(function () {
			SS6.domainIcon.openDialog($(this));
			return false;
		});
		$container.find('#domain_form_save').closest('form').submit(function () {
			SS6.domainIcon.uploadIcon($(this));
			return false;
		});
	};

	SS6.register.registerCallback(domainIcon);

	SS6.domainIcon.openDialog = function($editDomainIcon) {
		$.ajax({
			url: $editDomainIcon.closest('a').attr('href'),
			success: function (data) {
				SS6.window({
					content: data,
					wide: true
				});
			}
		});
	};

	SS6.domainIcon.uploadIcon = function($form) {
		var $iconErrorListContainer = $('#js-domain-icon-errors');
		var $spinner = $('.js-domain-icon-spinner');
		$iconErrorListContainer.hide();
		$spinner.show();
		$.ajax({
			url: $form.attr('action'),
			data: $form.serialize(),
			type: $form.attr('method'),
			dataType: 'json',
			success: function (data) {
				if (data['result'] === 'valid') {
					document.location.reload();
				} else if (data['result'] === 'invalid') {
					var $iconErrorList = $iconErrorListContainer.show().find('ul');
					$iconErrorList.find('li').remove();
					for (var i in data['errors'] ) {
						$iconErrorList.append('<li>' + data['errors'][i] + '</li>');
					}
					$spinner.hide();
				}
			}
		});
	};

})(jQuery);