/**
 * IE compatible hiding of select's options
 */
(function ($) {

	SS6 = window.SS6 || {};
	SS6.toggleOption = SS6.toggleOption || {};

	var wrapperClass = 'js-toggle-option-wrapper';


	SS6.toggleOption.hide = function($element) {
		$element.hide();
		if ($element.parent('span.' + wrapperClass).length === 0) {
			$element.wrap('<span class="' + wrapperClass + '" style="display: none;" />');
		}
	};

	SS6.toggleOption.show = function($element) {
		$element.show();
		if ($element.parent('span.' + wrapperClass).length > 0) {
			$element.unwrap();
		}
	};
})(jQuery);
