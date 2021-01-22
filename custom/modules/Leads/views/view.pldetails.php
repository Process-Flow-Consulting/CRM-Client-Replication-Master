<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/OssTimeDate.php';

class ViewPldetails extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){		
		if(isset($_REQUEST['lead_id']) && !empty($_REQUEST['lead_id'])){
			global $mod_strings, $app_list_strings, $timedate,$db;
			$oss_timedate = new OssTimeDate();
			$lead_id = $_REQUEST['lead_id'];
			require_once 'custom/modules/Leads/bbProjectLeads.php';
			$lead = new bbProjectLeads();
			$lead->retrieve($lead_id);		
			// modified by Ashutosh - 02/04/2014 mark this lead as viewed
			if($lead->status == 'New'){
			    /**
			     * There are anomalies in the fields like Bid due date(due to timezone logic)
			     * while saving from object, hence direct SQL is used
			     */
			    $updateSql = "UPDATE leads SET status='Viewed' WHERE id=".$db->quoted($lead_id)."";
			    $db->query($updateSql);
			}
			if(!empty($lead->lead_source))
			$lead->lead_source = $app_list_strings['lead_source_list'][$lead->lead_source];
			//BBSMP-341 -- Start
			if(!empty($lead->scope_of_work))
			$lead->scope_of_work = html_entity_decode($lead->scope_of_work, ENT_COMPAT, 'UTF-8');
			//BBSMP-341 -- End
			if(!empty($lead->pre_bid_meeting)){
			   if(trim($lead->mi_lead_id) != '' || trim($lead->onvia_id)!= ''){
				$db_pbm = $timedate->to_db($lead->pre_bid_meeting);
				$lead->pre_bid_meeting = $timedate->to_display_date_time($db_pbm,true,false);
			  }
				
			}
			
			$this->ss->assign('oss_timedate',$oss_timedate);
			$this->ss->assign('MOD',$mod_strings);
			$this->ss->assign('lead',$lead);
			$this->ss->display('custom/modules/Leads/tpls/pldetails.tpl');
		}else if(isset($_REQUEST['project_opportunity_id']) && !empty($_REQUEST['project_opportunity_id'])){
			
			$opportunity_id = $_REQUEST['project_opportunity_id'];
			$opportunity = new Opportunity();
			$opportunity->retrieve($opportunity_id);
			
			echo $opportunity->name;
			
		}
	}
}
