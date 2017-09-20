(function () {

    Shopsys = Shopsys || {};
    Shopsys.number = Shopsys.number || {};

    Shopsys.number.parseNumber = function (value) {

        var compareValue = value.toString().replace(',', '.');
        var regexpNumber = /^[-+]?[0-9]+((\.|,)?[0-9]+)?$/;
        if (regexpNumber.test(compareValue)) {
            return parseFloat(compareValue);
        } else {
            return null;
        }
    };

    Shopsys.number.formatDecimalNumber = function (value, scale) {
        return value.toFixed(scale).replace('.', ',');
    };

})();
