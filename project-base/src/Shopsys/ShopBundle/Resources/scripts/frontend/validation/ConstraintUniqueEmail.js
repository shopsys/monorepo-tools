(function ($) {

    ShopsysShopBundleComponentConstraintsUniqueEmail = function () {
        this.message = null;

        this.validate = function (value, element) {
            var self = this;
            var $emailInput = $('#' + element.id);
            var url = $emailInput.data('request-url');

            FpJsFormValidator.ajax.sendRequest(
                url,
                {email: value},
                function (response) {
                    var existsEmail = JSON.parse(response);

                    if (existsEmail) {
                        var sourceId = 'form-error-' + String(element.id).replace(/_/g, '-');
                        var message = self.message.replace('{{ email }}', value);
                        element.showErrors([message], sourceId);
                        $emailInput.addClass('form-input-error');
                    }
                }
            );

            return [];
        };
    };

})(jQuery);
