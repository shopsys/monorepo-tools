var SS6ShopBundleComponentConstraintsNotSelectedDomainToShow = function() {
	this.message = '';

	this.validate = function(value, ele) {
		if (value.length === ele.domNode.children.length) {
			return this.message;
		} else {
			return [];
		}
	};
};
