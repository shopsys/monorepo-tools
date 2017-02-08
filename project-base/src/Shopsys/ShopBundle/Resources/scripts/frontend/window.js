/**
 * Custom plugin window
 */
(function ($) {

	SS6 = window.SS6 || {};
	SS6.window = SS6.window || {};

	var $activeWindow = null;
	var animationTime = 300;
	var flexPopupHeightIssueDetectionBoundaryHeight = 45;

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
			$overlay = $('<div class="window-popup__overlay" id="js-overlay"></div>');
		}
		return $overlay;
	};

	var showOverlay = function () {
		var $overlay = getOverlay();
		$('body').addClass('web--window-activated').append($overlay);

		// timeout 0 to asynchronous run to fix css animation fade
		setTimeout(function(){
			$overlay.addClass('window-popup__overlay--active');
		}, 0);
	};

	var hideOverlay = function () {
		var $overlay = $('#js-overlay');
		$('body').removeClass('web--window-activated');
		$overlay.removeClass('window-popup__overlay--active');

		if ($overlay.length !== 0) {
			setTimeout(function(){
				$overlay.remove();
			}, animationTime);
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
			cssClass: 'window-popup--standard',
			cssClassContinue: '',
			cssClassCancel: '',
			closeOnBgClick: true,
			eventClose: function () {},
			eventContinue: function () {},
			eventCancel: function () {},
			eventOnLoad: function () {}
		};
		options = $.extend(defaults, options);

		if ($activeWindow !== null) {
			$activeWindow.trigger('windowFastClose');
		}

		var $window = $('<div class="window-popup" id="js-window"></div>');
		if (options.cssClass !== '') {
			$window.addClass(options.cssClass);
		}

		var $windowContent = $('<div class="js-window-content window-popup__in"></div>').html(options.content);

		$activeWindow = $window;

		$window.bind('windowClose', function () {
			$window.removeClass('window-popup--active');
			hideOverlay();

			setTimeout(function(){
				$activeWindow.trigger('windowFastClose');
			}, animationTime);
		});

		$window.bind('windowFastClose', function () {
			$(this).remove();
			hideOverlay();
			$activeWindow = null;
		});

		$window.append($windowContent);
		if (options.buttonClose) {
			var $windowButtonClose = $('<a href="#" class="window-button-close window-popup__close js-window-button-close" title="' + SS6.translator.trans('Close (Esc)') + '"><i class="svg svg-remove-thin"></i></a>');
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

		var $windowActions = $('<div class="window-popup__actions"></div>');
		if (options.buttonContinue && options.buttonCancel) {
			$windowActions.addClass('window-popup__actions--multiple-buttons');
		}

		if (options.buttonContinue) {
			var $windowButtonContinue = $('<a href="" class="window-popup__actions__btn window-popup__actions__btn--continue window-button-continue btn"><i class="svg svg-arrow"></i></a>');
			$windowButtonContinue
				.append(document.createTextNode(options.textContinue))
				.addClass(options.cssClassContinue)
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
			var $windowButtonCancel = $('<a href="#" class="window-popup__actions__btn window-popup__actions__btn--cancel window-button-cancel btn"><i class="svg svg-arrow"></i></a>');
			$windowButtonCancel
				.append(document.createTextNode(options.textCancel))
				.addClass(options.cssClassCancel)
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

		$(window).resize(function() {
			SS6.timeout.setTimeoutAndClearPrevious('window.window.resize', function() {
				fixVerticalAlign();
			}, 200);
		});

		/**
		 * Window with big height is fixed on top of viewport, smaller window is centered in viewport
		 */
		function fixVerticalAlign() {
			var windowAndViewportRatioLimitToCenter = 0.9;
			if ($window.height() / $(window).height() < windowAndViewportRatioLimitToCenter) {
				moveToCenter();
			} else {
				// remove css attribute "top" which is used by function moveToCenter()
				$window.css({ top: '' });
			}
		}

		function show() {
			showOverlay();
			if (options.closeOnBgClick) {
				getOverlay().click(function () {
					$window.trigger('windowClose');
					return false;
				});
			}
			$window.appendTo(getMainContainer());
			if ($window.height() < flexPopupHeightIssueDetectionBoundaryHeight) {
				$('html').addClass('is-flex-popup-height-issue-detected');
			}
			fixVerticalAlign();
			setTimeout(function(){
				$window.addClass('window-popup--active');
				options.eventOnLoad();
			}, animationTime);
		}

		function moveToCenter() {
			var relativeY = $(window).height() / 2 - $window.innerHeight() / 2;
			var minRelativeY = 10;

			if (relativeY < minRelativeY) {
				relativeY = minRelativeY;
			}

			var top = Math.round(relativeY);

			$window.css({ top: top + 'px' });
		}

		return $window;
	};

})(jQuery);
