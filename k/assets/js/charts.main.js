var blue		= '#348fe2',
    blueLight	= '#5da5e8',
    blueDark	= '#1993E4',
    aqua		= '#49b6d6',
    aquaLight	= '#6dc5de',
    aquaDark	= '#3a92ab',
	green		= '#00acac',
	greenLight	= '#33bdbd',
	greenDark	= '#008a8a',
	orange		= '#f59c1a',
	orangeLight	= '#f7b048',
	orangeDark	= '#c47d15',
    dark		= '#2d353c',
    grey		= '#b6c2c9',
    purple		= '#727cb6',
    purpleLight	= '#8e96c5',
    purpleDark	= '#5b6392',
    red         = '#ff5b57';
    


var handleMonthBarChart = function () {
	"use strict";
	if ($('#month-chart').length !== 0) {
        var data = monthChartData2;        
        $.plot("#month-chart", [ {data: data, color: purple} ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: 'center',
                    fill: true,
                    fillColor: purple,
                    zero: true
                }
            },
            xaxis: {
                mode: "categories",
                tickColor: '#ddd',
				tickLength: 0
            },
            grid: {
                borderWidth: 0
            }
        });
    }
};

var handleWeekBarChart = function () {
	"use strict";
	if ($('#week-chart').length !== 0) {
        var data = weekChartData2;        
        $.plot("#week-chart", [ {data: data, color: purple} ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: 'center',
                    fill: true,
                    fillColor: dark,
                    zero: true
                }
            },
            xaxis: {
                mode: "categories",
                tickColor: '#ddd',
				tickLength: 0
            },
            grid: {
                borderWidth: 0
            }
        });
    }
};

var handleDayBarChart = function () {
	"use strict";
	if ($('#day-chart').length !== 0) {
        var data = dayChartData2;        
        $.plot("#day-chart", [ {data: data, color: purple} ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: 'center',
                    fill: true,
                    fillColor: greenDark,
                    zero: true
                }
            },
            xaxis: {
                mode: "categories",
                tickColor: '#ddd',
				tickLength: 0
            },
            grid: {
                borderWidth: 0
            }
        });
    }
};



var handleBarChart2 = function () {
	"use strict";
	if ($('#bar-chart2').length !== 0) {
        var data = barChartData2;        
        $.plot("#bar-chart2", [ {data: data, color: orange} ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: 'center',
                    fill: true,
                    fillColor: orange,
                    zero: true
                }
            },
            xaxis: {
                mode: "categories",
                tickColor: '#ddd',
				tickLength: 0
            },
            grid: {
                borderWidth: 0
            }
        });
    }
};


var handleInteractivePieChart = function () {
	"use strict";
	if ($('#interactive-pie-chart').length !== 0) {
        
        var series = 4;
        var colorArray = [purple, dark, grey];
        //alert(interactivePieChartData);
        $.plot($("#interactive-pie-chart"), interactivePieChartData,
        {
            series: {
                pie: { 
                    show: true,
                    radius: 9/10,                
                    formatter: function(label, series) 
                    {                    
                    	return '<div style="font-size:11px ;text-align:center; padding:2px; color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';                
                    },                
                    threshold: 0.1
                }
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            legend: { 
            	show: true,           	
              labelBoxBorderColor: '#ddd',
              backgroundColor: 'none'
            }
        });
        
        $("#interactive-pie-chart").bind("plothover", pieHover);
    }
};


function pieHover(event, pos, obj) {    
	if (!obj) return;     
	percent = parseFloat(obj.series.percent).toFixed(2);    
	$("#pieHover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
}




var handleMonthChart = function () {
	"use strict";
    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="flot-tooltip">' + contents + '</div>').css( {
            top: y - 45,
            left: x - 55
        }).appendTo("body").fadeIn(200);
    }
	if ($('#month-chart').length !== 0) {
        var d1 = [[0, 42], [1, 53], [2,66], [3, 60], [4, 68], [5, 66], [6,71],[7, 75], [8, 69], [9,70], [10, 68], [11, 72], [12, 78], [13, 86]];
        var d2 = [[0, 12], [1, 26], [2,13], [3, 18], [4, 35], [5, 23], [6, 18],[7, 35], [8, 24], [9,14], [10, 14], [11, 29], [12, 30], [13, 43]];
        //var monthChartData1 = [[1,19],[2,2],[3,9],[4,0],[5,0]];
				//var monthChartData2 = [[1,190],[2,210],[3,900],[4,0],[5,0]];
				
        $.plot($("#month-chart"), [
                {
                    data: monthChartData1, 
                    label: "수량", 
                    color: purple,
                    lines: { show: true, fill:false, lineWidth: 2 },
                    points: { show: false, radius: 5, fillColor: '#fff' },
                    shadowSize: 0
                }
            ], 
            {
                xaxis: {  tickColor: '#ddd',tickSize: 2 },
                yaxis: {  tickColor: '#ddd', tickSize: 500 },
                grid: { 
                    hoverable: true, 
                    clickable: true,
                    tickColor: "#ccc",
                    borderWidth: 1,
                    borderColor: '#ddd'
                },
                legend: {
                    labelBoxBorderColor: '#ddd',
                    margin: 0,
                    noColumns: 1,
                    show: true
                }
            }
        );
        var previousPoint = null;
        $("#month-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint !== item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var y = item.datapoint[1].toFixed(2);
                    
                    var content = item.series.label + " " + y;
                    showTooltip(item.pageX, item.pageY, content);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
            event.preventDefault();
        });
    }
};


