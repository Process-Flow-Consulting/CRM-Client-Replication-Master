<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/OssTimeDate.php';

class ViewRelatedpl extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){
	    $leads = array();
	    global $mod_strings, $db, $app_list_strings;
	    if(isset($_REQUEST['lead_id']) && !empty($_REQUEST['lead_id'])){			
			$oss_timedate = new OssTimeDate();					
			$lead_id = $_REQUEST['lead_id'];
			$sql = "SELECT 
					    leads.id,
					    leads.project_title,
					    leads.lead_source,						
					    getBidsDueDate(leads.bids_due,leads.bid_due_timezone) bids_due,						
					    leads.bid_due_timezone,
						leads.pre_bid_meeting,
						leads.change_log_flag,
						leads.date_modified,
					    (SELECT count(1) FROM leads_audit WHERE parent_id = leads.id) audit_cnt
					FROM
					    leads
					WHERE
					    leads.deleted = 0 AND leads.parent_lead_id = '".$lead_id."' OR leads.id = '".$lead_id."'
					ORDER BY leads.parent_lead_id";
			$query = $db->query($sql);
			
			while ($row = $db->fetchByAssoc($query)){
				$leads[] = $row;
			}
			
	    }
			
			
			//$lead = new Lead();	
			//$where = "leads.parent_lead_id='".$lead_id."' OR leads.id='".$lead_id."'";
			//$orderby = "leads.parent_lead_id";		
			//$leads = $lead->get_full_list($orderby,$where);
						

			
			$from_oppr = '';
			if(isset($_REQUEST['from']) && $_REQUEST['from']=='opportunity'){
				$from_oppr = $_REQUEST['from'];
				
				if(isset($_REQUEST['opportunity_id']) && !empty($_REQUEST['opportunity_id'])){
					$opportunity_id = $_REQUEST['opportunity_id'];					
				}
				
				$opportunity = new Opportunity();
				$opportunity->retrieve($opportunity_id);
				$this->ss->assign('opportunity',$opportunity);
			}

			//Project Lead Source Dpm
			$lead_source_dom = $app_list_strings['lead_source_list'];
			
			
						
			$this->ss->assign('from_oppr',$from_oppr);
			$this->ss->assign('oss_timedate', $oss_timedate);
			$this->ss->assign('MOD',$mod_strings);
			$this->ss->assign('leads',$leads);
			$this->ss->assign('lead_source_dom',$lead_source_dom);
			$this->ss->display('custom/modules/Leads/tpls/relatedpl.tpl');
			
		
	}
}
?>