<?PHP
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */
require_once('custom/modules/Users/role_config.php');
require_once('modules/oss_user_filters/oss_user_filters.php');

class UsersViewSave_user_filter extends SugarView {

    function UsersViewSave_user_filter() {
    	parent::SugarView();
    }
    /**
     * @uses function to save user filters and flag for clients
     *       the function saves the setting and sends redirect info.
     * @param string $stValue
     * @param boolean $iApplyToClients
     * @modified by : Ashutosh
     * @Date : 27 Jan 2014
     */
    function save_geo_filter_config_setting($stValue,$iApplyToClients) {
        global $db;
        //set resonse text default redirect would be admin 
        $stResponse =  json_encode(array('sucess'=>1,'redirect'=>'admin'));
        
        $admin=new Administration();
        //get instance setting
        $arAdminData = $admin->retrieveSettings ( 'instance', true );
        //if the information is changed then only delete the filters
        if($arAdminData->settings['instance_geo_filter'] != $stValue){
            //remove all filters
            $stDeleteFilters = 'DELETE FROM oss_user_filters ';
            $this->bean->db->query($stDeleteFilters);
            //chagne response text to redirect to filters screen
            $stResponse = json_encode(array('sucess'=>1,'redirect'=>'filters'));
        }     
    	//set instance filter infot
    	$admin->saveSetting('instance','geo_filter',$stValue);
    	//set flag for filters if need to apply to clients   	
    	$admin->saveSetting('instance','geo_filter_for_clients',$iApplyToClients);
    	
    	sugar_die($stResponse);   
    }
    
