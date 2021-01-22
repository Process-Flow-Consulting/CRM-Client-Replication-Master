<?php
set_time_limit(0);
//ini_set('display_errors',1);

require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';


class LeadsViewSave_new_opportunity extends SugarView {
	
    private $formData;
    private $userData;
    
	function __construct($formData=null, $userId = '1') {
	    //if view called from lead conversion ajax request
	    $this->formData = $formData;
	    //current user id issue identified in BodScope integration and fixed
	    //Mohit Kumar Gupta 25-09-2015
	    $this->userData = $this->getUserData($userId);
		parent::SugarView ();
	}
	
	function display() {		
		global $db, $app_list_strings;
		$formData = $this->formData;
		// Check if bidder id is set and not empty
		$new_bidders = $_REQUEST;
		$statusFile = '';
		//if view called from lead conversion ajax request
		if (!empty($formData)) {
		    $new_bidders = $formData;
		    $statusFile = $formData['statusFilePath'];
		}
		
		if (isset ( $new_bidders ['bid'] ) && ! empty ( $new_bidders ['bid'] )) {
			// Fetch infromation from parent Opportunity
			$parent_oppr_obj = new Opportunity ();
			$parent_oppr_obj->disable_row_level_security = true;
			$parent_oppr_obj->retrieve ( $new_bidders ['opportunity_id'] );
			
			$notification_list = array();
			
			//get saved target Classifications start
			//@modified by Mohit Kumar Gupta
			//@date 18-nov-2013
			$arSavedTargetClass = getTargetClassifications();
			$arSavedTargetClassifications = array();
			foreach($arSavedTargetClass as $obSavedClass){
				$arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->id ;
			}
			$countSavedTargetClassifications = count($arSavedTargetClassifications);
			//get saved target Classifications end
			 
			//get saved roles classifications start
			//@modified by Mohit Kumar Gupta
			//@date 18-nov-2013
			$rolesClassificationsArr = getRolesClassifications();
			$countRolesClassifications = count($rolesClassificationsArr);
			if ($countRolesClassifications == 0) {
				setRolesClassifications();
				$rolesClassificationsArr = getRolesClassifications();
				$countRolesClassifications = count($rolesClassificationsArr);
			}
			//get saved roles classifications end
			
			// Prepare Date for save sub opportunity
			foreach ( $new_bidders ['bid'] as $bId ) {
				//require_once 'custom/include/OssTimeDate.php';
				//$oss_timedate = new OssTimeDate ();
				
				$assigned_user_id = $new_bidders ['assigned_user_id_' . $bId];
				$account_id = $new_bidders ['account_id_' . $bId];
				$contact_id = $new_bidders ['contact_id_' . $bId];
				$lead_id = $new_bidders ['lead_id_' . $bId];
				$lead = new Lead ();
				$lead->disable_row_level_security = true;
				$lead->retrieve ( $lead_id );
				$lead_source = $lead->lead_source;
				
				//$bid_due_date_db = $oss_timedate->convertDateForDB ( $lead->bids_due, $lead->bid_due_timezone, true );
				
				$sub_oppr_obj = new Opportunity ();
				$sub_oppr_obj->disable_row_level_security = true;
				$sub_oppr_obj->name = $parent_oppr_obj->name;
				$sub_oppr_obj->amount = $parent_oppr_obj->amount;
				//By default Sales Stage to be 'Estimating' 
				$sub_oppr_obj->sales_stage = 'Estimating';
				$sub_oppr_obj->account_id = $account_id;
				$sub_oppr_obj->contact_id = $contact_id;
				$sub_oppr_obj->assigned_user_id = $assigned_user_id;
				$sub_oppr_obj->project_lead_id = $lead_id;
				$sub_oppr_obj->leadclientdetail_id = $bId;
				$sub_oppr_obj->lead_source = $lead_source;
				//$sub_oppr_obj->date_closed = $bid_due_date_db;
				//$sub_oppr_obj->bid_due_timezone = $lead->bid_due_timezone;
				$sub_oppr_obj->date_closed = $new_bidders['earlier_date'];
				$sub_oppr_obj->bid_due_timezone = $new_bidders['earlier_bids_due_timezone'];
				$sub_oppr_obj->parent_opportunity_id = $parent_oppr_obj->id;
				$sub_oppr_obj->client_bid_status = 'Bidding';
				
				//update classification id to client opportunity start
				//Modified by Mohit Kumar Gupta
				//@date 20-Nov-2013
				$opportunityClassificationId = '';
				
				//get classification of an accounts related to that bidder
				$AccountCassificationArr = getAccountClassifications($account_id);
				$countAccountClassificationArr = count($AccountCassificationArr);
				
				//if classification of an accounts and target classifications exists
				if ($countAccountClassificationArr >0 && $countSavedTargetClassifications >0) {
					//if target classification matches to client classification
					//update alphabetically first target classification to classification id
					foreach ($arSavedTargetClassifications as $classificationId) {
						if (in_array($classificationId,$AccountCassificationArr)) {
							$opportunityClassificationId = $classificationId;
							break;
						}
					}
					 
				}
				//if classification id does not match from target classification and client classification
				//then select classification id from role mapping
				if (empty($opportunityClassificationId)) {
					//Save Opportunity Id into Bidder
					$bidderObj = new oss_LeadClientDetail();
					$bidderObj->disable_row_level_security = true;
					$bidderObj->retrieve($bId);
					$bidderRole = $bidderObj->role;
					$opportunityClassificationId = $rolesClassificationsArr[$bidderRole];
					
					//@modified By Mohit Kumar Gupta 17-11-2015
                    //if bidder role is having single quotes as special character BSI-787					
					if (empty($opportunityClassificationId)) {
					    $opportunityClassificationId = $rolesClassificationsArr[htmlspecialchars($bidderRole,ENT_QUOTES)];
					}
					
					unset($bidderObj);
				}
				$sub_oppr_obj->opportunity_classification = $opportunityClassificationId;
				//update classification id to client opportunity end
				
				
				/*$user = new User();
				$user->disable_row_level_security = true;
				$user->retrieve($assigned_user_id);
				$private_team = $user->getPrivateTeam();
				$sub_oppr_obj->team_id = $private_team;
				
				$team_set = new TeamSet();
				$team_set->disable_row_level_security = true;
				$team_set_id = $team_set->addTeams(array($private_team));
				
				$sub_oppr_obj->team_set_id = $team_set_id;*/
				
				$sub_oppr_obj->save();
				
				
				$account_sql = " SELECT name FROM accounts WHERE deleted = 0 AND id ='".$account_id."' ";
                $account_result = $db->query($account_sql);
                $account_row = $db->fetchByAssoc($account_result);
                
                $notification_list[$assigned_user_id][] = array(
                		'client_name' => $account_row['name'],
                );
				
				if(!empty($sub_oppr_obj->id)){
					$this->setTeams($sub_oppr_obj->id);
				}

				// Save Opportunity Id into Bidder
				$bidder = new oss_LeadClientDetail ();
				$bidder->disable_row_level_security = true;
				$bidder->retrieve ( $bId );
				$bidder->opportunity_id = $sub_oppr_obj->id;
				$bidder->save ();
				unset ( $bidder );
				
				// Set Flag as Converted for group of bidders on Bidder List
				// module
				$bidders_ids = $new_bidders ['biddersIds_' . $bId];
				$bidders_ids = explode ( ",", $bidders_ids );
				foreach ( $bidders_ids as $bidderId ) {
					$lcd = new oss_LeadClientDetail ();
					$lcd->disable_row_level_security = true;
					$lcd->retrieve ( $bidderId );
					$lcd->converted_to_oppr = 1;
					$lcd->save ();
				}
				
				// Make client and their all contacts visible
				$sql = "SELECT `mi_account_id`,visibility FROM `accounts` WHERE `id` = '".$account_id."' AND `deleted` = '0'";
				$query = $db->query($sql);
				$result = $db->fetchByAssoc($query);
				$mi_account_id = $result['mi_account_id'];
				$visibility = $result['visibility'];
				
				if(!empty($mi_account_id) && ($visibility == 0)){
					$pullObj = new PullBBH($mi_account_id);
					$pullObj->insertUpdateClients();
				}/**
                 * @Added By : Ashutosh 
                 * @date : 28 Aug 2013
                 * if client visibility is o and source is other then bluebook 
                 * then make this client visible
                 */               
                else if($visibility == '0'){
                    
                	//client should be visible
                    $obClient = new Account();
                    $obClient->retrieve($account_id);
                    $obClient->visibility = 1;
                    $obClient->save();
                                        
                    //related client contact shouldb be visible
                    if (!empty($obClient->id)) {
                        $arContacts = $obClient->get_contacts();
                        foreach ($arContacts as $obContact) {
                            $obClientContact = new Contact();
                            $obClientContact->retrieve($obContact->id);
                            $obClientContact->visibility = 1;
                            $obClientContact->save();
                        }
                    }                                   
                    
                }
                
                //Some cases client or client contact visibility not getted updated to 1.
                //then update bidders client and client contact visibility to 1 forcefully
                //Modified By Mohit Kumar Gupta 28-01-2014
                setBidderVisibility($account_id,$contact_id);

                if (!empty($formData)) {
                    // Upadte Current time into process lock file
                    $insertedOpp = file_get_contents ( $statusFile );
                    $fp2 = fopen ( $statusFile, "w" );
                    fwrite ( $fp2, ++$insertedOpp );
                    fclose ( $fp2 );
                }
                
				$parent_oppr_obj->sub_opp_count++;
			
			}
			/**
			 * Added By : Ashutosh
			 * Date : 9 May 2013
			 * Purpose : To change assignment of the Project opportunity 
			 *           if Client opportunity has different assinged users 

			//get assigned user for this Project opportunity
			$stGetAssignedSQL = 'SELECT COUNT(assigned_user_id) FROM opportunities WHERE 
			 parent_opportunity_id="'.$parent_oppr_obj.'" GROUP BY  assigned_user_id';
			$rsResult = $db->query($stGetAssignedSQL);
						 */
			$obProjectOpp = new Opportunity();
			$obProjectOpp->retrieve ( $new_bidders ['opportunity_id'] );
			$obProjectOpp->sub_opp_count = $parent_oppr_obj->sub_opp_count;
			$obProjectOpp->save();
			$parent_opportunity_id = $parent_oppr_obj->id;
			// Update total number of opportunity in parent opportunity
			//$parent_oppr_obj->sub_opp_count = $parent_oppr_obj->sub_opp_count+count($_REQUEST ['bid']);
			//$parent_oppr_obj->save();
			
			
			//update project project did due date on project opportunity
			updateProjectOpprBidDueDate($parent_oppr_obj->id);
						
			/**
			 * Display Confirmation Message when User is Lead Reviewer
			 */
			require_once 'custom/modules/Users/role_config.php';
			global $arUserRoleConfig;
			
			$user_id = $this->userData->id;
			
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
			
			updateProjectOpportunityTeamSet($parent_opportunity_id);
			
			
			//send customizied notification email
			if(count($notification_list) > 0){
				require_once 'custom/modules/Leads/views/view.save_opportunity.php';
				ViewSave_opportunity::sendNotificationEmail($notification_list, $parent_opportunity_id);
			}
			
			//if view called from lead conversion ajax request then no redirection should be there
			if (!empty($formData)) {
			    return true;
			} else if($current_user_role == 'lead_reviewer'){
			    /**
			     * Redirect page if user is lead reviewer page will be redirect on converted message else list view of opportunity.
			     */
				header('location:index.php?module=Opportunities&action=converted');
				exit();
			}else{
				/**
                 * Modified by : Ashutosh
                 * Date      : 08 May 2013
                 * Purpose: to redirect to opp summary view
                 */
                header('location:index.php?module=Opportunities&action=DetailView&record='.$parent_opportunity_id.'&ClubbedView=1');
            	//header('location:index.php?module=Opportunities&action=index');
				exit();
			}
		
		}
	}
	
	function setTeams($oppor_id){
		 
		global $db;
	
		$opportunity = new Opportunity();
		$opportunity->retrieve($oppor_id);
	
		$assigned_user_id = $opportunity->assigned_user_id;
	
		$user = new User();
		$user->disable_row_level_security = true;
		$user->retrieve($assigned_user_id);
	
		$private_team = $user->getPrivateTeam();
	
		$team_set = new TeamSet();
		$team_set->disable_row_level_security = true;
		$team_set_id = $team_set->addTeams(array($private_team));
	
		$sql = "UPDATE opportunities SET team_id = '".$private_team."', team_set_id = '".$team_set_id."' WHERE id = '".$oppor_id."' ";
		$db->query($sql);
	
	}
	
	/**
	 * get user data
	 */
	private function getUserData($userId = '1') {
	    $userId = (!empty($userId)) ? $userId : '1';
	    $obj = new User();
	    $obj->retrieve($userId);
	    return $obj;
	}
}
?>