var handleWeekChart = function () {
	"use strict";
    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="flot-tooltip">' + contents + '</div>').css( {
            top: y - 45,
            left: x - 55
        }).appendTo("body").fadeIn(200);
    }
	if ($('#week-chart').length !== 0) {
        var d1 = [[0, 42], [1, 53], [2,66], [3, 60], [4, 68], [5, 66], [6,71],[7, 75], [8, 69], [9,70], [10, 68], [11, 72], [12, 78], [13, 86]];
        var d2 = [[0, 12], [1, 26], [2,13], [3, 18], [4, 35], [5, 23], [6, 18],[7, 35], [8, 24], [9,14], [10, 14], [11, 29], [12, 30], [13, 43]];
        
        $.plot($("#week-chart"), [
                {
                    data: weekChartData1, 
                    label: "수량", 
                    color: purple,
                    lines: { show: true, fill:false, lineWidth: 2 },
                    points: { show: false, radius: 5, fillColor: '#fff' },
                    shadowSize: 0
                }, {
                    data: weekChartData2,
                    label: '금액',
                    color: green,
                    lines: { show: true, fill:false, lineWidth: 2, fillColor: '' },
                    points: { show: false, radius: 3, fillColor: '#fff' },
                    shadowSize: 0
                }
            ], 
            {
                xaxis: {  tickColor: '#ddd',tickSize: 2 },
                yaxis: {  tickColor: '#ddd', tickSize: 20 },
                grid: { 
                    hoverable: true, 
                    clickable: true,
                    tickColor: "#ccc",
                    borderWidth: 1,
                    borderColor: '#ddd'
                },
                legend: {
                    labelBoxBorderColor: '#ddd',
                    margin: 0,
                    noColumns: 1,
                    show: true
                }
            }
        );
        var previousPoint = null;
        $("#week-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint !== item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var y = item.datapoint[1].toFixed(2);
                    
                    var content = item.series.label + " " + y;
                    showTooltip(item.pageX, item.pageY, content);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
            event.preventDefault();
        });
    }
};


var handleDayChart = function () {
	"use strict";
    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="flot-tooltip">' + contents + '</div>').css( {
            top: y - 45,
            left: x - 55
        }).appendTo("body").fadeIn(200);
    }
	if ($('#day-chart').length !== 0) {
        var d1 = [[0, 42], [1, 53], [2,66], [3, 60], [4, 68], [5, 66], [6,71],[7, 75], [8, 69], [9,70], [10, 68], [11, 72], [12, 78], [13, 86]];
        var d2 = [[0, 12], [1, 26], [2,13], [3, 18], [4, 35], [5, 23], [6, 18],[7, 35], [8, 24], [9,14], [10, 14], [11, 29], [12, 30], [13, 43]];
        
        $.plot($("#day-chart"), [
                {
                    data: dayChartData1, 
                    label: "수량", 
                    color: purple,
                    lines: { show: true, fill:false, lineWidth: 2 },
                    points: { show: false, radius: 5, fillColor: '#fff' },
                    shadowSize: 0
                }, {
                    data: dayChartData2,
                    label: '금액',
                    color: green,
                    lines: { show: true, fill:false, lineWidth: 2, fillColor: '' },
                    points: { show: false, radius: 3, fillColor: '#fff' },
                    shadowSize: 0
                }
            ], 
            {
                xaxis: {  tickColor: '#ddd',tickSize: 2 },
                yaxis: {  tickColor: '#ddd', tickSize: 20 },
                grid: { 
                    hoverable: true, 
                    clickable: true,
                    tickColor: "#ccc",
                    borderWidth: 1,
                    borderColor: '#ddd'
                },
                legend: {
                    labelBoxBorderColor: '#ddd',
                    margin: 0,
                    noColumns: 1,
                    show: true
                }
            }
        );
        var previousPoint = null;
        $("#day-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint !== item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $("#tooltip").remove();
                    var y = item.datapoint[1].toFixed(2);
                    
                    var content = item.series.label + " " + y;
                    showTooltip(item.pageX, item.pageY, content);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
            event.preventDefault();
        });
    }
};



var Chart = function () {
	"use strict";
    return {
        //main function
        init: function () {            
            handleMonthBarChart();
            handleWeekBarChart();
            handleDayBarChart();

            //handleMonthChart();
            //handleWeekChart();
            //handleDayChart();
                        
        }
    };
}();