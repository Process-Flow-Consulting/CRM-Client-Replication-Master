<?php
require_once "modules/Opportunities/Opportunity.php";

class SaveOpportunity {
	
	function SaveOpportunityAmount(&$focus){
		global $timedate;
		
		if(!empty($focus->opportunity_id)){
			
			$opp = new Opportunity();
			$opp->disable_row_level_security = 1 ;
			$opp->retrieve($focus->opportunity_id);
			
			if($focus->proposal_delivery_method != 'M' || !empty($focus->proposal_delivery_method)){
				
				if($focus->proposal_verified == 2){
					$opp->sales_stage = 'Proposal - Unverified';				
				}else if($focus->proposal_verified == 1){
					$opp->sales_stage = 'Proposal - Verified';
				}
				
			}			
			
			$opp->amount = number_format($focus->proposal_amount,2,'.','');
			$opp->save();
			
		}
		
	}
	
}