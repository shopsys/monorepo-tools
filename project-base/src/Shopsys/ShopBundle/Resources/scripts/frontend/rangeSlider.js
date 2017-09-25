(function ($) {

    Shopsys = Shopsys || {};
    Shopsys.rangeSlider = Shopsys.rangeSlider || {};

    Shopsys.rangeSlider.RangeSlider = function ($sliderElement) {
        var $minimumInput = $('#' + $sliderElement.data('minimumInputId'));
        var $maximumInput = $('#' + $sliderElement.data('maximumInputId'));
        var minimalValue = Shopsys.number.parseNumber($sliderElement.data('minimalValue'));
        var maximalValue = Shopsys.number.parseNumber($sliderElement.data('maximalValue'));
        var steps = 100;

        this.init = function () {
            var lastMinimumInputValue, lastMaximumInputValue;

            $sliderElement.slider({
                range: true,
                min: 0,
                max: steps,
                start: function () {
                    lastMinimumInputValue = $minimumInput.val();
                    lastMaximumInputValue = $maximumInput.val();
                },
                slide: function (event, ui) {
                    var minimumSliderValue = getValueFromStep(ui.values[0]);
                    var maximumSliderValue = getValueFromStep(ui.values[1]);
                    $minimumInput.val(minimumSliderValue != minimalValue ? Shopsys.number.formatDecimalNumber(minimumSliderValue, 2) : '');
                    $maximumInput.val(maximumSliderValue != maximalValue ? Shopsys.number.formatDecimalNumber(maximumSliderValue, 2) : '');
                },
                stop: function () {
                    if (lastMinimumInputValue != $minimumInput.val()) {
                        $minimumInput.change();
                    }
                    if (lastMaximumInputValue != $maximumInput.val()) {
                        $maximumInput.change();
                    }
                }
            });

            $minimumInput.change(updateSliderMinimum);
            updateSliderMinimum();

            $maximumInput.change(updateSliderMaximum);
            updateSliderMaximum();
        };

        function updateSliderMinimum () {
            var value = Shopsys.number.parseNumber($minimumInput.val()) || minimalValue;
            var step = getStepFromValue(value);
            $sliderElement.slider('values', 0, step);
        }

        function updateSliderMaximum () {
            var value = Shopsys.number.parseNumber($maximumInput.val()) || maximalValue;
            var step = getStepFromValue(value);
            $sliderElement.slider('values', 1, step);
        }

        function getStepFromValue (value) {
            return Math.round((value - minimalValue) / (maximalValue - minimalValue) * steps);
        }

        function getValueFromStep (step) {
            return minimalValue + (maximalValue - minimalValue) * step / steps;
        }

    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-range-slider').each(function () {
            var $this = $(this);
            var rangeSlider = new Shopsys.rangeSlider.RangeSlider($this);
            rangeSlider.init();
        });
    });

})(jQuery);
