(function($) {

	SS6 = window.SS6 || {};

	var autocompleteSelectbox = function ($container) {
		$container.filterAllNodes('select.js-autocomplete-selectbox').selectize();
	};

	SS6.register.registerCallback(autocompleteSelectbox);

})(jQuery);
