(function ($) {

	SS6ShopBundleComponentConstraintsNotInArray = function() {
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

			return values.join();
		};

	};

})(jQuery);