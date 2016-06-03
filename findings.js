
function speedChart(title,dataList){

    var data = {
        labels: [],
        series: dataList,
    };

    // We are setting a few options for our chart and override the defaults
    var options = {
        // If high is specified then the axis will display values explicitly up to this value and the computed maximum from the data is ignored
        //high: 3,
        // If low is specified then the axis will display values explicitly down to this value and the computed minimum from the data is ignored
        //low: 0,
        //ticks: [1, 10, 20, 30],
        // Don't draw the line chart points
        showPoint: false,
        // Disable line smoothing
        lineSmooth: true,
        // X-Axis specific configuration
        axisX: {
            // We can disable the grid for this axis
            showGrid: false,
            // and also don't show the label
            showLabel: false
        },
        // Y-Axis specific configuration
        axisY: {
            onlyInteger: false,
            // Lets offset the chart a bit from the labels
            offset: 60,
            // The label interpolation function enables you to modify the values
            // used for the labels on each axis. Here we are converting the
            // values into million pound.
            labelInterpolationFnc: function(value) {
                return value + 'm/s';
            }
        }
    };

    // All you need to do is pass your configuration as third parameter to the chart function
    new Chartist.Line('#'+title, data, options);
}

