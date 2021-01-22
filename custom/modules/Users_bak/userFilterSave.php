<?php
require_once ('custom/modules/Users/role_config.php');
class userFilterSave {
	
	function tuneUserFilterSave($bean, $event, $arguments) {
		
		global $arUserRoleConfig, $db, $current_user;
		$arAllFilterClauses = array();
		// if otherthen save action then leave this logic hook
		// As only admin can change the filters execute line below if current
		// user is admin
		// if any of the user filter field is set like classification_filters
		// then execute lines below
		
		if ((isset ( $_REQUEST ['action'] ) && strtolower ( trim ( $_REQUEST ['action'] ) ) != 'save' && ! $current_user->is_admin) || ! isset ( $_REQUEST ['classification_name'] )) {
			// print_r($_REQUEST);die;
			return;
		}
		
		// save user filters for state
		$arAllFilters = array_merge ( array (
				'state' => $_REQUEST ['state_apply'] 
		), array (
				'county' => $_REQUEST ['county_filters'] 
		), array (
				'zip' => $_REQUEST ['zip_filters'] 
		), array (
				'type' => $_REQUEST ['type_filters'] 
		), array (
				'classification' => $_REQUEST ['classification_filters'] 
		), array (
				'labor' => $_REQUEST ['labor_filters'] 
		),array('team_member'=>isset($_REQUEST['tms_filter'])?$_REQUEST['tms_filter']:""), 
		 array (
				'geo_filter_for' => isset ( $_REQUEST ['geo_filter_for'] ) ? array (
						$_REQUEST ['geo_filter_for'] 
				) : "" 
		) );
		// #####################
		// ## Save User Team ###
		
		/* $obTeam = new Team ();
		// remove other teams from this user
		if(isset($_REQUEST ['tms']))
		{
			
			
			$arMyMembership = $obTeam->get_teams_for_user ( $bean->id );
			foreach ( $_REQUEST ['tms'] as $id ) {
				
				$obUserData = new User();
				$obUserData->retrieve($id);
			
				if($obUserData->reports_to_id ==$bean->id )
				{
					$stUpdate = 'UPDATE users set reports_to_id = NULL WHERE id ='.$db->quoted($id);
					$db->query($stUpdate);
				}
				
				foreach ( $arMyMembership as $obAssociatedTeam ) {
				
					//print_r($obAssociatedTeam->associated_user_id);
					if ($obAssociatedTeam->id != '1' && $obAssociatedTeam->associated_user_id != $bean->id) {
						
						$obAssociatedTeam->retrieve ( $obAssociatedTeam->id );
						$obAssociatedTeam->remove_user_from_team ( $bean->id );
					}				
					
				}
			
			}
		}	 */
		//	echo '<pre>';print_r($_REQUEST['tms']);die;
		/* if(isset($_REQUEST ['tms_filter']))
		{
				foreach ( $_REQUEST ['tms_filter'] as $id ) {
				
				$obTeam->retrieve_by_string_fields ( array (
						"associated_user_id " => $id 
				) );
				
				$obTeam->add_user_to_team ( $bean->id );
				
				//set reports to for this user	
				// do not use save method 
				 $stUpdate = 'UPDATE users set reports_to_id = '
								. $db->quoted($bean->id).' WHERE id ='.$db->quoted($id);
			
				$db->query($stUpdate);
				
				
			}
		} */
		// # END SAVE USER TEAM ####
		// #########################
		$arAllFilterClauses = $this->generateFilterWhereClause ( $arAllFilters, $bean->id );
	   
		// remove all filters for this user
		$stDeleteFilters = 'DELETE FROM oss_user_filters where assigned_user_id ="' . $bean->id . '"';
		$bean->db->query ( $stDeleteFilters );
		foreach ( $arAllFilters as $stFilterType => $arValues ) {
			
			if (count ( $arValues ) > 0) {
				foreach ( $arValues as $stValue ) {
					$obUserFilters = new oss_user_filters ();
					$obUserFilters->name = 'Filter for ' . $stFilterType;
					$obUserFilters->filter_value = $stValue;
					$obUserFilters->filter_type = $stFilterType;
					$obUserFilters->assigned_user_id = $bean->id;
					$obUserFilters->save ();
				}
			}
		}
		//save filter join and conditions 
		$obUserFilterClauses = new oss_user_filters();
		$obUserFilterClauses->name = 'JOIN and Conditions for user ' . $bean->id;
		$obUserFilterClauses->filter_clauses = base64_encode(json_encode($arAllFilterClauses));
		$obUserFilterClauses->filter_type = 'joins_and_where';
		$obUserFilterClauses->assigned_user_id = $bean->id;
		$obUserFilterClauses->save ();
		
		
		// remove all the filters for this user
		$db->query ( "DELETE FROM acl_roles_users WHERE user_id ='" . $bean->id . "'" );
		
		/*
		 * * OLD METHOD TO SAVE ASSIGNED USERS if($_REQUEST['user_role'] ==
		 * 'team_manager'){ //remove this user from assigned user list
		 * $db->query("DELETE FROM oss_user_filters WHERE
		 * filter_value='".$bean->id."' AND filter_type='team_member'"); }
		 */
		
		$bean->set_relationship ( 'acl_roles_users', array (
				'role_id' => $arUserRoleConfig [$_REQUEST ['user_role']],
				'user_id' => $bean->id 
		) );
	
	}
	
