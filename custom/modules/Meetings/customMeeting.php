<?php
require_once 'modules/Meetings/Meeting.php';

class customMeeting extends Meeting{
	
	function customMeeting(){
		parent::Meeting();
	}
	
	
	function create_new_list_query($order_by, $where, $filter=array(), $params=array(), $show_deleted = 0, $join_type='', $return_array = false, $parentbean=null, $singleSelect = false) {
		
		global $current_user,$timedate;
		
		$stLeadSQL = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect);
		
		if(isset($_REQUEST['date_from_advanced']) && !empty($_REQUEST['date_from_advanced'])){
            $date_from = trim($_REQUEST['date_from_advanced']);
            $stLeadSQL['where'] .= " AND meetings.date_start >='".$timedate->to_db($date_from)."' ";
       }
       if(isset($_REQUEST['date_to_advanced']) && !empty($_REQUEST['date_to_advanced'])){
            $date_to = trim($_REQUEST['date_to_advanced']);
            $gmt_date_to = $timedate->getDayStartEndGMT($date_to,$current_user);
            $stLeadSQL['where'] .= " AND meetings.date_start <='".$gmt_date_to['end']."'  ";
       }
		
       return $stLeadSQL;
       
	}

	
	function fill_in_additional_list_fields() {
		
		global $app_list_strings;
		
		$meeting = new Meeting();
		$meeting->retrieve($this->id);


		if(isset($meeting->contact_id) && !empty($meeting->contact_id)){
			
			$this->parent_id = $meeting->contact_id;
			$this->parent_type = 'Contacts';
		}
		
		$parent_id = $this->parent_id;
		$parent_type_name = $app_list_strings['record_type_display'][$this->parent_type];
		
		if(isset($parent_id) && !empty($parent_id)){
			
			$this->parent_id = '<a href="index.php?module='.$this->parent_type.'&return_module='.$this->parent_type.'&action=DetailView&record='.$parent_id.'">';
			
			
			if($this->parent_type == 'Contacts'){
				
				$contacts = new Contact();
				$contacts->retrieve($parent_id);
				$account_name = $contacts->account_name;
				$this->parent_id .= $parent_type_name.' - '.$contacts->name.' - '.$account_name;

				
			}else if($this->parent_type == 'Opportunities'){
					
				$opp = new Opportunity();
				$opp->retrieve($parent_id);
				
				if(isset($opp->parent_opportunity_id) && !empty($opp->parent_opportunity_id)){
					
					$opportunity_name = $opp->opportunity_name;
					$this->parent_id .= 'Client Opportunity  - '.$opp->account_name.' - '.$this->parent_name;

				}else{
					
					$this->parent_id .= 'Project Opportunity  - '.$this->parent_name;
				}
				
			}else if($this->parent_type == 'Quotes'){
				
					$quote =  new Quote();
					$quote->retrieve($parent_id);
				
					$this->parent_id .= $quote->name.' - '.$quote->account_name;
				
			}else{
				
				$this->parent_id .= $parent_type_name.' - '.$this->parent_name;
				
			}
			
			$this->parent_id .= '</a>';
		}
		
	}
}