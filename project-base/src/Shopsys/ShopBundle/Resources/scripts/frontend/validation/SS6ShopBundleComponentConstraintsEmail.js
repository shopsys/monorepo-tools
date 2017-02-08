(function ($) {

	SS6ShopBundleComponentConstraintsEmail = function() {
		this.message = '';

		this.validate = function (value) {
			var regexp = /^.+\@\S+\.\S+$/i;
			var errors = [];
			var f = FpJsFormValidator;

			if (!f.isValueEmty(value) && !regexp.test(value)) {
				errors.push(this.message.replace('{{ value }}', String(value)));
			}

			return errors;
		};
	};

})(jQuery);