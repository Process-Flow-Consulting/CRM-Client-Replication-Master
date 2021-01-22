<?php
require_once "custom/include/common_functions.php";
require_once 'modules/oss_Zone/oss_Zone.php';
class OpportunityHooks {
    
    protected static $fetchedRow = array();
    /**
     * Called as before_save logic hook to grab the fetched_row values
     * @author Mohit Kumar Gupta
     * @date 27-02-2014
     */
    public function saveFetchedRow($bean, $event, $arguments)
    {
        if ( !empty($bean->id) ) {
            self::$fetchedRow[$bean->id] = $bean->fetched_row;
            //the dup_account_name holds previous account Name
            self::$fetchedRow[$bean->id]['pre_account_name'] = $bean->dup_account_name;
        }
    }
	
	/**
	 * Calculate the Average amount from the client opportuinty and
	 * save it to parent Opoortunity
	 *
	 * @param $bean object        	
	 *
	 */
	function getAverageAmount(&$bean) {
	    global $db;
	    
		//if ('Save' == $_REQUEST ['action'] && $_REQUEST ['module'] == 'Opportunities' || $_REQUEST ['opportunity_id']
			//		|| ( $_REQUEST['emailUIAction']== 'saveQuickCreate' ) ) {
			
			// project opportunity id
			$parentId = $bean->parent_opportunity_id;
			
			
			/**
			 * After save logic if user elects to change the opportunity
			 * amount for client opportunities
			 * @Added BY : Ashutosh
			 * @date : 11 Sept 2013
			 */
			if(trim($parentId) == ''  && isset( $_REQUEST['copy_amount']) && trim($_REQUEST['copy_amount']) == 'copy'){
			    	
			    //copy project opportunity amount to all client opportunity
			    $stProjOppAmountSQL  = "UPDATE opportunities SET amount_usdollar='".$bean->amount."' , amount='".$bean->amount."'
					WHERE parent_opportunity_id  = '" . $bean->id . "'" ;
			    $db->query($stProjOppAmountSQL);		
			}
			
			if (! empty ( $parentId )) {
				
				
				
				// check if there is any client opportunity won closed
				$check_sql = " SELECT count(*) c FROM opportunities 
						WHERE parent_opportunity_id = '" . $parentId . "' 
						AND sales_stage = 'Won (closed)' AND deleted = 0 ";
				$check_query = $db->query ( $check_sql );
				$check_result = $db->fetchByAssoc ( $check_query );
				
				// get average amount of cleint opportunity amount.
				$avg_sql = "SELECT AVG(opportunities.amount) amount 
						FROM opportunities 
						WHERE opportunities.parent_opportunity_id = '" . $parentId . "'  ";
				
				// get sum of won closed client opportunity amount.
				if (($bean->sales_stage == 'Won (closed)') || ($check_result ['c'] > 0)) {
					$avg_sql = " SELECT SUM(opportunities.amount)amount 
						FROM opportunities 
						WHERE opportunities.parent_opportunity_id = '" . $parentId . "' 
						AND opportunities.sales_stage = 'Won (closed)' ";
				}
				
				$avg_sql .= " AND opportunities.deleted = 0 ";
				
				$avg_query = $db->query ( $avg_sql );
				$avg_result = $db->fetchByAssoc ( $avg_query );
				$new_avg_amount = number_format($avg_result ['amount'],2,'.','');
				// get count of total client opportunity amount
				$client_count_sql = " SELECT COUNT(opportunities.amount) tot_opp  
						FROM opportunities 
						WHERE opportunities.parent_opportunity_id='" . $parentId . "'  
						AND opportunities.deleted = 0";
				$client_count_query = $db->query ( $client_count_sql );
				$client_count_result = $db->fetchByAssoc ( $client_count_query );
				
				$update_parent_query = "UPDATE opportunities SET amount = '" . $new_avg_amount . "', date_modified = NOW(),
						 amount_usdollar = '" . $new_avg_amount . "',
						 sub_opp_count = '" . $client_count_result ['tot_opp'] . "' WHERE 
						 		id = '" . $parentId . "' AND deleted = 0 ";
				
				$old_amount = $this->getOldPOAmount ( $parentId );				
				$this->createOppAudit ( $parentId, $old_amount, $new_avg_amount, 'amount', 'number' );
				$db->query ( $update_parent_query );
				
				updateProjectOpprBidDueDate ( $parentId );
			}
		//}
	}
	
