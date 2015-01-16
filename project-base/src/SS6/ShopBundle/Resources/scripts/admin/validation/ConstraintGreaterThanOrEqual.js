SymfonyComponentValidatorConstraintsGreaterThanOrEqual = function() {
	this.message = '';
	this.value = null;

	this.validate = function(value) {

	var f = FpJsFormValidator;
	var compareValue = value.toString().replace(',', '.');
	var regexpNumber = /^[-+]?[0-9]+((\.|,)?[0-9]+)?$/;
	var isNumber = regexpNumber.test(compareValue);
	if (f.isValueEmty(compareValue) || compareValue >= this.value || !isNumber) {
		return [];
	} else {
		return [
		this.message
			.replace('{{ value }}', String(value))
			.replace('{{ compared_value }}', String(this.value))
		];
	}
	};
};
