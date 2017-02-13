(function ($){

    Shopsys = Shopsys || {};
    Shopsys.grid = Shopsys.grid || {};
    Shopsys.grid.massAction = Shopsys.grid.massAction || {};

    Shopsys.grid.massAction = function ($grid) {
        var $selectAllCheckbox = $grid.find('.js-grid-mass-action-select-all');

        this.init = function () {
            $selectAllCheckbox.click(onSelectAll);
        };

        var onSelectAll = function () {
            $grid.find('.js-grid-mass-action-select-row').prop('checked', $selectAllCheckbox.is(':checked'));
        };

    };

    $(document).ready(function () {
        $('.js-grid').each(function () {
            var massAction = new Shopsys.grid.massAction($(this));
            massAction.init();
        });
    });

})(jQuery);