	/**
	 * Update sales stage based on different conditions
	 *
	 * @param $focus object        	
	 */
	function UpdateSalesStage(&$focus) {
		
		global $db, $current_user, $sugar_config;
		
		$lost_all = 1;
		
		//opportunity details
		$parentId = $focus->parent_opportunity_id;
		$salesStage = $focus->sales_stage;
		$clientBidStatus = $focus->client_bid_status;
		$opId = $focus->id;
		$clientClassification = $focus->opportunity_classification;
		
		//if user created a new record or going to modify existing record with the change of slaes stage
		$salesStageStatusChanges = $this->checkExistingFieldValue(self::$fetchedRow[$opId], 'sales_stage',$salesStage);		
			
		//1. A. If the same user is assigned the Project and all related Client Opportunities, then if the user changes the Project Sales Stage to (Estimating or Follow Up) the related Client sales stages are set to match
		if( ($salesStage == 'Estimating' || $salesStage == 'Follow Up') && empty($parentId) && $salesStageStatusChanges){		    
	        $checkUserGroup = $this->checkAssignedUserGroup($opId);
	        if(count($checkUserGroup) < 2){
	            $opportunity_query = " SELECT id,sales_stage FROM opportunities WHERE parent_opportunity_id = '" . $opId . "' AND deleted = 0 AND sales_stage != '".$salesStage."' ";
	            $opportunity_result = $db->query ( $opportunity_query );
	            while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {
	                $old_sales_stage = $opportunity_row ['sales_stage'];
	                $update_client_query = "UPDATE opportunities SET sales_stage = '".$salesStage."', date_modified = Now() WHERE id = '" . $opportunity_row ['id'] . "' AND deleted = 0 ";
	                $db->query ( $update_client_query );
	                $this->createOppAudit($opportunity_row ['id'], $old_sales_stage, $salesStage, 'sales_stage', 'enum');
	            }
	        }
		    							
		}
		
		//1. B. Regardless of If the same user is assigned the Project and all related Client Opportunities or not, then if a user changes a Client Sales Stage to (Won (closed) ) , the related Project sales stage is set to match
		if ($salesStage == 'Won (closed)' &&  !empty( $parentId ) && $salesStageStatusChanges) {
		    
			$old_sales_stage = $this->getOldPOSalesStage ( $parentId );
			$update_parent_query = "UPDATE opportunities SET sales_stage = 'Won (closed)', date_modified = Now() WHERE id = '" . $parentId . "' AND deleted = 0";
			$db->query ( $update_parent_query );
			$this->createOppAudit ( $parentId, $old_sales_stage, 'Won (closed)', 'sales_stage', 'enum' );
		}
		
		//2. A. If the Client Opportunity Sales Stage is set to Won (closed) then the Client Bid Status related to that client should be set to awarded.
		if ($salesStage == 'Won (closed)' && !empty ( $parentId ) && $salesStageStatusChanges) {
			$old_client_bid_status = $focus->client_bid_status;
			$update_client_query = "UPDATE opportunities SET client_bid_status = 'Awarded', date_modified = Now()  WHERE id = '" .$opId. "' AND deleted = 0 ";
			$db->query ( $update_client_query );
			$this->createOppAudit($opId, $old_client_bid_status, 'Awarded', 'client_bid_status', 'enum');
		}
			
		//2. B. If the Client Opportunity Sales Stage is set to Won (closed) then the Sales Stage for all other Clients assigned to the user should be changed to Lost (closed)
		//Do not change Client Bid Status and Sales Stage of Won (closed)
		if ($salesStage == 'Won (closed)' && !empty ( $parentId ) && $salesStageStatusChanges) {
			//Modified by Mohit Kumar Gupta
			//@date 11-Dec-2013
			//if user changes the sales stage for a Client Opportunity to Won (closed) then update other client opportunity(related to the Project Opportunity)
			//bid status to lost and sales stage to Lost (closed) if other client opportunity having same classification.			
			$opportunity_query = " SELECT id,sales_stage, client_bid_status FROM opportunities  WHERE parent_opportunity_id = '" . $parentId . "'  AND sales_stage != 'Lost (closed)'  AND sales_stage != 'Won (closed)' AND id != '" . $opId . "' AND assigned_user_id = '".$focus->assigned_user_id."' AND deleted = 0";
    		if (!empty($clientClassification)) {
    		   //handling for same classification
    	       $opportunity_query .= " AND opportunity_classification = '".$clientClassification."'";
    		} else {
    		   //handling for blank or null classification
    		   $opportunity_query .= " AND (opportunity_classification = '".$clientClassification."' OR opportunity_classification IS NULL)";
    		}
			
			$opportunity_result = $db->query ( $opportunity_query );
			while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {
				$update_query = " UPDATE opportunities SET sales_stage = 'Lost (closed)', date_modified = Now() ";
				//Modified by Mohit Kumar Gupta
				//@date 26-nov-2013
				//If user changes the sales stage for a client opportunity to "Won (Closed)" then do not change the Client Bid Status for other client opportunity related to that project opportunity if it is set as "Awarded" or "Withdrawn".
				if($opportunity_row['client_bid_status'] != 'Withdrawn' &&  $opportunity_row['client_bid_status'] != 'Awarded'){
					$update_query .= ",client_bid_status = 'Lost' ";
				}
				$update_query .= " WHERE id = '" . $opportunity_row['id'] . "' AND deleted = 0 ";	
				$db->query ( $update_query );
				$this->createOppAudit($opportunity_row['id'], $opportunity_row ['sales_stage'], 'Lost (closed)', 'sales_stage', 'enum');
				//Modified by Mohit Kumar Gupta
				//@date 26-nov-2013
				//If user changes the sales stage for a client opportunity to "Won (Closed)" then do not change the Client Bid Status for other client opportunity related to that project opportunity if it is set as "Awarded" or "Withdrawn".
				if($opportunity_row['client_bid_status'] != 'Withdrawn' && $opportunity_row['client_bid_status'] != 'Awarded'){
					$this->createOppAudit($opportunity_row['id'], $opportunity_row ['client_bid_status'], 'Lost', 'client_bid_status', 'enum');
				}
			}

			
			$opportunity_non_assigned_query = " SELECT opportunities.id, opportunities.assigned_user_id, CONCAT_WS(' ',users.first_name, users.last_name) assigned_user_name, ea.email_address FROM opportunities LEFT JOIN users ON opportunities.assigned_user_id = users.id AND users.deleted = 0 LEFT JOIN email_addr_bean_rel eabr ON  eabr.bean_id=opportunities.assigned_user_id AND eabr.bean_module='Users' AND eabr.deleted = 0 LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0 WHERE opportunities.parent_opportunity_id = '" . $parentId . "'  AND opportunities.sales_stage != 'Lost (closed)' AND opportunities.client_bid_status != 'Lost' AND opportunities.id != '" . $opId . "' AND opportunities.assigned_user_id != '".$focus->assigned_user_id."' AND opportunities.deleted = 0 GROUP BY opportunities.assigned_user_id";
			$opportunity_non_assigned_result = $db->query ( $opportunity_non_assigned_query );
			while ( $opportunity_non_assigned_row = $db->fetchByAssoc ( $opportunity_non_assigned_result ) ) {
				
				require_once('custom/modules/EmailTemplates/CustomEmailTemplate.php');
				
				// Getting the Email Template
				$email = new CustomEmailTemplate();
				$email->disable_row_level_security = true;
				$name = 'Opportunity Sales Stage Change Notification';
				$var = $email->retrieve_by_string_fields ( array (
						'name' => $name
				) );
				$a = $var->body_html;
				
				// SugarMail Defaults
				require_once 'include/SugarPHPMailer.php';
				$emailobj = new Email ();
				$defaults = $emailobj->getSystemDefaultEmail ();
				$mail = new SugarPHPMailer ();
				$mail->setMailerForSystem ();
				$mail->From = $defaults ['email'];
				$mail->FromName = $defaults ['name'];
				$mail->Subject = $var->subject;
				
				
				$a = str_replace("\$contact_user_user_name", $opportunity_non_assigned_row['assigned_user_name'] , $a);
				$a = str_replace("\$project_opportunity_name", $focus->opportunity_name , $a);
				$a = str_replace("\$current_user_name", $current_user->name , $a);
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
		
				
				$a = str_replace("\$project_opportunity_link", $cleanUrl."/index.php?module=Opportunities&action=DetailView&record=$parentId&ClubbedView=1" , $a);
				
				//echo $a; die;
				
				//The String is Converted to HTML and sent to the Recipients
				$mail->IsHTML ( true );
				$mail->Body = from_html ( $a );
				$mail->prepForOutbound ();
				
				$add_str=$opportunity_non_assigned_row['email_address'];
				$address=explode(',', $add_str);
				foreach($address as $eml_add){
					$mail->AddAddress(trim($eml_add));
				}
				
				if(!empty($add_str))
				$mail->Send ();
			}
		}
		
		//2. C. If the Client Opportunity Sales Stage is set to Lost (closed) then the Client Bid Status for the related user should be set to Lost (unless the existing client bid status value is withdrawn)
		if ($salesStage == 'Lost (closed)' &&  !empty ( $parentId ) && $salesStageStatusChanges) {
			if( $focus->client_bid_status != 'Withdrawn'){
				$update_client_query = "UPDATE opportunities SET client_bid_status = 'Lost', date_modified = Now() WHERE id = '" . $opId . "' AND deleted = 0 ";
				$db->query ( $update_client_query );
				$this->createOppAudit($opId, $focus->client_bid_status, 'Lost', 'client_bid_status', 'enum');
			}
		}
		
		
		//If saved opportunity is Client Opportunity and New Sales Stage is Lost (closed) and all other Client Opportunities for same Project Opportunity is Lost (closed) then change the Project Opportunity Sales Stage to Lost (closed).
		if ($salesStage == 'Lost (closed)' && ! empty ( $parentId )  && $salesStageStatusChanges) {
			$opportunity_query = " SELECT id,sales_stage FROM opportunities WHERE parent_opportunity_id = '" . $parentId . "'	AND sales_stage != 'Lost (closed)' AND id != '" . $opId . "' AND deleted = 0 ";
			$opportunity_result = $db->query ( $opportunity_query );	
			while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {
				if ($opportunity_row ['sales_stage'] != 'Lost (closed)') {
					$lost_all = 0;
				}
			}	
			if ($lost_all == 1) {
				$old_sales_stage = $this->getOldPOSalesStage ( $parentId );
				$update_parent_query = "UPDATE opportunities SET sales_stage = 'Lost (closed)', date_modified = Now() WHERE id = '" . $parentId . "' AND deleted = 0 ";
				$db->query ( $update_parent_query );
				$this->createOppAudit ( $parentId, $old_sales_stage, 'Lost (closed)', 'sales_stage', 'enum' );
			}
		}
		
		//If saved opportunity is Project Opportunity and New Sales Stage is Lost (closed)  then change all the Client Opportunity Sales Stage to Lost (closed).
		if ($salesStage == 'Lost (closed)' && empty ( $parentId ) && $salesStageStatusChanges) {
			$opportunity_query = " SELECT id,sales_stage FROM opportunities WHERE parent_opportunity_id = '" . $opId . "' AND sales_stage != 'Lost (closed)' AND deleted = 0 ";
			$opportunity_result = $db->query ( $opportunity_query );	
			while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {
				$old_sales_stage = $opportunity_row ['sales_stage'];
				$update_client_query = "UPDATE opportunities SET sales_stage = 'Lost (closed)', date_modified = Now() WHERE id = '" . $opportunity_row ['id'] . "' AND deleted = 0 ";
				$db->query ( $update_client_query );
				$this->createOppAudit($opportunity_row ['id'], $old_sales_stage, 'Lost (closed)', 'sales_stage', 'enum');
		
			}
		}
		
		//If saved opportunity is Project Opportunity and New Sales Stage is Won (closed) and it has only one Client Opportunity then change the Client Opportunity Sales Stage to Won (closed).
		if ($salesStage == 'Won (closed)' && empty ( $parentId ) && $salesStageStatusChanges) {
			$opportunity_query = " SELECT id,sales_stage FROM opportunities WHERE parent_opportunity_id = '" . $opId . "'	AND sales_stage != 'Won (closed)' AND deleted = 0 ";
			$opportunity_result = $db->query ( $opportunity_query );
			$opportunity_count = $db->getRowCount ( $opportunity_result );
			if ($opportunity_count < 2) {
				while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {
					$old_sales_stage = $opportunity_row ['sales_stage'];
					$update_client_query = "UPDATE opportunities SET sales_stage = 'Won (closed)', date_modified = Now()
						 WHERE id = '" . $opportunity_row ['id'] . "' AND deleted = 0 ";
					$db->query ( $update_client_query );
					$this->createOppAudit($opportunity_row ['id'], $old_sales_stage, 'Won (closed)', 'sales_stage', 'enum');
				}
			}
		}
	}
	
