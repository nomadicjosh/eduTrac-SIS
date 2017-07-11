/*
 * Author: Joshua Parker
 * Date: January 14, 2017
 * Description:
 *      Highcharts to be used for the dashboard.
 **/

$(function () {
    var options = {
        credits: {
            enabled: false
        },
        chart: {
            renderTo: 'getSACP',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: null
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.point.name + '</b>: ' + this.y;
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                innerSize: 100,
                depth: 45,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function () {
                        return '<b>' + this.point.name + '</b>: ' + Highcharts.numberFormat(this.percentage) + ' %';
                    }
                },
                showInLegend: true
            }
        },
        series: [{
                type: 'pie',
                name: null,
                data: []
            }]
    };

    $.getJSON(rootPath + "dashboard/getSACP/", function (json) {
        options.series[0].data = json;
        chart = new Highcharts.Chart(options);
    });
});

$(function () {
    var options = {
        colors: ['#70b7f0', '#e76486'],
        credits: {
            enabled: false
        },
        chart: {
            renderTo: 'getDEPT',
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 15,
                beta: 15,
                viewDistance: 25
            }
        },
        title: {
            text: null,
            x: -20 //center
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            title: {
                text: 'Students'
            },
            plotLines: [{
                    value: 0,
                    width: 1
                }]
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                        this.x + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || '#000000'
                },
                showInLegend: true
            }
        },
        series: []
    };

    $.getJSON(rootPath + "dashboard/getDEPT/", function (json) {
        options.xAxis.categories = json[0]['data'];
        options.series[0] = json[1];
        options.series[1] = json[2];
        chart = new Highcharts.Chart(options);
    });
});