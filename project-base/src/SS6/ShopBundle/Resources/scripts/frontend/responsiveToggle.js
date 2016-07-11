(function($) {

	/*
	 1. add to button class 'js-responsive-toggle'
	 2. set data-element to ID of target element to show/hide only on non desktop version
	 3. set hide-on-click-out to true, if it should hide after click on page or different element
	 4. switch to desktop version reset visibility of data-element from 2.
	 */

	SS6 = window.SS6 || {};

	var activeButtonClass = 'active';
	var instanceCoutner = 0;

	SS6.ResponsiveToggle = function ($button, $elementToHide, hideOnClickOut) {
		var defaultActive = null;
		var instanceNumber = instanceCoutner;
		instanceCoutner++;

		this.init = function () {
			defaultActive = isActive();
			$button.click(function () {
				toggle(!isActive());
				return false;
			});

			if (hideOnClickOut) {
				$(document).click(onClickOut);
			}

			$(window).resize(function() {
				SS6.timeout.setTimeoutAndClearPrevious('ResponsiveToggle.window.resize.' + instanceNumber, onWindowResize, 200);
			});
		};

		function isActive() {
			return $button.hasClass(activeButtonClass);
		}

		function toggle(show) {
			$button.toggleClass(activeButtonClass, show);
			$elementToHide.fadeToggle(show);
		}

		function onClickOut(event) {
			if (
				isActive()
				&& $(event.target).closest($button).length === 0
				&& $(event.target).closest($elementToHide).length === 0
			) {
				toggle(false);
			}
		}

		function onWindowResize() {
			if (SS6.responsive.isDesktopVersion()) {
				if ($elementToHide.is(':animated')) {
					$elementToHide.stop(true, true);
				}
				$button.toggleClass(activeButtonClass, defaultActive);
				$elementToHide.css('display', '');
			}
		}

	};

	$(document).ready(function() {

		$('.js-responsive-toggle').each(function() {
			var $button = $(this);
			var $elementToHide = $('#' + $button.data('element'));
			var hideOnClickOut = $button.data('hide-on-click-out');

			var responsiveToggle = new SS6.ResponsiveToggle($button, $elementToHide, hideOnClickOut);
			responsiveToggle.init();
		});

	});
})(jQuery);