	/**
	 * format account proview url
	 *
	 * @param $focus object        	
	 * @return void
	 */
	function setAccountProviewLink(&$focus) {
		if ($_REQUEST ['action'] == 'EditView') {
			return;
		}
		
		if (! empty ( $focus->account_id )) {
			$account = new Account ();
			$account->retrieve ( $focus->account_id );
			$account->disable_row_level_security = true;
			/* $focus->account_proview_url = proview_url ( array (
					'url' => $account->proview_url 
			) ); */
		}
	}
	
	/**
	 * Update Project Opportunity Team to Combined of all the Client Opportunity
	 * Teams
	 *
	 * @param
	 *        	focus object
	 * @return void
	 */
	function UpdateTeams(&$focus) {
		
		global $db;
		
		// project opportunity
		$parentId = $focus->parent_opportunity_id;
		$team_set_ids = array ();
		$team_ids = array ();
		
		if (isset ( $parentId ) && ! empty ( $parentId )) {
			
			//if all client opportunity assigned to same user, 
			//project opportunity will be assigned to the same user or assigned to admin
			$assigned_user_group =  $this->checkAssignedUserGroup($parentId);
			$old_assigned_user = $this->getOldAssignedUser($parentId);
			
			if( (count($assigned_user_group) < 2) && !empty($assigned_user_group[0]) ){
				$db->query(" UPDATE opportunities SET assigned_user_id ='".$assigned_user_group[0]."', date_modified = Now() WHERE id ='".$parentId."' AND deleted = 0 ");
				
				if($old_assigned_user != $assigned_user_group[0]){
					$this->createOppAudit($parentId, $old_assigned_user, $assigned_user_group[0], 'assigned_user_id', 'relate');
				}
			}else{
				
				$db->query(" UPDATE opportunities SET assigned_user_id ='1', date_modified = Now() WHERE id ='".$parentId."' AND deleted = 0 ");
				if($old_assigned_user != '1'){
					$this->createOppAudit($parentId, $old_assigned_user, '1', 'assigned_user_id', 'relate');
				}
			}
			
			updateProjectOpportunityTeamSet ( $parentId );
		}
	}
	
