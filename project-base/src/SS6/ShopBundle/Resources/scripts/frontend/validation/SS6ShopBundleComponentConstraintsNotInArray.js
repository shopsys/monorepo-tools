var SS6ShopBundleComponentConstraintsNotInArray = function() {
	var self = this;
	this.message = '';
	this.array = [];

	this.validate = function (value) {
		console.log($.inArray(value, this.array));
		if ($.inArray(value, this.array) !== -1) {
			return [self.message];
		} else {
			return [];
		}
	};

};
