<?php
require_once 'include/MVC/View/SugarView.php';
require_once "custom/include/common_functions.php";
require_once 'custom/modules/Leads/views/view.save_opportunity.php';

class CustomOpportunitiesViewUnlinkop extends SugarView{
	
	function CustomOpportunitiesViewUnlinkop(){
		parent::SugarView();
	}
	
	function display(){
		
		
		global $timedate,$current_user, $db;
		
		$opportunity_id = $_REQUEST['linked_id'];
		
		$oppor = new Opportunity ();
		$oppor->retrieve ( $opportunity_id );
		
		$pre_parent_op_id = $oppor->parent_opportunity_id;
		$account_id = $oppor->account_id;
		$contact_id = $oppor->contact_id;
		$lead_id = $oppor->project_lead_id;
		$lead_source = $oppor->lead_source;
		$oppName = $oppor->name;
		$oppAmount = $oppor->amount;
		$oppDateClosed = $oppor->date_closed;
		$oppTimeZone = $oppor->bid_due_timezone;
		
		$pre_Oppor = new Opportunity ();
		$pre_Oppor->retrieve ( $pre_parent_op_id );
		
		// Craete new Parent Opportunity
		$p_Oppor = new Opportunity ();
		$p_Oppor->name = $oppName;
		$p_Oppor->amount = $oppAmount;
		$p_Oppor->date_closed = $oppDateClosed;
		$p_Oppor->sub_opp_count = 1;
		$p_Oppor->project_lead_id = $lead_id;
		$p_Oppor->lead_source = $lead_source;
		$p_Oppor->assigned_user_id = 1;
		$p_Oppor->bid_due_timezone = $oppTimeZone;
		$p_Oppor->my_project_status = 'Interested';
		
		//project lead details
		$p_Oppor->sales_stage = $pre_Oppor->sales_stage;
		$p_Oppor->lead_received = $pre_Oppor->lead_received;
		$p_Oppor->lead_address = $pre_Oppor->lead_address;
		$p_Oppor->lead_state = $pre_Oppor->lead_state;
		$p_Oppor->lead_structure = $pre_Oppor->lead_structure;
		$p_Oppor->lead_county = $pre_Oppor->lead_county;
		$p_Oppor->lead_type = $pre_Oppor->lead_type;
		$p_Oppor->lead_city = $pre_Oppor->lead_city;
		$p_Oppor->lead_owner = $pre_Oppor->lead_owner;
		$p_Oppor->lead_zip_code = $pre_Oppor->lead_zip_code;
		$p_Oppor->lead_project_status = $pre_Oppor->lead_project_status;
		$p_Oppor->lead_start_date = $pre_Oppor->lead_start_date;
		$p_Oppor->lead_end_date = $pre_Oppor->lead_end_date;
		$p_Oppor->lead_source = $pre_Oppor->lead_source;
		$p_Oppor->lead_contact_no = $pre_Oppor->lead_contact_no;
		$p_Oppor->lead_valuation = $pre_Oppor->lead_valuation;
		$p_Oppor->lead_union_c = $pre_Oppor->lead_union_c;
		$p_Oppor->lead_non_union = $pre_Oppor->lead_non_union;
		$p_Oppor->lead_prevailing_wage = $pre_Oppor->lead_prevailing_wage;
		$p_Oppor->lead_square_footage = $pre_Oppor->lead_square_footage;
		$p_Oppor->lead_stories_below_grade = $pre_Oppor->lead_stories_below_grade;
		$p_Oppor->lead_number_of_buildings = $pre_Oppor->lead_number_of_buildings;
		$p_Oppor->lead_stories_above_grade = $pre_Oppor->lead_stories_above_grade;
		$p_Oppor->lead_scope_of_work = $pre_Oppor->lead_scope_of_work;
		$p_Oppor->custom_field_1 = $pre_Oppor->custom_field_1;
		$p_Oppor->custom_field_2 = $pre_Oppor->custom_field_2;
		
		$p_Oppor->save ();
		
		if (! empty ( $p_Oppor->id )) {
						
			//save child Opportunity account relationship with parent Opportunity
			$relate_values = array (
				'opportunities_accountsopportunities_ida' => $p_Oppor->id,
				'opportunities_accountsaccounts_idb' => $account_id 
			);
			$p_Oppor->set_relationship ( 'opportunities_accounts_c', $relate_values );
			
			//Modified by Mohit Kumar Gupta 27-11-2015
			//If any client opportunities have been unlinked or deleted, those client opportunities should not be recreated.
			//for the change request of client BSI-797
			//Create a duplicate client opportunity of existing client opportunity
			$newChildOpp = new Opportunity();
			$newChildOpp->name = $oppName;
			$newChildOpp->amount = $oppAmount;
			$newChildOpp->created_by = $oppor->created_by;
			$newChildOpp->date_entered = $oppor->date_entered;
			$newChildOpp->assigned_user_id = $oppor->assigned_user_id;
			$newChildOpp->modified_user_id = $current_user->id;
			$newChildOpp->team_id = $oppor->team_id;
			$newChildOpp->team_set_id = $oppor->team_set_id;
			$newChildOpp->sales_stage = $oppor->sales_stage;
			$newChildOpp->client_bid_status = $oppor->client_bid_status;
			$newChildOpp->project_lead_id = $lead_id;
			$newChildOpp->leadclientdetail_id = $oppor->leadclientdetail_id;
			$newChildOpp->date_closed = $oppDateClosed;
			$newChildOpp->bid_due_timezone = $oppTimeZone;
			$newChildOpp->lead_source = $lead_source;
			$newChildOpp->parent_opportunity_id = $p_Oppor->id;
			$newChildOpp->opportunity_classification = $oppor->opportunity_classification;
			$newChildOpp->contact_id = $oppor->contact_id;
			$newChildOpp->save ();
			
			//save child opportunity account and contact relationship
			$relate_values = array('opportunity_id'=>$newChildOpp->id,'account_id' => $account_id);
			$newChildOpp->set_relationship('accounts_opportunities', $relate_values);
			 
			$relate_values = array('opportunity_id'=>$newChildOpp->id,'contact_id' => $contact_id);
			$newChildOpp->set_relationship('opportunities_contacts', $relate_values);
			 
			//update client opportunity private team to it
			//ViewSave_opportunity:: setTeams($newChildOpp->id);
			
			//Save Opportunity Id.converted to opportunity flag into Bidder
			$bidderUpdateQuery = "UPDATE oss_leadclientdetail SET converted_to_oppr='1', opportunity_id=".$db->quoted($newChildOpp->id). " WHERE id=".$db->quoted($newChildOpp->leadclientdetail_id);
			$db->query($bidderUpdateQuery);			
			unset ( $newChildOpp );			
			
			//mark deleted to the exsisting previous client opportunity
			$oppor->mark_deleted ( $oppor->id );
			unset ( $oppor );
			
		}
		unset ( $p_Oppor );
		
		
		// check if any child Opportunity has same opportunity as our
		$AccountCountQuery = "SELECT count(*) total
					FROM opportunities
					LEFT JOIN accounts_opportunities ao
					ON opportunities.id = ao.opportunity_id AND ao.deleted=0
					LEFT JOIN accounts
					ON accounts.id = ao.account_id AND accounts.deleted = 0
					WHERE opportunities.parent_opportunity_id = '" . $pre_parent_op_id . "'
					AND accounts.id = '" . $account_id . "'
					AND opportunities.deleted = 0";
		
		$AccountCountResult = $pre_Oppor->db->query ( $AccountCountQuery );
		$AccountCountData = $pre_Oppor->db->fetchByAssoc ( $AccountCountResult );
		
		
		// count child Opportunity -1
		$pre_Oppor->sub_opp_count = ( int ) $pre_Oppor->sub_opp_count - 1;
		
		// if sub Opportunity goes to 0 delete Parent Opportunity
		if ($pre_Oppor->sub_opp_count == 0) {
			
			$pre_Oppor->mark_deleted ( $pre_parent_op_id );
			
		} else {
			
			// get Child Opportunity data
			$totalAmount = 0;
			$bidDueDate = array ();
			$SubOpCount = 0;
			$zoneArray = array ();
			
			$SubOpQuery = "SELECT opportunities.date_closed, opportunities.amount,
					opportunities.bid_due_timezone
					FROM opportunities
					WHERE opportunities.parent_opportunity_id = '" . $pre_parent_op_id . "'
					AND opportunities.deleted = 0";
			$SubOpResult = $pre_Oppor->db->query ( $SubOpQuery );
			// $SubOpData = $pre_Oppor->db->fetchByAssoc($SubOpResult);
			
			while ( $SubOpData = $pre_Oppor->db->fetchByAssoc ( $SubOpResult ) ) {
				$totalAmount = $totalAmount + $SubOpData ['amount'];
				$bidDueDate [] = $SubOpData ['date_closed'];
				$zoneArray [] = $SubOpData ['bid_due_timezone'];
				$SubOpCount ++;
			}
			
			$pre_Oppor->amount = $totalAmount / $SubOpCount;
			$earlierDate = min ( $bidDueDate );
			$tmpDates = array_flip ( $bidDueDate );
			$iTimezoneIndex = $tmpDates [$earlierDate];
			
			$pre_Oppor->bid_due_timezone = $zoneArray [$iTimezoneIndex];
			$pre_Oppor->date_closed = $earlierDate;
			
			$pre_Oppor->save ();
			
			// if no common child Opportunity delete relationship
			/* if ($AccountCountData ['total'] > 0) {
				$pre_Oppor->load_relationship ( 'opportunities_accounts' );
				$pre_Oppor->opportunities_accounts->delete( $account_id );
			} */
		}
		
		unset ( $pre_Oppor );
		updateProjectOpportunityTeamSet ( $pre_parent_op_id );
		if(isset($_REQUEST['from_url']) && $_REQUEST['from_url']=='sub_opp'){
			SugarApplication::redirect("index.php?module=Opportunities&action=index");
		}
	}	
	
