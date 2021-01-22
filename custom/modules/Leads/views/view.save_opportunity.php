<?php
//ini_set('display_errors',1);
set_time_limit ( 0 );

require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/common_functions.php';
//require_once('modules/Teams/TeamSet.php');
require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/modules/Users/role_config.php';

class ViewSave_opportunity extends SugarView {

    private $formData;
    private $userData;
    
    function __construct($formData=null, $userId = '1') {
        //if view called from lead conversion ajax request
        $this->formData = $formData;
        //current user id issue identified in BodScope integration and fixed
        //Mohit Kumar Gupta 25-09-2015
        $this->userData = $this->getUserData($userId);
        parent::SugarView();
    }

    function display() {
    	global $db, $app_list_strings, $timedate, $arUserRoleConfig;
    	$formData = $this->formData;              	           	
       	
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
       	           	
       	$oss_timedate = new OssTimeDate();
       	$stParentOppAssingedId = '';
       	$bidders  = $_REQUEST;
       	$statusFile = '';
       	//if view called from lead conversion ajax request
       	if (!empty($formData)) {
       		$bidders = $formData;
       		$statusFile = $formData['statusFilePath'];
       	}
       	
        $name = $bidders['name'];
        $amount = (trim($bidders['amount']) != '') ? preg_replace('/[^0-9.]*/','',$bidders['amount']):'';
        $sales_stage = $bidders['sales_stage'];
        $totalOppr = count($bidders['bid']);
        
        $newOpprIds =array();
        
        $assigned_user_id = $this->userData->id;
        
        $primaryLeadId = $bidders['primary_lead_id'];
        $plead = new Lead();
        $plead->disable_row_level_security = true;
        $plead->retrieve($primaryLeadId);            
        
        /**
         * Process to Create Parent Opportunity
         */
        $oppr_parent = new Opportunity();
        $oppr_parent->disable_row_level_security = true;        
        $oppr_parent->name = $name;
        $oppr_parent->amount = $amount;
        $oppr_parent->sales_stage = $sales_stage;
        $oppr_parent->sub_opp_count = $totalOppr;
        $oppr_parent->project_lead_id = $plead->id;
        $oppr_parent->lead_source = $plead->lead_source;
        
        $oppr_parent->date_closed = $bidders['earlier_date'];
        $oppr_parent->bid_due_timezone = $bidders['earlier_bids_due_timezone'];
        $oppr_parent->my_project_status = 'Interested';
        /**
         * Added By Ashutosh on 8 May to save Project Information fields
         */
        $stExpectedAssignedUserId= '';
        
        foreach ($bidders['bid'] as $bId) {
        
            if(trim($stExpectedAssignedUserId) != '' && $stExpectedAssignedUserId != $bidders['assigned_user_id_'.$bId] ){
                $stParentOppAssingedId = 1;
                break;
            }else{
                $stParentOppAssingedId=$stExpectedAssignedUserId;
            }
            $stExpectedAssignedUserId = $bidders['assigned_user_id_'.$bId];                
        }
        //if no user is assigned then set current user
        if(trim($stParentOppAssingedId) == ''){
        	
        	if($current_user_role != 'lead_reviewer'){            	
        		$stParentOppAssingedId = $assigned_user_id;
        		//$oppr_parent->team_id = $this->userData->getPrivateTeam();
        	}else{
        		$stParentOppAssingedId = '1';
        		$obUser = BeanFactory::getBean('Users',$stParentOppAssingedId);
        		//$oppr_parent->team_id = $obUser->getPrivateTeam();
        	}           	            	
        	
        	//$oppr_parent->load_relationship('teams');
        	//$oppr_parent->teams->add(array($oppr_parent->team_id));
        	            	            	            	
        }
        if (!empty($bidders['opportunity_id'])) {
            $oppr_parent->id = $bidders['opportunity_id'];
            $oppr_parent->new_with_id = true;
            $oppr_parent->set_created_by = false;
            $obQuotes->created_by = $stParentOppAssingedId;
        }
        $oppr_parent->assigned_user_id = $stParentOppAssingedId;
        $oppr_parent->created_by = $stParentOppAssingedId;
        $oppr_parent->lead_received = $plead->received;
        $oppr_parent->lead_address = $plead->address;            
        $oppr_parent->lead_state = $plead->state;
        $oppr_parent->lead_structure = $plead->structure;
        $oppr_parent->lead_county = $plead->county_id;
        $oppr_parent->lead_type = $plead->type;
        $oppr_parent->lead_city = $plead->city;
        $oppr_parent->lead_owner = $plead->owner;
        $oppr_parent->lead_zip_code = $plead->zip_code;
        $oppr_parent->lead_project_status = $plead->project_status;
        $oppr_parent->lead_start_date = $plead->start_date;
        $oppr_parent->lead_end_date = $plead->end_date;            
        $oppr_parent->lead_contact_no = $plead->contact_no;            
        $oppr_parent->lead_valuation = $plead->valuation;
        $oppr_parent->lead_union_c = $plead->union_c;
        $oppr_parent->lead_non_union = $plead->non_union;
        $oppr_parent->lead_prevailing_wage = $plead->prevailing_wage;
        $oppr_parent->lead_square_footage = $plead->square_footage;
        $oppr_parent->lead_stories_below_grade = $plead->stories_below_grade;
        $oppr_parent->lead_number_of_buildings = $plead->number_of_buildings;
        $oppr_parent->lead_stories_above_grade = $plead->stories_above_grade;
        $oppr_parent->lead_scope_of_work = $plead->scope_of_work;
        $initialPulledUserEmail = '';
        //update initial pulled user id by "created by" of project opportunity if lead imported from BBHub
        if (!empty($plead->mi_lead_id) && !empty($stParentOppAssingedId)) {
        	$emailQuery = "SELECT ea.email_address FROM email_addr_bean_rel eab INNER JOIN email_addresses ea 
        	        ON ea.id=eab.email_address_id WHERE ea.deleted='0' AND eab.deleted='0' AND eab.bean_module='Users' 
        	        AND eab.bean_id='".$stParentOppAssingedId."'";
        	$emailResult = $db->query($emailQuery);
        	$emailData = $db->fetchByAssoc($emailResult);
        	$initialPulledUserEmail = $emailData['email_address'];
        	$oppr_parent->initial_pulled_user_email = strtolower($initialPulledUserEmail);
        }
        
        $oppr_parent->save();
        
        
        $parent_opportunity_id = $oppr_parent->id;
        
        //get all related documents of project lead and 
        //make a relationship of related documents to project opportunity
        //@modified bY Mohit Kumar Gupta
        //@date 26-06-2014
        $plead->load_relationship('documents_leads');
        $arRelatedDocuments = $plead->documents_leads->get();
        foreach ($arRelatedDocuments as $documentId) {
            $relateValues = array(
                    'opportunity_id'=>$oppr_parent->id,
                    'document_id' => $documentId
            );
            $oppr_parent->set_relationship('documents_opportunities', $relateValues);
        }
        
        $arRelatedAccounts = array();
        //Prepare data for Sub Opportunity
        
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
        
        foreach ($bidders['bid'] as $bId) {                
            
        	$assigned_user_id = $bidders['assigned_user_id_'.$bId];
            $account_id = $bidders['account_id_'.$bId];
            $contact_id = $bidders['contact_id_'.$bId];
            $lead_id = $bidders['lead_id_'.$bId];
            $lead = new Lead();
            $lead->disable_row_level_security = true;
            $lead->retrieve($lead_id);
            $lead_source = $lead->lead_source;
            $bidders_ids = $bidders['biddersIds_'.$bId];
            
                            
            //Process to Create Sub Opportunity
            $oppr = new Opportunity();
            $oppr->disable_row_level_security = true;
            $oppr->name = $name;
            $oppr->amount = $amount;               
            $oppr->sales_stage = $sales_stage;
            $oppr->account_id = $account_id;
            $oppr->contact_id = $contact_id;
            $oppr->project_lead_id = $lead_id;
            $oppr->assigned_user_id = $assigned_user_id;
            $oppr->leadclientdetail_id = $bId;                
            $oppr->date_closed = $bidders['earlier_date'];
            $oppr->bid_due_timezone = $bidders['earlier_bids_due_timezone'];
            $oppr->lead_source = $lead_source;
            $oppr->client_bid_status = 'Bidding';
            $oppr->parent_opportunity_id = $oppr_parent->id;
            
            //Modified by Mohit Kumar Gupta 02-12-2015
            //Update initial pulled user email address to client opportunities also, those are pulling from BBHub 
            if (!empty($initialPulledUserEmail)) {
            	$oppr->initial_pulled_user_email = $initialPulledUserEmail;
            }
            
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
            $oppr->opportunity_classification = $opportunityClassificationId;
            //update classification id to client opportunity end
            
            /*
             * bug resolve for non admin users. 
             * teams are now added through query
            $user = new User();
            $user->disable_row_level_security = true;
            $user->retrieve($assigned_user_id);
            $private_team = $user->getPrivateTeam();
            $oppr->team_id = $private_team;
            
            $team_set = new TeamSet();
            $team_set->disable_row_level_security = true;
            $team_set_id = $team_set->addTeams(array($private_team));
            
            $oppr->team_set_id = $team_set_id;*/
            
            $oppr->save(); 
            
            
            $account_sql = " SELECT name FROM accounts WHERE deleted = 0 AND id ='".$account_id."' ";
            $account_result = $db->query($account_sql);
            $account_row = $db->fetchByAssoc($account_result);
            
            $notification_list[$assigned_user_id][] = array(
            		'client_name' => $account_row['name'],
            );
            

            
            $arRelatedAccounts[] = $account_id;
            
            //Save Opportunity Id into Bidder
            $bidder = new oss_LeadClientDetail();
            $bidder->disable_row_level_security = true;
            $bidder->retrieve($bId);
            $bidder->opportunity_id = $oppr->id;
            $bidder->save();
            unset($bidder);
            
            //echo '<pre>'; print_r($lead);
            
            //Change Project Lead Status into Converted
            // modified by Basudeba, Date : 22/Oct/2012.
            if($plead->id != $lead->id){
            	$lead->status = 'Converted';
            	// Converted Date Added By : Ashutosh on 7 May 2013
            	$lead->converted_date = $timedate->to_db($timedate->now());
            	$lead->save();
            }
            
            //Process to update converted to opportunity flag in Lead Client Detail module
            $bidders_ids = explode(",", $bidders_ids);
            foreach($bidders_ids as $bidderId){                    
                $lcd = new oss_LeadClientDetail();
                $lcd->disable_row_level_security = true;
                $lcd->retrieve($bidderId);
                $lcd->converted_to_oppr = 1;
                $lcd->save();
            }
            
			//make clients visible
			
            $sql = "SELECT `mi_account_id`,visibility FROM `accounts` WHERE `id` = '".$account_id."' AND `deleted` = '0'";
            $query = $db->query($sql);
            $result = $db->fetchByAssoc($query);
            $mi_account_id = $result['mi_account_id'];                
            $visibility = $result['visibility'];                
            
            if(!empty($mi_account_id) && ($visibility == 0)){
                $pullObj = new PullBBH($mi_account_id);
                $pullObj->insertUpdateClients();
            }
            /**
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
            unset($oppr);
        }//end foreach loop of sub opportunity.
        //die;
        //save child opportunity account
        foreach($arRelatedAccounts as $stAccountId){
        	$relate_values = array('opportunities_accountsopportunities_ida'=>$oppr_parent->id,
        			'opportunities_accountsaccounts_idb' => $stAccountId);
        	$oppr_parent->set_relationship('opportunities_accounts_c', $relate_values);
        }
           
             
        //Change Project Lead Status into Converted
        $plead->status = 'Converted';
        // Converted Date Added By : Ashutosh on 7 May 2013
        $plead->converted_date = $timedate->to_db($timedate->now());
        $plead->save();
        
        // UPDATE PREVIOUS BID TO COUNT FOR PRIMARY LEAD
        updatePreviousBidToCount($plead->id);
        unset($oppr);
        
        
        if(count($bidders['bid']) > 0){
        	updateProjectOpportunityTeamSet($parent_opportunity_id);
        }
        
        //print_r($sales_stage); die;
        
        
        if($sales_stage == 'Won (closed)'){
        	
        	global $db;
        	
        	$avg_sql = " SELECT SUM(opportunities.amount)amount
					FROM opportunities
					WHERE opportunities.parent_opportunity_id='" . $parent_opportunity_id . "'
					AND opportunities.sales_stage = 'Won (closed)' 
        			AND opportunities.deleted = 0 ";
        	
        	$avg_query = $db->query ( $avg_sql );
        	$avg_result = $db->fetchByAssoc ( $avg_query );
        	
        	$update_parent_query = " UPDATE opportunities SET 
        		amount = '".$avg_result ['amount']."',
				amount_usdollar = '".$avg_result ['amount']."'
				WHERE id = '".$parent_opportunity_id."' AND deleted = 0 ";
        	
        	$db->query($update_parent_query);

        }
        
        
        //send customizied notification email
        if(count($notification_list) > 0)
        	$this->sendNotificationEmail($notification_list, $parent_opportunity_id); 
        
        
        //if view called from lead conversion ajax request then no redirection should be there
       	if (!empty($formData)) {
        	return true;
        } else if($current_user_role == 'lead_reviewer'){
            /**
             * Redirect page if user is lead reviewer page will be redirect on converted message else list view of opportunity.
             */
        	header('location:index.php?module=Opportunities&action=converted');
        	exit();
        } elseif(!isset($_REQUEST['bid']) && empty($_REQUEST['bid'])){
            /**
             * Modified by : Ashutosh
             * Date      : 03 June 2013
             * Purpose: to redirect to Project opportunity Detail view
             */
			header('location:index.php?module=Opportunities&action=DetailView&record='.$parent_opportunity_id);            	
        	exit();	
		} else{
            /**
             * Modified by : Ashutosh
             * Date      : 08 May 2013
             * Purpose: to redirect to opp summary view
             */
            header('location:index.php?module=Opportunities&action=DetailView&record='.$parent_opportunity_id.'&ClubbedView=1');
        	//header('location:index.php?module=Opportunities&action=index');
        	exit();	
        }
         /*   
        }else{
    		header ( 'location:index.php?module=Leads&action=convert_to_opportunity&record=' . $bidders ['primary_lead_id'] );
    		exit ();
    	}//end if $_REQUEST['bid']
    	*/ 
		
    }//end display function.
    
    
    function sendNotificationEmail($notification_list, $parent_opportunity_id)
    {
    	$admin = new Administration();
    	$admin->retrieveSettings();
    	$sendNotifications = false;
    
    	if ($admin->settings['notify_on'])
    	{
    		$GLOBALS['log']->info("Notifications: user assignment has changed, checking if user receives notifications");
    		$sendNotifications = true;
    	}
    	else
    	{
    		$GLOBALS['log']->info("Notifications: not sending e-mail, notify_on is set to OFF");
    	}
    
    
    	if($sendNotifications == true)
    	{
    		foreach ($notification_list as $notify_user_id => $client_opportunity)
    		{    			
    			ViewSave_opportunity::send_assignment_notifications($notify_user_id, $client_opportunity, $parent_opportunity_id, $admin);
    		}
    	}
    	
    }
    
    
    /**
     * Handles sending out email notifications when items are first assigned to users
     *
     * @param string $notify_user user to notify
     * @param string $client_opportunities for the client_opportunities to notify
     * @param string $parent_opportunity_id of the client opportunities
     * @param object $admin the admin user that sends out the notification
     */
    function send_assignment_notifications($notify_user_id, $client_opportunities, $parent_opportunity_id, $admin){
    
    	global $current_user;
    	
    	//current user id issue identified in BodScope integration and fixed
    	//Mohit Kumar Gupta 25-09-2015
        if (empty($current_user) && !empty($this->userData)) {
        	$current_user = $this->userData;
        }
    	$notify_user = new User();
    	$notify_user->retrieve($notify_user_id);
    	
    	if($notify_user->receive_notifications) {
    	
	    	$sendToEmail = $notify_user->emailAddress->getPrimaryAddress($notify_user);
	    	$sendEmail = TRUE;
	    	if(empty($sendToEmail)) {
	    		$GLOBALS['log']->warn("Notifications: no e-mail address set for user {$notify_user->user_name}, cancelling send");
	    		$sendEmail = FALSE;
	    	}
	    
	    	$notify_mail = ViewSave_opportunity::create_notification_email($notify_user, $client_opportunities, $parent_opportunity_id);
	    	$notify_mail->setMailerForSystem();
	    	
	    
	    	if(empty($admin->settings['notify_send_from_assigning_user'])) {
	    		$notify_mail->From = $admin->settings['notify_fromaddress'];
	    		$notify_mail->FromName = (empty($admin->settings['notify_fromname'])) ? "" : $admin->settings['notify_fromname'];
	    	} else {
	    		// Send notifications from the current user's e-mail (if set)
	    		$fromAddress = $current_user->emailAddress->getReplyToAddress($current_user);
	    		$fromAddress = !empty($fromAddress) ? $fromAddress : $admin->settings['notify_fromaddress'];
	    		$notify_mail->From = $fromAddress;
	    		//Use the users full name is available otherwise default to system name
	    		$from_name = !empty($admin->settings['notify_fromname']) ? $admin->settings['notify_fromname'] : "";
	    		$from_name = !empty($current_user->full_name) ? $current_user->full_name : $from_name;
	    		$notify_mail->FromName = $from_name;
	    	}
	    
	    	$oe = new OutboundEmail();
	    	$oe = $oe->getUserMailerSettings($current_user);
	    	//only send if smtp server is defined
	    	if($sendEmail){
	    		$smtpVerified = false;
	    
	    		//first check the user settings
	    		if(!empty($oe->mail_smtpserver)){
	    			$smtpVerified = true;
	    		}
	    
	    		//if still not verified, check against the system settings
	    		if (!$smtpVerified){
	    			$oe = $oe->getSystemMailerSettings();
	    			if(!empty($oe->mail_smtpserver)){
	    				$smtpVerified = true;
	    			}
	    		}
	    		//if smtp was not verified against user or system, then do not send out email
	    		if (!$smtpVerified){
	    			$GLOBALS['log']->fatal("Notifications: error sending e-mail, smtp server was not found ");
	    			//break out
	    			return;
	    		}
	    
	    		if(!$notify_mail->Send()) {
	    			$GLOBALS['log']->fatal("Notifications: error sending e-mail (method: {$notify_mail->Mailer}), (error: {$notify_mail->ErrorInfo})");
	    		}else{
	    			$GLOBALS['log']->info("Notifications: e-mail successfully sent");
	    		}
	    	}
    	}
    
    }
    
