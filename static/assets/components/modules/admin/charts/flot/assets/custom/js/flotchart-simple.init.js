(function($)
{
	if (typeof charts == 'undefined') 
		return;

	charts.chart_simple = 
	{
		// data
		data: 
		{
			sin: [],
			cos: []
		},
		
		// will hold the chart object
		plot: null,

		// chart options
		options: 
		{
			colors: [ primaryColor, "#7c7c7c" ],
			grid: {
				color: "#dedede",
			    borderWidth: 1,
			    borderColor: "transparent",
			    clickable: true, 
			    hoverable: true
			},
	        series: {
	        	grow: {active: false},
	            lines: {
            		show: true,
            		fill: false,
            		lineWidth: 4,
            		steps: false
            	},
	            points: {
	            	show:true,
	            	radius: 5,
	            	symbol: "circle",
	            	fill: true,
	            	borderColor: "#fff"
	            }
	        },
	        legend: { position: "se", backgroundColor: null, backgroundOpacity: 0, noColumns: 2 },
	        shadowSize:0,
	        yaxis: { ticks:3 },
	        xaxis: { ticks:4, tickDecimals: 0, tickColor: 'transparent' },
	        tooltip: true, //activate tooltip
			tooltipOpts: {
				content: "%s : %y.3",
				shifts: {
					x: -30,
					y: -50
				},
				defaultTheme: false
			}
		}
	};
	
})(jQuery);