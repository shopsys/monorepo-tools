(function ($) {

	SS6ShopBundleComponentTransformersRemoveWhitespacesTransformer = function() {
		this.reverseTransform = function(value, ele) {
			return value.replace(/\s/g, '');
		};
	}

})(jQuery);