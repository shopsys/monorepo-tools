(function ($) {

    ShopsysFrameworkBundleComponentConstraintsUniqueProductParameters = function () {
        this.message = '';

        /**
         * This method is required
         * Should return an error message or an array of messages
         */
        this.validate = function (value) {
            var uniqueCollectionValidator = new ShopsysFrameworkBundleComponentConstraintsUniqueCollection();
            uniqueCollectionValidator.message = this.message;
            uniqueCollectionValidator.fields = ['parameter', 'locale'];
            uniqueCollectionValidator.allowEmpty = false;

            return uniqueCollectionValidator.validate(value);
        };

    };

})(jQuery);
