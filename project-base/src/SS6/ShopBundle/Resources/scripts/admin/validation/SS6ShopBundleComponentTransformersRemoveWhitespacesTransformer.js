(function ($) {

	function SS6ShopBundleComponentTransformersRemoveWhitespacesTransformer() {
		this.reverseTransform = function(value, ele) {
			return value.replace(/\s/g, '');
		};
	}

})(jQuery);