    function display(){
    	global $db;
    	//if instance filter need to be saved 
    	if(isset($_REQUEST['instance_filter'])&& $_REQUEST['instance_filter'] ==1){
    	    
    	    //check if need to apply the filters on clients
    	 	$iApplyToClients = (isset($_REQUEST['for_clients']) && ( $_REQUEST['for_clients'] =='true'))?'1':'0';	
    		$this->save_geo_filter_config_setting($_REQUEST['filter_value'],$iApplyToClients);
    		   		
    	}
    	
    	    	
    	//save user filters for state
    	$arAllFilters = array_merge(
    				array('state'=>isset($_REQUEST['state_apply'])?$_REQUEST['state_apply']:""),
    				array('county'=> isset($_REQUEST['county_filters'])?$_REQUEST['county_filters']:""),
    				array('zip'=>isset($_REQUEST['zip_filters'])?$_REQUEST['zip_filters']:""),
    				array('type'=>isset($_REQUEST['type_filters'])?$_REQUEST['type_filters']:""),
    				array('classification'=>isset($_REQUEST['classification_filters'])?$_REQUEST['classification_filters']:""),
    				array('labor'=>isset($_REQUEST['labor_filters'])?$_REQUEST['labor_filters']:""),
    				array('team_member'=>isset($_REQUEST['tms_filter'])?$_REQUEST['tms_filter']:""),
    				array('geo_filter_for'=>isset($_REQUEST['geo_filter_for'])?array($_REQUEST['geo_filter_for']):"")
    				);    	      	
    	
    	$arAllFilterClauses = $this->generateFilterWhereClause ( $arAllFilters, $_REQUEST['user'] );
    	//remove all filters for this user
    	$stDeleteFilters = 'DELETE FROM oss_user_filters where assigned_user_id ="'.$_REQUEST['user'].'"';
    	$this->bean->db->query($stDeleteFilters);
    	$bFilterSelected = true;
    	foreach($arAllFilters as $stFilterType => $arValues){
    		
    		if(is_array($arValues) && count($arValues)>0){
	    		foreach ($arValues as $stValue)
	    		{	$bFilterSelected = false;
	    			$obUserFilters = new oss_user_filters();
	    			$obUserFilters->name = 'Filter for '.$stFilterType;
	    			$obUserFilters->filter_value = $stValue;
	    			$obUserFilters->filter_type = $stFilterType;
	    			$obUserFilters->assigned_user_id = $_REQUEST['user'];
	    			$obUserFilters->save();
	    		}
    		}
    	}
    	
    	//save filter join and conditions
    	$obUserFilterClauses = new oss_user_filters();
    	$obUserFilterClauses->name = 'JOIN and Conditions for user ' . $_REQUEST['user'];
    	$obUserFilterClauses->filter_clauses = base64_encode(json_encode($arAllFilterClauses));
    	$obUserFilterClauses->filter_type = 'joins_and_where';
    	$obUserFilterClauses->assigned_user_id = $_REQUEST['user'];
    	$obUserFilterClauses->save ();
    	
    	//remove from teams
    	/* $obTeam = new Team ();
    	if(isset($_REQUEST ['tms']))
    	{
    		
    			
    		$arMyMembership = $obTeam->get_teams_for_user ( $_REQUEST['user'] );
    		
    		foreach ( $_REQUEST ['tms'] as $id ) {
    		
    			$obUserData = new User();
    			$obUserData->retrieve($id);
    			
    			if($obUserData->reports_to_id ==$_REQUEST['user'])
    			{
    				$stUpdate = 'UPDATE users set reports_to_id = NULL WHERE id ='.$db->quoted($id);
    				$db->query($stUpdate);
    			}
    			
    		foreach ( $arMyMembership as $obAssociatedTeam ) {
    	
    			// print_r($obAssociatedTeam->associated_user_id);
    			if ($obAssociatedTeam->id != '1' && $obAssociatedTeam->associated_user_id != $_REQUEST['user']) {
    					
    				$obAssociatedTeam->retrieve ( $obAssociatedTeam->id );
    				$obAssociatedTeam->remove_user_from_team ( $_REQUEST['user'] );
    			}
    				
    		}
    		}
    	} */
		//add to teams
    	/* if(isset($_REQUEST ['tms_filter']))
    	{
    		foreach ( $_REQUEST ['tms_filter'] as $id ) {
    			$obTeam->retrieve_by_string_fields ( array (
    					"associated_user_id " => $id
    			) );
    	
    			$obTeam->add_user_to_team ( $_REQUEST['user'] );
    			
    			//save reports to for this user
    				//set reports to for this user	
				// do not use save method 
				$stUpdate = 'UPDATE users set reports_to_id = '
								. $db->quoted($_REQUEST['user']).' WHERE id ='.$db->quoted($id);
			
				$db->query($stUpdate);
    		}
    	} */
    	
    	
    	//no more redirection check if there are users without any roles 
    	//SugarApplication::redirect('index.php?module=Home&action=index');
    	//get list of users having no filters set 
    	$obUsers = new User();    	
    	$arUserSQL = $obUsers->create_new_list_query('', '',array(),array(),  0,'', true);
    	$arUserSQL['from'] .= ' LEFT JOIN oss_user_filters filters on filters.assigned_user_id = users.id ';
    	$arUserSQL['where'] .= ' AND filters.assigned_user_id is null AND users.is_admin <>1';
    	$userSQL = $arUserSQL['select']." ".$arUserSQL['from']." ".$arUserSQL['where']."  ORDER BY  concat(users.first_name,users.last_name) ASC  ";
    	$arUsers = $obUsers->process_full_list_query($userSQL);
    	$arUsersRemaining = array();
    	$_SESSION['user_filter_diplayed'][$_REQUEST['user']]=1;
    	if(is_array($arUsers))
    	foreach($arUsers as $obFetchedUser){
    		if(!isset($_SESSION['user_filter_diplayed'][$obFetchedUser->id])){
    			$arUsersRemaining[] = $obFetchedUser->id;
    		}
    	}
    	
    	$arResponse = array(	'remaining_users'=>is_array($arUsersRemaining)?count($arUsersRemaining):0
    			,'user_id'=> ( is_array($arUsersRemaining) && isset($arUsersRemaining[0]))?$arUsersRemaining[0]:""
    			,'filter_selected'=>$bFilterSelected=0 );
    	
    	echo json_encode($arResponse);
    	die;
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
    		$arProjectLeadsWhere[] = ' ( leads.zip IN ("'.implode('","',$arAllFilters['zip']).'") )';
    
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
    			'where' => ' AND (' . implode ( ' AND  ', $arProjectLeadsWhere ) . ' OR leads.assigned_user_id = "' . $stUserId . '" )'
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
    				'where' => ' AND (' . implode ( ' AND  ', $arOppListViewWhere ) . ' OR opportunities.assigned_user_id = "' . $stUserId . '" )'
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
    'where' => ' AND (' . implode ( ' AND  ', $arOppListViewWhere ) . '  OR opportunities.assigned_user_id = "' . $stUserId . '")'
    );
    
    /**
    * FILTERS FOR OPPORTUNITY SUMMARY VIEW
    */
    $arWhereClauses ['opportunties'] ['summaryview'] = array (
    		'joins' => implode ( ' ', $arOppSummaryViewJoins ),
    		'where' => ' AND (' . implode ( ' AND  ', $arOppSummaryViewWhere ) . '  OR opportunities.assigned_user_id = "' . $stUserId . '")'
    		);
    
    /**
    * Filters for projectLeads List view
    */
    $arWhereClauses ['leads'] ['listview'] = array (
    'joins' => implode ( ' ', $arProjectLeadsJoins ),
    'where' => ' AND (' . implode ( ' AND  ', $arProjectLeadsWhere ) . '  OR leads.assigned_user_id = "' . $stUserId . '")'
    );
    
    return $arWhereClauses;
    }
}

?>