	function checkAssignedUserGroup($parent_opp_id){
		global $db;
		$check_assigned_sql = " SELECT assigned_user_id FROM opportunities WHERE parent_opportunity_id ='".$parent_opp_id."' AND deleted= 0 GROUP BY assigned_user_id ";
		$check_assigned_query = $db->query($check_assigned_sql);
		//$check_assigned_count = $db->getRowCount($check_assigned_query);		
		$assign_users = array();
		while($check_assigned_result = $db->fetchByAssoc($check_assigned_query)){
			$assign_users[] = $check_assigned_result['assigned_user_id'];
		}
		return $assign_users;
	}
	
	function getOldAssignedUser($parent_opp_id){
		global $db;
		$sql = "SELECT assigned_user_id FROM opportunities WHERE id='" . $parent_opp_id . "' AND deleted = 0";
		$query = $db->query ( $sql );
		$result = $db->fetchByAssoc ( $query );
		return $result ['assigned_user_id'];
	}
	
	function getOldPOAmount($parent_opp_id) {
		global $db;
		$sql = "SELECT amount FROM opportunities WHERE id='" . $parent_opp_id . "' AND deleted = 0";
		$query = $db->query ( $sql );
		$result = $db->fetchByAssoc ( $query );
		return number_format($result ['amount'],2,'.','');
	}
	
