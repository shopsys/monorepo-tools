(function ($) {

    $.fn.filterAllNodes = function (selector) {
        var $result = $(this).find(selector).addBack(selector);

        // .addBack() does not change .prevObject, so we need to do it manually for proper functioning of .end() method
        $result.prevObject = $result.prevObject.prevObject;

        return $result;
    };

})(jQuery);