	/*
	 * logic hook to validate while editing users to validate how many users
	 * left
	 */
	function validateUserPackage($bean, $event, $arguments) {
		
		//change the emailaddress class for this module
		require_once 'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php';
	
		if(!in_array(trim($_REQUEST['action']) ,array( 'SaveUserWizard','bbwizard')) ){
			$bean->emailAddress = new CustomSugarEmailAddress();
		}
		/*
		 * # validate number of users for this instance
		 */
		require_once ('custom/modules/Users/filters/instancePackage.class.php');
		$obPackage = new instancePackage ();
		$arData = $obPackage->getPacakgeDetails ();
		// get this instance addtional users
		$obUsers = new User ();
		$arUsers = $obUsers->get_full_list ( '', '  users.status="Active" ' );
		
		if (isset($_REQUEST['record']) &&  (($bean->fetched_row['status'] == 'Inactive' || $bean->fetched_row['user_name'] == '' )&& $bean->status == 'Active') && count( $arUsers ) >= $arData ['no_of_users']) {
			// do not save this record
			if($_REQUEST['_to_pdf']){
                
			    sugar_die("<script>alert('User limit exceeded only " . $arData ['no_of_users'] . " additional users allowed, Please contact support.'); </script>" );
				
			}else{
    			header ( "refresh:5;url=index.php?module=Home" );
    			sugar_die ( "User limit exceeded only " . $arData ['no_of_users'] . " additional users allowed, Please contact support.
    			<br/> You will be redirected to home page after 5 second." );
			}
		}
	
	}
	/**
	 * function to get where cluase array
	 * 
	 * @param array $arAllFilters,
	 *        	string user id
	 * @return array filters where
	 */
	function generateFilterWhereClause($arAllFilters, $stUserId) {
		global $current_user;
		$arWhereClause = array ();
		// check if location filter is saved for this istance
		$admin = new Administration ();
		$admin = $admin->retrieveSettings ( 'instance', true );
		
		if (isset ( $admin->settings ['instance_geo_filter'] )) {
			switch ($admin->settings ['instance_geo_filter']) {
				
				case 'project_location' :
					$arWhereClause = $this->getProjectLocationWhere ( $arAllFilters, $stUserId );
					break;
				
				case 'client_location' :
					$arWhereClause = $this->getClientLocationWhere ( $arAllFilters, $stUserId );
					break;
			}
		}
		return $arWhereClause;
	}
	/**
	 * function to get where clause for project Location
	 * 
	 * @param array $arAllFilters        	
	 */
	function getProjectLocationWhere($arAllFilters, $stUserId) {
		$arWhereClauses = array ();		
		$arOppListViewWhere = array ();
		$arOppListViewJoins = array ();
		$arOppSummaryViewJoins = array ();
		$arOppSummaryViewWhere = array ();
		$arProjectLeadsJoins = array ();
		$arProjectLeadsWhere = array ();
		
		if(is_array($arAllFilters['state'])){			
			//set filters for Project Leads list view
			$arProjectLeadsWhere[] = ' ( leads.state IN ("'.implode('","',$arAllFilters['state']).'") )';
		
		}
		if(is_array($arAllFilters['county'])){
			//set filters for Project Leads list view
			$arProjectLeadsWhere[] = ' ( leads.county_id IN ("'.implode('","',$arAllFilters['county']).'") )';
		
		}
		if(is_array($arAllFilters['zip'])){
			//set filters for Project Leads list view
			$arProjectLeadsWhere[] = ' ( leads.zip_code IN ("'.implode('","',$arAllFilters['zip']).'") )';
		
		}
		if(is_array($arAllFilters['type'])){
			//set filters for Project Leads list view
			$arProjectLeadsWhere[] = ' ( leads.type IN ("'.implode('","',$arAllFilters['type']).'")) ';
		
		}
		if(is_array($arAllFilters['labor'])){
			$stWhereClause = '';
			$arLabFieldName = array();
			
			//set filters for Project Leads list view			
			foreach ( $arAllFilters['labor'] as $iValue ) {
				
						if ($iValue == 0) {
							$arLabFieldName [] = 'union_c = 1';
						} elseif ($iValue == 1) {
							$arLabFieldName [] = 'non_union = 1 ';
						} elseif ($iValue == 2) {
							$arLabFieldName [] = 'prevailing_wage = 1';
						} elseif ($iValue == 3) {
							// special case : if undefined filter is checked
							// then get all the results
							// on which union, non-union and prevailing wage
							// fields are not set
							//$stWhereClauseUndef = ' OR (ProjectLeads.union_c is null AND ProjectLeads.non_union is null AND ProjectLeads.prevailing_wage is null ) ';
							$stWhereClauseUndef = ' ( (leads.union_c = 0 OR leads.union_c IS NULL) AND (leads.non_union = 0 OR leads.non_union IS NULL) AND (leads.prevailing_wage = 0 OR leads.prevailing_wage is null))';
						}
						// $stWhereClause .= ' OR
					// ProjectLeads.'.$stLabFieldName.'=1 ' ;
					}
					if (count ( $arLabFieldName ) > 0) {
						$stUndefinedStmt = ($stWhereClauseUndef !='')?' OR ' .$stWhereClauseUndef:'';
						$stWhereClause .= ' ( leads.' . implode ( ' OR leads.', $arLabFieldName ) . $stUndefinedStmt. ')';
					} elseif ($stWhereClauseUndef != '') {
						$stWhereClause .= '  '.$stWhereClauseUndef;
					}
					
			$arProjectLeadsWhere[] =$stWhereClause;
			
		
		}
		if(is_array($arAllFilters['classification'])){
			
			//set filters for Project Leads list view
			$arProjectLeadsJoins[] = ' LEFT JOIN oss_classifcation_leads_c leadClsfication ON leadClsfication.oss_classi7103dsleads_idb= leads.id AND leadClsfication.deleted =0 ';
			
			$arProjectLeadsWhere[] = ' (leadClsfication.oss_classi4427ication_ida IN ("'.implode('","',$arAllFilters['classification']).'")) ';
		
		}
		/**
		 * Filters for projectLeads List view
		 */
		$arWhereClauses ['leads'] ['listview'] = array (
				'joins' => implode ( ' ', $arProjectLeadsJoins ),
				'where' => (count($arProjectLeadsWhere) > 0)? ' AND (' . implode ( ' AND  ', $arProjectLeadsWhere ) . ' OR leads.assigned_user_id = "' . $stUserId . '" )'
				           : ' OR leads.assigned_user_id = "' . $stUserId . '" '
		);
		//if any of leads filter is set then save opportunities filter clause
		if(count($arProjectLeadsWhere) >0){
			
			$arOppListViewJoins[] = ' LEFT JOIN leads on opportunities.project_lead_id = leads.id AND leads.deleted = 0 ';
			
			//if classification filters are set 
			if(is_array($arAllFilters['classification'])){

				$arOppListViewJoins[] = ' LEFT JOIN oss_classifcation_leads_c leadClsfication ON leadClsfication.oss_classi7103dsleads_idb= leads.id AND leadClsfication.deleted =0 ';					
				$arOppListViewWhere [] = '  (leadClsfication.oss_classi4427ication_ida IN ("'.implode('","',$arAllFilters['classification']).'")) ';;
			}
			
		}
		
		/**
		 * filters for opportunities
		 */
		$arOppListViewWhere = array_merge($arOppListViewWhere,$arProjectLeadsWhere);
		$arWhereClauses ['opportunties'] ['listview'] = array (
				'joins' => implode ( ' ', $arOppListViewJoins ),
				'where' => (count($arOppListViewWhere)>0) ? ' AND (' . implode ( ' AND  ', $arOppListViewWhere ) . ' OR opportunities.assigned_user_id = "' . $stUserId . '" )'
							:'  OR opportunities.assigned_user_id = "' . $stUserId . '" '
		);
		$arWhereClauses ['opportunties'] ['summaryview'] = $arWhereClauses ['opportunties'] ['listview'];
		
		return $arWhereClauses;
	}
	
	/**
	 * function to get where clause for client Location
	 * 
	 * @param array $arAllFilters,
	 *        	string userid
	 */
	function getClientLocationWhere($arAllFilters, $stUserId) {
		$arWhereClauses = array ();
		$arOppListViewWhere = array ();
		$arOppListViewJoins = array ();
		$arOppSummaryViewJoins = array ();
		$arOppSummaryViewWhere = array ();
		$arProjectLeadsJoins = array ();
		$arProjectLeadsWhere = array ();
		
		/**
		 * FILTERS FOR OPPORUTNITES AND PROJECT LEADS
		 */
		
		// Where clause for opportunties List view
		// need to match state with filters join related accounts
		 if ( ( is_array ( $arAllFilters ['state'] )
			|| is_array ( $arAllFilters ['zip'] )
    		|| is_array ( $arAllFilters ['county'] )
    		 ) || is_array ( $arAllFilters ['classification'] )) {
			$arOppListViewJoins [] = ' LEFT JOIN opportunities_accounts_c ON opportunities_accountsopportunities_ida = opportunities.id and opportunities_accounts_c.deleted = 0
			LEFT JOIN accounts filter_accounts ON opportunities_accountsaccounts_idb = filter_accounts.id and filter_accounts.deleted = 0
			';
			
			$arProjectLeadsJoins [] = ' LEFT JOIN oss_leadclientdetail bidders ON bidders.lead_id = leads.id and bidders.deleted = 0
    		 LEFT JOIN accounts ON accounts.id = bidders.account_id AND accounts.deleted = 0 ';
			if ((is_array ( $arAllFilters ['state'] ))) {
				
				// add states condition opp List view
				$arOppListViewWhere [] = ' (filter_accounts.billing_address_state IN ( "' . implode ( '","', $arAllFilters ['state'] ) . '")) ';
				
				// add states condition for opp summary View
				$arOppSummaryViewWhere [] = ' (accounts.billing_address_state IN ( "' . implode ( '","', $arAllFilters ['state'] ) . '")) ';
				
				// add states condition for Project Leads list view
				$arProjectLeadsWhere [] = ' ( accounts.billing_address_state IN ( "' . implode ( '","', $arAllFilters ['state'] ) . '") ) ';
			}
		
		}
		
		if(is_array($arAllFilters['county'])){
			
			// add states condition opp List view
			$arOppListViewWhere [] = ' (filter_accounts.county_id IN ( "' . implode ( '","', $arAllFilters ['county'] ) . '")) ';
			
			// add states condition for opp summary View
			$arOppSummaryViewWhere [] = ' (accounts.county_id IN ( "' . implode ( '","', $arAllFilters ['county'] ) . '")) ';
			
			// add states condition for Project Leads list view
			$arProjectLeadsWhere [] = ' ( accounts.county_id IN ( "' . implode ( '","', $arAllFilters ['county'] ) . '") ) ';
		
		}
		if(is_array($arAllFilters['zip'])){
			//set filters for Project Leads list view
			$arOppListViewWhere[] = ' ( filter_accounts.billing_address_postalcode IN ("'.implode('","',$arAllFilters['zip']).'") )';
			// add states condition for opp summary View
			$arOppSummaryViewWhere [] = ' (accounts.billing_address_postalcode IN ( "' . implode ( '","', $arAllFilters ['zip'] ) . '")) ';
				
			// add states condition for Project Leads list view
			$arProjectLeadsWhere [] = ' ( accounts.billing_address_postalcode IN ( "' . implode ( '","', $arAllFilters ['zip'] ) . '") ) ';
			
		
		}
		
		// need to join classifications if filters set
		if (is_array ( $arAllFilters ['classification'] )) {
			
			// JOIN FOR OPPORTUTNITY LIST VIEW
			$arOppListViewJoins [] = ' LEFT JOIN oss_classifion_accounts_c classification ON filter_accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0 ';
			// add classification conditions for opportunity list view
			$arOppListViewWhere [] = ' (classification.oss_classi48bbication_ida IN ( "' . implode ( '","', $arAllFilters ['classification'] ) . '")) ';
			
			// JOIN FOR OPPORTUNITIES SUMMARY VIEW
			$arOppSummaryViewJoins [] = ' LEFT JOIN oss_classifion_accounts_c classification ON accounts_opportunities.id = classification.oss_classid41cccounts_idb
			AND classification.deleted = 0 ';
			// add classification conditions opportunity summary view
			$arOppSummaryViewWhere [] = ' (classification.oss_classi48bbication_ida IN ( "' . implode ( '","', $arAllFilters ['classification'] ) . '"))';
			
			$arProjectLeadsJoins[] = ' LEFT JOIN oss_classifion_accounts_c classification ON accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0 ';
			
			$arProjectLeadsWhere[] = ' (classification.oss_classi48bbication_ida IN ( "' . implode ( '","', $arAllFilters ['classification'] ) . '"))';
		
		}
		
		$arWhereClauses ['opportunties'] ['listview'] = array (
				'joins' => implode ( ' ', $arOppListViewJoins ),
				'where' => (count($arOppListViewWhere)>0) ? ' AND (' . implode ( ' AND  ', $arOppListViewWhere ) . ' OR opportunities.assigned_user_id = "' . $stUserId . '" )'
				             :  ' OR opportunities.assigned_user_id = "' . $stUserId . '" ' 
		);
		
		/**
		 * FILTERS FOR OPPORTUNITY SUMMARY VIEW
		 */
		$arWhereClauses ['opportunties'] ['summaryview'] = array (
				'joins' => implode ( ' ', $arOppSummaryViewJoins ),
				'where' => (count($arOppSummaryViewWhere)>0)? ' AND (' . implode ( ' AND  ', $arOppSummaryViewWhere ) . '  OR opportunities.assigned_user_id = "' . $stUserId . '" )'
							:  ' OR opportunities.assigned_user_id = "' . $stUserId . '" ' 
		);
		
		/**
		 * Filters for projectLeads List view
		 */
		$arWhereClauses ['leads'] ['listview'] = array (
				'joins' => implode ( ' ', $arProjectLeadsJoins ),
				'where' => (count($arProjectLeadsWhere)>0)? ' AND (' . implode ( ' AND  ', $arProjectLeadsWhere ) . '  OR leads.assigned_user_id = "' . $stUserId . '" )'
				             :  '  OR leads.assigned_user_id = "' . $stUserId . '" '
		);	
		
		return $arWhereClauses;
	}
}
?>
