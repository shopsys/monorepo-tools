(function ($) {

    ShopsysFrameworkBundleComponentTransformersProductParameterValueToProductParameterValuesLocalizedTransformer = function () {

        this.transform = function (normData) {
            console.log('transform', normData);

            return normData;
        };

        this.reverseTransform = function (viewData) {
            var normData = [];

            for (var i in viewData) {
                var productParameterValuesLocalized = viewData[i];

                for (var locale in productParameterValuesLocalized.valueText) {
                    var valueText = productParameterValuesLocalized.valueText[locale];

                    if (valueText !== '') {
                        var productParameterValue = {
                            parameter: productParameterValuesLocalized.parameter,
                            locale: locale,
                            valueText: valueText
                        };

                        normData.push(productParameterValue);
                    }
                }
            }

            return normData;
        };
    };

})(jQuery);
