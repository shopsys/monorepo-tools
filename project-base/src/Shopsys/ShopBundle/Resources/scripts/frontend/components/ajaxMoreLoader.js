(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.AjaxMoreLoader = Shopsys.AjaxMoreLoader || {};
    Shopsys.list = Shopsys.list || {};

    var optionsDefaults = {
        buttonTextCallback: function (loadNextCount) {
            return Shopsys.translator.transChoice(
                '{1}Load next %loadNextCount% item|[2,Inf]Load next %loadNextCount% items',
                loadNextCount,
                { '%loadNextCount%': loadNextCount }
            );
        }
    };

    Shopsys.AjaxMoreLoader = function ($wrapper, options) {
        var $loadMoreButton;
        var $currentList;
        var $paginationToItemSpan;

        var totalCount;
        var pageSize;
        var page;
        var pageQueryParameter;
        var paginationToItem;
        var url;

        options = $.extend({}, optionsDefaults, options);

        this.init = function () {
            $loadMoreButton = $wrapper.filterAllNodes('.js-load-more-button');
            $currentList = $wrapper.filterAllNodes('.js-list');
            $paginationToItemSpan = $wrapper.filterAllNodes('.js-pagination-to-item');

            totalCount = $loadMoreButton.data('total-count');
            pageSize = $loadMoreButton.data('page-size');
            page = $loadMoreButton.data('page');
            pageQueryParameter = $loadMoreButton.data('page-query-parameter') || 'page';
            paginationToItem = $loadMoreButton.data('pagination-to-item');
            url = $loadMoreButton.data('url') || document.location;

            updateLoadMoreButton();
            $loadMoreButton.on('click', onClickLoadMoreButton);
        };

        var onClickLoadMoreButton = function () {
            $(this).hide();

            var requestData = {};
            requestData[pageQueryParameter] = page + 1;

            Shopsys.ajax({
                loaderElement: $wrapper,
                type: 'GET',
                url: url,
                data: requestData,
                success: function (data) {
                    var $response = $($.parseHTML(data));
                    var $nextItems = $response.find('.js-list > *');
                    $currentList.append($nextItems);
                    page++;
                    paginationToItem += $nextItems.length;
                    $paginationToItemSpan.text(paginationToItem);
                    updateLoadMoreButton();

                    Shopsys.register.registerNewContent($nextItems);
                }
            });
        };

        var updateLoadMoreButton = function () {
            var remaining = totalCount - page * pageSize;
            var loadNextCount = remaining >= pageSize ? pageSize : remaining;
            var buttonText = options.buttonTextCallback(loadNextCount);

            $loadMoreButton
                .val(buttonText)
                .toggle(remaining > 0);
        };

    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-list-with-paginator').each(function () {
            var ajaxMoreLoader = new Shopsys.AjaxMoreLoader($(this));
            ajaxMoreLoader.init();
        });
    });

})(jQuery);
