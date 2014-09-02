/**
 * Custom plugin window
 */

(function ($) {

	SS6 = window.SS6 || {};
	SS6.window = SS6.window || {};
	
	var windowPrefix = '#window-container-';
	var windowButtonCloseSelector = '.window__close';
	var windowButtonContinueSelector = '.window__continue';
	var windowMainContainerId = 'window-main-container';
	
	/**
	 * eventOnClose (function) - on close callback
	 * eventCloseButton (function) - button Close callback
	 * eventContinueButton (function) - button Yes callback
	 */
	SS6.window.create = function (options) {
		var defaults = {
			eventOnClose: function () {},
			eventCloseButton: function () {},
			eventContinueButton: function () {},
		};
		var options = $.extend(defaults, options);
		
		var $window = $(windowPrefix + options.id);
		
		$window
			.bind('windowClose', function (event) {
				options.eventOnClose.apply($window, [event]);
				$window.fadeOut('fast', function () { $(this).appendTo('body') });
			})
			.on('click.windowClose', windowButtonCloseSelector, function (event) {
				options.eventCloseButton.apply($window, [event]);
				$window.trigger('windowClose');
				event.preventDefault();
			})
			.on('click.windowContinue', windowButtonContinueSelector, function (event) {
				options.eventContinueButton.apply($window, [event]);
				$window.trigger('windowClose');
				if ($(this).attr('href') === '#') {
					event.preventDefault();
				}
			});
			
	}
	
	SS6.window.open = function(id) {
		var $window = $(windowPrefix + id);
		var windowId = $window.attr('id');
		var $mainContainer = this.getMainContainer();
		
		var isOpenned = false;
		$mainContainer.children().each(function() {
			if ($(this).attr('id') === windowId) {
				isOpenned = true;
			} else {
				$(this).trigger('windowClose');
			}
		});
		
		if (!isOpenned) {
			$window.hide().appendTo($mainContainer).fadeIn('fast').addClass('window--active');
		}
	}
	
	SS6.window.close = function(id) {
		var $window = $(windowPrefix + id);
		$window.trigger('windowClose');
	}
	
	SS6.window.getMainContainer = function() {
		var $mainContainer = $('#' + windowMainContainerId);
		if ($mainContainer.size() === 0) {
			$('body').append('<div id="' + windowMainContainerId + '"></div>');
			$mainContainer = $('#' + windowMainContainerId);
		}
		return $mainContainer;
	}
	
})(jQuery);