    /**
     * This function handles create the email notifications email.
     * @param string $notify_user the user to send the notification email to
     * @param string $client_opportunities for the client_opportunities to notify
     * @param string $parent_opportunity_id of the client opportunities
     */
    function create_notification_email($notify_user, $client_opportunities, $parent_opportunity_id) {
    	global $sugar_version;
    	global $sugar_config;
    	global $app_list_strings;
    	global $current_user;
    	global $locale;
    	global $beanList;
    	$OBCharset = $locale->getPrecedentPreference('default_email_charset');
    
    	//current user id issue identified in BodScope integration and fixed
    	//Mohit Kumar Gupta 25-09-2015
    	if (empty($current_user) && !empty($this->userData)) {
    	    $current_user = $this->userData;
    	}
    	
    	require_once("include/SugarPHPMailer.php");
    
    	$notify_address = $notify_user->emailAddress->getPrimaryAddress($notify_user);
    	$notify_name = $notify_user->full_name;
    	$GLOBALS['log']->debug("Notifications: user has e-mail defined");
    
    	$notify_mail = new SugarPHPMailer();
    	$notify_mail->AddAddress($notify_address,$locale->translateCharsetMIME(trim($notify_name), 'UTF-8', $OBCharset));
    
    	if(empty($_SESSION['authenticated_user_language'])) {
    		$current_language = $sugar_config['default_language'];
    	} else {
    		$current_language = $_SESSION['authenticated_user_language'];
    	}
    	$xtpl = new XTemplate(get_notify_template_file($current_language));

    	$opp_details = '';
    			
    	foreach (  $client_opportunities as $client_opportunity ){
    
    		$opp_details .= "Client Name: ".$client_opportunity['client_name']."
";
    	}

    	
    	require_once 'custom/modules/Opportunities/OpportunitySummary.php';
    	$project_opportunity = new OpportunitySummary();
    	$project_opportunity->disable_row_level_security = true;
    	$project_opportunity->retrieve($parent_opportunity_id);
    	
    	
    	$xtpl->assign("CLIENT_OPPORTUNITY_DETAILS", $opp_details);
    	
    	$xtpl->assign("OPPORTUNITY_NAME", $project_opportunity->name);
    	$xtpl->assign("OPPORTUNITY_AMOUNT", $project_opportunity->amount);
    	
    	require_once 'custom/include/OssTimeDate.php';
    	$oss_timedate = new OssTimeDate();
    	$bid_due_date_time = $oss_timedate->convertDBDateForDisplay($project_opportunity->fetched_row['date_closed'], $project_opportunity->bid_due_timezone,false);
    	
    	$xtpl->assign("OPPORTUNITY_CLOSEDATE", $bid_due_date_time);
    	$xtpl->assign("OPPORTUNITY_STAGE", isset($project_opportunity->sales_stage)?$app_list_strings['sales_stage_dom'][$project_opportunity->sales_stage]:"" );
    	$xtpl->assign("OPPORTUNITY_DESCRIPTION", $project_opportunity->description);
    	
    	$template_name = $project_opportunity->module_dir;

    	
    	$xtpl->assign("ASSIGNED_USER", $notify_name);
    	$xtpl->assign("ASSIGNER", $current_user->name);
    	$xtpl->assign("COUNT", count($client_opportunities));
    	

    	if(count($client_opportunities) > 1){
    		$xtpl->assign("ABBR", 'Opportunities');
    	}else{
    		$xtpl->assign("ABBR", 'Opportunity');
    	}
    	
    	
    	$port = '';
    	
    	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
    		$port = $_SERVER['SERVER_PORT'];
    	}
    	
