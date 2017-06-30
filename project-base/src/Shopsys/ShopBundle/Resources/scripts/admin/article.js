(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.article = Shopsys.article || {};

    Shopsys.article.init = function () {
        var $domainSelectInput = $('#article_form_domainId');
        var $metaDescriptionInput = $('#article_form_seoMetaDescription');

        $($domainSelectInput).on('change', function () {
           Shopsys.article.changeMetaDescriptionPlaceholderByDomainId($metaDescriptionInput, $(this).val());
        });

    };

    $(document).ready(function () {
        Shopsys.article.init();
    });

    Shopsys.article.changeMetaDescriptionPlaceholderByDomainId = function ($metaDescriptionInput, domainId) {
        var metaDescriptionPlaceHolderText = $metaDescriptionInput.data('placeholderDomain' + domainId);

        $metaDescriptionInput.attr('placeholder', metaDescriptionPlaceHolderText);
    };

})(jQuery);
