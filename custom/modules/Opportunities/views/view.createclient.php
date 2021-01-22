<?php
require_once 'custom/include/common_functions.php';
require_once 'include/MVC/View/SugarView.php';
class OpportunitiesViewCreateclient extends SugarView{
	
	function OpportunitiesViewCreateclient(){
		parent::SugarView();
	}
	
	function display(){
		
		global $db;
		
		$contact_ids = array();
		
		if(isset($_REQUEST['client_data']) && !empty($_REQUEST['client_data'])){
			$contact_ids = explode(",",$_REQUEST['client_data']);
		}else{
			die("No Contact is Selected");
		}
		$select_entire_list = $_REQUEST['select_entire_list'];
		
		if($select_entire_list == 1){
			$sql = "SELECT id FROM contacts WHERE deleted = 0";
			$result = $db->query($sql);
			while($row = $db->fetchByAssoc($result)){
				$contact_ids[] = $row['id']; 
			}
		}
		
		$record = $_REQUEST['record'];
		$opportunity = new Opportunity();
		$opportunity->retrieve($record);
		$sub_opportunity_count = $opportunity->sub_opp_count;
		
		foreach($contact_ids as $contact_id){
			
			$contact = new Contact();
			$contact->retrieve($contact_id);
			
			$sub_opportunity = new Opportunity();
			$sub_opportunity->name = $opportunity->name;
			$sub_opportunity->amount = $opportunity->amount;
			//By default Sales Stage to be 'Estimating'
			$sub_opportunity->sales_stage = 'Estimating';
			$sub_opportunity->client_bid_status = 'Bidding';
			$sub_opportunity->account_id = $contact->account_id;
			$sub_opportunity->contact_id = $contact_id;
			$sub_opportunity->date_closed = $opportunity->date_closed;
			$sub_opportunity->bid_due_timezone = $opportunity->bid_due_timezone;
			$sub_opportunity->parent_opportunity_id = $opportunity->id;
			$sub_opportunity->probability = $opportunity->probability;
			$sub_opportunity->next_step = $opportunity->next_step;
			$sub_opportunity->next_action_date = $opportunity->next_action_date;
			$sub_opportunity->description = $opportunity->description;
			$sub_opportunity->save();
			$sub_opportunity_count++;
		}
		
		$opportunity->sub_opp_count = $sub_opportunity_count;
		$opportunity->save();
		
		updateProjectOpprBidDueDate($record);
		
		SugarApplication::redirect('index.php?module=Opportunities&action=DetailView&record='.$record.'&ClubbedView=1');
		exit();
	}
	
	
}
?>