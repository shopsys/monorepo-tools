SymfonyComponentValidatorConstraintsRange = function() {
	this.maxMessage = '';
	this.minMessage = '';
	this.invalidMessage = '';
	this.max = null;
	this.min = null;

	this.validate = function(value) {

		var f = FpJsFormValidator;
		var compareValue = SS6.number.parseNumber(value);

		if (f.isValueEmty(value) || (compareValue !== null && compareValue >= this.min && compareValue <= this.max)) {
			return [];
		} else if (compareValue < this.min) {
			return [
				this.minMessage
					.replace('{{ value }}', String(value))
					.replace('{{ compared_value }}', String(this.min))
			];
		} else if (compareValue > this.max) {
			return [
				this.maxMessage
					.replace('{{ value }}', String(value))
					.replace('{{ compared_value }}', String(this.max))
			];
		} else {
			return [
				this.invalidMessage
			];
		}
	};
};
