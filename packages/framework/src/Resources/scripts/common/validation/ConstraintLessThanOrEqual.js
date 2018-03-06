(function ($) {

    SymfonyComponentValidatorConstraintsLessThanOrEqual = function () {
        this.message = '';
        this.value = null;

        this.validate = function (value) {

            var f = FpJsFormValidator;
            var compareValue = Shopsys.number.parseNumber(value);

            if (f.isValueEmty(value) || (compareValue !== null && compareValue <= this.value)) {
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
