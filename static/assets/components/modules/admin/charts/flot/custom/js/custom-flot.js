    $(function() {	 
		 
		//////////     CAPLET COLOR    //////////
		var cepletColor=({ 
			"primary":"#0090d9",
			"info":"#B5D1D8",
			"success":"#2ECC71",
			"Approved":"#2ECC71",
			"warning":"#FFCC33",
			"Pending":"#FFCC33",
			"danger":"#E15258",
			"Rejected":"#E15258",
			"inverse":"#62707D",
			"theme":"#f35958",
			"theme-inverse":"#0aa699",
			"palevioletred":"#372b32" ,
			"green":"#99CC00",
			"lightseagreen":"#1ABC9B",
			"purple":"#736086",
			"darkorange":"#f9ba46",
			"pink":"#d13a7a"
		});
		$.inColor= function(value, obj) {
			var foundVal;
				$.each(obj, function(key, val) {
					if (value === key) {
						foundVal =  val;
						return;
					}
				});
			return foundVal;
		};
		$.fillColor= function(obj) {
			var inColor=$.inColor(obj.data("color") || obj.data("toolscolor") || obj.data("counter-color") , cepletColor);
			var codeColor= inColor || obj.data("color") || obj.data("toolscolor") || obj.data("counter-color") ;
			return codeColor;
		};
		$.rgbaColor=function( hex, opacity) {
		    var bigint = parseInt(hex.replace("#",""), 16),
				r = (bigint >> 16) & 255,
				g = (bigint >> 8) & 255,
				b = bigint & 255;
				if(opacity || opacity<=1){
		    			return "rgba("+r + "," + g + "," + b+","+ ( opacity || 1 )+")";
				}else{
					return "rgb("+r + "," + g + "," + b+")";
				}
		}
		
		
		
	//////////     FLOT  CHART     //////////
	var 	bars = false,
			lines = true,
			pie=false;
	var  	createFlot=function($chageType , $change){ 
				var el=$("table"+($change ? $change:".flot-chart"));
				el.each(function() {	  
						var colors = [], data = $(this).data(),
						gridColor=data.tickColor || "rgba(0,0,0,0.2)";
						$(this).find("thead th:not(:first)").each(function() {
						  colors.push($(this).css("color"));
						});
						if($chageType){
							bars = $chageType.indexOf("bars") != -1;
							lines = $chageType.indexOf("lines") != -1;
							pie = $chageType.indexOf("pie") != -1;
							$(this).next(".chart_flot").hide();
						}else{
							if(data.type){
								bars = data.type.indexOf("bars") != -1;
								lines = data.type.indexOf("lines") != -1;
								pie = data.type.indexOf("pie") != -1;
							}
						}
						$(this).graphTable({ series: 'columns', position: data.position || 'after',  width: data.width, height: data.height, colors: colors },
						{
								series: { stack: data.stack ,    pie: { show: pie , innerRadius: data.innerRadius || 0,  stroke: {  shadow: data.pieStyle=="shadow" ? true:false } , label:{ show:data.pieLabel } }, bars: { show: bars , barWidth: data.barWidth || 0.5, fill: data.fill || 1, align: "center" } ,lines: { show: lines  , fill:0.1 , steps: data.steps } },
								xaxis: { mode: "categories", tickLength: 0 },
								yaxis: { tickColor: gridColor ,max:data.yaxisMax,
									tickFormatter: function number(x) {  var num; if (x >= 1000) { num=(x/1000)+"k"; }else{ num=x; } return num; }
								},  
								grid: { borderWidth: {top: 0, right: 0, bottom: 1, left: 1},color : gridColor },
								tooltip: data.toolTip=="show" ? true:false  ,
								tooltipOpts: { content: (pie ? "%p.0%, %s":"<b>%s</b> :  %y")  }
						});
				});
	}
	// Create Flot Chart
	createFlot();
	
	$(".chart-change .btn").click(function (e) {
			var el=$(this),data=el.data();
			el.closest(".chart-change").find(".btn").toggleClass("active");
			createFlot(data.changeType,data.forId);
	});
	
	$(".label-flot-custom").each(function () {
		var el=$(this), data=el.data() ,colors = [] ,lable=[] , li="";
			$(data.flotId).find("thead th:not(:first)").each(function() {
						  colors.push($(this).css("color"));
						  lable.push($(this).text());
			});			
			for(var i=0;i<lable.length;i++){
				li += '<li><span style="background-color:'+ colors[i] +'"></span>'+ lable[i] +" ( "+$(data.flotId).find("tbody td").eq(i).text()+' ) </li> ';
			}
			el.append("<ul>"+li+"</ul>");
			if($(data.flotId).prev(".label-flot-custom-title")){
				var height=$(data.flotId).next(".chart_flot").css("height");
				$(data.flotId).prev(".label-flot-custom-title").css({"height":height, "line-height":height });
			}
	});
	


		//////////     KNOB  CHART     //////////
		$('.knob').each(function () {
			var thisKnob = $(this) , $data = $(this).data();
			$data.fgColor=$.fillColor( thisKnob ) || "#F37864";
			thisKnob.knob($data);
			if ( $data.animate ) {
				$({  value: 0 }).animate({   value: this.value }, {
					duration: 1000, easing: 'swing',
					step: function () { thisKnob.val(Math.ceil(this.value)).trigger('change'); }
				});
			}
		});
		$('.knob_save').on('click', function() {
			alert("Save  "+$("#add_item").val()+" Item");
		});
		$(".showcase-chart-knob").each(function () {
			var color='', ico=$(this).find("h5 i"),  $label=$(this).find("span"), $knob=$(this).find("input");
			$label.each(function (i) {
				if (i == 0) {
					color = $knob.attr("data-color")  || '#87CEEB' ;
				}else{
					color=$knob.attr("data-bgColor")  || '#CCC';
				}
				$(this).find("i").css("color", color );
				$(this).find("a small").css("color", color );
			});
			ico.css("margin-left",Math.ceil(-1*(ico.width()/2)));
		});
		
		
		
		//////////     SPARKLINE CHART     //////////
		$('.sparkline[data-type="bar"]').each(function () {
				var thisSpark=$(this) , $data = $(this).data();
				$data.barColor = $.fillColor( thisSpark ) || "#6CC3A0";
				$data.minSpotColor = false;
				thisSpark.sparkline($data.data || "html", $data);
		});	
		$('.sparkline[data-type="pie"]').each(function () {
				var thisSpark=$(this) , $data = $(this).data();
				$data.barColor = $.fillColor( thisSpark ) || "#6CC3A0";
				$data.minSpotColor = false;
				thisSpark.sparkline($data.data || "html", $data);
		});	
		var sparklineCreate = function($resize) {
			$('.sparkline[data-type="line"]').each(function () {
					var thisSpark=$(this) , $data = $(this).data();
					$data.lineColor = $.fillColor( thisSpark ) || "#F37864";
					$data.fillColor = $.rgbaColor( ($.fillColor( thisSpark ) || "#F37864") , 0.1 );
					$data.width = $data.width || "100%";
					$data.lineWidth = $data.lineWidth || 3;
					$(this).sparkline($data.data || "html", $data);
					if($data.compositeForm){
						var thisComposite=$($data.compositeForm);
						$comData=thisComposite.data();
						$comData.composite = true;
						$comData.lineWidth = $data.lineWidth || 3;
						$comData.lineColor = $.fillColor( thisComposite ) || "#F37864";
						$comData.fillColor = $.rgbaColor( ($.fillColor( thisComposite ) || "#6CC3A0") , 0.1 );
						$(this).sparkline($comData.data , $comData);
					}
			});
		}
		var sparkResize;
		$(window).resize(function(e) {
			clearTimeout(sparkResize);
			sparkResize = setTimeout(sparklineCreate(true), 500);
		});
		sparklineCreate();
		$('.label-sparkline span[data-color]').each(function(i) {
			var label=$(this);
			label.css("background-color", $.fillColor(label) );
		});
		
		
		
		//////////     EASY PIE CHART     //////////
/*		$('.avatar-chart').easyPieChart({
			lineCap: "butt",
			trackColor:'#2E2E31',
			barColor: "#6CC3A0",
			scaleColor:false,
			size: 118,
			lineWidth:5
			, onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}
		});*/
		$('.easy-c').easyPieChart({
			lineCap: "butt",
			trackColor:'#EEE',
			barColor: "#F19F34",
			scaleColor:false,
			size:138,
			lineWidth:15
			,onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}
		});
		$('.easy-chart').each(function () {
				var thisEasy=$(this) , $data = $(this).data();
				$data.barColor = $.fillColor( thisEasy ) || "#6CC3A0";
				$data.size = $data.size || 119;
				$data.trackColor = $data.trackColor  || "#EEE";
				$data.lineCap = $data.lineCap  || "butt";
				$data.lineWidth = $data.lineWidth  || 20;
				$data.scaleColor = $data.scaleColor || false,
				$data.onStep = function(from, to, percent) {
						$(this.el).find('.percent').text(Math.round(percent));
					}
				thisEasy.find('.percent').css({"line-height": $data.size+"px"});
				thisEasy.easyPieChart($data);
		});	
		$('.js_update').on('click', function() {
			$('.easy-chart').each(function () {
				var chart = window.chart = $(this).data('easyPieChart');			  
				chart.update(Math.random()*100);			  
			});
		});


		// Slider right Flot Chart Real Time 
		var IDrealtimeChart=document.getElementById("realtimeChart");
		if(IDrealtimeChart){
				var livedata = [] , totalPoints = 12;
				function getRealtimeData() {
					if (livedata.length > 0)
						livedata = livedata.slice(1);
						while (livedata.length < totalPoints) {
							var prev = livedata.length > 0 ? livedata[livedata.length - 1] : 20,
								y = prev + Math.random() * 10 - 5;
								if (y < 0) {  y = 0; }else if (y > 30) { 	y = 30; }
							$("#realtimeChartCount  span").text(Math.ceil( y));
							livedata.push(y);
						}
						var res = [];
						for (var i = 0; i < livedata.length; ++i) {
							res.push([i, livedata[i]])
						}
					return res;
				}

				var updateInterval = 1000;
				var realtimePlot = $.plot("#realtimeChart", [ getRealtimeData() ], {
					colors: ["#F37864"],
					series: { lines: { show: true  , fill:0.1 } ,shadowSize: 0 },
					yaxis: { tickColor: "rgba(255,255,255,0.2)" ,min: 0, max: 30,},  
					grid: { borderWidth: { top: 0, right: 0, bottom: 1, left: 1 },color :  "rgba(255,255,255,0.2)" },
					tooltip: true,
					tooltipOpts: { content: ("%y")  },
					xaxis: { show: false}
				});
				function realtimeChart() {
					realtimePlot.setData( [getRealtimeData()] );
					realtimePlot.draw();
					setTimeout(realtimeChart, updateInterval);
				}

				realtimeChart();
		}
		
		
		
		
    });