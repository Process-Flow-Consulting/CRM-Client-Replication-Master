<!DOCTYPE html>
<html>
<head>
<link href="css/extension-page-style.css" rel="stylesheet" type="text/css"  />
<script type="text/javascript" src="http://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<style>

.code-block-holder pre {
      max-height: 188px;  
      min-height: 188px; 
      overflow: auto;
      border: 1px solid #ccc;
      border-radius: 5px;
}


.tab-btn-holder {
    width: 100%;
    margin: 20px 0 0;
    border-bottom: 1px solid #dfe3e4;
    min-height: 30px;
}

.tab-btn-holder a {
    background-color: #fff;
    font-size: 14px;
    text-transform: uppercase;
    color: #006bb8;
    text-decoration: none;
    display: inline-block;
    *zoom:1; *display:inline;


}

.tab-btn-holder a.active {
    color: #858585;
    padding: 9px 10px 8px;
    border: 1px solid #dfe3e4;
    border-bottom: 1px solid #fff;
    margin-bottom: -1px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    position: relative;
    z-index: 300;
}

</style>

</head>
<body>
	<?php
    
    /**
    * This example describes the gauge preparation using FusionCharts PHP wrapper
    */

    // Including the wrapper file in the page
	include("../src/fusioncharts.php");

    // Preparing the object of FusionCharts with needed informations
    /**
    * The parameters of the constructor are as follows
    * chartType   {String}  The type of chart that you intend to plot. e.g. Column3D, Column2D, Pie2D etc.
    * chartId     {String}  Id for the chart, using which it will be recognized in the HTML page. Each chart on the page should have a unique Id.
    * chartWidth  {String}  Intended width for the chart (in pixels). e.g. 400
    * chartHeight {String}  Intended height for the chart (in pixels). e.g. 300
    * containerId {String}  The id of the chart container. e.g. chart-1
    * dataFormat  {String}  Type of data used to render the chart. e.g. json, jsonurl, xml, xmlurl
    * dataSource  {String}  Actual data for the chart. e.g. {"chart":{},"data":[{"label":"Jan","value":"420000"}]}
    */
$servername = "bbdbinstance-prod-suite.c3st5e2yjcaj.us-west-1.rds.amazonaws.com";
$username = "bbcrm";
$password = "bbcrmpassword";
$dbname = "trial_jyrofq1157";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}	

$selectOpp = "SELECT count(*) AS num from opportunities WHERE assigned_user_id = '".$_GET['user_id']."' AND deleted=0 AND parent_opportunity_id IS  NULL  AND sales_stage = 'Won (Closed)' ";
$resultOpp = $conn->query($selectOpp);
$row_OPP = $resultOpp->fetch_assoc();
$total =  $row_OPP['num'];
$amount1 = $total/3;
$amount2 = $total - $amount1;
$amount2 = $amount2/2;
$amount3 = $amount1 + $amount2;


$select = "SELECT count(*) AS num FROM opportunities WHERE assigned_user_id = '".$_GET['user_id']."' AND sales_stage = 'Won (Closed)' AND deleted=0  AND parent_opportunity_id IS NOT NULL";
$result = $conn->query($select);
$row = $result->fetch_assoc();
$total_actual = $row['num'];

$angularChart = new FusionCharts("AngularGauge", "ex1" , "100%", "200", "chart-1", "json", '{
      "chart": {
        "caption": "My Closed Won Project Opportunities Gauge",
        "subcaption": "",
        "lowerlimit": "0",
        "upperlimit": "'.$total.'",
        "lowerlimitdisplay": "0",
        "upperlimitdisplay": "'.$total.'",
        "numbersuffix": "",
        "tickvaluedistance": "10",
        "gaugeinnerradius": "0",
        "bgcolor": "FFFFFF",
        "pivotfillcolor": "333333",
        "pivotradius": "8",
        "pivotfillmix": "333333, 333333",
        "pivotfilltype": "radial",
        "pivotfillratio": "0,100",
        "showtickvalues": "1",
        "majorTMThickness": "2",
        "majorTMHeight": "15",
        "minorTMHeight": "3",
        "showborder": "0",
        "plottooltext": "<div>Average Score : <b>$value</b></div>",
      },
	  "colorrange": {
        "color": [{
          "minvalue": "0",
          "maxvalue": "'.$amount1.'",
          "code": "e44a00"
        }, {
          "minvalue": "'.$amount1.'",
          "maxvalue": "'.$amount3.'",
          "code": "f8bd19"
        }, {
          "minvalue": "'.$amount3.'",
          "maxvalue": "'.$total.'",
          "code": "6baa01"
        }]
      },
     "dials": {
        "dial": [{
          "value": "'.$total_actual.'",
          "rearextension": "15",
          "radius": "100",
          "bgcolor": "333333",
          "bordercolor": "333333",
          "basewidth": "8"
        }]
      }
    }');
	
	// Render the chart
	$angularChart->render();
?>
<div class="live-chart-wrapper">
<span id="chart-1" class="chart" ><!-- Fusion Charts will render here--></span>

</div>

</body>
</html>


