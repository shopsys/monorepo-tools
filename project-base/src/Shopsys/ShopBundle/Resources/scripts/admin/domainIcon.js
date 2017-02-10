(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.domainIcon = Shopsys.domainIcon || {};

	var domainIcon = function ($container) {
		$container.filterAllNodes('.js-edit-domain-icon').click(function () {
			Shopsys.domainIcon.openDialog($(this));
			return false;
		});
		$container.filterAllNodes('#domain_form_save').closest('form').submit(function () {
			Shopsys.domainIcon.uploadIcon($(this));
			return false;
		});
	};

	Shopsys.register.registerCallback(domainIcon);

	Shopsys.domainIcon.openDialog = function($editDomainIcon) {
		Shopsys.ajax({
			url: $editDomainIcon.closest('.js-domain-icon-edit-container').data('url'),
			success: function (data) {
				Shopsys.window({
					content: data,
					wide: true
				});
			}
		});
	};

	Shopsys.domainIcon.uploadIcon = function($form) {
		var $iconErrorListContainer = $('#js-domain-icon-errors');
		var $spinner = $('.js-overlay-spinner');
		$iconErrorListContainer.hide();
		$spinner.show();
		Shopsys.ajax({
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