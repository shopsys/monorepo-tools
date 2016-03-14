(function ($) {

	SymfonyComponentValidatorConstraintsNotNull = function () {
		this.message = '';

		this.validate = function (value) {
			var errors = [];
			var f = FpJsFormValidator;

			if (f.isValueEmty(value)) {
				errors.push(this.message.replace('{{ value }}', String(value)));
			}

			return errors;
		}
	};

})(jQuery);