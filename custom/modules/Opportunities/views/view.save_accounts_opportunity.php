<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/master-subscription-agreement
* By installing or using this file, You have unconditionally agreed to the
* terms and conditions of the License, and You may not use this file except in
* compliance with the License.  Under the terms of the license, You shall not,
* among other things: 1) sublicense, resell, rent, lease, redistribute, assign
* or otherwise transfer Your rights to the Software, and 2) use the Software
* for timesharing or service bureau purposes such as hosting the Software for
* commercial gain and/or for the benefit of a third party.  Use of the Software
* may be subject to applicable fees and any use of the Software without first
* paying applicable fees is strictly prohibited.  You do not have the right to
* remove SugarCRM copyrights from the source code or user interface.
*
* All copies of the Covered Code must include on each user interface screen:
*  (i) the "Powered by SugarCRM" logo and
*  (ii) the SugarCRM copyright notice
* in the same form as they appear in the distribution.  See full license for
* requirements.
*
* Your Warranty, Limitations of liability and Indemnity are expressly stated
* in the License.  Please refer to the License for the specific language
* governing these rights and limitations under the License.  Portions created
* by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
/**
 * Added save functionality to create opportunity from accounts.
* @author Mohit Kumar Gupta
* @date 08-Nov-2013
*/
set_time_limit ( 0 );

require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/common_functions.php';
//require_once('modules/Teams/TeamSet.php');
require_once('include/formbase.php');

