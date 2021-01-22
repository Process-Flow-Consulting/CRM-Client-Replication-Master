<?php
require_once('custom/modules/Users/role_config.php');
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

function pr($arData) {

	echo '<pre>';
	print_r($arData);
	echo '</pre>';
}

######################### Check County exits #####################################
//NOTE : This block will check which county doesnot exists in selected states
if(isset($_REQUEST['filter_counties']) && $_REQUEST['filter_counties'] == '1'){

	global $db;
	$arCountis = array();
	$arSelCounties = array();
	$stGetCounty = 'SELECT id,name,county_abbr FROM oss_county where county_abbr IN ("'.implode('","',$_POST['state_apply']).'")  ';
	$rsResp = $db->query($stGetCounty);
	if(isset($_POST['county_filters']))
	foreach($_POST['county_filters'] as $values)
	{
		$arSelCounties[]= trim(strtolower($values));
	}
	if($rsResp){
	while($row = $db->fetchByAssoc($rsResp)){
		$stCname = trim((strtolower($row['id'])));
		if(in_array($stCname,$arSelCounties) ){
			$arCountis[$row['id']] = ucfirst(strtolower($row['name']));
		}
	}
	}

	echo json_encode($arCountis );
	die;
}
######################### EOF : Check County exits ###################################


######################### display team members #####################################
//NOTE: this block will be used to display team members container
if(isset($_REQUEST['getTmsContainer'])){

	$arAllUsers = $this->bean->get_full_list('',' users.is_admin=0');
	foreach($arAllUsers as $obUsers){
		$bContinue = false;
		//if its a team manager escape
		if(isTeamManager($obUsers->id)){
			continue;
		}
		$arTeamMembers[$obUsers->id]= $obUsers->name;
	}
	$this->ss->assign('DOM_TEAMMEMBERS',$arTeamMembers);
	$this->ss->display('custom/modules/Users/tpls/team_members.tpl');
	die;

}
######################### EOF:display team members #####################################

