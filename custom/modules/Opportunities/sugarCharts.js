/*******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement By installing or
 * using this file, You have unconditionally agreed to the terms and conditions
 * of the License, and You may not use this file except in compliance with the
 * License. Under the terms of the license, You shall not, among other things:
 * 1) sublicense, resell, rent, lease, redistribute, assign or otherwise
 * transfer Your rights to the Software, and 2) use the Software for timesharing
 * or service bureau purposes such as hosting the Software for commercial gain
 * and/or for the benefit of a third party. Use of the Software may be subject
 * to applicable fees and any use of the Software without first paying
 * applicable fees is strictly prohibited. You do not have the right to remove
 * SugarCRM copyrights from the source code or user interface.
 * 
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice in
 * the same form as they appear in the distribution. See full license for
 * requirements.
 * 
 * Your Warranty, Limitations of liability and Indemnity are expressly stated in
 * the License. Please refer to the License for the specific language governing
 * these rights and limitations under the License. Portions created by SugarCRM
 * are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ******************************************************************************/
function loadSugarChart(chartId, jsonFilename, css, chartConfig) {
	if (document.getElementById(chartId) == null) {
		return false;
	}
	var labelType, useGradients, nativeTextSupport, animate;
	(function() {
		var ua = navigator.userAgent, typeOfCanvas = typeof HTMLCanvasElement, nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'), textSupport = nativeCanvasSupport
				&& (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
		labelType = 'Native';
		nativeTextSupport = labelType == 'Native';
		useGradients = nativeCanvasSupport;
		animate = false;
	})();
	var delay = 500;
	switch (chartConfig["chartType"]) {
	case "barChart":
		var handleFailure = function(o) {
			alert('fail');
			if (o.responseText !== undefined) {
				alert('failed');
			}
		}
		var handleSuccess = function(o) {
			if (o.responseText !== undefined && o.responseText != "No Data") {
				var json = eval('(' + o.responseText + ')');
				var properties = $jit.util.splat(json.properties)[0];
				var marginBottom = (chartConfig["orientation"] == 'vertical' && json.values.length > 8) ? 20 * 4
						: 20;
				if (chartConfig["orientation"] == 'vertical') {
					function fixChartContainer(event, itemsCount) {
						var region = YAHOO.util.Dom.getRegion('content');
						if (region && region.width) {
							var realWidth = itemsCount * 40;
							if (realWidth > region.width) {
								var chartCanvas = YAHOO.util.Dom
										.getElementsByClassName('chartCanvas',
												'div');
								var chartContainer = YAHOO.util.Dom
										.getElementsByClassName(
												'chartContainer', 'div');
								if (chartContainer.length > 0
										&& chartCanvas.length > 0) {
									chartContainer = YAHOO.util.Dom
											.get(chartContainer[0])
									YAHOO.util.Dom.setStyle(chartContainer,
											'width', region.width + 'px')
									chartCanvas = YAHOO.util.Dom
											.get(chartCanvas[0]);
									YAHOO.util.Dom.setStyle(chartCanvas,
											'width', realWidth + 'px');
									if (!event) {
										YAHOO.util.Event.addListener(window,
												"resize", fixChartContainer,
												json.values.length);
									}
								}
							}
						}
					}
					fixChartContainer(null, json.values.length);
				}
				var barChart = new $jit.BarChart(
						{
							injectInto : chartId,
							animate : false,
							nodeCount : json.values.length,
							renderBackground : chartConfig['imageExportType'] == "jpg" ? true
									: false,
							backgroundColor : 'rgb(255,255,255)',
							colorStop1 : 'rgba(255,255,255,.8)',
							colorStop2 : 'rgba(255,255,255,0)',
							shadow : {
								enable : true,
								size : 2
							},
							orientation : chartConfig["orientation"],
							hoveredColor : false,
							Title : {
								text : properties['title'],
								size : 16,
								color : '#444444',
								offset : 20
							},
							Subtitle : {
								text : properties['subtitle'],
								size : 11,
								color : css["color"],
								offset : 20
							},
							Ticks : {
								enable : true,
								color : css["gridLineColor"]
							},
							barsOffset : (chartConfig["orientation"] == "vertical") ? 30
									: 20,
							Margin : {
								top : 20,
								left : 30,
								right : 20,
								bottom : marginBottom
							},
							ScrollNote : {
								text : (chartConfig["scroll"] && SUGAR.util
										.isTouchScreen()) ? "Use two fingers to scroll"
										: "",
								size : 12
							},
							Events : {
								enable : true,
								onClick : function(node) {
									if (!node || SUGAR.util.isTouchScreen())
										return;
									if (node.link == 'undefined'
											|| node.link == '')
										return;
									window.location.href = node.link;
								}
							},
							labelOffset : 5,
							type : useGradients ? chartConfig["barType"]
									+ ':gradient' : chartConfig["barType"],
							showAggregates : true,
							showLabels : true,
							Label : {
								type : labelType,
								size : 12,
								family : css["font-family"],
								color : css["color"],
								colorAlt : "#ffffff"
							},
							Tips : {
								enable : true,
								onShow : function(tip, elem) {	
									
									if (elem.link != 'undefined'
											&& elem.link != '') {
										drillDown = (SUGAR.util.isTouchScreen()) ? "<br><a href='"
												+ elem.link
												+ "'>Click to drilldown</a>"
												: "<br>Click to drilldown";
									} else {
										drillDown = "";
									}
									if (elem.valuelabel != 'undefined'
											&& elem.valuelabel != undefined
											&& elem.valuelabel != '') {
										value = "elem.valuelabel";
									} else {
										value = "elem.value";
									}
									var ss = elem.label;									
									var ss_val = ssArray[ss];
									var ss_val_array = ss_val.split('#%#');									
									
									var tip_string = '<table width="100%">';									
									for(var i=0;i<ss_val_array.length;i++){
										var ss_val_str_arr = ss_val_array[i].split(':');
										tip_string += '<tr>';
										tip_string += '<td><b>'+ss_val_str_arr[0]+'</b></td>';
										tip_string += '<td width="50px">&nbsp</td>';
										tip_string += '<td><b>'+ss_val_str_arr[1]+'</b></td>';
										tip_string += '</tr>';
									}
									tip_string += '</table>';							
									tip_string += drillDown;
									
									tip.innerHTML = tip_string;
									
									/*eval("tip.innerHTML = '<b>' + elem."
											+ chartConfig["tip"]
											+ " + '</b>: ' + "
											+ value
											+ " + ' - ' + elem.percentage + '%' + drillDown");*/
								}
							}
						});
				barChart.loadJSON(json);
				var list = $jit.id('legend' + chartId);
				var legend = barChart.getLegend(), cols = (typeof SUGAR == 'undefined' || typeof SUGAR.mySugar == 'undefined') ? 8
						: 4, rows = Math.ceil(legend["name"].length / cols), table = "<table cellpadding='0' cellspacing='0' align='left'>";
				var j = 0;
				for (i = 0; i < rows; i++) {
					table += "<tr>";
					for (td = 0; td < cols; td++) {
						table += '<td width=\'16\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += '<div class=\'query-color\' style=\'background-color:'
									+ legend["color"][j] + '\'>&nbsp;</div>';
						}
						table += '</td>';
						table += '<td class=\'label\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += legend["name"][j];
						}
						table += '</td>';
						j++;
					}
					table += "</tr>";
				}
				table += "</table>";
				list.innerHTML = table;
				$jit.util.saveImageTest(chartId, jsonFilename,
						chartConfig["imageExportType"]);
				trackWindowResize(barChart, chartId, json);
			}
		}
		var callback = {
			success : handleSuccess,
			failure : handleFailure,
			argument : {
				foo : 'foo',
				bar : ''
			}
		};
		var request = YAHOO.util.Connect.asyncRequest('GET', jsonFilename
				+ "?r=" + new Date().getTime(), callback);
		break;
	case "lineChart":
		var handleFailure = function(o) {
			alert('fail');
			if (o.responseText !== undefined) {
				alert('failed');
			}
		}
		var handleSuccess = function(o) {
			if (o.responseText !== undefined && o.responseText != "No Data") {
				var json = eval('(' + o.responseText + ')');
				var properties = $jit.util.splat(json.properties)[0];
				var lineChart = new $jit.LineChart(
						{
							injectInto : chartId,
							animate : false,
							renderBackground : chartConfig['imageExportType'] == "jpg" ? true
									: false,
							backgroundColor : 'rgb(255,255,255)',
							colorStop1 : 'rgba(255,255,255,.8)',
							colorStop2 : 'rgba(255,255,255,0)',
							selectOnHover : false,
							Title : {
								text : properties['title'],
								size : 16,
								color : '#444444',
								offset : 20
							},
							Subtitle : {
								text : properties['subtitle'],
								size : 11,
								color : css["color"],
								offset : 20
							},
							Ticks : {
								enable : true,
								color : css["gridLineColor"]
							},
							Margin : {
								top : 20,
								left : 40,
								right : 40,
								bottom : 20
							},
							Events : {
								enable : true,
								onClick : function(node) {
									if (!node || SUGAR.util.isTouchScreen())
										return;
									if (node.link == 'undefined'
											|| node.link == '')
										return;
									window.location.href = node.link;
								}
							},
							labelOffset : 5,
							type : useGradients ? chartConfig["lineType"]
									+ ':gradient' : chartConfig["lineType"],
							showAggregates : true,
							showLabels : true,
							Label : {
								type : labelType,
								size : 12,
								family : css["font-family"],
								color : css["color"],
								colorAlt : "#ffffff"
							},
							Tips : {
								enable : true,
								onShow : function(tip, elem) {
									if (elem.link != 'undefined'
											&& elem.link != '') {
										drillDown = (SUGAR.util.isTouchScreen()) ? "<br><a href='"
												+ elem.link
												+ "'>Click to drilldown</a>"
												: "<br>Click to drilldown";
									} else {
										drillDown = "";
									}
									if (elem.valuelabel != 'undefined'
											&& elem.valuelabel != undefined
											&& elem.valuelabel != '') {
										var value = "elem.valuelabel";
									} else {
										var value = "elem.value";
									}
									if (elem.collision) {
										eval("var name = elem."
												+ chartConfig["tip"] + ";");
										var content = '<table>';
										for ( var i = 0; i < name.length; i++) {
											content += '<tr><td><b>' + name[i]
													+ '</b>:</td><td> '
													+ elem.value[i] + ' - '
													+ elem.percentage[i] + '%'
													+ '</td></tr>';
										}
										content += '</table>';
										tip.innerHTML = content;
									} else {
										eval("tip.innerHTML = '<b>' + elem."
												+ chartConfig["tip"]
												+ " + '</b>: ' + "
												+ value
												+ " + ' - ' + elem.percentage + '%' + drillDown");
									}
								}
							}
						});
				lineChart.loadJSON(json);
				var list = $jit.id('legend' + chartId);
				var legend = lineChart.getLegend(), cols = (typeof SUGAR == 'undefined' || typeof SUGAR.mySugar == 'undefined') ? 8
						: 4, rows = Math.ceil(legend["name"].length / cols), table = "<table cellpadding='0' cellspacing='0' align='left'>";
				var j = 0;
				for (i = 0; i < rows; i++) {
					table += "<tr>";
					for (td = 0; td < cols; td++) {
						table += '<td width=\'16\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += '<div class=\'query-color\' style=\'background-color:'
									+ legend["color"][j] + '\'>&nbsp;</div>';
						}
						table += '</td>';
						table += '<td class=\'label\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += legend["name"][j];
						}
						table += '</td>';
						j++;
					}
					table += "</tr>";
				}
				table += "</table>";
				list.innerHTML = table;
				$jit.util.saveImageTest(chartId, jsonFilename,
						chartConfig["imageExportType"]);
				trackWindowResize(lineChart, chartId, json);
			}
		}
		var callback = {
			success : handleSuccess,
			failure : handleFailure,
			argument : {
				foo : 'foo',
				bar : ''
			}
		};
		var request = YAHOO.util.Connect.asyncRequest('GET', jsonFilename
				+ "?r=" + new Date().getTime(), callback);
		break;
	case "pieChart":
		var handleFailure = function(o) {
			alert('fail');
			if (o.responseText !== undefined) {
				alert('failed');
			}
		}
		var handleSuccess = function(o) {
			if (o.responseText !== undefined) {
				var json = eval('(' + o.responseText + ')');
				var properties = $jit.util.splat(json.properties)[0];
				var pieChart = new $jit.PieChart(
						{
							injectInto : chartId,
							animate : false,
							renderBackground : chartConfig['imageExportType'] == "jpg" ? true
									: false,
							backgroundColor : 'rgb(255,255,255)',
							colorStop1 : 'rgba(255,255,255,.8)',
							colorStop2 : 'rgba(255,255,255,0)',
							labelType : properties['labels'],
							hoveredColor : false,
							offset : 50,
							sliceOffset : 0,
							labelOffset : 30,
							type : useGradients ? chartConfig["pieType"]
									+ ':gradient' : chartConfig["pieType"],
							showLabels : true,
							Title : {
								text : properties['title'],
								size : 16,
								color : '#444444',
								offset : 20
							},
							Subtitle : {
								text : properties['subtitle'],
								size : 11,
								color : css["color"],
								offset : 20
							},
							Margin : {
								top : 20,
								left : 20,
								right : 20,
								bottom : 20
							},
							Events : {
								enable : true,
								onClick : function(node) {
									if (!node || SUGAR.util.isTouchScreen())
										return;
									if (node.link == 'undefined'
											|| node.link == '')
										return;
									window.location.href = node.link;
								}
							},
							Label : {
								type : labelType,
								size : 12,
								family : css["font-family"],
								color : css["color"]
							},
							Tips : {
								enable : true,
								onShow : function(tip, elem) {
									if (elem.link != 'undefined'
											&& elem.link != '') {
										drillDown = (SUGAR.util.isTouchScreen()) ? "<br><a href='"
												+ elem.link
												+ "'>Click to drilldown</a>"
												: "<br>Click to drilldown";
									} else {
										drillDown = "";
									}
									if (elem.valuelabel != 'undefined'
											&& elem.valuelabel != undefined
											&& elem.valuelabel != '') {
										value = "elem.valuelabel";
									} else {
										value = "elem.value";
									}
									eval("tip.innerHTML = '<b>' + elem.label + '</b>: ' + "
											+ value
											+ " + ' - ' + elem.percentage + '%' + drillDown");
								}
							}
						});
				pieChart.loadJSON(json);
				var list = $jit.id('legend' + chartId);
				var legend = pieChart.getLegend(), cols = (typeof SUGAR == 'undefined' || typeof SUGAR.mySugar == 'undefined') ? 8
						: 4, rows = Math.ceil(legend["name"].length / cols);
				table = "<table cellpadding='0' cellspacing='0' align='left'>";
				var j = 0;
				for (i = 0; i < rows; i++) {
					table += "<tr>";
					for (td = 0; td < cols; td++) {
						table += '<td width=\'16\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += '<div class=\'query-color\' style=\'background-color:'
									+ legend["color"][j] + '\'>&nbsp;</div>';
						}
						table += '</td>';
						table += '<td class=\'label\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += legend["name"][j];
						}
						table += '</td>';
						j++;
					}
					table += "</tr>";
				}
				table += "</table>";
				list.innerHTML = table;
				$jit.util.saveImageTest(chartId, jsonFilename,
						chartConfig["imageExportType"]);
				trackWindowResize(pieChart, chartId, json);
			}
		}
		var callback = {
			success : handleSuccess,
			failure : handleFailure,
			argument : {
				foo : 'foo',
				bar : ''
			}
		};
		var request = YAHOO.util.Connect.asyncRequest('GET', jsonFilename
				+ "?r=" + new Date().getTime(), callback);
		break;
	case "funnelChart":
		var handleFailure = function(o) {
			alert('fail');
			if (o.responseText !== undefined) {
				alert('failed');
			}
		}
		var handleSuccess = function(o) {
			if (o.responseText !== undefined && o.responseText != "No Data") {
				var json = eval('(' + o.responseText + ')');
				var properties = $jit.util.splat(json.properties)[0];
				var funnelChart = new $jit.FunnelChart(
						{
							injectInto : chartId,
							animate : false,
							renderBackground : chartConfig['imageExportType'] == "jpg" ? true
									: false,
							backgroundColor : 'rgb(255,255,255)',
							colorStop1 : 'rgba(255,255,255,.8)',
							colorStop2 : 'rgba(255,255,255,0)',
							orientation : "vertical",
							hoveredColor : false,
							Title : {
								text : properties['title'],
								size : 16,
								color : '#444444',
								offset : 20
							},
							Subtitle : {
								text : properties['subtitle'],
								size : 11,
								color : css["color"],
								offset : 20
							},
							segmentOffset : 20,
							Margin : {
								top : 20,
								left : 20,
								right : 20,
								bottom : 20
							},
							Events : {
								enable : true,
								onClick : function(node) {
									if (!node || SUGAR.util.isTouchScreen())
										return;
									if (node.link == 'undefined'
											|| node.link == '')
										return;
									window.location.href = node.link;
								}
							},
							labelOffset : 10,
							type : useGradients ? chartConfig["funnelType"]
									+ ':gradient' : chartConfig["funnelType"],
							showAggregates : true,
							showLabels : true,
							Label : {
								type : labelType,
								size : 12,
								family : css["font-family"],
								color : css["color"],
								colorAlt : "#ffffff"
							},
							Tips : {
								enable : true,
								onShow : function(tip, elem) {
									if (elem.link != 'undefined'
											&& elem.link != '') {
										drillDown = (SUGAR.util.isTouchScreen()) ? "<br><a href='"
												+ elem.link
												+ "'>Click to drilldown</a>"
												: "<br>Click to drilldown";
									} else {
										drillDown = "";
									}
									if (elem.valuelabel != 'undefined'
											&& elem.valuelabel != undefined
											&& elem.valuelabel != '') {
										value = "elem.valuelabel";
									} else {
										value = "elem.value";
									}
									eval("tip.innerHTML = '<b>' + elem."
											+ chartConfig["tip"]
											+ " + '</b>: ' + "
											+ value
											+ "  + ' - ' + elem.percentage + '%' +  drillDown");
								}
							}
						});
				funnelChart.loadJSON(json);
				var list = $jit.id('legend' + chartId);
				var legend = funnelChart.getLegend(), cols = (typeof SUGAR == 'undefined' || typeof SUGAR.mySugar == 'undefined') ? 8
						: 4, rows = Math.ceil(legend["name"].length / cols);
				table = "<table cellpadding='0' cellspacing='0' align='left'>";
				var j = 0;
				for (i = 0; i < rows; i++) {
					table += "<tr>";
					for (td = 0; td < cols; td++) {
						table += '<td width=\'16\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += '<div class=\'query-color\' style=\'background-color:'
									+ legend["color"][j] + '\'>&nbsp;</div>';
						}
						table += '</td>';
						table += '<td class=\'label\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += legend["name"][j];
						}
						table += '</td>';
						j++;
					}
					table += "</tr>";
				}
				table += "</table>";
				list.innerHTML = table;
				$jit.util.saveImageTest(chartId, jsonFilename,
						chartConfig["imageExportType"]);
				trackWindowResize(funnelChart, chartId, json);
			}
		}
		var callback = {
			success : handleSuccess,
			failure : handleFailure,
			argument : {
				foo : 'foo',
				bar : ''
			}
		};
		var request = YAHOO.util.Connect.asyncRequest('GET', jsonFilename
				+ "?r=" + new Date().getTime(), callback);
		break;
	case "gaugeChart":
		var handleFailure = function(o) {
			alert('fail');
			if (o.responseText !== undefined) {
				alert('failed');
			}
		}
		var handleSuccess = function(o) {
			if (o.responseText !== undefined) {
				var json = eval('(' + o.responseText + ')');
				var properties = $jit.util.splat(json.properties)[0];
				var gaugeChart = new $jit.GaugeChart(
						{
							injectInto : chartId,
							animate : false,
							renderBackground : chartConfig['imageExportType'] == "jpg" ? true
									: false,
							backgroundColor : 'rgb(255,255,255)',
							colorStop1 : 'rgba(255,255,255,.8)',
							colorStop2 : 'rgba(255,255,255,0)',
							labelType : properties['labels'],
							hoveredColor : false,
							Title : {
								text : properties['title'],
								size : 16,
								color : '#444444',
								offset : 20
							},
							Subtitle : {
								text : properties['subtitle'],
								size : 11,
								color : css["color"],
								offset : 5
							},
							offset : 20,
							gaugeStyle : {
								backgroundColor : '#aaaaaa',
								borderColor : '#999999',
								needleColor : 'rgba(255,0,0,.8)',
								borderSize : 4,
								positionFontSize : 24,
								positionOffset : 2
							},
							type : useGradients ? chartConfig["gaugeType"]
									+ ':gradient' : chartConfig["gaugeType"],
							showLabels : true,
							Events : {
								enable : true,
								onClick : function(node) {
									if (!node || SUGAR.util.isTouchScreen())
										return;
									if (node.link == 'undefined'
											|| node.link == '')
										return;
									window.location.href = node.link;
								}
							},
							Label : {
								type : labelType,
								size : 12,
								family : css["font-family"],
								color : css["color"]
							},
							Tips : {
								enable : true,
								onShow : function(tip, elem) {
									if (elem.link != 'undefined'
											&& elem.link != '') {
										drillDown = (SUGAR.util.isTouchScreen()) ? "<br><a href='"
												+ elem.link
												+ "'>Click to drilldown</a>"
												: "<br>Click to drilldown";
									} else {
										drillDown = "";
									}
									if (elem.valuelabel != 'undefined'
											&& elem.valuelabel != undefined
											&& elem.valuelabel != '') {
										value = "elem.valuelabel";
									} else {
										value = "elem.value";
									}
									eval("tip.innerHTML = '<b>' + elem.label + '</b>: ' + "
											+ value + " + drillDown");
								}
							}
						});
				gaugeChart.loadJSON(json);
				var list = $jit.id('legend' + chartId);
				var legend = gaugeChart.getLegend(), cols = (typeof SUGAR == 'undefined' || typeof SUGAR.mySugar == 'undefined') ? 8
						: 4, rows = Math.ceil(legend["name"].length / cols);
				table = "<table cellpadding='0' cellspacing='0' align='left'>";
				var j = 1;
				for (i = 0; i < rows; i++) {
					table += "<tr>";
					for (td = 0; td < cols; td++) {
						table += '<td width=\'16\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += '<div class=\'query-color\' style=\'background-color:'
									+ legend["color"][j] + '\'>&nbsp;</div>';
						}
						table += '</td>';
						table += '<td class=\'label\' valign=\'top\'>';
						if (legend["name"][j] != undefined) {
							table += legend["name"][j];
						}
						table += '</td>';
						j++;
					}
					table += "</tr>";
				}
				table += "</table>";
				list.innerHTML = table;
				$jit.util.saveImageTest(chartId, jsonFilename,
						chartConfig["imageExportType"]);
				trackWindowResize(gaugeChart, chartId, json);
			}
		}
		var callback = {
			success : handleSuccess,
			failure : handleFailure,
			argument : {
				foo : 'foo',
				bar : ''
			}
		};
		var request = YAHOO.util.Connect.asyncRequest('GET', jsonFilename
				+ "?r=" + new Date().getTime(), callback);
		break;
	}
	function trackWindowResize(chart, chartId, json) {
		var origWindowWidth = document.documentElement.scrollWidth, container = document
				.getElementById(chartId), widget = document
				.getElementById(chartId + "-canvaswidget");
		var timeout;
		YAHOO.util.Event.addListener(window, "resize", function() {
			if (timeout) {
				clearTimeout(timeout);
			}
			timeout = setTimeout(function() {
				var newWindowWidth = document.documentElement.scrollWidth;
				if (newWindowWidth != origWindowWidth) {
					widget.style.display = "none";
					setTimeout(function() {
						var width = container.offsetWidth;
						widget.style.display = "";
						chart.resizeGraph(json, width);
						origWindowWidth = newWindowWidth;
					}, 0);
				}
			}, delay);
		});
	}
}
