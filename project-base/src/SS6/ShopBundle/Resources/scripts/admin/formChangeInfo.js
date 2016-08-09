(function ($) {

	SS6 = window.SS6 || {};
	SS6.formChangeInfo = SS6.formChangeInfo || {};

	var isFormSubmitted = false;
	var isInfoShown = false;

	SS6.formChangeInfo.initContent = function ($container) {
		$container.filterAllNodes('.web__content form')
			.change(SS6.formChangeInfo.showInfo)
			.each(function() {
				if ($(this).find('.form-input-error:first, .js-validation-errors-list li:first').length > 0) {
					SS6.formChangeInfo.showInfo();
				}
			});
	};

	SS6.formChangeInfo.initDocument = function () {
		$(document).on('submit', '.web__content form', function (event) {
			if (event.isDefaultPrevented() === false) {
				isFormSubmitted = true;
			}
		});

		$(window).on('beforeunload', function() {
			if (isInfoShown && !isFormSubmitted) {
				return SS6.translator.trans('Máte neuložené změny!');
			}
		});
	};

	SS6.formChangeInfo.initWysiwygEditors = function () {
		if (typeof CKEDITOR !== 'undefined') {
			for (var i in CKEDITOR.instances) {
				var instance = CKEDITOR.instances[i];
				if (!instance.formChangeInfoInitilized) {
					instance.on('change', SS6.formChangeInfo.showInfo);
					instance.formChangeInfoInitilized = true;
				}
			}
		}
	};

	SS6.formChangeInfo.showInfo = function () {
		var textToShow = SS6.translator.trans('Provedli jste změny, nezapomeňte je uložit!');
		var $fixedBarIn = $('.web__content .window-fixed-bar .window-fixed-bar__in');
		var $infoDiv = $fixedBarIn.find('#js-form-change-info');
		if (!isInfoShown) {
			$fixedBarIn.prepend(
				'<div class="window-fixed-bar__item">\
					<div id="js-form-change-info" class="window-fixed-bar__item__cell text-center">\
						<strong>' + textToShow + '</strong>\
					</div>\
				</div>');
		} else {
			$infoDiv.text = textToShow;
		}
		if ($fixedBarIn.length > 0) {
			isInfoShown = true;
		}
	};

	SS6.formChangeInfo.removeInfo = function () {
		$('#js-form-change-info').remove();
		isInfoShown = false;
	};

	SS6.register.registerCallback(function ($container) {
		SS6.formChangeInfo.initContent($container);
		SS6.formChangeInfo.initWysiwygEditors();
	});

	$(document).ready(function () {
		SS6.formChangeInfo.initDocument();
	});

})(jQuery);
