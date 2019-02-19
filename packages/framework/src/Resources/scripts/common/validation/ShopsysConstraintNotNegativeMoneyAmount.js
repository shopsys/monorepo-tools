(function () {

    ShopsysFrameworkBundleFormConstraintsNotNegativeMoneyAmount = function () {
        var self = this;
        this.message = '';

        this.validate = function (value) {
            if (value < 0) {
                return [self.message];
            }

            return [];
        };

    };

})();
