/**
 * Custom plugin window
 */
(function ($) {

	SS6 = window.SS6 || {};
	SS6.window = SS6.window || {};

	var $activeWindow = null;

	var getMainContainer = function() {
		var $mainContainer = $('#window-main-container');
		if ($mainContainer.length === 0) {
			$mainContainer = $('<div id="window-main-container"></div>');
			$('body').append($mainContainer);
		}
		return $mainContainer;
	};

	var getOverlay = function() {
		var $overlay = $('#js-overlay');
		if ($overlay.length === 0) {
			$overlay = $('<div id="js-overlay"></div>');
		}
		return $overlay;
	};

	var showOverlay = function () {
		var $overlay = getOverlay();
		$('body').append($overlay);
	};

	var hideOverlay = function () {
		if ($('#js-overlay').length !== 0) {
			$('#js-overlay').remove();
		}
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
			wide: false,
			cssClass: '',
			closeOnBgClick: true,
			eventClose: function () {},
			eventContinue: function () {},
			eventCancel: function () {}
		};
		var options = $.extend(defaults, options);

		if ($activeWindow !== null) {
			$activeWindow.trigger('windowFastClose');
		}

		var $window = $('<div class="window window--active"></div>');
		if (options.wide) {
			$window.addClass('window--wide');
		}
		if (options.cssClass !== '') {
			$window.addClass(options.cssClass);
		}

		var $windowContent = $('<div class="js-window-content"></div>').html(options.content);

		$activeWindow = $window;

		$window.bind('windowClose', function () {
			hideOverlay();
			$(this).fadeOut('fast', function () {$(this).trigger('windowFastClose')});
		});

		$window.bind('windowFastClose', function () {
			$(this).remove();
			$activeWindow = null;
		});

		$window.append($windowContent);
		if (options.buttonClose) {
			var $windowButtonClose = $('<a href="#" class="window-button-close window__close js-window-button-close" title="Zavřít (Esc)">X</a>');
			$windowButtonClose
				.bind('click.window', options.eventClose)
				.bind('click.windowClose', function () {
					$window.trigger('windowClose');
					return false;
				});
			$window.append($windowButtonClose);
		}

		$('body').keyup(function (event) {
			if (event.keyCode === SS6.keyCodes.ESCAPE) {
				$window.trigger('windowClose');
				return false;
			}
		});

		var $windowActions = $('<div class="window__actions"></div>');
		if (options.buttonContinue) {
			var $windowButtonContinue = $('<a href="" class="window__actions__btn window-button-continue btn"></a>');
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
			var $windowButtonCancel = $('<a href="#" class="window__actions__btn window-button-cancel btn btn--default"></a>');
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

		if ($windowActions.children().length > 0) {
			$window.append($windowActions);
		}

		SS6.register.registerNewContent($window);

		show();

		return $window;

		function show() {
			showOverlay();
			if (options.closeOnBgClick) {
				getOverlay().click(function () {
					$window.trigger('windowClose');
					return false;
				});
			}
			$window.hide().appendTo(getMainContainer());
			if (options.wide) {
				moveToCenter();
			}
			$window.fadeIn('fast');
		}

		function moveToCenter() {
			var relativeY = $(window).height() / 2 - $window.height() / 2;
			var minRelativeY = $(window).height() * 0.1;

			if (relativeY < minRelativeY) {
				relativeY = minRelativeY;
			}

			var top = Math.round($(window).scrollTop() + relativeY);

			$window.css({ top: top + 'px' });
		}
	};

})(jQuery);
