<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once 'include/SugarCharts/SugarChartFactory.php';
class OpportunitiesCharts{
    
    function opportunities_by_sales_stage($datay= array(),$targets=array(),$parent_opp_id, $cache_file_name='a_file', $refresh=false,$marketing_id='',$is_dashlet=false,$dashlet_id=''){
        global $db;
    	
        $opp_sql = "SELECT sales_stage, count(sales_stage) total_opp FROM opportunities WHERE parent_opportunity_id = '".$parent_opp_id."' AND deleted = 0 GROUP BY sales_stage";
        $opp_query = $db->query($opp_sql);
        $opp_data = array();
        $row = $db->fetchByAssoc($opp_query);
        while($row != null){
        	$opp_data[$row['sales_stage']] = $row['total_opp'];
        	$row = $db->fetchByAssoc($opp_query);
        }   	    	
    	//print_r($opp_data);
        $return = '<br />';
        $sugarChart = SugarChartFactory::getInstance();
        $sugarChart->group_by = array('ss','sales_stage');
        $sugarChart->base_url = array( 	'module' => 'Reports',
							'action' => 'ReportCriteriaResults'						
						 );
        $sugarChart->url_params = array('page'=>'report','id'=>'45d963e0-be3b-1b4b-f775-508542cc82ac','parent_opp_id'=>$parent_opp_id);
        
        
        $sugarChart->setData($opp_data);
        $sugarChart->setProperties('Open Client Opportunities by Sales Stage', '', 'horizontal bar chart');
        $xmlFile = $sugarChart->getXMLFileName('opp_sales_stage_chart');
        $sugarChart->saveXMLFile($xmlFile, $sugarChart->generateXML());        
        $width ="100%";
		$return .= $sugarChart->display('opp_sales_stage_chart', $xmlFile, $width, '480');
        return $return;       
    }
}
?>
