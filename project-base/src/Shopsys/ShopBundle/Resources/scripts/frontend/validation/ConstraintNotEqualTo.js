(function ($) {

    SymfonyComponentValidatorConstraintNotEqualTo = function () {
        this.message = '';
        this.value = null;

        this.validate = function (value) {

            var f = FpJsFormValidator;
            if (Shopsys.number.parseNumber(this.value) !== null) {
                var compareValue = Shopsys.number.parseNumber(value);
            } else {
                var compareValue = value;
            }

            if (f.isValueEmty(value) || (compareValue !== null && compareValue != this.value)) {
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

})(jQuery);