class ViewSave_accounts_opportunity extends SugarView {
	/*
	 * Constructor for the View
	*/
    function __construct() {
        parent::SugarView();
    }
    /**
     * display method to render the view
     *
     * @see SugarView::display()
     *
     */
    function display() {
    	global $db, $app_list_strings, $timedate, $current_user;
        $requestData  = $_REQUEST;
        $parentOpportunityId = '';
        //If clients opportunities updating to existing parent opportunity
        if (!empty($requestData['parent_opportunity_id'])) {
        	$parentOpportunityId = $requestData['parent_opportunity_id'];
        } else {
        	//If clients opportunities updating to new parent opportunity
        	//Then create a new parent opportunity
        	//Process to Create Parent Opportunity
        	$opprParent = new Opportunity();
        	$opprParent = populateFromPost('', $opprParent);
        	if( !ACLController::checkAccess($opprParent->module_dir, 'edit', $opprParent->isOwner($current_user->id))){
        		ACLController::displayNoAccess(true);
        	}
        	$check_notify = FALSE;
        	if (isset($GLOBALS['check_notify'])) {
        		$check_notify = $GLOBALS['check_notify'];
        	}
        	$opprParent->save($check_notify);
        	$parentOpportunityId = $opprParent->id;
        }
        
        //If parent opportunity esists in the system and client ids coming from form post
        if (!empty($parentOpportunityId) && !empty($requestData['clientIds'])) {
        	
        	$arSelectIds = explode(",",$requestData['clientIds']);
        	
        	// Fetch infromation from parent Opportunity
        	$parent_oppr_obj = new Opportunity ();
        	$parent_oppr_obj->disable_row_level_security = true;
        	$parent_oppr_obj->retrieve ( $parentOpportunityId );
        	
        	$notification_list = array();        	        	        	        	
        	
	        // Prepare Data for save sub opportunity
	        foreach ( $arSelectIds as $clientId ) {
	        	// Save Opportunity Id into Bidder
	        	$account = new Account();
	        	$account->disable_row_level_security = true;
	        	$account->retrieve ( $clientId );
	        	$assignedUserId = $requestData ['assigned_user_id_' . $clientId];
	        	$account_id = $clientId;
	        	$contact_id = $requestData ['contact_id_' . $clientId];
	        	$lead = new Lead ();
	        	$lead->disable_row_level_security = true;
	        	$lead->retrieve ( $parent_oppr_obj->project_lead_id );
	        	$lead_source = $lead->lead_source; 
	        
	        
	        	$sub_oppr_obj = new Opportunity ();
	        	$sub_oppr_obj->disable_row_level_security = true;
	        	$sub_oppr_obj->name = $parent_oppr_obj->name;
	        	$sub_oppr_obj->amount = $parent_oppr_obj->amount;
	        	$sub_oppr_obj->sales_stage = 'Estimating';
	        	$sub_oppr_obj->account_id = $account_id;
	        	$sub_oppr_obj->contact_id = $contact_id;
	        	$sub_oppr_obj->assigned_user_id = $assignedUserId;
	        	$sub_oppr_obj->project_lead_id = $parent_oppr_obj->project_lead_id;
	        	//$sub_oppr_obj->leadclientdetail_id = $bId;
	        	$sub_oppr_obj->lead_source = $lead_source; 
	        	$sub_oppr_obj->date_closed = $parent_oppr_obj->date_closed;
	        	$sub_oppr_obj->bid_due_timezone = $parent_oppr_obj->bid_due_timezone;
	        	$sub_oppr_obj->parent_opportunity_id = $parent_oppr_obj->id;
	        	$sub_oppr_obj->client_bid_status = 'Bidding';
	        	$sub_oppr_obj->opportunity_classification = $requestData ['opportunity_classification_' . $clientId];
	        	
	        	$sub_oppr_obj->save();
	        
	        	$notification_list[$assignedUserId][] = array(
	        			'client_name' => $account->name,
	        	);
	        
	        	if(!empty($sub_oppr_obj->id)){
	        		$this->setTeams($sub_oppr_obj->id);
	        	}
	        
	        	// Save Opportunity Id into Bidder
	        	/* $bidder = new oss_LeadClientDetail ();
	        	$bidder->disable_row_level_security = true;
	        	$bidder->retrieve ( $bId );
	        	$bidder->opportunity_id = $sub_oppr_obj->id;
	        	$bidder->save ();
	        	unset ( $bidder ); */
	        
	        	// Set Flag as Converted for group of bidders on Bidder List
	        	// module
	        	/* $bidders_ids = $new_bidders ['biddersIds_' . $bId];
	        	$bidders_ids = explode ( ",", $bidders_ids );
	        	foreach ( $bidders_ids as $bidderId ) {
	        		$lcd = new oss_LeadClientDetail ();
	        		$lcd->disable_row_level_security = true;
	        		$lcd->retrieve ( $bidderId );
	        		$lcd->converted_to_oppr = 1;
	        		$lcd->save ();
	        	} */
	        	        	
	        	$parent_oppr_obj->sub_opp_count++;
	        	$arRelatedAccounts[] = $account_id;
	        }
	        
	        // Update total number of opportunity in parent opportunity
	        $obProjectOpp = new Opportunity();
	        $obProjectOpp->retrieve ( $parentOpportunityId );
	        $obProjectOpp->sub_opp_count = $parent_oppr_obj->sub_opp_count;
	        $parentOppSalesStage = $obProjectOpp->sales_stage;
	        $obProjectOpp->save();
	        	
	        //update project project bid due date on project opportunity
	        updateProjectOpprBidDueDate($parent_oppr_obj->id);
	        
	        //save each child opportunity account information to parent opportunity relationship
	        foreach($arRelatedAccounts as $stAccountId){
	        	$relate_values = array('opportunities_accountsopportunities_ida'=>$obProjectOpp->id,
	        			'opportunities_accountsaccounts_idb' => $stAccountId);
	        	$obProjectOpp->set_relationship('opportunities_accounts_c', $relate_values);
	        }
	        	
	        /**
	         * Display Confirmation Message when User is Lead Reviewer
	         */
	        require_once 'custom/modules/Users/role_config.php';
	        global $arUserRoleConfig;
	        	
	        $user_id = $current_user->id;
	        	
	        //Fetch roles based on user id
	        $roleObj = new ACLRole();
	        $roleObj->disable_row_level_security = true;
	        $roles = $roleObj->getUserRoles($user_id,0);
	        	
	        $current_user_role = '';
	        	
	        //Checking current user role with Role Config Array
	        foreach($arUserRoleConfig as $roleName => $roleId){
	        	if($roleId==$roles[0]->id){
	        		$current_user_role = $roleName;
	        	}
	        }
	        	
	        updateProjectOpportunityTeamSet($parentOpportunityId);
	        	
			//update parent opportunity total amount
			//if sales stage is won(closed)
			//this case specially for new parent opportunity creation
	        if($parentOppSalesStage == 'Won (closed)'){
	        	 
	        	$avg_sql = " SELECT SUM(opportunities.amount)amount
						FROM opportunities
						WHERE opportunities.parent_opportunity_id='" . $parentOpportunityId . "'
						AND opportunities.sales_stage = 'Won (closed)'
            			AND opportunities.deleted = 0 ";
	        	 
	        	$avg_query = $db->query ( $avg_sql );
	        	$avg_result = $db->fetchByAssoc ( $avg_query );
	        	 
	        	$update_parent_query = " UPDATE opportunities SET
            		amount = '".$avg_result ['amount']."',
					amount_usdollar = '".$avg_result ['amount']."'
					WHERE id = '".$parentOpportunityId."' AND deleted = 0 ";
	        	 
	        	$db->query($update_parent_query);
	        
	        }
	        
	        //send customizied notification email
	        if(count($notification_list) > 0){
	        	require_once 'custom/modules/Leads/views/view.save_opportunity.php';
	        	ViewSave_opportunity::sendNotificationEmail($notification_list, $parentOpportunityId);
	        }
	        
        }                                      
                  
        if(!isset($requestData['clientIds']) && empty($requestData['clientIds']) && !empty($parentOpportunityId)){
        	$queryParams = array(
        		'module' => 'Opportunities',
        		'action' => 'DetailView',
        		'record' => $parentOpportunityId,
        	);
        	SugarApplication::redirect('index.php?' . http_build_query($queryParams));
		} else if(!isset($requestData['clientIds']) && empty($requestData['clientIds']) && empty($parentOpportunityId)){
        	$queryParams = array(
        		'module' => 'Accounts',
        		'action' => 'index',
        	);
        	SugarApplication::redirect('index.php?' . http_build_query($queryParams));
		} else {                
            $queryParams = array(
        		'module' => 'Opportunities',
        		'action' => 'DetailView',
        		'record' => $parentOpportunityId,
            	'ClubbedView'=>1,
        	);
        	SugarApplication::redirect('index.php?' . http_build_query($queryParams));
        }
    }//end display function.
    
    /**
     * used for set teams corresponding to assigned user id for each client opportunity
     * @param string $oppor_id
     * @return void
     */
    function setTeams($oppor_id){
		 
		global $db;
	
		$opportunity = new Opportunity();
		$opportunity->retrieve($oppor_id);
	
		$assignedUserId = $opportunity->assigned_user_id;
	
		$user = new User();
		$user->disable_row_level_security = true;
		$user->retrieve($assignedUserId);
	
		$private_team = $user->getPrivateTeam();
	
		//$team_set = new TeamSet();
		$team_set->disable_row_level_security = true;
		//$team_set_id = $team_set->addTeams(array($private_team));
	
		$sql = "UPDATE opportunities SET team_id = '".$private_team."', team_set_id = '".$team_set_id."' WHERE id = '".$oppor_id."' ";
		$db->query($sql);
	
	}

}

?>
