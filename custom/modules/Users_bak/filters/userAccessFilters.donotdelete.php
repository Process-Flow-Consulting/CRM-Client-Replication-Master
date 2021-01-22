<?php

/**
 * CLASS : userAccessFilters 
 * Purpose : Class definition for manipulating the user filters 
 *           for current logged in user. 
 */

require_once ('modules/oss_user_filters/oss_user_filters.php');
require_once ('custom/modules/Users/role_config.php');

class userAccessFilters {
	
	/**
	 * Method : userAccessFilters
	 * params : void
	 * return : array (saved filters)
	 * Purpose : Constructor Definition
	 */
	function userAccessFilters() {
	
	}
	/**
	 * Method : getCurrentUserFilters
	 * params : void
	 * return : array (saved filters)
	 * Purpose : Get all the saved filters for logged in
	 * user.
	 */
	public static function getCurrentUserFilters() {
		global $current_user;
		$arSavedUserFilters = array ();
		
		$obUserFilters = new oss_user_filters ();
		$arUserFilters = $obUserFilters->get_full_list ( '', 'assigned_user_id = "' . $current_user->id . '"' );
		if (count ( $arUserFilters ) > 0) {
			foreach ( $arUserFilters as $obUserFilter ) {
				
				$arSavedUserFilters [$obUserFilter->filter_type] [] = $obUserFilter->filter_value;
			
			}
		}
		
		return $arSavedUserFilters;
	}
	/**
	 * Method : getLeadFilterClause
	 * params : void
	 * return : string (SQL)
	 * Purpose : to get the current saved filters SQL
	 * further this sql will be used to get the
	 * leads as per the filteration criterea
	 * for Project location
	 */
	
	public static function getLeadFilterClause() {
		
		global $current_user;
		$stFinalSql ='';
		// check if location filter is saved for this istance
		$admin = new Administration ();
		$admin = $admin->retrieveSettings ( 'instance', true );
		
		if(isset($admin->settings ['instance_geo_filter'])){
		switch ($admin->settings ['instance_geo_filter']) {
			
			case 'project_location' :
				$stFinalSql = self::getProjectGeoFilterSql ();
				break;
			
			case 'client_location' :
				$stFinalSql = self::getClientGeoFilterSql ();
				break;
		
		}
		}
		
		return $stFinalSql;
	
	}
	
	/**
	 * Method : getProjectGeoFilterSql
	 * params : void
	 * return : string (SQL)
	 * Purpose : To get ehe filter sql based on the geo location
	 * for Project Leads
	 */
	function getProjectGeoFilterSql() {
		global $current_user;
		
		$stWhereClause = '';
		$stFinalSql = '';
		$arMappings = array ('team_member' => 'assigned_user_id'
							, 'type' => 'type', 'zip' => 'zip_code', 'county' => 'county_id', 'state' => 'state' );

		
		$arAllFilters = self::getCurrentUserFilters ();
		
		$stSelectedGeoFilter = '';
		// get geographic filter value
		if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
			
			$stSelectedGeoFilter = $arAllFilters ['geo_filter_for'] [0];
		}
		// remove geofilter from filters this will be used to check type of
		// filter
		unset ( $arAllFilters ['geo_filter_for'] );
		
