(function ($) {

    SymfonyComponentValidatorConstraintsNotBlank = function () {
        this.message = '';

        this.validate = function (value, element) {
            var errors = [];
            var f = FpJsFormValidator;

            if (f.isValueEmty(value, element)) {
                errors.push(this.message.replace('{{ value }}', String(value)));
            }

            return errors;
        };
    };

})(jQuery);
