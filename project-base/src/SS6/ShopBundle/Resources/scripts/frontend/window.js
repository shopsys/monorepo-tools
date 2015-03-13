/**
 * Custom plugin window
 */
(function ($) {

	SS6 = window.SS6 || {};
	SS6.window = SS6.window || {};

	var $activeWindow = null;

	var getMainContainer = function() {
		var $mainContainer = $('#window-main-container');
		if ($mainContainer.size() === 0) {
			$mainContainer = $('<div id="window-main-container"></div>');
			$('body').append($mainContainer);
		}
		return $mainContainer;
	};

	/**
	 * content (string)
	 * buttonClose (bool)
	 * buttonContinue (bool)
	 * textContinue (string)
	 * eventClose (function)
	 * eventContinue (function)
	 * urlContinue (string)
	 */
	SS6.window = function (options) {
		var defaults = {
			content: '',
			buttonClose: true,
			buttonCancel: false,
			buttonContinue: false,
			textContinue: 'Ano',
			textCancel: 'Ne',
			urlContinue: '#',
			eventClose: function () {},
			eventContinue: function () {},
			eventCancel: function () {}
		};
		var options = $.extend(defaults, options);

		if ($activeWindow !== null) {
			$activeWindow.trigger('windowFastClose');
		}

		var $window = $('<div class="window window--active"></div>');
		var $windowContent = $('<div></div>').html(options.content);

		$activeWindow = $window;

		$window.bind('windowClose', function () {
			$(this).fadeOut('fast', function () {$(this).trigger('windowFastClose')});
		});

		$window.bind('windowFastClose', function () {
			$(this).remove();
			$activeWindow = null;
		});

		$window.append($windowContent);
		if (options.buttonClose) {
			var $windowButtonClose = $('<a href="#" class="window-button-close window__close" title="Zavřít">X</a>');
			$windowButtonClose
				.bind('click.window', options.eventClose)
				.bind('click.windowClose', function () {
					$window.trigger('windowClose');
					return false;
				});
			$window.append($windowButtonClose);
		}

		var $windowActions = $('<div class="window__actions"></div>');
		if (options.buttonContinue) {
			var $windowButtonContinue = $('<a href="" class="window-button-continue button btn btn-primary"></a>');
			$windowButtonContinue
				.text(options.textContinue)
				.attr('href', options.urlContinue)
				.bind('click.window', options.eventContinue)
				.bind('click.windowContinue', function () {
					$window.trigger('windowClose');
					if ($(this).attr('href') === '#') {
						return false;
					}
				});
			$windowActions.append($windowButtonContinue);
		}

		if (options.buttonCancel) {
			var $windowButtonCancel = $('<a href="#" class="window-button-cancel button btn btn-primary ml-1"></a>');
			$windowButtonCancel
				.text(options.textCancel)
				.bind('click.windowEventCancel', options.eventCancel)
				.bind('click.windowEventClose', options.eventClose)
				.bind('click.windowClose', function () {
					$window.trigger('windowClose');
					return false;
				});
			$windowActions.append($windowButtonCancel);
		}

		if ($windowActions.children().size() > 0) {
			$window.append($windowActions);
		}

		$window.find('.js-tooltip[title]').tooltip();
		$window.hide().appendTo(getMainContainer()).fadeIn('fast');

		return $window;
	};

})(jQuery);
