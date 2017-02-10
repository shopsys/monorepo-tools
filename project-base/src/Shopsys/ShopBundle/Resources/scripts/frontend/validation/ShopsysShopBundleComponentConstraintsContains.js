(function ($) {

	ShopsysShopBundleComponentConstraintsContains = function() {
		var self = this;
		this.message = '';
		this.needle = null;

		this.validate = function (value) {
			var result = [];

			if (value.indexOf(self.needle) === -1) {
				result.push(FpJsBaseConstraint.prepareMessage(
					self.message,
					{
						'{{ value }}': '"' + value + '"',
						'{{ needle }}': '"' + self.needle + '"'
					}
				));
			}

			return result;
		};

	};

})(jQuery);