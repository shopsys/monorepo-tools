(function ($) {

    var initLineChart = function ($chartCanvas) {
        // eslint-disable-next-line no-new
        new Chart($chartCanvas, {
            type: 'bar',
            data: {
                labels: $chartCanvas.data('chart-labels'),
                datasets: [{
                    data: $chartCanvas.data('chart-values'),
                    backgroundColor: 'rgba(0, 155, 217, 0.2)',
                    borderColor: 'rgb(0, 155, 217)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: $chartCanvas.data('chart-title')
                }
            }
        });
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-line-chart').each(function () {
            initLineChart($(this));
        });
    });

})(jQuery);