######################### display Full Details #####################################
##NOTE: This request might be from users edit view or user wizard
###     any further modifications should be perfomed to consider both
###     actions.
if(isset($_REQUEST['getFullDetails'])){
	
	$this->ss->assign('GEO_OPTION', array(
			'project_location' => 'Project Information',
			'client_location' => 'Client Information',
	));
	
	//check if location filter is saved for this istance
	$admin=new Administration();
	$admin=$admin->retrieveSettings('instance',true);
	//pr($admin->settings);die;
	if(isset($_REQUEST['geofilters']) || !isset($admin->settings['instance_geo_filter']) || $admin->settings['instance_geo_filter'] == ''){
		//display select geo filter screen
		$this->ss->assign('GEO_OPTION_SELECTED',$admin->settings['instance_geo_filter']);
		$this->ss->assign('GEO_OPTION_SELECTED_FOR_CLIENTS',$admin->settings['instance_geo_filter_for_clients']);
		$this->ss->display('custom/modules/Users/tpls/geo_filters.tpl');
		die;		
	}else{
			//set saved selected GEO filter option
			$this->ss->assign('GEO_OPTION_SELECTED',$admin->settings['instance_geo_filter']);
			$this->ss->assign('GEO_OPTION_SELECTED_FOR_CLIENTS',$admin->settings['instance_geo_filter_for_clients']);
	}
	
	$obUsers = new User();
	$arAllUsers =  $obUsers->get_full_list(" concat(users.first_name,users.last_name) ASC",' users.is_admin=0 and users.deleted = 0' );
	//get all the roles
	foreach($arAllUsers as $obUserData){
		$arUserDom[$obUserData->id] = $obUserData->name;
		$arUserRoles[$obUserData->id] = array_shift(ACLRole::getUserRoles($obUserData->id));
	}
	
	$this->ss->assign('AR_URER_ROLE_MAP',json_encode($arUserRoles));
	//if its from user edit view then set role DOM user name
	if(isset($_REQUEST['showFiltersOnly'])){
		$this->ss->assign('USER_EDIT_VIEW',true);

		//create role dom
		$obRoles = new ACLRole();
		$stRoleDb = array_shift(ACLRole::getUserRoles($_REQUEST['record']));
		$stSelectedRole ='';

		//create dropdown for roles
		foreach($arUserRoleConfig as $stRole =>  $stRoleid){

			$obRoles->retrieve($stRoleid);
			$arRoleDOM[$stRole] = $obRoles->name;
			//determine which role this user is in
			if($stRoleDb == $obRoles->name){
				$stSelectedRole = $stRole;
			}
		}

		$this->ss->assign('SELECTED_ROLE',$stSelectedRole);
		$this->ss->assign('IS_TEAM_MEMBER',$stSelectedRole);
		$this->ss->assign('DOM_USER_ROLES',$arRoleDOM);
		$this->ss->assign('USER_NAME',$this->bean->name);
	}else{
		//else its from user wizard
		
		$this->ss->assign('DOM_USERS',$arUserDom);
		$arUserIds = array_keys($arUserDom);
		$stFilterSelectedUser = (trim($this->bean->id) != '')?$this->bean->id: $arUserIds[0];
		
		$this->ss->assign('SELECTED_USER_ROLE',array_shift(ACLRole::getUserRoles($stFilterSelectedUser)));
		
		$this->ss->assign('SELECTED_USER',$stFilterSelectedUser);



	}

	$arFilterSaved = array(
	'state'=>array()
	,'county'=>array()
	,'zip'=>array()
	,'type'=>array()
	,'labor'=>array()
	,'classification'=>array()
	,'team_member'=>array()
	,'geo_filter_for' => array()
	);

	//get filter details for this user
	$obUserFilters = new oss_user_filters();
	$arAllFilters = $obUserFilters->get_full_list('',' oss_user_filters.assigned_user_id ="'.$this->bean->id.'"');
	if($arAllFilters){
		foreach($arAllFilters as $obUserFilter)
		{
			$arFilterSaved[$obUserFilter->filter_type][]= $obUserFilter->filter_value;
		}
	}
	
	
	$stSelectedFilters = isset($arFilterSaved['geo_filter_for'][0])?$arFilterSaved['geo_filter_for'][0]:'state';
	
	$this->ss->assign('GEO_FILTER_FOR',$stSelectedFilters);
	$this->ss->assign('DOM_LABOUR_OTIONS',array('Union','Non Union','Prevaling Wage','Undefined'));
	//pr($arFilterSaved);
	//set selected options for state
	$arSelState = $arFilterSaved['state'];

	global $arTmpSelState;
	$fun = function($key){
		global $arTmpSelState;
		$arTmpSelState[$key] = $GLOBALS['app_list_strings']['state_dom'][$key];
		return $arTmpSelState;
	};
	array_map($fun,$arSelState);
	//check if there are
	$arRmainingStats = (count($arTmpSelState)>0) ?array_diff_assoc($GLOBALS['app_list_strings']['state_dom'],$arTmpSelState):$GLOBALS['app_list_strings']['state_dom'];
	$this->ss->assign('DOM_STATE', $arRmainingStats);
	//now we are displaying all the STATES FOR COUNTY
	//$this->ss->assign('DOM_STATE', $GLOBALS['app_list_strings']['state_dom']);
	$this->ss->assign('STATE_OPTOIONS',$arTmpSelState);


	//set selected options for Type
	$arSelState = $arFilterSaved['type'];
	$arTmpSelType = array();

	global $arTmpSelType;
	$fun = function($key){
		global $arTmpSelType;
		$arTmpSelType[$key] = $GLOBALS['app_list_strings']['project_type_dom'][$key];
		return $arTmpSelType;
	};
	array_map($fun,$arSelState);
	$arRmainingType = (count($arTmpSelType)>0) ?array_diff_assoc($GLOBALS['app_list_strings']['project_type_dom'],$arTmpSelType):$GLOBALS['app_list_strings']['project_type_dom'];
	$this->ss->assign('DOM_TYPE_PL',$arRmainingType);
	$this->ss->assign('TYPE_PL_OPTIONS',$arTmpSelType);

	//set selected options for county
	$arCountySelectedOptions =(count($arFilterSaved['county'])>0)? getUserCounties($arFilterSaved['county']):array();

	$this->ss->assign('COUNTY_OPTOIONS',$arCountySelectedOptions);
	//set selected options for zip
	$arZipSelectedOptions =( count($arFilterSaved['zip'])>0)? array_combine(array_values($arFilterSaved['zip']),array_values($arFilterSaved['zip'])):array();
	$this->ss->assign('ZIP_OPTOIONS',$arZipSelectedOptions);
	//set selected options for zip
	$arTypeSelectedOptions =( count($arFilterSaved['type'])>0)? array_combine(array_values($arFilterSaved['type']),array_values($arFilterSaved['type'])):array();
	$this->ss->assign('TYPE_OPTOIONS',$arTypeSelectedOptions);
	//set selected options for zip

	$this->ss->assign('LABOUR_OPTOIONS',array_values($arFilterSaved['labor']));
	$this->ss->assign('IS_TEAM_MANAGER',isTeamManager($this->bean->id));
	//get selected classificateions
	if(count($arFilterSaved['classification'])>0){
		$obClassification = new oss_Classification();
		$arClassificationList =  $obClassification->get_full_list('','oss_classification.id IN ("'.implode('","',$arFilterSaved['classification']).'")');
		foreach($arClassificationList as $obClassficationData )
		{
			$arClassificationDom[$obClassficationData->id]= $obClassficationData->name;
		}
		$this->ss->assign('CLASSIFICATION_OPTOIONS',$arClassificationDom);
	}
	$arTeamMembers=array();
	//get selected Team Members
	if(isTeamManager($this->bean->id)){

		//get all team members
		$arAllUsers = $this->bean->get_full_list('',' users.is_admin =0 and users.status ="Active"');
		foreach($arAllUsers as $obUsers){
			$bContinue = false;
			//if its a team manager escape
			if(isTeamManager($obUsers->id)){
				continue;
			}

			$arTeamMembers[$obUsers->id]= $obUsers->name;
		}
		//pr($arAllUsers);
		$this->ss->assign('DOM_TEAMMEMBERS',$arTeamMembers);
		$arRemainingUserDom = $arTeamMembers;
		
		
		#### GET SAVED TMs List ####
		### this list will be generated from the saved teams with this team manager,
		###	 all owner of teams associated to this team mangers team
		### will be filtered
		//get this TMs team
		$obUserTeam = new Team();
		$obUser = new User();
		//for wizard to not display this user in next 
		$_SESSION['user_filter_diplayed'][$this->bean->id] =1;
		$arMyMembership = $obUserTeam->get_teams_for_user($this->bean->id);
		$arSelectedUsersDom = array();
		
		foreach ($arMyMembership as $obAssociatedTeam) {

			
			if(trim($obAssociatedTeam->associated_user_id) != '' && $obAssociatedTeam->associated_user_id != $this->bean->id){
				
				$obUser->retrieve($obAssociatedTeam->associated_user_id);
				$arSelectedUsersDom[$obUser->id]= $obUser->name;

			}

		}
if(isset($arSelectedUsersDom[0]) && trim($arSelectedUsersDom[0]) == ''){
	unset($arSelectedUsersDom[0]);
}

		$this->ss->assign('TEAM_MEMBER_OPTOIONS',$arSelectedUsersDom);
		$arRemainingUserDom = (count($arSelectedUsersDom)>0) ?array_diff_assoc($arRemainingUserDom,$arSelectedUsersDom):$arRemainingUserDom;
		$this->ss->assign('DOM_TEAMMEMBERS',$arRemainingUserDom);
	}
	//get list of users having no filters set
	$obUsers = new User();
	
	$arUserSQL = $obUsers->create_new_list_query('', '',array(),array(),  0,'', true);
	$arUserSQL['from'] .= ' LEFT JOIN oss_user_filters filters on filters.assigned_user_id = users.id ';
	$arUserSQL['where'] .= ' AND filters.assigned_user_id is null AND users.is_admin <>1 ';
	$userSQL = $arUserSQL['select']." ".$arUserSQL['from']." ".$arUserSQL['where'].' ORDER BY full_name ASC ';
	$arUsers = $obUsers->process_full_list_query($userSQL);	
	$arUsersRemaining = array();
	
	foreach($arUsers as $obFetchedUser){
		if(!isset($_SESSION['user_filter_diplayed'][$obFetchedUser->id]) ){
			$arUsersRemaining[] = $obFetchedUser->id;
		}
	}

	$this->ss->assign('FILTERED_NOT_APPLIED_USR_COUNT',count($arUsersRemaining));
	
	
	
	echo $this->ss->display('custom/modules/Users/tpls/user_filters.tpl');

	die;
}


