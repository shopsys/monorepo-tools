/*!
 *
 * Inspired: Responsive and Mobile-Friendly Tooltip
 * https://osvaldas.info/elegant-css-and-jquery-tooltip-responsive-mobile-friendly
 *
 */

$(function () {
    var targets = $('.form-error__icon');
    var target = false;
    var tooltip = false;

    targets.bind('mouseenter', function () {
        target = $(this);
        tooltip = $('.form-error__list');

        var initTooltip = function () {
            if ($(window).width() < tooltip.outerWidth() * 1.5) {
                tooltip.css('max-width', $(window).width() / 2);
            } else {
                tooltip.css('max-width', 340);
            }

            var posLeft = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2);
            var posTop = target.offset().top - tooltip.outerHeight() - 20;

            if (posLeft < 0) {
                posLeft = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            } else {
                tooltip.removeClass('left');
            }

            if (posLeft + tooltip.outerWidth() > $(window).width()) {
                posLeft = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            } else {
                tooltip.removeClass('right');
            }

            if (posTop < 0) {
                posTop = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            } else {
                tooltip.removeClass('top');
            }
        };

        initTooltip();
        $(window).resize(initTooltip);

    });
});
