(function ($) {

	ShopsysShopBundleComponentTransformersRemoveWhitespacesTransformer = function() {
		this.reverseTransform = function(value, ele) {
			return value.replace(/\s/g, '');
		};
	}

})(jQuery);