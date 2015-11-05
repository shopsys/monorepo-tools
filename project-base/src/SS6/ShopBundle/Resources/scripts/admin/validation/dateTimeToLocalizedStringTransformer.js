(function ($) {

	SymfonyComponentFormExtensionCoreDataTransformerDateTimeToLocalizedStringTransformer = function() {
		this.reverseTransform = function(value) {
			if (this.pattern.toLowerCase() === 'dd.mm.yyyy') {
				var regexp = /^(\d{2})\.(\d{2})\.(\d{4})$/;
				var parts = regexp.exec(value);
				if (parts) {
					value = parts[3] + '-' + parts[2] + '-' + parts[1];
				}
			}
			return value;
		};
	}

})(jQuery);