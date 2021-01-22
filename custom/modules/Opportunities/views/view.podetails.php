<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/OssTimeDate.php';

class OpportunitiesViewPOdetails extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){
		if(isset($_REQUEST['project_opportunity_id']) && !empty($_REQUEST['project_opportunity_id'])){
			global $mod_strings, $db, $app_list_strings;
			$oss_timedate = new OssTimeDate();
			$opportunity_id = $_REQUEST['project_opportunity_id'];
			$opportunity = new Opportunity();
			$opportunity->retrieve($opportunity_id);
			
			//Get County by County id
    		$sql = "SELECT `name` FROM oss_county WHERE `id` = '".$opportunity->lead_county."' AND deleted = 0";
    		$query = $db->query($sql);
    		$result = $db->fetchByAssoc($query);
    		$opportunity->lead_county = $result['name'];
			
    		$opportunity->lead_source = $app_list_strings['lead_source_dom'][$opportunity->lead_source];
    		
			$this->ss->assign('oss_timedate',$oss_timedate);
			$this->ss->assign('MOD',$mod_strings);
			$this->ss->assign('opportunity',$opportunity);
			$this->ss->display('custom/modules/Opportunities/tpls/podetails.tpl');
			
		}
	}
}