<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/modules/Opportunities/Charts.php';
class ViewAtaglance_graph extends SugarView {
	
	function __construct() {
		parent::SugarView ();
	}
	
	function display() {
		if (isset ( $_REQUEST ['opportunity_id'] ) && ! empty ( $_REQUEST ['opportunity_id'] )) {
			$parent_opp_id = $_REQUEST ['opportunity_id'];
			global $db;
			//Fetch Sub Opportunity name and it's Assigned User
			$select_sql = "SELECT opportunities.sales_stage,
				    GROUP_CONCAT(accounts.name, ' : ', CONCAT(IFNULL(users.first_name,''),' ',IFNULL(users.last_name,'')) SEPARATOR '#%#') disp_string
					FROM opportunities
						LEFT JOIN
					accounts_opportunities ao ON ao.opportunity_id=opportunities.id AND ao.deleted = 0
						LEFT JOIN 
					accounts ON accounts.id = ao.account_id AND accounts.deleted = 0
				        LEFT JOIN
				    users ON users.id = opportunities.assigned_user_id
					WHERE
				    parent_opportunity_id = '".$parent_opp_id."' AND opportunities.deleted=0 GROUP BY opportunities.sales_stage";
			$select_query = $db->query($select_sql);
			$display_string_arr = array();
			$row = $db->fetchByAssoc($select_query);
			$js_array = "<script>";
			$js_array .= "var ssArray = Array();";
			while($row != null){				
				$display_string = ucwords(htmlspecialchars_decode($row['disp_string']));							
				$js_array .= "ssArray['".$row['sales_stage']."']='".$display_string."';";
				$row = $db->fetchByAssoc($select_query);			
			}
			$js_array .="</script>";		
			
			$chart = new OpportunitiesCharts ();
			$this->ss->assign ( "SALES_STAGE_CHART", $chart->opportunities_by_sales_stage ( '', '', $parent_opp_id, true, true ) );
			require_once ('include/SugarCharts/SugarChartFactory.php');
			$sugarChart = SugarChartFactory::getInstance ();
			$resources = $sugarChart->getChartResources ();
			$this->ss->assign('js_array',$js_array);
			$this->ss->assign ( 'chartResources', $resources );
			
			echo $this->ss->fetch ( 'custom/modules/Opportunities/tpls/ataglance_graph.tpl' );
		}
	}
}