	function getOldPOSalesStage($parent_opp_id) {
		global $db;
		$sql = "SELECT sales_stage FROM opportunities WHERE id='" . $parent_opp_id . "' AND deleted = 0";
		$query = $db->query ( $sql );
		$result = $db->fetchByAssoc ( $query );
		return $result ['sales_stage'];
	}
	
	function createOppAudit($bean_id, $old_value, $new_value, $field_name, $field_type) {
		global $db, $current_user;
		if ($old_value != $new_value) {
			$insert_sql = "INSERT INTO opportunities_audit(`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) 
				VALUES(UUID(),'" . $bean_id . "',UTC_TIMESTAMP(),'" . $current_user->id . "','" . $field_name . "','" . $field_type . "','" . $old_value . "','" . $new_value . "') ";
			$db->query ( $insert_sql );
		}
	}
	
	/**
	 * Update Bid Status on BB Hub if BB status is interested or withdrawn
	 *
	 * @param
	 *        	focus object
	 * @return void
	 */
	function UpdateBidStatusOnHub(&$focus) {
		global $db;
		
		$parentId = $focus->parent_opportunity_id;
		$bid_status = $focus->my_project_status;
		$project_lead_id = $focus->project_lead_id;
		
		// if parent_opportunity id is empty i.e. a project opportunity and not
		// empty
		// project lead id and not empty bid status
		if (empty ( $parentId ) && ! empty ( $project_lead_id ) && ! empty ( $bid_status )) {
			
			// if status is withdrawn check if there is any other project
			// opportunity
			// related to the project lead who has Interested my project status
			if (($bid_status == 'Withdrawn')) {
				
				$sql = " SELECT count(*)c FROM opportunities WHERE 
						project_lead_id = '" . $project_lead_id . "' AND deleted = 0 
						AND ( my_project_status = 'Interested' OR 
								 my_project_status = 'Bidding')
						AND parent_opportunity_id IS NULL ";
				$result = $db->query ( $sql );
				$row = $db->fetchByAssoc ( $result );
				
				if ($row ['c'] > 0) {
					$bid_status = 'Interested';
				}
			} else {
				// any other status to be treated as Interested
				$bid_status = 'Interested';
			}
			
			$GLOBALS ['log']->info ( '----->Bid Status to Send in Hub: ' . $bid_status );
			
			// compare to the last sent bid status
			$sql = "SELECT mi_lead_id, pl_bid_status FROM leads WHERE id = '" . $project_lead_id . "' AND deleted = 0 ";
			$result = $db->query ( $sql );
			$row = $db->fetchByAssoc ( $result );
			
			// if the last sent bid status is different call for the API
			if (($row ['pl_bid_status'] != $bid_status) && ! empty ( $row ['mi_lead_id'] )) {
				
				// current instance account no
				$account_no = getCurrentInstanceAccountNo ();
				
				// API URL for Sending Project Bid Status
				$api_url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blcrmbidtstat_upd.php?";
				$api_url .= "Proj_no=" . $row ['mi_lead_id'] . "&";
				$api_url .= "Bid_status=" . strtolower ( $bid_status ) . "&";
				$api_url .= "Sugarcrm_account=" . $account_no . "&";
				// $api_url .="CRC=".md5(
				// $row['mi_lead_id']."_".$account_no."sugarcrm2012")."&";
				
				$GLOBALS ['log']->fatal ( '----->Project Bid Status URL: ' . $api_url );
				
				$output = getRemoteData ( $api_url );
				
				$GLOBALS ['log']->fatal ( '----->Project Bid Status Feedback: ' . $output );
				
				$output = json_decode ( $output );
				
				if ($output->response->status == 'success') {
					$sql = "UPDATE leads SET pl_bid_status ='" . $bid_status . "' WHERE id = '" . $project_lead_id . "' ";
					$db->query ( $sql );
				} else {
					$GLOBALS ['log']->fatal ( '----->Error Sending Bid Status to Hub: ' . $output->response->ErrorMsg );
				}
				// echo '<pre>'; print_r($output); echo '</pre>'; die;
			} else {
				$GLOBALS ['log']->fatal ( '----->Not Sending Bid Status to Hub: Already Same Status' );
			}
		}
		
		return true;
	}
	
	
	/**
	 * update other cilnet opprtunities client_bid_staus if awarded
	 * @param client opportyunity $focus
	 */
	function updateClientBidStatus(&$focus){
		global $db;
		$parentId = $focus->parent_opportunity_id;
		$client_bid_status = $focus->client_bid_status;
		$clientSalesStage = $focus->sales_stage;
		$clientClassification = $focus->opportunity_classification;
		
		//if user created a new record or going to modify existing record with the change of client bid status
		$clientBidStatusChanges = $this->checkExistingFieldValue(self::$fetchedRow[$focus->id], 'client_bid_status',$client_bid_status);
		
		//Modified by Mohit Kumar Gupta
		//@date 11-Dec-2013
		//if user changes the Client Bid Status for a Client Opportunity to awarded then update other client opportunity(related to the Project Opportunity) 
		//bid status to lost if other client opportunity having same classification.
		if( !empty($parentId) && ($client_bid_status == 'Awarded' ) && $clientBidStatusChanges){
			//Modified by Mohit Kumar Gupta
			//@date 04-Dec-2013
			//if user changes the Client Bid Status for a Client Opportunity do not change the Client Bid Status 
			//if it is set to "Withdrawn" or "Awarded" for the other Client Opportunities related to the Project Opportunity.
			$opportunity_query = " SELECT client_bid_status, id FROM opportunities
					WHERE parent_opportunity_id = '" . $parentId . "'
					AND client_bid_status NOT IN('Withdrawn','Awarded')
					AND id != '".$focus->id."'					
					AND deleted = 0 ";
			
			if (!empty($clientClassification)) {
			    //handling for same classification
			    $opportunity_query .= " AND opportunity_classification = '".$clientClassification."'";
			} else {
			    //handling for blank or null classification
			    $opportunity_query .= " AND (opportunity_classification = '".$clientClassification."' OR opportunity_classification IS NULL)";
			}
			
			$opportunity_result = $db->query ( $opportunity_query );	
			while ( $opportunity_row = $db->fetchByAssoc ( $opportunity_result ) ) {				
				$old_client_bid_status = $opportunity_row ['client_bid_status'];				
				$update_client_query = "UPDATE opportunities SET client_bid_status = 'Lost', date_modified = Now()
					 WHERE id = '" . $opportunity_row ['id'] . "' AND deleted = 0 ";
				$db->query ( $update_client_query );
				$this->createOppAudit($opportunity_row ['id'], $old_client_bid_status, 'Lost', 'client_bid_status', 'enum');
			}
		}
		
	}
	
