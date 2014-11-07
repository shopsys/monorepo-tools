var SS6ShopBundleComponentNotSelectedDomainToShow = function() {
	this.message = '';

	this.validate = function(value, ele) {
		if (value.length === ele.domNode.children.length) {
			return this.message;
		} else {
			return [];
		}
	};
};
