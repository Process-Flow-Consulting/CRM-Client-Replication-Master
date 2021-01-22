<?php
set_time_limit ( 600 );
ini_set ( 'memory_limit', '256M' );
require_once 'include/MVC/View/SugarView.php';
require_once ('custom/modules/Users/filters/instancePackage.class.php');
require_once 'custom/modules/Users/filters/userAccessFilters.php';
require_once 'custom/include/OssTimeDate.php';

class LeadsViewConvert_to_opportunity extends SugarView {
	
	function __construct($bean = null, $view_object_map = array()) {
		parent::SugarView ( $bean, $view_object_map );
	}
	
	function display() {
		global $app_list_strings, $db, $current_user, $app_strings, $timedate, $sugar_config, $mod_strings,$current_user_role;
		
		$bidderIds = array();
		$dateArray = array();
		$zoneArray = array();
		
		
		
		
		$admin=new Administration();	
		$admin_settings = $admin->retrieveSettings('instance', true);
		$geo_filter = $admin->settings ['instance_geo_filter'];
		
		// ##############################
		// ## validate package data #####
		$obPackage = new instancePackage ();
		if ($obPackage->validateOpportunities ()) {
			sugar_die ( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
		}		
		// ## EOF validate package data ####
		// #################################
		
		############################################################
		#### USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
		############################################################
		$stUserFilters = '';
		//apply USER Filters if applicable
		if(!$current_user->is_admin){
			$obBidders = new oss_LeadClientDetail();
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			$obAccessFilters = new userAccessFilters();
			//Need to change this property as count query is putting leads
			//$this->table_name = $obBidders->table_name;
		
			$stUserFilters =  $obAccessFilters->getBiddersFilterClause();
			if(is_object($stUserFilters)){
				//do nothing there should be no impact on bidders list
				// if geo location is set as Project location
			}else {
				// get unique bidders [NOTE :: this will be applicable when geo loation is client]
				$arBidderListSql['select'] = str_replace('SELECT ', 'SELECT DISTINCT  oss_leadclientdetail.id, ', $arBidderListSql['select'])  ;
			}
			//$arBidderListSql['where'] .= ' GROUP BY oss_leadclientdetail.id ';
			
			
		}
		$stUserFilters = (!is_object($stUserFilters) && trim($stUserFilters) != '')?$stUserFilters:'';
		###################################################################
		#### END OF USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
		###################################################################
		if (isset ( $_REQUEST ['record'] )) {
			
			echo $this->getModuleTitle ( false );
			
			// Get Parent Id
			$lead = new Lead ();
			$lead->retrieve ( $_REQUEST ['record'] );
			if (! empty ( $lead->parent_lead_id )) {
				$pLeadId = $lead->parent_lead_id;
			} else {
				$pLeadId = $_REQUEST ['record'];
			}
			
			$pLeadName = $lead->project_title;
			
			/**
			 * update status to viewed if lead status is new
			 * @modified by Mohit Kumar Gupta
			 * @date 20-02-2014
			 */
			if($lead->status == 'New' ){
			    $updateSqlLead = "UPDATE leads SET status='Viewed' WHERE id='".$pLeadId."'";
			    $db->query($updateSqlLead);
			}
		}
		// check lead version for this project lead
		$rsGetLeadVersion = $db->query('SELECT lead_version FROM project_lead_lookup WHERE project_lead_id="' . $pLeadId . '"');
		$arLeadVersion = $db->fetchByAssoc($rsGetLeadVersion);
		//get assigned user suggestion
		//get UsersRole Map
		$arAllUserRoleMap = $this->getUsersRoleMap();
		
		// Get Current User Role
		$current_user_role = $arAllUserRoleMap[$current_user->id];
		$user_id = $current_user->id;
		if($geo_filter == 'project_location'){
			
			$lead->load_relationship ( 'oss_classification_leads' );
			$leadClassIds = $lead->oss_classification_leads->get ();
			$lead_labor = array();
			
			if($lead->union_c == 1)
				$lead_labor[] = 'union_c';
			
			if($lead->non_union == 1)
				$lead_labor[] = 'non_union';
			
			if($lead->prevailing_wage == 1)
				$lead_labor[] = 'prevailing_wage';
			
			$assigned_user = $this->getAssignedUser('Leads',$lead->state,$lead->county_id,$lead->zip_code,$lead->type,$leadClassIds,$lead_labor);
			
			$arProjectLocationAssignedUser = $assigned_user;
		}
		
		
         /*   require_once 'custom/modules/Users/role_config.php';
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
		 */
		
		$iOffset = (! isset ( $_REQUEST ['offset'] ) && trim ( $_REQUEST ['offset'] ) == '') ? '0' : $_REQUEST ['offset'];
		
		$iRows = $sugar_config ['list_max_entries_per_page'];
		
		$iNewBidders = (isset ( $_REQUEST ['new_bidders'] ) && trim ( $_REQUEST ['new_bidders'] )) ? $_REQUEST ['new_bidders'] : 0;
		
		// get total number of bidders
		if (isset($arLeadVersion['lead_version']) && $arLeadVersion['lead_version'] > 1) {
		
		$stSPGetBiddersCount = 'call get_deduped_bidders("' . $pLeadId . '","' . $iNewBidders . '",1,"' . $iOffset . '","' . $iRows . '", " ", " account_name ASC",\''.$stUserFilters.'\',0,"");';
		$rsCountResult = $lead->db->query ( $stSPGetBiddersCount );
		$arTotalResult = $lead->db->fetchByAssoc ( $rsCountResult );
		$iTotalCount = $arTotalResult ['c'];
		}
		
		// to free the last result need to reset the connection
		$lead->db->disconnect ();
		$lead->db->connect ();
		
		$filteredBidders = array ();
		
		$stOrderType = (isset($_REQUEST['odr']))?$_REQUEST['odr']:'ASC';
		$stSortBy = (isset($_REQUEST['sort']))?$_REQUEST['sort']:'account_name';
		$order_by = $stSortBy.' '.$stOrderType;
		$this->ss->assign('order',(isset($_REQUEST['odr']) && $_REQUEST['odr'] == 'ASC')?'DESC':'ASC');
		
		// Get Deduped bidders list    	
    	if (isset($arLeadVersion['lead_version']) && $arLeadVersion['lead_version'] > 1) {
    	    
            $stSPGetBidders = 'call get_deduped_bidders("' . $pLeadId . '","' . $iNewBidders . '",0,"' . $iOffset . '","' . $iTotalCount . '",  " ", "' . $order_by . '",\'' . $stUserFilters . '\',0,"" );';
            $rsResult = $GLOBALS['db']->query($stSPGetBidders, false, '', true, true);
            
        } else {
            // ##############################################
            $obBidders = new oss_LeadClientDetail();
            $arBidderListSql = $obBidders->create_new_list_query('', '', array (), array (), 0, '', 1);
            $arBidderListSql['select'] .= ',oss_leadclientdetail.id bidder_group_ids,accounts.first_classification classifications,accounts.name lcd_account,CONCAT(COALESCE(CONCAT(accounts.billing_address_city," / "),""),accounts.billing_address_state) AS city_state,contacts.assigned_user_id assigned_contact_id,CONCAT(COALESCE(contact_users.first_name,"")," ",COALESCE(contact_users.last_name,"")) contact_assinged_user , accounts.assigned_user_id assigned_client_id,CONCAT(COALESCE(accounts_users.first_name,"")," ",COALESCE(accounts_users.last_name,"")) client_assinged_user  ';
            $arBidderListSql['from'] .= ' LEFT JOIN accounts  ON oss_leadclientdetail.account_id = accounts.id AND accounts.deleted=0
    	LEFT JOIN contacts contacts ON oss_leadclientdetail.contact_id = contacts.id AND contacts.deleted=0 
        LEFT JOIN users contact_users ON contacts.assigned_user_id = contact_users.id AND contact_users.deleted =0
        LEFT JOIN users accounts_users ON accounts.assigned_user_id = accounts_users.id AND accounts_users.deleted =0    ';
            // get leads table alias
            $arTmpAliases = explode(' ', $arBidderListSql['from']);
            foreach ($arTmpAliases as $iKey => $stValue) {
                if ($stValue == 'leads') {
                    $stLeadsAlias = $arTmpAliases[$iKey + 1];
                }
            }
            
            if ($stLeadsAlias == '') {
                $stLeadsAlias = ' leads';
                $arBidderListSql['from'] .= ' LEFT JOIN leads on oss_leadclientdetail.lead_id = leads.id and leads.deleted=0 ';
            }
            $stParentProjectLeadid = $pLeadId;
            $arBidderListSql['where'] .= "  AND ( COALESCE($stLeadsAlias.parent_lead_id,$stLeadsAlias.id)= '" . $stParentProjectLeadid . "' AND oss_leadclientdetail.deleted=0)";
            // echo '<pre>';print_r($arBidderListSql);
            
            $arBidderListSql['from'] .= (!is_object($stUserFilters) && trim($stUserFilters) != '') ? $stUserFilters : '';
            
            $stSQL = $arBidderListSql['select'] . ' ' . $arBidderListSql['from'] . ' ' . $arBidderListSql['where'];
            $rsResult = $GLOBALS['db']->query($stSQL, false, '', true, true);
            // #####################################################
        }
	
	    
	    
		while ( $arResult = $GLOBALS ['db']->fetchByAssoc ( $rsResult ) ) {
			$arData ['list'] [] = ( array ) $arResult;
		}	
		$GLOBALS ['db']->disconnect();
		$GLOBALS ['db']->connect();
		
		$allClassification = array ();
		$i = 0;
		
		foreach($arData ['list'] as $arResult){
			
			//$stUpdateViewdCount = "UPDATE oss_leadclientdetail set is_viewed=1
			//	where id= '".$arResult['id']."'";
			//$GLOBALS['db']->query($stUpdateViewdCount);		
			
			$classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters` uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE `filter_type`='classification'  AND uf.assigned_user_id = '".$GLOBALS['current_user']->id."' AND uf.`deleted`=0";
			$classification_filter_result = $db->query($classification_filter_query);
			$classification_filter_count = $db->getRowCount($classification_filter_result);
			
			if($classification_filter_count > 0){
				while($classification_filter_row = $db->fetchByAssoc($classification_filter_result)){
					$classification_filter_array[$classification_filter_row['id']] = $classification_filter_row['name'];
				}
					
				$classification_filter = implode("','", $classification_filter_array);
				$stGetAccountsClassifications = " SELECT group_concat(oc.description ORDER BY FIELD(oc.name, '$classification_filter') DESC SEPARATOR '#$#') as classifications ";
			}else{
				$stGetAccountsClassifications = ' SELECT group_concat(oc.description SEPARATOR "#$#") as classifications ';
			}
			
			$stGetAccountsClassifications .= ' FROM oss_classifion_accounts_c  oca
			LEFT JOIN oss_classification oc ON oc.id=oss_classi48bbication_ida AND oc.deleted=0
			WHERE  oca.oss_classid41cccounts_idb = '.$db->quoted($arResult['account_id']).'  AND oca.deleted = 0
			GROUP BY oca.oss_classid41cccounts_idb  ';
			
			$rsResult = $db->query($stGetAccountsClassifications);
			$arAccountClsfication = $db->fetchByAssoc($rsResult);
			$arResult['classifications'] = $arAccountClsfication['classifications'];
			
			$filteredBidders [$i] ['id'] = $arResult['id'];
			$filteredBidders [$i] ['company_id'] = $arResult['account_id'];
			$filteredBidders [$i] ['company'] = $arResult['account_name'];
			$filteredBidders [$i] ['contact'] = $arResult['contact_name'];
			$filteredBidders [$i] ['contact_id'] = $arResult['contact_id'];
			$filteredBidders [$i] ['lead_id'] = $arResult['lead_id'];
			$filteredBidders [$i] ['account_id'] = $arResult['account_id'];
			$filteredBidders [$i] ['source'] = $arResult['lead_source'];
			$filteredBidders [$i] ['roles'] = explode("#$#",$arResult['classifications']);
			$filteredBidders [$i] ['role'] = $arResult['role'];
			$filteredBidders [$i] ['bid_status'] = $arResult['bid_status'];
			$filteredBidders [$i] ['converted_to_oppr'] = $arResult['converted_to_oppr'];
			
			//echo '<pre>'; print_r($arResult); echo '</pre>';
			
			// Get All Classification from clients
			$client = new Account ();
			$client->retrieve ( $arResult['account_id'] );
			
			$filteredBidders [$i] ['client_visibility'] = $client->visibility;
			$filteredBidders [$i] ['proview_url'] = $client->proview_url;
			
			$client->load_relationship ( 'opportunities' );
			$opp_count = $client->opportunities->get ();
			
			if (count ( $opp_count ) > 0) {
				$prev_bid_to = '1';
			} else {
				$prev_bid_to = '';
			}
			
			$filteredBidders [$i] ['prev_bid_to'] = $prev_bid_to;
			
			
			/**
			 * Modified By : Ashutosh
			 * Date : 8 May 2013
			 * Purpose : get Assigned user for this Client Opportunity
			 *
			 */
			//check if client contat
			if( isset($arResult['assigned_contact_id']) && $arAllUserRoleMap[$arResult['assigned_contact_id']] != 'lead_reviewer' && trim($arResult['assigned_contact_id']) !='' ){
			    $assigned_user = array('id' => $arResult['assigned_contact_id'], 'name' =>$arResult['contact_assinged_user'] );
			    
			}			//check if Client 
			else if(isset($arResult['assigned_client_id']) && $arAllUserRoleMap[$arResult['assigned_client_id']] != 'lead_reviewer' && trim($arResult['assigned_client_id']) !='' ){
			    $assigned_user = array('id' => $arResult['assigned_client_id'], 'name' =>$arResult['client_assinged_user'] );
			    
			}//get assigned to user
			else if($geo_filter == 'client_location'){
				
				$client->load_relationship ( 'oss_classifation_accounts' );
				$classIds = $client->oss_classifation_accounts->get ();
				
				$assigned_user = $this->getAssignedUser('Accounts',$client->billing_address_state,$client->county_id,$client->billing_address_postalcode,NULL,$classIds);
				
			}else if($geo_filter == 'project_location'){ 
			    //if the geo location is project location 
			    //lets reset the assigned user for next bidder
			     $assigned_user =$arProjectLocationAssignedUser ;				
			}else{
				//no filter location is set then suggest logged in users
				if($current_user_role  != 'lead_reviewer' ){
			        $assigned_user = array('id' => $current_user->id, 'name' => $current_user->name);
				}else{
				    //lead reviewer is the role set empty array
				    $assigned_user = array();
				}
			}	
			
			
			foreach(explode("#$#",$arResult['classifications']) as $classfic)
			{
				if(trim($classfic) != ''){
					if(!in_array($classfic, $allClassification)){
						$allClassification[] = $classfic;
					}
				}
			}
			
			
			$lead = new Lead ();
			$lead->retrieve ( $arResult['lead_id'] );
			
			// Convert Bid Due Date based on TimeZone
			require_once 'custom/include/common_functions.php';
			$bid_due_date_time = convertDbDateToTimeZone ( $lead->bids_due, $lead->bid_due_timezone );
			$bid_due_date = strstr ( $bid_due_date_time, " ", true );
			
			
			$oss_timedate = new OssTimeDate();
			
			//bid due date array of the deduped leads.
			$bid_due_date_db = $oss_timedate->convertDateForDB($lead->bids_due, $lead->bid_due_timezone, true);
			$dateArray[] = $bid_due_date_db;
			if(!empty($lead->bid_due_timezone)){
				$zoneArray[] = $lead->bid_due_timezone;
			}
			
			$filteredBidders [$i] ['bidder_ids_str'] = $arResult['bidder_group_ids'];
			/**
			 * Commented by Ashutosh
			 * Assigned user is already handled
			if(!isset($assigned_user) || empty($assigned_user)){
					
				$user = new User();
				$user->retrieve('1');
					
				$assigned_user = array('id' => 1, 'name' => $user->name);
			
			}
			*/
			
			$filteredBidders [$i] ['assigned_user_id'] = $assigned_user['id'];
			$filteredBidders [$i] ['assigned_user_name'] = $assigned_user['name'];

			$earlierDate = min($dateArray);
			$tmpDates = array_flip($dateArray);
			$iTimezoneIndex = $tmpDates[$earlierDate];
			$earlier_bids_due_timezone = $zoneArray[$iTimezoneIndex];
			
			$i++;

			
		}
		//if earliast date not set and there are no bidders then evaluate the date 
		if(empty($earlierDate) && count($arData ['list']) ==0 ) {
		    $obLead = $this->bean;
		    $obLead->retrieve ( $_REQUEST['primary_lead_id'] );
		    
		
		    $obLead->load_relationship('lead_to_lead_var');
		    $arRelatedIds = $obLead->lead_to_lead_var->get();
		    
		    if(count($arRelatedIds) > 0){	        
		        $oss_timedate = new OssTimeDate();
		        //bid due date array of the deduped leads.
		        $bid_due_date_db = $oss_timedate->convertDateForDB($this->bean->bids_due, $this->bean->bid_due_timezone, true);
		        $dateArray[] = $bid_due_date_db;
		        $zoneArray[] = $this->bean->bid_due_timezone;
		        
		    	 foreach($arRelatedIds as $stLeadId){
		    	     $obLead = new Lead();
		    	     $obLead->retrieve($stLeadId);
		    	   		    	     
		    	    //bid due date array of the deduped leads.
		    	    $bid_due_date_db = $oss_timedate->convertDateForDB($obLead->bids_due, $obLead->bid_due_timezone, true);
		    	    $dateArray[] = $bid_due_date_db;
		    	    $zoneArray[] = $obLead->bid_due_timezone;
    		    } 
		        
		    }else{
		        $oss_timedate = new OssTimeDate();		    	     
		    	//bid due date array of the deduped leads.
		    	$bid_due_date_db = $oss_timedate->convertDateForDB($obLead->bids_due, $obLead->bid_due_timezone, true);
		    	$dateArray[] = $bid_due_date_db; 
		    	$zoneArray[] = $obLead->bid_due_timezone;
		        
		    }	    
		  
		    $earlierDate = min($dateArray);
			$tmpDates = array_flip($dateArray);
			$iTimezoneIndex = $tmpDates[$earlierDate];
			$earlier_bids_due_timezone = $zoneArray[$iTimezoneIndex];
    		
		}
		
		// Filter Bidder's list corrosponding to Classification
		$classification = isset ( $_REQUEST ['classification'] ) ? $_REQUEST ['classification'] : '';
		if (! empty ( $classification )) {
			$count = count ( $filteredBidders );
			for($i = 0; $i < $count; $i ++) {
				if (! in_array ( $classification, $filteredBidders [$i] ['roles'] )) {
					unset ( $filteredBidders [$i] );
				}else{
				    
				    //display as first class					
					$arClassification = array_flip( $filteredBidders[$i]['roles']);
					$iIndex = $arClassification[$classification];
					$filteredBidders[$i] ['roles'][$iIndex] =$filteredBidders[$i] ['roles'][0];
					$filteredBidders[$i] ['roles'][0] = $classification;		
					
				}
			}
			$filteredBidders = array_values ( $filteredBidders );
		}
		//echo '<pre>';print_r($filteredBidders);echo '</pre>';
		$this->ss->assign ( 'all_classification', $allClassification );
		$this->ss->assign ( 'classification', $classification );
		$this->ss->assign ( 'record', $pLeadId );
		$this->ss->assign ( 'sales_stage_dom', $app_list_strings ['client_sales_stage_dom'] );
		$this->ss->assign ( 'bidders_list', $filteredBidders );
		$this->ss->assign ( 'us_timezone_dom', $app_list_strings ['us_timezone_dom'] );
		$this->ss->assign ( 'timedate', $timedate );
		$this->ss->assign ( 'name', $pLeadName );
		$this->ss->assign ( 'earlier_date', $earlierDate);
		$this->ss->assign ( 'earlier_bids_due_timezone', $earlier_bids_due_timezone);
		$this->ss->assign ( 'current_user_id', $current_user->id);
		
		/**
		* use for assign return action if it is not set then default is detail view
		* @modified by Mohit Kumar Gupta
		* @date 11-Feb-2014
		*/
		$returnAction = (isset($_REQUEST ['return_action']))?$_REQUEST ['return_action']:'DetailView';
		$this->ss->assign ( 'return_action', $returnAction );
		
		$stUpdateViewdCount = "UPDATE oss_leadclientdetail lcd, leads SET lcd.is_viewed=1 
				WHERE leads.id=lcd.lead_id AND (leads.parent_lead_id = '".$pLeadId."' OR leads.id='".$pLeadId."')";
		$GLOBALS['db']->query($stUpdateViewdCount);
		
		$stUpdateCount = "UPDATE project_lead_lookup lookup
			SET lookup.new_bidder = 0
			 WHERE lookup.project_lead_id= '".$pLeadId."'";
		$GLOBALS['db']->query($stUpdateCount);
		
		/*echo '<pre>';
		print_r($arData ['list']);
		echo '</pre>';*/
		
		/**
		 * Change template if opportunity id is set
		 */
		if (isset ( $_REQUEST ['opportunity_id'] ) && ! empty ( $_REQUEST ['opportunity_id'] )) {
			// Fetch values from Parent opportunity
			$parent_oppr = new Opportunity();
			$parent_oppr->retrieve($_REQUEST ['opportunity_id']);
			// Fetch already converted bidders of project opportunity
			$client_oppr_obj = new Opportunity();
			$where = "opportunities.parent_opportunity_id = '".$_REQUEST ['opportunity_id']."'";
			$client_oppr_res = $client_oppr_obj->get_full_list('',$where);
				
			$prev_bidders_array = array();
			$i=0;
			foreach($client_oppr_res as $client_oppr){
				$client_oppr->retrieve($client_oppr->id);
				$prev_bidders_array[$i]['id'] = $client_oppr->id;
				$prev_bidders_array[$i]['account_name'] = $client_oppr->account_name;
				$prev_bidders_array[$i]['lead_source'] = $client_oppr->lead_source;
				// Get all classification from clients
				$client_obj = new Account();
				$client_obj->retrieve($client_oppr->account_id);
				$client_obj->load_relationship ( 'oss_classifation_accounts' );
				$classIds = $client_obj->oss_classifation_accounts->get ();
				$client_roles = array ();
				foreach ( $classIds as $classId ) {
					$class = new oss_Classification ();
					$class->retrieve ( $classId );
					$client_roles [] = $class->description;
				}
				$prev_bidders_array[$i]['classifications'] = $client_roles;
				$i++;
			}
				
			$this->ss->assign('prev_bidders_array', $prev_bidders_array);
			$this->ss->assign('p_oppr_amount',$parent_oppr->amount);
			$this->ss->assign('p_oppr_sales_stage',$parent_oppr->sales_stage);
			$this->ss->assign ( 'opportunity_id', $_REQUEST ['opportunity_id'] );
			$this->ss->display ( 'custom/modules/Leads/tpls/add_new_opportunity.tpl' );
		} else {
		    $this->ss->assign ( 'opportunity_id',create_guid() );
			$this->ss->display ( 'custom/modules/Leads/tpls/convert_to_opportunity.tpl' );
		}
		
	}
	
	/**
	 *
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams($browserTitle = false) {
		global $mod_strings;
		$params = parent::_getModuleTitleParams ( $browserTitle );
		$params [] = "<a href='index.php?module=Leads&action=DetailView&record={$this->bean->id}'>{$this->bean->name}</a>";
		$params [] = $mod_strings ['LBL_CONVERTLEAD'];
		return $params;
	}

	function getAssignedUser($module, $state = NULL, $county = NULL, $zip = NULL, $type = NULL, $classification = array(), $labor = array() ){
		 
//		print_r(func_get_args());
	
		global $db,$current_user,$current_user_role;
		 
		$user_filter_query = " SELECT
    		group_concat(DISTINCT osf.filter_type) as set_filters, 
			osf.assigned_user_id as user_id,  TRIM(CONCAT(users.first_name,' ',users.last_name)) as user_name
    		FROM oss_user_filters osf
			INNER JOIN users ON users.id = osf.assigned_user_id AND users.deleted = 0
			INNER JOIN acl_roles_users ON acl_roles_users.user_id = users.id 
			AND acl_roles_users.deleted = 0
			INNER JOIN acl_roles ON acl_roles.id  = acl_roles_users.role_id  
			AND acl_roles.deleted = 0 AND acl_roles.name != 'Lead Reviewer'
			WHERE osf.filter_type != 'geo_filter_for' AND osf.filter_type != 'joins_and_where'
			AND osf.filter_type != 'team_member'
			GROUP BY acl_roles.assign_order, osf.assigned_user_id ORDER BY CASE WHEN user_id='".$current_user->id."' THEN 1 ELSE 0 END desc ";
		
		$user_filter_result = $db->query($user_filter_query);
		 //echo $user_filter_query."<br/>";
		while( $ufrow = $db->fetchByAssoc($user_filter_result)){
	
			$sql = " SELECT count(*) c
    				FROM oss_user_filters osf ";
    				
	
			$filter_types = explode(",",$ufrow['set_filters']);
			$user_id = $ufrow['user_id'];
			$user_name = $ufrow['user_name'];
			
			$sql_where = '';
			$sql_join = '';
			$i = 0;
			foreach ($filter_types as $filter_type){
				 
				
				$sql_join .= " INNER JOIN  oss_user_filters osf_$i ON osf.assigned_user_id = osf_$i.assigned_user_id AND osf_$i.deleted = 0 ";
				
				if($filter_type == 'state')
					$sql_where .= " AND (osf_$i.filter_type = 'state' AND osf_$i.filter_value = '".$state."') ";
				 
				if($filter_type == 'county')
					$sql_where .= " AND (osf_$i.filter_type = 'county' AND osf_$i.filter_value = '".$county."')";
				 
				if($filter_type == 'zip')
					$sql_where .= " AND (osf_$i.filter_type = 'zip' AND osf_$i.filter_value = '".$zip."')";
				 
				if($module == 'Leads'){
	
					if($filter_type == 'type' )
						$sql_where .= " AND (osf_$i.filter_type = 'type' AND osf_$i.filter_value = '".$type."')";
					
					if($filter_type == 'labor'){
						
						$labor_or = array();
						
						if(in_array('union_c', $labor))
							$labor_or[] = " (osf_$i.filter_type = 'labor' AND osf_$i.filter_value = '0') ";
						
						if(in_array('non_union', $labor))
							$labor_or[] = " (osf_$i.filter_type = 'labor' AND osf_$i.filter_value = '1') ";
						
						if(in_array('prevailing_wage', $labor))
							$labor_or[] = " (osf_$i.filter_type = 'labor' AND osf_$i.filter_value = '2') ";
						
						if(count($labor_or) > 0 )
							$sql_where .= " AND ( ".implode("OR", $labor_or). ") ";
						
						
						if(count($labor) < 1){
							$sql_where .= " AND  (osf_$i.filter_type = 'labor' AND osf_$i.filter_value = '3')  ";
						}
						
					}
	
				}
	
				if($filter_type == 'classification'){
					$classIdStr = implode ( "','", $classification );
					$sql_where .= " AND (osf_$i.filter_type = 'classification' AND osf_$i.filter_value IN ('" . $classIdStr . "') )  ";
				}
				
				$i++;
			}
			$sql .= $sql_join." WHERE osf.deleted = 0  ".$sql_where;
			$sql .= " AND osf.assigned_user_id = '".$user_id."' ";
	
//			echo $sql;
	
			$result = $db->query($sql);
				
			$row = $db->fetchByAssoc($result);
				
			if($row['c'] > 0){
							
				return array('id' => $user_id, 'name' => $user_name);
			}
				
		}
		
		/*if Lead Reviewer is not the role of current user then assign this
		 opp to logged in user else assign to admin
		*/
		
		if($current_user_role == 'lead_reviewer'){

		    
		   /**
		    * Commented By : Ashutosh
		    * Date : July 2, 2013
		    * Purpose : if lead reviewer is the role then do not 
		    *      m     suggest user
		    $user = new User();
		    $user->retrieve('1');
		    $assigned_user = array('id' => 1, 'name' => $user->name); 
		    */
		    
		    $assigned_user = array();
		   
		}else{
		
		    $assigned_user = array('id' => $current_user->id, 'name' => $current_user->name);
		}
		return $assigned_user;
		/*$user = new User();
		$user->retrieve('1');
		
		return array('id' => 1, 'name' => $user->name);
		*/
 
	}
	
	/**
	 * Added By : Ashutosh
	 * Date : 13 May 2013
	 * purpose : to get all users role map
	 * 
	 */
	function getUsersRoleMap(){
	    require_once 'custom/modules/Users/role_config.php';
	    global $arUserRoleConfig;
	    global $db;
	    
	    $stRoleUidSQL = 'SELECT users.id user_id ,acl_roles.id role_id FROM users 
               INNER JOIN acl_roles_users ON acl_roles_users.user_id = users.id
               AND acl_roles_users.deleted = 0
               INNER JOIN acl_roles ON acl_roles.id  = acl_roles_users.role_id
               AND acl_roles.deleted = 0 WHERE users.deleted = 0';
	 
	    $rsRoleResult = $db->query($stRoleUidSQL);


        $arAllUsedRoles = array_flip($arUserRoleConfig);

	    while($arRoleUser = $db->fetchByAssoc($rsRoleResult)){
	    	
	       $arUserRoleMap[$arRoleUser['user_id']] =  $arAllUsedRoles[$arRoleUser['role_id']];
	    }
	    return $arUserRoleMap;	    
	}
}
?>