	/**
	 * save check box correct value
	 * @param unknown $focus
	 */
	function saveCheckBox(&$focus){
		
		if ('Save' == $_REQUEST ['action']) {	
			$focus->lead_union_c = isset ( $_REQUEST ['lead_union_c'] ) ? $_REQUEST ['lead_union_c'] : '0';
			$focus->lead_non_union = isset ( $_REQUEST ['lead_non_union'] ) ? $_REQUEST ['lead_non_union'] : '0';
			$focus->lead_prevailing_wage = isset ( $_REQUEST ['lead_prevailing_wage'] ) ? $_REQUEST ['lead_prevailing_wage'] : '0';
		}
		
	}
	
	
	/**
	 * create a related project lead form the submitted data if there is no project lead attached
	 * @param opportunity object
	 */
	function createUpdateRelatedProjectLead(&$focus){
		
		global $db, $timedate;
		
		if( empty($focus->parent_opportunity_id) ){
			
			//bux fix for lead bids due and check boxes
			if(isset($_REQUEST ['action'])){
				$action =  $_REQUEST ['action'];
				unset($_REQUEST ['action']);
			}
				
			$lead = new Lead();
			
			if( !empty($focus->project_lead_id ) ){
				
				$check_lead_source_sql = " SELECT dodge_id, reed_id, mi_lead_id FROM leads WHERE id ='".$focus->project_lead_id."' AND deleted = 0";
				$lead_source_query = $db->query($check_lead_source_sql);
				$lead_source_result = $db->fetchByAssoc($lead_source_query);
					
				if( empty($lead_source_result['dodge_id']) && empty( $lead_source_result['reed_id'] ) && empty($lead_source_result['mi_lead_id']) ){
					$lead->id = $focus->project_lead_id;
				}else{	
					return '';
				}
				
			}	
			
			if(isset($focus->name) && !empty($focus->name) )
			$lead->last_name = $focus->name;
			
			if(isset($focus->name) && !empty($focus->name) )
			$lead->project_title = $focus->name;
			
			if(isset($focus->lead_received) && !empty($focus->lead_received) )
			$lead->received = $focus->lead_received;
			
			if(isset($focus->lead_address) && !empty($focus->lead_address) )
			$lead->address = $focus->lead_address;
			
			$lead->status = 'Converted';
			
			if(isset($focus->lead_state) && !empty($focus->lead_state) )
			$lead->state = $focus->lead_state;
			
			if(isset($focus->lead_structure) && !empty($focus->lead_structure) )
			$lead->structure = $focus->lead_structure;
			
			if(isset($focus->lead_county) && !empty($focus->lead_county) )
			$lead->county_id = $focus->lead_county;
			
			if(isset($focus->lead_type) && !empty($focus->lead_type) )
			$lead->type = $focus->lead_type;
			
			if(isset($focus->lead_city) && !empty($focus->lead_city) )
			$lead->city = $focus->lead_city;
			
			if(isset($focus->lead_owner) && !empty($focus->lead_owner) )
			$lead->owner = $focus->lead_owner;
			
			if(isset($focus->lead_zip_code) && !empty($focus->lead_zip_code) )
			$lead->zip_code = $focus->lead_zip_code;
			
			if(isset($focus->lead_project_status) && !empty($focus->lead_project_status) )
			$lead->project_status = $focus->lead_project_status;
			
			if(isset($focus->lead_start_date) && !empty($focus->lead_start_date) )
			$lead->start_date = $focus->lead_start_date;
			
			if(isset($focus->lead_end_date) && !empty($focus->lead_end_date) )
			$lead->end_date = $focus->lead_end_date;
			
			if(isset($focus->lead_source) && !empty($focus->lead_source) )
			$lead->lead_source = $focus->lead_source;
			
			if(isset($focus->date_closed) && !empty($focus->date_closed) )
			$lead->bids_due = $timedate->to_display_date_time($focus->date_closed);
				
			if(isset($focus->lead_contact_no) && !empty($focus->lead_contact_no) )
			$lead->contact_no = $focus->lead_contact_no;
			
			if(isset($focus->bid_due_timezone) && !empty($focus->bid_due_timezone) )
			$lead->bid_due_timezone = $focus->bid_due_timezone;
			
			if(isset($focus->lead_valuation) && !empty($focus->lead_valuation) )
			$lead->valuation = $focus->lead_valuation;
			
			if(isset($focus->lead_union_c) && !empty($focus->lead_union_c) )
			$lead->union_c = $focus->lead_union_c;
			
			if(isset($focus->lead_non_union) && !empty($focus->lead_non_union) )
			$lead->non_union = $focus->lead_non_union;
			
			if(isset($focus->lead_prevailing_wage) && !empty($focus->lead_prevailing_wage) )
			$lead->prevailing_wage = $focus->lead_prevailing_wage;
			
			if(isset($focus->lead_square_footage) && !empty($focus->lead_square_footage) )
			$lead->square_footage = $focus->lead_square_footage;
			
			if(isset($focus->lead_stories_below_grade) && !empty($focus->lead_stories_below_grade) )
			$lead->stories_below_grade = $focus->lead_stories_below_grade;
			
			if(isset($focus->lead_number_of_buildings) && !empty($focus->lead_number_of_buildings) )
			$lead->number_of_buildings = $focus->lead_number_of_buildings;
			
			if(isset($focus->lead_stories_above_grade) && !empty($focus->lead_stories_above_grade) )
			$lead->stories_above_grade = $focus->lead_stories_above_grade;
			
			if(isset($focus->lead_scope_of_work) && !empty($focus->lead_scope_of_work) )
			$lead->scope_of_work = $focus->lead_scope_of_work;
			
			$lead->save();
			
			$lead_id = $lead->id;
			
			if(!empty($lead_id) && empty($focus->project_lead_id) ){
				$update_lead_id = "UPDATE opportunities SET project_lead_id = '".$lead_id."'
						 WHERE id = '" . $focus->id . "' AND deleted = 0 ";
				$db->query($update_lead_id);
			}
			//add online plans also if exists
			if(isset($_REQUEST['online_plan']) && !empty($_REQUEST['online_plan'])){
			    //create online plan for this lead			    
			    $obOnlinePlans = new oss_OnlinePlans();			    
			    $obOnlinePlans->lead_id = $lead->id;
			    $obOnlinePlans->description = $_REQUEST['online_plan'];
			    $obOnlinePlans->save();			
			}
			
			//bux fix for lead bids due and check boxes
			if(isset($action)){
				$_REQUEST ['action'] = $action;
				 unset($action);
			}
			
		}else if(!empty($focus->parent_opportunity_id)){
			
			if(empty($focus->project_lead_id)){

				$query = "SELECT project_lead_id FROM opportunities WHERE id = '".$focus->parent_opportunity_id."' AND deleted = 0 ";
				$result = $db->query($query);
				$row = $db->fetchByAssoc($result);
				
				if(!empty($row['project_lead_id'])){
					$update_lead_id = "UPDATE opportunities SET project_lead_id = '".$row['project_lead_id']."'
						 WHERE id = '" . $focus->id . "' AND deleted = 0 ";
					$db->query($update_lead_id);
				}
			}
			
		}
		
		/**
		 * Add 20 chars of client to the client opportunity with / separator,
		 * @modified By : Ashutosh
		 * @date : 28 March 2014
		 *
		 */
		//check if the current object is for Client Opportunity
		if(trim($focus->parent_opportunity_id ) != '' ){
			
			//if account_name is not present check is there any client associated
			if($focus->account_name == '' && $focus->account_id != ''){
				$obAccount = new Account();
				$obAccount->disable_row_level_security = 1;
				$obAccount->retrieve($focus->account_id);
				$focus->account_name = $obAccount->name;
			}
			
		    //take first 20 chars from client name
		    $stClientsSubStr = (trim(self::$fetchedRow[$focus->id]['pre_account_name']) != '') ?self::$fetchedRow[$focus->id]['pre_account_name']:$focus->account_name;
		    $stClientsSubStr = trim(substr($stClientsSubStr, 0,19));
		    
		    //check if there is a change in account name
		    if(strstr($focus->name,$stClientsSubStr)){
		        $stNewClientsSubStr = substr($focus->account_name, 0,19);
		        $stClientsSubStr = str_replace('/'.$stClientsSubStr,'/'.$stNewClientsSubStr ,$focus->name );
		        $newClientOppName = $stClientsSubStr;
		    }else{
		    	$stClientsSubStr = (trim($stClientsSubStr) != '')?$stClientsSubStr:$focus->account_name;
		    	$stClientsSubStr = (trim($stClientsSubStr) != '')?'/'.$stClientsSubStr:'';
		        $newClientOppName = $focus->name.$stClientsSubStr;
		
		    }
		    $db->query('UPDATE opportunities set name='.$db->quoted($newClientOppName).' WHERE id='.$db->quoted($focus->id));
		}
	}
    /**
     * Added By Ashutosh
     * Date : 15 Oct 2013
     * purpose : to set relationship if an project opportunity is added to 
     *           the system
     * @param Opportunity $obOpportunity
     */
	function updateZoneRelationship(&$obOpportunity){
	    global $db;
	    if($obOpportunity->id){
	    	//flush the relationship talbe for opportunity
	    	$stDeleteRelZone = 'DELETE FROM oss_zone_opportunities_1_c WHERE oss_zone_opportunities_1opportunities_idb='.$db->quoted($obOpportunity->id);
	    	$db->query($stDeleteRelZone);
	    }	    
	        
	    $atGetRealtedZones = array();
	    if($obOpportunity->lead_state != ''){
	        $atGetRealtedZones[] = " (zone_type='state' AND zone_value LIKE '%^".$obOpportunity->lead_state."^%' )";
	    }
	    
	    if($obOpportunity->lead_city != ''){
	        $atGetRealtedZones[] = "  (zone_type='city' AND zone_value LIKE '%^".$obOpportunity->lead_city."^%' )";
	    }
	    if($obOpportunity->lead_county != ''){
	        $atGetRealtedZones[] = "  (zone_type='county' AND zone_value LIKE '%^".$obOpportunity->lead_county."^%' )";
	    }
	    if($obOpportunity->lead_zip_code != ''){
	        $atGetRealtedZones[] = "  (zone_type='zip' AND zone_value LIKE '%^".$obOpportunity->lead_zip_code."^%' )";
	    }
	    
	    $stGetRelatedZoneConditions = implode( ' OR ',$atGetRealtedZones);
	    
	    if(trim($stGetRelatedZoneConditions) != ''){
    	    $stGetRealtedZones = "SELECT id FROM oss_zone WHERE  ".$stGetRelatedZoneConditions;
    	    $rsResult = $db->query($stGetRealtedZones);
    	    
    	    while($obMatchingStateZone = $db->fetchByAssoc($rsResult)){
    	        $obOpportunity->set_relationship ( 'oss_zone_opportunities_1_c', array (
    	                'oss_zone_opportunities_1oss_zone_ida' => $obMatchingStateZone['id'],
    	                'oss_zone_opportunities_1opportunities_idb' => $obOpportunity->id
    	        ) );
    	    }
	    }
	}
	/**
	 * return the existing value  is modifed or not
	 * @author Mohit Kumar Gupta
	 * @date 27-02-2014
	 * @param array $fetchedArray
	 * @param string $fieldType
	 * @param string $fieldValue
	 * @return true or false
	 */
	function checkExistingFieldValue($fetchedArray=array(), $fieldType='',$fieldValue=''){
	    
	    //modified by : Ashutosh, Date: 23 Apr 2014
	    if(isset($_REQUEST['massupdate']) && $_REQUEST['massupdate'] == true){
	        //return alway true : if the save is from massupdate then apply the changes to client opportunities
	    	return true;
	    }
	    
	    if ($fieldType=='sales_stage') {
	        //if user created a new record or going to modify existing record with the change of slaes stage
	        if (empty($fetchedArray) || (!empty($fetchedArray) && $fetchedArray['sales_stage'] != $fieldValue)) {
	            return true;
	        } else {
	            return false;
	        }
	    }else if ($fieldType=='client_bid_status') {
	        //if user created a new record or going to modify existing record with the change of client bid status
	        if (empty($fetchedArray) || (!empty($fetchedArray) && $fetchedArray['client_bid_status'] != $fieldValue)) {
	            return true;
	        } else {
	            return false;
	        }
	    }else{
	        return false;
	    }
	}
	
	/**
	 * update CRM data to corresponding outlook data
	 * @author Mohit KUmar Gupta
	 * @date 06-03-2014
	 */
	function updateOutlookData($bean, $event, $arguments){
	   if (!empty($bean->account_id) && empty($bean->parent_opportunity_id)) {
            $objBean = clone $bean;
            /**
             * Process to Create Parent Opportunity
             */
            $objBean->account_id = '';
            $objBean->id = create_guid();
            $objBean->new_with_id = true;
            $objBean->save();
            /**
             * Process to Create child Opportunity
             */
            $parentOppId = $objBean->id;
            $bean->parent_opportunity_id = $parentOppId;
       }
	}
	
	
}
?>
