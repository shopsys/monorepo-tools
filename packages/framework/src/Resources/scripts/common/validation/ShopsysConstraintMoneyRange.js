(function () {

    ShopsysFrameworkBundleFormConstraintsMoneyRange = function () {
        var self = this;
        this.minMessage = '';
        this.maxMessage = '';
        this.min = null;
        this.max = null;

        this.validate = function (value) {
            if (!FpJsFormValidator.isValueEmty(value)) {
                var compareValue = Shopsys.number.parseNumber(value);

                if (self.max !== null && compareValue > Shopsys.number.parseNumber(self.max.amount)) {
                    return [self.maxMessage.replace('{{ limit }}', self.max.amount)];
                }
                if (self.min !== null && compareValue < Shopsys.number.parseNumber(self.min.amount)) {
                    return [self.minMessage.replace('{{ limit }}', self.min.amount)];
                }
            }

            return [];
        };

    };

})();
