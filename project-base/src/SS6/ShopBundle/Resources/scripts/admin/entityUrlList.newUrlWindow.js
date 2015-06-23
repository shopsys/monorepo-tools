(function ($) {

	SS6 = window.SS6 || {};
	SS6.entityUrls = SS6.entityUrls || {};

	SS6.entityUrls.NewUrlWindow = function ($window, $entityUrls) {
		var $domainSelectbox = $window.find('#url_list_new_url_form_domain');
		var $slugInput = $window.find('#url_list_new_url_form_slug');
		var $domainUrl = $window.find('.js-entity-url-list-window-content-domain-url');
		var $newUrlForm = $window.find('form[name=url_list_new_url_form]');
		var $domainRow = $window.find('.js-entity-url-list-window-content-domain-row');
		var domainUrlsById = $domainUrl.data('domain-urls-by-id');

		this.init = function () {
			$newUrlForm.submit(createNewUrlOnSubmit);
			$domainSelectbox.change(refreshDomainUrl);
			refreshDomainUrl();
			hideUnusedDomain();
		};

		var refreshDomainUrl = function () {
			$domainUrl.text(getSelectedDomainUrl());
		};

		var getSelectedDomainUrl = function () {
			return domainUrlsById[$domainSelectbox.val()] + '/';
		};

		var hideUnusedDomain = function () {
			$domainSelectbox.find('option').each(function (){
				var $urlListOnDomain = $entityUrls.find('.js-entity-url-list-domain-' + $(this).attr('value'));
				if ($urlListOnDomain.size() === 0) {
					$(this).remove();
				}
			});

			if ($domainSelectbox.find('option').size() === 1) {
				$domainRow.hide();
			}
		};

		var createNewUrlOnSubmit = function () {
			var $domainTable = $entityUrls.find('.js-entity-url-list-domain-' + $domainSelectbox.val());
			var prototypeHtml = $domainTable.data('new-url-prototype');
			var $newUrl = $($.parseHTML(prototypeHtml.replace(/__name__/g, '')));

			$newUrl.find('.js-entity-url-list-new-row-domain-url').text(getSelectedDomainUrl());
			$newUrl.find('.js-entity-url-list-new-row-slug').text($slugInput.val());
			$newUrl.find('.js-entity-url-list-new-row-slug-input').val($slugInput.val());

			$newUrl.appendTo($domainTable.find('>tbody'));
			$window.trigger('windowClose');

			return false;
		};
	};

})(jQuery);
