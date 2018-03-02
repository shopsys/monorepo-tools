(function ($) {

    SymfonyComponentValidatorConstraintsNotNull = function () {
        this.message = '';

        this.validate = function (value, element) {
            var errors = [];
            var f = FpJsFormValidator;

            var isValueNull;

            if (element.type === Shopsys.constant('\\Symfony\\Component\\Form\\Extension\\Core\\Type\\CheckboxType::class')) {
                isValueNull = value === null;
            } else if (element.type === Shopsys.constant('\\Shopsys\\FrameworkBundle\\Form\\SingleCheckboxChoiceType::class')) {
                isValueNull = true;
                for (var i in value) {
                    if (value.hasOwnProperty(i) && value[i] === true) {
                        isValueNull = false;
                        break;
                    }
                }
            } else {
                isValueNull = f.isValueEmty(value);
            }

            if (isValueNull) {
                errors.push(this.message.replace('{{ value }}', String(value)));
            }

            return errors;
        };
    };

})(jQuery);