		$stInclause = "";
		$iCount = 0;
		// chekc is there any filter
		if (count ( $arAllFilters ) > 0) {
			
			// check if logged in user is Team manager
			// if there are any team members then include tms in condition
			/**
			 * @Commented By : Ashutosh @purpose : This segment is commented as
			 * for team managers no need to consider the filters of team
			 * members.
			 */
			/**
			 $bIsTeamMember = self::isTeamManager($current_user->id);
			//if yes then get all the associated team members
			if($bIsTeamMember){
			//get this TMs team
			$arTeamMembers = array();
			$obUserTeam = new Team();
			$obUser = new User();
			$arMyMembership = $obUserTeam->get_teams_for_user($current_user->id);
		
			foreach ($arMyMembership as $obAssociatedTeam) {
				
			//	print_r($obAssociatedTeam->associated_user_id);
				if($obAssociatedTeam->associated_user_id != '' && $obAssociatedTeam->associated_user_id != $current_user->id){
					$obUser->retrieve($obAssociatedTeam->associated_user_id);
					$arTeamMembers[$obUser->id]= $obUser->name;
				}
				
			}
			}
			if(count($arTeamMembers)>0 ){
				$arTeamMembers = array_merge(array($current_user->id=>''),$arTeamMembers);
				$stUserClause = ' IN ("'.implode('","',array_keys($arTeamMembers)).'")';
			}else{
				$stUserClause = ' = "'.$current_user->id.'"';
			}
			*/
			$stUserClause = ' = "' . $current_user->id . '"';
			
			$stFilterSql = 'SELECT
			ProjectLeads.id
			,ProjectLeads.project_title
			,ProjectLeads.state
			,ProjectLeads.zip_code
			FROM leads ProjectLeads
			';
			$stFilterJoin = '';
			$stWhereClauseUndef = '';
			foreach ( $arAllFilters as $stFieldName => $arFieldValues ) {
				// Labor filters can be checked in lead table itself so put it
				// on where
				if ($stFieldName == 'labor') {
					$stWhereClause = '';
					foreach ( $arFieldValues as $iValue ) {
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
							$stWhereClauseUndef = '  (ProjectLeads.union_c = 0 OR ProjectLeads.union_c IS NULL) AND (ProjectLeads.non_union = 0 OR ProjectLeads.non_union IS NULL) AND (ProjectLeads.prevailing_wage = 0 OR ProjectLeads.prevailing_wage is null)';
							
						}
						
						// $stWhereClause .= ' OR
					// ProjectLeads.'.$stLabFieldName.'=1 ' ;
					}
					
					if (count ( $arLabFieldName ) > 0) {
						$stUndefinedStmt = ($stWhereClauseUndef !='')?' OR ' .$stWhereClauseUndef:'';
						$stWhereClause .= 'AND ( ProjectLeads.' . implode ( ' OR ProjectLeads.', $arLabFieldName ) . $stUndefinedStmt. ')';
					} elseif ($stWhereClauseUndef != '') {
						$stWhereClause .= ' AND '.$stWhereClauseUndef;
					}
					//$stWhereClause .=  ' OR ProjectLeads.assigned_user_id = "'.$current_user->id.'"';
				} elseif ($stFieldName == 'classification') {
					// for classifications get the ids of project leads which
					// are matching with selected filter classification
					$stFilterJoin .= 'INNER JOIN  oss_classifcation_leads_c classification on ProjectLeads.id = classification.oss_classi7103dsleads_idb AND classification.deleted = 0   
				INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi4427ication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' . $stUserClause . ')  ';
				
				} elseif ($stFieldName == $stSelectedGeoFilter || $stFieldName == 'type') {
					$stFilterJoin .= ' INNER JOIN  oss_user_filters ' . $stFieldName . 'Filters ON (' . $stFieldName . 'Filters.filter_type= "' . $stFieldName . '" AND ProjectLeads.' . $arMappings [$stFieldName] . ' =' . $stFieldName . 'Filters.filter_value AND ' . $stFieldName . 'Filters.assigned_user_id ' . $stUserClause . ')	';
				}
			
			}
			$stFinalSql = $stFilterSql . $stFilterJoin . ' WHERE ProjectLeads.deleted=0 ' . $stWhereClause . ' GROUP BY  ProjectLeads.id';
		
		}
		
		return $stFinalSql;
	}
	/**
	 * Method : getClientGeoFilterSql
	 * params : void
	 * return : string (SQL)
	 * Purpose : To get ehe filter sql based on the geo location
	 * for Project location
	 */
	function getClientGeoFilterSql(){
		global $current_user;
		
		$stWhereClause = '';
		$stFinalSql = '';
		$arMappings = array ('team_member' => 'assigned_user_id'							 
							, 'zip' => 'billing_address_postalcode'
							, 'county' => 'county_id'  
							, 'state' => 'billing_address_state' );
		
		$arAllFilters = self::getCurrentUserFilters ();
		
		$stSelectedGeoFilter = '';
		// get geographic filter value
		if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
				
			$stSelectedGeoFilter = $arAllFilters ['geo_filter_for'] [0];
		}
		// remove geofilter from filters this will be used to check type of
		// filter
		unset ( $arAllFilters ['geo_filter_for'] );
		
		$stInclause = "";
		$iCount = 0;
		// chekc is there any filter
		if (count ( $arAllFilters ) > 0) {			
			
			$stUserClause = ' = "' . $current_user->id . '"';
				
			$stFilterSql = 'SELECT
							ProjectLeads.id
							,ProjectLeads.project_title
							,ProjectLeads.state
							,ProjectLeads.zip_code
							FROM leads ProjectLeads
							LEFT JOIN oss_leadclientdetail bidders ON bidders.lead_id = ProjectLeads.id and bidders.deleted =0
							LEFT JOIN accounts ON accounts.id = bidders.account_id AND accounts.deleted =0
							';
			$stFilterJoin = '';
			
			$stWhereClauseUndef = '';
			foreach ( $arAllFilters as $stFieldName => $arFieldValues ) {
				
				if ($stFieldName == 'classification') {
					// for classifications get the ids of project leads which
					// are matching with selected filter classification
					$stFilterJoin .= 'INNER JOIN  oss_classifion_accounts_c classification on accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0 OR ProjectLeads.assigned_user_id = "'.$current_user->id.'"
					INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi48bbication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' . $stUserClause . ') OR ProjectLeads.assigned_user_id = "'.$current_user->id.'"  ';
		
				} elseif ($stFieldName == $stSelectedGeoFilter || $stFieldName == 'type') {
					$stFilterJoin .= ' INNER JOIN  oss_user_filters ' . $stFieldName . 'Filters ON (' . $stFieldName . 'Filters.filter_type= "' . $stFieldName . '" AND accounts.' . $arMappings [$stFieldName] . ' =' . $stFieldName . 'Filters.filter_value AND ' . $stFieldName . 'Filters.assigned_user_id ' . $stUserClause . ')
					OR ProjectLeads.assigned_user_id = "'.$current_user->id.'" ';
				}
					
			}
			$stFinalSql = $stFilterSql . $stFilterJoin . ' WHERE ProjectLeads.deleted=0 ' . $stWhereClause . ' GROUP BY  ProjectLeads.id';
		
		}
		return $stFinalSql;
	}
	
	/**
	 * Method : getOpporutnityFilterWehreClause
	 * params : Void
	 * return : string
	 * Purpose : To get ehe filter sql based on the geo location
	 * 			 for Opportunities
	 */
	function getOpporutnityFilterWehreClause($bForParent=false){
		global $current_user;
		$stFinalSql ='';
		// check if location filter is saved for this istance
		$admin = new Administration ();
		$admin = $admin->retrieveSettings ( 'instance', true );
		if(isset($admin->settings ['instance_geo_filter'])){
			switch ($admin->settings ['instance_geo_filter']) {
					
				case 'project_location' :
					$stFinalSql = self::getOppProjectGeoFilterSql ();
				break;
						
				case 'client_location' :
				 	$stFinalSql = self::getOppClientGeoFilterSql($bForParent);
				break;
			
			}
		}
		
		return $stFinalSql;
		
	}
	/**
	 * Method : getOppProjectGeoFilterSql
	 * params : Void
	 * return : string 
	 * Purpose : To get the filter SQL for oportunities  
	 */
	function getOppProjectGeoFilterSql(){
		
		global $current_user;
		
		$stWhereClause = '';
		$stFinalSql = '';
		$arMappings = array ('team_member' => 'assigned_user_id'
							, 'type' => 'type'
							, 'zip' => 'zip_code'
							, 'county' => 'county_id'
							, 'state' => 'state' );
		
		$arAllFilters = self::getCurrentUserFilters ();
		
		$stSelectedGeoFilter = '';
		// get geographic filter value
		if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
			
			$stSelectedGeoFilter = $arAllFilters ['geo_filter_for'] [0];
		}
		// remove geofilter from filters this will be used to check type of
		// filter
		unset ( $arAllFilters ['geo_filter_for'] );
		
		$stInclause = "";
		$iCount = 0;
		// chekc is there any filter
		if (count ( $arAllFilters ) > 0) {		
			
			$stUserClause = ' = "' . $current_user->id . '"';
			
			$stFilterSql = 'SELECT opportunities.id,opportunities.name,opportunities.parent_opportunity_id
							FROM opportunities 
							INNER JOIN leads ProjectLeads  ON ProjectLeads.id = opportunities.project_lead_id  AND ProjectLeads.deleted=0 OR opportunities.assigned_user_id = "'.$current_user->id.'" 
							';
			$stFilterJoin = '';
			$stWhereClauseUndef = '';
			$stUndefinedStmt = '';
			foreach ( $arAllFilters as $stFieldName => $arFieldValues ) {
				// Labor filters can be checked in lead table itself so put it
				// on where
				if ($stFieldName == 'labor') {
					$stWhereClause = '';
					foreach ( $arFieldValues as $iValue ) {
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
							$stWhereClauseUndef = ' ( (ProjectLeads.union_c = 0 OR ProjectLeads.union_c IS NULL) AND (ProjectLeads.non_union = 0 OR ProjectLeads.non_union IS NULL) AND (ProjectLeads.prevailing_wage = 0 OR ProjectLeads.prevailing_wage is null))';
						}
						// $stWhereClause .= ' OR
					// ProjectLeads.'.$stLabFieldName.'=1 ' ;
					}
					/* if (count ( $arLabFieldName ) > 0) {
						$stWhereClause .= 'AND ( ProjectLeads.' . implode ( ' OR ProjectLeads.', $arLabFieldName ) . $stWhereClauseUndef . ')';
					} elseif ($stWhereClauseUndef != '') {
						$stWhereClause .= $stWhereClauseUndef;
					} */
					if (count ( $arLabFieldName ) > 0) {
						$stUndefinedStmt = ($stWhereClauseUndef !='')?' OR ' .$stWhereClauseUndef:'';
						$stWhereClause .= 'AND ( ProjectLeads.' . implode ( ' OR ProjectLeads.', $arLabFieldName ) . $stUndefinedStmt. ')';
					} elseif ($stWhereClauseUndef != '') {
						$stWhereClause .= ' AND '.$stWhereClauseUndef;
					}
				} elseif ($stFieldName == 'classification') {
					// for classifications get the ids of project leads which
					// are matching with selected filter classification
					$stFilterJoin .= 'INNER JOIN  oss_classifcation_leads_c classification on ProjectLeads.id = classification.oss_classi7103dsleads_idb AND classification.deleted = 0
				INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi4427ication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' . $stUserClause . ') OR opportunities.assigned_user_id = "'.$current_user->id.'"  ';
				
				} elseif ($stFieldName == $stSelectedGeoFilter || $stFieldName == 'type') {
					$stFilterJoin .= ' INNER JOIN  oss_user_filters ' . $stFieldName . 'Filters ON (' . $stFieldName . 'Filters.filter_type= "' . $stFieldName . '" AND ProjectLeads.' . $arMappings [$stFieldName] . ' =' . $stFieldName . 'Filters.filter_value AND ' . $stFieldName . 'Filters.assigned_user_id ' . $stUserClause . ')
				OR opportunities.assigned_user_id = "'.$current_user->id.'" ';
				}
			
			}
		$stJoinCondition =(isset($_REQUEST['action']) && $_REQUEST['action'] == 'index')?' tmpUFilters.parent_opportunity_id = opportunities.id': 'tmpUFilters.id = opportunities.id';

                $stWhereCondition =  (isset($_REQUEST['action']) && $_REQUEST['action'] == 'index')?' AND opportunities.parent_opportunity_id IS NOT NULL':'';

 			$stFinalSql = ' INNER JOIN ('.$stFilterSql . $stFilterJoin . ' WHERE  opportunities.deleted=0 ' . $stWhereClause . ' GROUP BY  opportunities.id)tmpUFilters on '. $stJoinCondition;
		
		}

		return $stFinalSql;
	}
	/**
	 * Method : getOppClientGeoFilterSql
	 * params : Bool  if its for a parent opportunity or opportunity list view
	 * return : string
	 * Purpose : To get the filter SQL for oportunities
	 */
	function getOppClientGeoFilterSql($bForParent=false){
		
		global $current_user;
		
		$stWhereClause = '';
		$stFinalSql = '';
		$arMappings = array ('team_member' => 'assigned_user_id'
				, 'zip' => 'billing_address_postalcode'
				, 'county' => 'county_id'
				, 'state' => 'billing_address_state' );
		
		$arAllFilters = self::getCurrentUserFilters ();
		
		$stSelectedGeoFilter = '';
		// get geographic filter value
		if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
		
			$stSelectedGeoFilter = $arAllFilters ['geo_filter_for'] [0];
		}
		// remove geofilter from filters this will be used to check type of
		// filter
		unset ( $arAllFilters ['geo_filter_for'] );
		
		$stInclause = "";
		$iCount = 0;
		// chekc is there any filter
		if (count ( $arAllFilters ) > 0) {
				
			$stUserClause = ' = "' . $current_user->id . '"';
		
			$stFilterSql = 'SELECT opportunities.id,opportunities.name,opportunities.parent_opportunity_id
							FROM opportunities 
							LEFT JOIN accounts_opportunities acop  on acop.opportunity_id = opportunities.id AND acop.deleted =0
							LEFT JOIN accounts filter_accounts ON filter_accounts.id = acop.account_id AND filter_accounts.deleted =0
			                ';
			$stFilterJoin = '';
				
			$stWhereClauseUndef = '';
			foreach ( $arAllFilters as $stFieldName => $arFieldValues ) {
		
				if ($stFieldName == 'classification') {
					// for classifications get the ids of accounts which
					// are matching with selected filter classification
					$stFilterJoin .= 'INNER JOIN  oss_classifion_accounts_c classification on filter_accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0 OR opportunities.assigned_user_id = "'.$current_user->id.'" 
					INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi48bbication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' . $stUserClause . ') OR opportunities.assigned_user_id = "'.$current_user->id.'" ';
		
				} elseif ($stFieldName == $stSelectedGeoFilter || $stFieldName == 'type') {
					$stFilterJoin .= ' INNER JOIN  oss_user_filters ' . $stFieldName . 'Filters ON (' . $stFieldName . 'Filters.filter_type= "' . $stFieldName . '" AND filter_accounts.' . $arMappings [$stFieldName] . ' =' . $stFieldName . 'Filters.filter_value AND ' . $stFieldName . 'Filters.assigned_user_id ' . $stUserClause . ') 
					OR opportunities.assigned_user_id = "'.$current_user->id.'" ';
				}
					
			}
			//echo '<pre>';print_r($_REQUEST);die;
			//for list view filters has been optimized
			
			if($_REQUEST['action'] == 'index' || $bForParent){
				 $stFinalSql = '
				 			INNER JOIN opportunities_accounts_c on opportunities_accountsopportunities_ida = opportunities.id and opportunities_accounts_c.deleted =0
				 			INNER JOIN accounts filter_accounts on opportunities_accountsaccounts_idb = filter_accounts.id and filter_accounts.deleted =0 ' 
									.$stFilterJoin ;
				
			}else{
				$stFinalSql = 'INNER JOIN ('.$stFilterSql . $stFilterJoin . 'WHERE opportunities.deleted=0 ' . $stWhereClause . ' GROUP BY  opportunities.id)tmpUfilter ON tmpUfilter.id = opportunities.id';
			}
		
		}
		return $stFinalSql;
	}
	/**
	 * Method : isLeadAccessable
	 * params : GUID, bool
	 * return : bool 
	 * Purpose : to check if a record is accesable as per the filters set for
	 * 			current user
	 */	
	public static function isLeadAccessable($stLeadId, $bDieWithError = false) {
		global $db, $current_user;
		$bReturn = false;
		// get the filter sql
		$stFilterSql = self::getLeadFilterClause ();
		
		if ($stFilterSql != '') {
		 	$stGetSql = 'SELECT accLead.id from leads accLead INNER JOIN (' . $stFilterSql . ')filtered on accLead.id = filtered.id OR accLead.assigned_user_id = "'.$current_user->id.'" 
							  where filtered.id ="' . $stLeadId . '" 
							OR accLead.id = "'.$stLeadId.'" ';
			$rsResult = $db->query ( $stGetSql );
			$arResult = $db->fetchByAssoc($rsResult);
			
			if (is_array ( $arResult ) && count ( $arResult ) > 0) {
				$bReturn = true;
			} else {
				$bReturn = false;
			}
		} else {
			//if no filter set allow them to access the lead
			$bReturn = true;
		}
		if (! $bReturn  && ! $bDieWithError) {
			sugar_die ( 'You are not authorized to view this record.' );
		}
		return $bReturn;
	}
	/**
	 * Method : isOpporunityAccessable
	 * params : GUID, bool
	 * return : bool
	 * Purpose : to check if a record is accesable as per the filters set for
	 * 			current user
	 */
	public static function isOpporunityAccessable($stLeadId, $bDieWithError = false,$bForParent=false){
		
		global $db, $current_user;
		$bReturn = false;
		// get the filter sql
		//var_dump($bForParent);
		 $stFilterSql = self::getOpporutnityFilterWehreClause($bForParent) ;
		
		if ($stFilterSql != '') {
			if(!$bForParent){
			 $stGetSql = 'SELECT opportunities.id  from opportunities ' . $stFilterSql . '
						where tmpUfilter.id ="' . $stLeadId . '" OR opportunities.id= "' . $stLeadId . '" ';
			}else{
			 $stGetSql = 'SELECT opportunities.id  FROM opportunities ' . $stFilterSql . '
			where  opportunities.id= "' . $stLeadId . '" ';
			}
			$rsResult = $db->query ( $stGetSql );
			
			$arResult = $db->fetchByAssoc($rsResult);
			if (is_array ( $arResult ) && count ( $arResult ) > 0) {
				$bReturn = true;
			} else {
				$bReturn = false;
			}
		} else {
			$bReturn = false;
		}
		if (! $bReturn && $stFilterSql !== '' && ! $bDieWithError) {
			sugar_die ( 'You are not authorized to view this record.' );
		}
		return $bReturn;
	}
	
	/**
	 * @Method : getBiddersFilterClause
	 * @params : void
	 * @return : sql if filter is set as client
	 * @Purpose : to get bidders list filter caluse
	 */
	function getBiddersFilterClause(){
		
		global $current_user;
		$arMappings = array ('team_member' => 'assigned_user_id'
				, 'zip' => 'billing_address_postalcode'
				, 'county' => 'county_id'
				, 'state' => 'billing_address_state' );
		
		$stFinalSql ='';
		$stFilterJoin = '';
		// check if location filter is saved for this istance
		$admin = new Administration ();
		$admin = $admin->retrieveSettings ( 'instance', true );		
		
		if(isset($admin->settings ['instance_geo_filter']) && $admin->settings ['instance_geo_filter'] == 'client_location') {	
			
			$arAllFilters = self::getCurrentUserFilters ();
			$stSelectedGeoFilter = '';
			
			//echo '<pre>';print_r($arAllFilters);echo '</pre>';
			$stUserClause = ' = "' . $current_user->id . '"';
			$stSelectedGeoFilter = '';
			// get geographic filter value
			if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
					
				$stSelectedGeoFilter = $arAllFilters ['geo_filter_for'] [0];
			}
			// get geographic filter value
			if (isset ( $arAllFilters ['geo_filter_for'] [0] ) && trim ( $arAllFilters ['geo_filter_for'] [0] ) != '') {
			
				//echo $arMappings[$arAllFilters ['geo_filter_for'] [0]];
				
				$stWhereClauseUndef = '';
				foreach ( $arAllFilters as $stFieldName => $arFieldValues ) {
				
					if ($stFieldName == 'classification') {
						// for classifications get the ids of accounts which
						// are matching with selected filter classification
						$stFilterJoin .= 'INNER JOIN  oss_classifion_accounts_c classification on accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0 
						INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi48bbication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' . $stUserClause . ') ';
				
					} elseif ($stFieldName == $stSelectedGeoFilter ) {
						$stFilterJoin .= ' INNER JOIN  oss_user_filters ' . $stFieldName . 'Filters ON (' . $stFieldName . 'Filters.filter_type= "' . $stFieldName . '" AND accounts.' . $arMappings [$stFieldName] . ' =' . $stFieldName . 'Filters.filter_value AND ' . $stFieldName . 'Filters.assigned_user_id ' . $stUserClause . ')
						';
					}
						
				}
			}			
			
					
		}
		return $stFilterJoin;
		
	}	
	
	/**
	 * @Method : isTeamManager
	 * @params : GUID
	 * @return : bool
	 * @Purpose : to check if user has team manager role
	 */
	function isTeamManager($stUid) {
		global $arUserRoleConfig;
		$bContinue = false;
		$arUserRoles = (ACLRole::getUserRoles ( $stUid, 0 ));
		
		// check if this user is not in team manager role
		foreach ( $arUserRoles as $obUserRole ) {
			
			if ($obUserRole->id == $arUserRoleConfig ['team_manager']) {
				$bContinue = true;
			}
		}
		return $bContinue;
	}

}

?>