    	if (!isset($_SERVER['HTTP_HOST'])) {
    		$_SERVER['HTTP_HOST'] = '';
    	}
    	
    	$httpHost = $_SERVER['HTTP_HOST'];
    	
    	if($colon = strpos($httpHost, ':')) {
    		$httpHost    = substr($httpHost, 0, $colon);
    	}
    	
    	$parsedSiteUrl = parse_url($sugar_config['site_url']);
    	$host = $parsedSiteUrl['host'];
    	if(!isset($parsedSiteUrl['port'])) {
    		$parsedSiteUrl['port'] = 80;
    	}
    	
    	$port		= ($parsedSiteUrl['port'] != 80) ? ":".$parsedSiteUrl['port'] : '';
    	$path		= !empty($parsedSiteUrl['path']) ? $parsedSiteUrl['path'] : "";
    	$cleanUrl	= "{$parsedSiteUrl['scheme']}://{$host}{$port}{$path}";
    	 
    	$xtpl->assign("URL", $cleanUrl."/index.php?module={$project_opportunity->module_dir}&action=DetailView&record={$project_opportunity->id}&ClubbedView=1");
    
    	$xtpl->assign("SUGAR", "Sugar v{$sugar_version}");
    	$xtpl->parse("Multiple_".$template_name);
    	$xtpl->parse("Multiple_".$template_name ."_Subject");
    
    	$notify_mail->Body = from_html(trim($xtpl->text("Multiple_".$template_name)));
    	$notify_mail->Subject = from_html($xtpl->text("Multiple_".$template_name . "_Subject"));
    
    	// cn: bug 8568 encode notify email in User's outbound email encoding
    	$notify_mail->prepForOutbound();
    
    	return $notify_mail;
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