################################################################################
###### 				Fucntions used by handle requrest					########	 
################################################################################


function isTeamManager($stUid){
	global $arUserRoleConfig;
	$bContinue = false;
	$arUserRoles = (ACLRole::getUserRoles($stUid,0));
	//check if this user is not in team manager role
	foreach ($arUserRoles as $obUserRole){

		if($obUserRole->id == $arUserRoleConfig['team_manager']){
			$bContinue = true;
		}
	}
	return $bContinue;
}

function getUserCounties($arFilterSaved){
	$arReturn = array();
	
	$obCounty = new oss_County();
	$arData = $obCounty->get_full_list(' oss_county.name ASC',' oss_county.id IN ("'.implode('","',$arFilterSaved).'")');
	if(count($arData)){
		foreach ($arData as $obUserCounty)
		{
			$arReturn[$obUserCounty->id] = ucfirst(strtolower($obUserCounty->name));
		}
	}
	return  $arReturn;
}
//save filters setting

/**
 * @author Mohit Kumar Gupta
 * @date 23-06-2015
 * used to validate user's email by ajax request from user's profile 
 */
if (isset($_REQUEST['checkUserEmail']) && $_REQUEST['checkUserEmail'] == '1') {
    global $db;
	$emailFlag = 0;
	if(isset($_REQUEST['userEmailId']) && !empty($_REQUEST['userEmailId'])){
	    $emailQuery = "SELECT count(email_addresses.id) total 
	            FROM 
	            email_addresses 
	            INNER JOIN 
	            email_addr_bean_rel 
	            ON 
	            email_addresses.id=email_addr_bean_rel.email_address_id AND email_addr_bean_rel.bean_module='Users' 
	            WHERE 
	            email_addresses.deleted='0' AND email_addr_bean_rel.deleted='0'";
	    $emailQuery .= " AND email_addresses.email_address_caps=".$db->quoted(strtoupper($_REQUEST['userEmailId']));
	    if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])){
	        $emailQuery .= " AND email_addr_bean_rel.bean_id !=".$db->quoted($_REQUEST['userId']);
	    }	    
	    $emailQueryRes = $db->query($emailQuery);
	    $emailQueryData = $db->fetchByAssoc($emailQueryRes);
		if ($emailQueryData['total'] > 0) {
			$emailFlag = $emailQueryData['total'];
		}
	}
	echo $emailFlag;
	return ''; 
}
?>