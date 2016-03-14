(function ($) {

	SymfonyComponentValidatorConstraintsNotNull = function () {
		this.message = '';

		this.validate = function (value, element) {
			var errors = [];
			var f = FpJsFormValidator;

			var isValueNull;

			if (element.type === SS6.constant('SS6\\ShopBundle\\Form\\FormType::CHECKBOX')) {
				isValueNull = value === null;
			} else {
				isValueNull = f.isValueEmty(value);
			}

			if (isValueNull) {
				errors.push(this.message.replace('{{ value }}', String(value)));
			}

			return errors;
		}
	};

})(jQuery);