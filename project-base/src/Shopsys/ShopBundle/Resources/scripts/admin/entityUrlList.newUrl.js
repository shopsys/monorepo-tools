(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.entityUrls = Shopsys.entityUrls || {};

    Shopsys.entityUrls.NewUrl = function ($entityUrls) {
        var $buttonAdd = $entityUrls.find('.js-entity-url-list-button-add-url');
        var $newUrlsContainer = $entityUrls.find('.js-entity-url-list-new-urls');
        var newUrlsId = $newUrlsContainer.attr('id');

        this.init = function () {
            $buttonAdd.click(addNewUrl);
            $entityUrls.on('click', '.js-entity-url-list-new-row-delete-button', onClickRemoveNewUrl);
        };

        var addNewUrl = function () {
            var prototype = $newUrlsContainer.data('new-url-prototype');
            var index = getNextNewUrlIndex();
            var newUrl = prototype.replace(/__name__/g, index);
            var $newUrl = $($.parseHTML(newUrl));

            $newUrlsContainer.append($newUrl);

            Shopsys.validation.addNewItemToCollection('#' + newUrlsId, index);
            Shopsys.formChangeInfo.showInfo();
        };

        var getNextNewUrlIndex = function () {
            var index = 0;
            while ($newUrlsContainer.find('.js-entity-url-list-new-row[data-index=' + index.toString() + ']').length > 0) {
                index++;
            }

            return index;
        };

        var onClickRemoveNewUrl = function () {
            var $row = $(this).closest('.js-entity-url-list-new-row');
            var index = $row.data('index');
            Shopsys.validation.removeItemFromCollection('#' + newUrlsId, index);
            Shopsys.formChangeInfo.showInfo();
            $row.remove();
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-entity-url-list').each(function () {
            var entityUrlsNewUrl = new Shopsys.entityUrls.NewUrl($(this));
            entityUrlsNewUrl.init();
        });
    });

})(jQuery);