	function old_display(){
		
		global $timedate,$current_user;
		
		//get all the selected sub Opportunity to be unlinked
		$mass_id = $_REQUEST['mass'];
		
		if(is_array($mass_id)){
			
			foreach($mass_id as $opportunity_id){

				$oppor = new Opportunity();
				
				$oppor->retrieve($opportunity_id);
				
				$pre_parent_op_id = $oppor->parent_opportunity_id;
				$account_id = $oppor->account_id;
				
				//Craete new Parent Opportunity 
				$p_Oppor = new Opportunity();
				$p_Oppor->name = $oppor->name;
				$p_Oppor->amount = $oppor->amount;
				$p_Oppor->date_closed = $oppor->date_closed;
				$p_Oppor->sales_stage = $oppor->sales_stage;
				//$p_Oppor->account_id =  '';
				$p_Oppor->sub_opp_count = 1;
				$p_Oppor->project_lead_id = $oppor->project_lead_id;
				$p_Oppor->lead_source = $oppor->lead_source;
				$p_Oppor->assigned_user_id = 1;
				$p_Oppor->bid_due_timezone = $oppor->bid_due_timezone;
				$p_Oppor->my_project_status = 'Interested';
				$p_Oppor->save();
				
				
				if(!empty($p_Oppor->id)){
					
					//Create Relationship between Parent Opportunity and Child Opportunity
					$oppor->parent_opportunity_id = $p_Oppor->id;
					$oppor->save();
					unset($oppor);
					
					//save child Opportunity account relationship with parent Opportunity
					$relate_values = array('opportunities_accountsopportunities_ida'=>$p_Oppor->id,
							'opportunities_accountsaccounts_idb' => $account_id);
					$p_Oppor->set_relationship('opportunities_accounts_c', $relate_values);
				}
				unset($p_Oppor);
				
				
				$pre_Oppor = new Opportunity();
				
				//check if any child Opportunity has same opportunity as our
				$AccountCountQuery = "SELECT count(*) total
					FROM opportunities 
					LEFT JOIN accounts_opportunities ao 
					ON opportunities.id = ao.opportunity_id AND ao.deleted=0
					LEFT JOIN accounts 
					ON accounts.id = ao.account_id AND accounts.deleted = 0
					WHERE opportunities.parent_opportunity_id = '".$pre_parent_op_id."' 
					AND accounts.id = '".$account_id."' 
					AND opportunities.deleted = 0";
				$AccountCountResult = $pre_Oppor->db->query($AccountCountQuery);
				$AccountCountData = $pre_Oppor->db->fetchByAssoc($AccountCountResult);
				
				
				$pre_Oppor->retrieve($pre_parent_op_id);
				
				
				//count child Opportunity -1
				$pre_Oppor->sub_opp_count = (int)$pre_Oppor->sub_opp_count - 1;
				
				//if sub Opportunity goes to 0 delete Parent Opportunity
				if($pre_Oppor->sub_opp_count == 0){
					
					$pre_Oppor->mark_deleted($pre_parent_op_id);
				
				}else{
					
					//get Child Opportunity data
					$totalAmount = 0;
					$bidDueDate = array();
					$SubOpCount = 0;
					$zoneArray = array();
					
					$SubOpQuery = "SELECT opportunities.date_closed, opportunities.amount,
					opportunities.bid_due_timezone
					FROM opportunities
					WHERE opportunities.parent_opportunity_id = '".$pre_parent_op_id."'
					AND opportunities.deleted = 0";
					$SubOpResult = $pre_Oppor->db->query($SubOpQuery);
					//$SubOpData = $pre_Oppor->db->fetchByAssoc($SubOpResult);
					
					while ($SubOpData = $pre_Oppor->db->fetchByAssoc($SubOpResult)){
						$totalAmount = $totalAmount +  $SubOpData['amount'];
						$bidDueDate[] =  $SubOpData['date_closed'];
						$zoneArray[] = $SubOpData['bid_due_timezone'];
						$SubOpCount++;
					}
					
					$pre_Oppor->amount = $totalAmount/$SubOpCount;
					$earlierDate = min($bidDueDate);
					$tmpDates = array_flip($bidDueDate);
					$iTimezoneIndex = $tmpDates[$earlierDate];
					
					$pre_Oppor->bid_due_timezone = $zoneArray[$iTimezoneIndex];
					$pre_Oppor->date_closed = $earlierDate;
					
					$pre_Oppor->save();
					
					//if no common child Opportunity delete relationship
					if($AccountCountData['total'] > 0){
						$pre_Oppor->load_relationship('opportunities_accounts');
						$pre_Oppor->opportunities_accounts->delete($account_id);
					}
				}
				unset($pre_Oppor);
				updateProjectOpportunityTeamSet($pre_parent_op_id);
			}
		}
		
		SugarApplication::redirect("index.php?module=Opportunities&action=index");
	}
}

?>