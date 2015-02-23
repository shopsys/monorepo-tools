var SS6ShopBundleComponentConstraintsNotInArray = function() {
	var self = this;
	this.message = '';
	this.array = [];

	this.validate = function (value) {
		if ($.inArray(value, this.array) !== -1) {
			self.message = self.message.replace('{{ array }}', self.formatArray(self.array));
			return [self.message];
		} else {
			return [];
		}
	};

	this.formatArray = function (values) {
		if (!$.isArray(values)) {
			return values;
		}
		var output = '';
		for (var i = 0; i < (values.length - 1); i++) {
			output = output.concat(values[i]).concat(', ');
		}
		output = output.concat(values[values.length - 1]);

		return output;
	};

};
