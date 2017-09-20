(function ($) {

    SymfonyComponentValidatorConstraintsAll = function() {
        this.constraints = null;
        this.groups = null;

        this.validate = function (value, element) {
            var constraints = FpJsFormValidator.parseConstraints(this.constraints);
            var sourceId = 'form-input-error-' + String(element.id).replace(/_/g, '-');

            for (var childName in element.children) {
                var childElement = element.children[childName];
                var childValue = FpJsFormValidator.getElementValue(childElement);
                var errorPath = FpJsFormValidator.getErrorPathElement(childElement);

                var errors = FpJsFormValidator.validateConstraints(
                    childValue,
                    constraints,
                    this.groups,
                    childElement
                );

                FpJsFormValidator.customize(errorPath.domNode, 'showErrors', {
                    errors: errors,
                    sourceId: sourceId
                });
            }

            return [];
        };
    };

})(jQuery);