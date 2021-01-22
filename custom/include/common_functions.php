<?php
function convertDbDateToTimeZone($date,$timezone){
	global $timedate;
	
	if(trim($date) != ''){
	$db_date_time = $timedate->to_db($date);
	$db_date_time = strtotime($db_date_time);
	$time_zone = $timezone;
	
	switch($time_zone){
		case 'Eastern';
		$gmt_time = date('Y-m-d H:i:s',strtotime('-5 hour',$db_date_time));
		break;
		case 'Central';
		$gmt_time = date('Y-m-d H:i:s',strtotime('-6 hour',$db_date_time));
		break;
		case 'Mountain';
		$gmt_time = date('Y-m-d H:i:s',strtotime('-7 hour',$db_date_time));
		break;
		case 'Pacific';
		$gmt_time = date('Y-m-d H:i:s',strtotime('-8 hour',$db_date_time));
		break;
		default:
			 $gmt_time = date('Y-m-d H:i:s',$db_date_time);
	}
	
	$bid_due_display_date = $timedate->to_display_date_time($gmt_time,true,false);
	}else 
	{
		$bid_due_display_date = $date;
	}
	return $bid_due_display_date;
}

/**
 * @method : get_structure_array
 * @author : Hirak
 * @return : array
 * @purpose : to retun all structure type combined
 */

function get_structure_array(){
        
        global $app_list_strings;
        
        $structure = array();
        
        foreach ($app_list_strings['structure_residential'] as $key => $value) {
            $structure['Residential Building'][$key]= $value;
        }
        foreach ($app_list_strings['structure_non_residential'] as $key => $value) {
           $structure['Non-Residential Building'][$key]= $value;
        }
        
        foreach ($app_list_strings['structure_non_building'] as $key => $value) {
           $structure['Non-Building Construction'][$key] = $value;
        }
        
        return $structure;
}

/**
 * @method  : checkLookupLeadExists
 * @author: Ashutosh
 * @params 	: GUID (Lead Id)
 * @purpose : To check if record exists in lookup table  
 * 
 */
function checkLookupLeadExists($stProjectLeadId){
	global $db;
	$stCheckSQL = 'SELECT project_lead_id id FROM  project_lead_lookup WHERE project_lead_id ="'.$stProjectLeadId.'"';
	
	$rsChkResult = $db->query($stCheckSQL);
	$arChkData = $db->fetchByAssoc($rsChkResult);
	
	return $arChkData; 
}

/**
 * @method 	: updateLeadVersionBidDueDate
 * @@author: Ashutosh
 * @params 	: GUID (Lead Id)
 * @purpose : To Update the look up table for project leads
 * 
 */
function updateLeadVersionBidDueDate($stProjectLeadId){
	
	global $db;
	//get Project Lead Lookup object
	$obLeadsLookup = new project_lead_lookup();
	
	//SQL to get leadversion and min bid due date
	$obLeads = new Lead($stProjectLeadId);
	$obLeads->disable_row_level_security = true;
	$obLeads->retrieve($stProjectLeadId);	
	$stParentLeadId = ($obLeads->parent_lead_id != '')? $obLeads->parent_lead_id : $obLeads->id;
	
	$arChkData = checkLookupLeadExists($stParentLeadId);
	 
	$stLeadVerBidDueSQL = 'SELECT count(leads.id) countt 
								 , coalesce(parent_lead_id,leads.id) leadid 
								 ,min(bids_due) bids_due_grops
								 /* ,if(min(bids_due) = bids_due,bid_due_timezone,null ) bids_due_grops_timezone*/
								 ,GROUP_CONCAT( CONCAT(bids_due," {} ",bid_due_timezone )  ORDER BY bids_due ASC SEPARATOR "$$") bids_due_grops_timezone
							FROM leads WHERE leads.deleted =0 
								and coalesce(parent_lead_id,leads.id)="'.$stParentLeadId.'" 
							GROUP BY coalesce(parent_lead_id,leads.id)';
	
	$rsResult = $db->query($stLeadVerBidDueSQL);
	$arData = $db->fetchByAssoc($rsResult);
	if(isset($arData['bids_due_grops_timezone']))
	{
		//get the timezone of min date
		$arTmp = explode("$$",$arData['bids_due_grops_timezone']);
		$arTmpMinDateZone = explode(' {} ',$arTmp[0]);
		
		$arData['bids_due_grops_timezone'] = $arTmpMinDateZone[1]; 
	}
	
	if(isset($arChkData['id']) && trim($arChkData['id']) == $stParentLeadId) {
		
		$obLeadsLookup->retrieve_by_string_fields(array('project_lead_id'=>$stParentLeadId));
		
		/*$stReplace = "UPDATE project_lead_lookup SET
					lead_version = '".$arData['countt']."'
					,first_bid_due_date = '".$arData['bids_due_grops']."'
					,first_bid_due_timezone = '".$arData['bids_due_grops_timezone']."'
					WHERE  project_lead_id = '".$stParentLeadId."' ";		
		*/
		$obLeadsLookup->first_bid_due_date = $arData['bids_due_grops'];
		$obLeadsLookup->first_bid_due_timezone = $arData['bids_due_grops_timezone'];
		$obLeadsLookup->lead_version = $arData['countt'];
		$obLeadsLookup->save();
		
	}else{
		
		/*$stReplace = "INSERT INTO project_lead_lookup(project_lead_id,lead_version,first_bid_due_date,first_bid_due_timezone)
		VALUES('".$stParentLeadId."','".$arData['countt']."','".$arData['bids_due_grops']."','".$arData['bids_due_grops_timezone']."')";
		*/
		$obLeadsLookup->first_bid_due_date = $arData['bids_due_grops'];
		$obLeadsLookup->first_bid_due_timezone = $arData['bids_due_grops_timezone'];
		$obLeadsLookup->lead_version = $arData['countt'];
		$obLeadsLookup->project_lead_id = $stParentLeadId;
		$obLeadsLookup->save();
	}
	
	//$db->query($stReplace);	
}

/**
 * @method: updateNewTotalBidderCount
 * @author: Ashutosh
 * @params 	: GUID (Lead Id)
 * @purpose : to update the bidders count for project leads
 */

function updateNewTotalBidderCount($stProjectLeadId){
	
	global $db;
	
	$arChkData = checkLookupLeadExists($stProjectLeadId);
	
	//SQL to get the count of new and total bidders for this project lead/parent project lead
	$stGetBidderCount = "SELECT COALESCE(leads.parent_lead_id,leads.id) ldgrpid
	,sum(if(bidders.is_viewed = 0,1,0)) newbidders
	,count(bidders.id) total_bidders
	FROM leads LEFT JOIN oss_leadclientdetail bidders on leads.id = lead_id AND bidders.deleted =0
	WHERE COALESCE(leads.parent_lead_id,leads.id) ='".$stProjectLeadId."' AND leads.deleted=0 GROUP BY coalesce(parent_lead_id,leads.id)";
	
	$rsResult = $db->query($stGetBidderCount);
	$arCountData = $db->fetchByAssoc( $rsResult);
	
	$obLeadsLookup = new project_lead_lookup();
	//set new and total count for this project lead
	if(isset($arChkData['id']) && trim($arChkData['id']) != '')
	{
		/* $stReplace = "UPDATE project_lead_lookup  SET new_bidder ='".$arCountData['newbidders']."'
		 * 				,total_bidder = '".$arCountData['total_bidders']."'
						WHERE project_lead_id = '".$arCountData['ldgrpid']."'	"; */
		$obLeadsLookup->retrieve_by_string_fields(array('project_lead_id'=>$arChkData['id']));
		$obLeadsLookup->new_bidder = $arCountData['newbidders'];
		$obLeadsLookup->total_bidder = $arCountData['total_bidders'];
		$obLeadsLookup->save();
		
	}else
	{
		/* $stReplace = "INSERT INTO project_lead_lookup(project_lead_id,new_bidder,total_bidder) VALUES('".$arCountData['ldgrpid']."','".$arCountData['newbidders']."','".$arCountData['total_bidders']."')";
		 */
		$obLeadsLookup->new_bidder = $arCountData['newbidders'];
		$obLeadsLookup->total_bidder = $arCountData['total_bidders'];
		$obLeadsLookup->project_lead_id = $arChkData['id'];
		$obLeadsLookup->save();
	}
	// 	$db->query($stReplace);
	
}

/**
 * @method : updatePreviousBidTo
 * @author : Ashutosh
 * @param : GUID Lead id
 * @purpose : to update the lookup table for project leads
 * 
 */
function updatePreviousBidToCount($stLeadId){
	/*
	 * This function is no longer in use
	 * we are calculating the previous bid to count dynamically
	 * 
	global $db;
	$stPreBidSQL = 'SELECT		
				COALESCE(leads.parent_lead_id,leads.id) prebidleadid
				,count(DISTINCT prebid.account_id) prebidcount
			FROM leads 
			LEFT JOIN oss_leadclientdetail bid on bid.lead_id = leads.id AND bid.deleted =0
			LEFT JOIN accounts_opportunities prebid on bid.account_id = prebid.account_id AND  prebid.deleted=0
			LEFT JOIN opportunities opp on  opp.id =prebid.opportunity_id AND opp.deleted=0 AND opp.parent_opportunity_id is not null
			WHERE 	leads.deleted=0 AND COALESCE(leads.parent_lead_id,leads.id) = "'.$stLeadId.'"
			GROUP BY prebidleadid';
	$rsResult = $db->query($stPreBidSQL);
	$arCountData = $db->fetchByAssoc($rsResult);
	
	
	$arChkData = checkLookupLeadExists($stLeadId);
	$obLeadsLookup = new project_lead_lookup();
	//set new and total count for this project lead
	if(isset($arChkData['id']) && trim($arChkData['id']) != '')
	{
		$stReplace = "UPDATE project_lead_lookup  SET previous_bid_to ='".$arCountData['prebidcount']."'
						WHERE project_lead_id = '".$arCountData['prebidleadid']."'	";	
		
		
	}else
	{
		$stReplace = "INSERT INTO project_lead_lookup(project_lead_id,previous_bid_to) VALUES('".$arCountData['prebidleadid']."','".$arCountData['prebidcount']."')";
	
	}
	$db->query($stReplace);
	*/
	
}

/**
 * @method : updateOnlineCount
 * @author : Ashutosh
 * @param : GUID Lead id
 * @purpose : to update the lookup table for online links count
 *
 */
function updateOnlineCount($stPlId){
	global $db;
	require_once 'custom/modules/Leads/bbProjectLeads.php';
	$bean = new bbProjectLeads();
	$bean->disable_row_level_security = true;
	
	$bean->retrieve($stPlId);
	$stParentLeadId = ($bean->parent_lead_id != '')? $bean->parent_lead_id : $bean->id;
	
	//get subpanel SQL for this lead
	$stSql = $bean->get_leads_online_plans();
	
	$stCountSql = $bean->create_list_count_query($stSql);
	$rsCounts = $bean->db->query($stCountSql);
	$arCounts = $bean->db->fetchByAssoc($rsCounts);
	$arChkData = checkLookupLeadExists($stParentLeadId);
	$obLeadsLookup = new project_lead_lookup();
	
	if(isset($arChkData['id']) && trim($arChkData['id']) != '')
	{
		$obLeadsLookup->retrieve_by_string_fields(array('project_lead_id'=>$stParentLeadId));
		
	}else{
		$obLeadsLookup->project_lead_id = $stParentLeadId;
	}
	if($arCounts['c']==''){
		$arCounts['c'] = 0;
	}
	
	$obLeadsLookup->online_link_count = $arCounts['c'];
	$obLeadsLookup->save();	
	
	
	
}

/**
 * @method : updateProjectOpportunityBidDueDate
 * @author : Hirak
 * @param : Parent Lead Id
 * @purpose : to update the parent bid due date
 *
 */
function updateProjectOpprBidDueDate($parent_id)
{
	global $db,$current_user;
	
	//$pre_Oppor = new Opportunity();
	//$pre_Oppor->retrieve($parent_id);

	$zoneArray = array();
	$bidDueDate = array();
		
	$SubOpQuery = "SELECT opportunities.date_closed, opportunities.bid_due_timezone
	FROM opportunities
	WHERE opportunities.parent_opportunity_id = '".$parent_id."'
	AND opportunities.deleted = 0";
	$SubOpResult = $db->query($SubOpQuery);
    $countRows = $db->getRowCount($SubOpResult);
    if($countRows >=1) {
	while ($SubOpData = $db->fetchByAssoc($SubOpResult)){
		$bidDueDate[] =  $SubOpData['date_closed'];
		$zoneArray[] = $SubOpData['bid_due_timezone'];
	}
		
	$earlierDate = min($bidDueDate);
	$tmpDates = array_flip($bidDueDate);
	$iTimezoneIndex = $tmpDates[$earlierDate];
		
	//$pre_Oppor->bid_due_timezone = $zoneArray[$iTimezoneIndex];
	//$pre_Oppor->date_closed = $earlierDate;
	//$pre_Oppor->save();
	
	$update_parent_query = "UPDATE opportunities SET bid_due_timezone = '".$zoneArray[$iTimezoneIndex]."',
						 date_closed = '".$earlierDate."' WHERE
						 		id = '".$parent_id."' AND deleted = 0 ";
	
	//get Old Expected Close Date of Project Opportunity.
	$op_sql = "SELECT date_closed,bid_due_timezone FROM opportunities WHERE id='".$parent_id."' AND deleted=0";
	$op_query = $db->query($op_sql);
	$op_result = $db->fetchByAssoc($op_query);
	$old_date_closed_date = $op_result['date_closed'];
	$old_timezone = $op_result['bid_due_timezone'];
	
	insertChangeLog($db, 'opportunities', $parent_id, $old_date_closed_date, $earlierDate, 'date_closed', 'datetimecombo', $current_user->id);
	insertChangeLog($db, 'opportunities', $parent_id, $old_timezone, $zoneArray[$iTimezoneIndex], 'bid_due_timezone', 'enum', $current_user->id);
	
	//Update Date Closed and Time Zone of Project Opportunity.

	$db->query($update_parent_query);
}
}

/**
 * @method : updateProjectOpportunityTeamSet
 * @author : Hirak
 * @param : Parent Lead Id
 * @purpose : to update the parent team set
 *
 */
function updateProjectOpportunityTeamSet($parentId){
	
	global $db, $current_user;
	
	$opp = new Opportunity ();
	$opp->disable_row_level_security = true;
	$opp->retrieve ( $parentId );
		
	$user =  new User();
	$user->disable_row_level_security = true;
	$user->retrieve($opp->assigned_user_id);
	
	$where = " opportunities.parent_opportunity_id = '" . $parentId . "' ";
	$subOpp = $opp->get_full_list ( "", $where );
	
	foreach ( $subOpp as $sOpp ) {
		$sOppr = new Opportunity ();
		$sOppr->disable_row_level_security = true;
		$sOppr->retrieve ( $sOpp->id );
		
	}
	//$opp->team_id = $user->getPrivateTeam();
	
	//$opp->team_set_id = $parent_opprtunity_team_set_id;
		
	//$opp->save();
	
	
	
	//print_r($private_team_id); die;
 	
	$sql = "UPDATE opportunities SET team_id = '".$private_team_id."', team_set_id = '".$parent_opprtunity_team_set_id."' WHERE id = '".$parentId."' ";
	$db->query($sql);
	
	insertChangeLog($db, 'opportunities', $parentId, $opp->team_set_id, $parent_opprtunity_team_set_id, 'team_set_id', 'id', $current_user->id);
}


/**
 * @method : checkExistingClient
 * @author : Hirak
 * @params : name, phone_no,fax_no, email_address 
 * @return : false/client_id
 * @pupose : To check if a client already exist the system whenn pulling / importing
 */
function checkExistingClient($name, $phone_no, $fax_no, $email_address){
	
	global $db;
		
	$name = addslashes(trim($name));
	$phone_no = addslashes(trim($phone_no));
	$fax_no = addslashes(trim($fax_no));
	$email_address = addslashes(trim($email_address));
	
	if(empty($name) && empty($phone_no) && empty($fax_no) && empty($email_address)){
		return;
	}
	
	$client_sql = " SELECT accounts.id FROM accounts ";
	
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$client_sql .= " LEFT JOIN email_addr_bean_rel ear 
				ON  ear.bean_id = accounts.id AND  ear.bean_module = 'Accounts'
				AND ear.deleted = 0 
				LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id 
				AND ea.deleted = 0 ";
	}
	
	$client_sql .= " WHERE ( accounts.name = '" . $name . "' )   ";
	
	$client_sql_or = array ();
	
	if (isset ( $phone_no ) && ! empty ( $phone_no )) {
		$client_sql_or [] = " ( accounts.phone_office = '" . $phone_no . "' )  ";
	}
	if (isset ( $fax_no ) && ! empty ( $fax_no )) {
		$client_sql_or [] = " ( accounts.phone_fax = '" . $fax_no . "' )  ";
	}
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$client_sql_or [] = " ( ea.email_address = '" . $email_address . "' ) ";
	}
	
	if (! empty ( $client_sql_or )) {
		$client_sql .= " AND ( " . implode ( " AND ", $client_sql_or ) . " )";
	}
	
	$client_sql .= " AND accounts.deleted = 0 ";
	
	
	$client_sql .= " ORDER BY accounts.date_entered LIMIT 0,1 ";
	
	//echo $client_sql;
	
	$client_result = $db->query ( $client_sql );
	$client_row = $db->fetchByAssoc ( $client_result );
	$existing_client_id = $client_row ['id'];
	
	if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
		return $existing_client_id;
	}else{
		return false;
	}
	
}

/**
 * @method : checkExistingClientContact
 * @author : Hirak
 * @params : name, phone_no,fax_no, email_address
 * @return : false/client_contact_id
 * @pupose : To check if a client contact already exist the system whenn pulling / importing
 */
function checkExistingClientContact($name, $phone_no, $fax_no, $email_address){

	global $db;
	
	
	$name = addslashes(trim($name));
	$phone_no = addslashes(trim($phone_no));
	$fax_no = addslashes(trim($fax_no));
	$email_address = addslashes(trim($email_address));
	
	if(empty($name) && empty($phone_no) && empty($fax_no) && empty($email_address)){
		return;
	}
	
	$client_contact_sql = " SELECT contacts.id FROM contacts ";
	
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$client_contact_sql .= " LEFT JOIN email_addr_bean_rel ear 
			ON  ear.bean_id = contacts.id AND  ear.bean_module = 'Contacts'
			 AND ear.deleted = 0 
			LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id 
				AND ea.deleted = 0 ";
	}	
		
	$client_contact_sql .= " WHERE ( TRIM(CONCAT_WS(' ', contacts.first_name, contacts.last_name)) = '" . $name . "' )   ";
		
	if (isset ( $phone_no ) && ! empty ( $phone_no )) {
		$client_contact_sql .= " AND  ( contacts.phone_work = '" . $phone_no . "' )  ";
	}
	if (isset ( $fax_no ) && ! empty ( $fax_no )) {
		$client_contact_sql .= " AND ( contacts.phone_fax = '" . $fax_no . "' )  ";
	}
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$client_contact_sql .= " AND ( ea.email_address = '" . $email_address . "' ) ";
	}
		
	$client_contact_sql .= " AND contacts.deleted = 0 ";
		
	$client_contact_sql .= " ORDER BY contacts.date_entered LIMIT 0,1 ";
	
	
	$client_contact_result = $db->query ( $client_contact_sql );
	$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
	$existing_client_contact_id = $client_contact_row ['id'];
	
	if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
		return $existing_client_contact_id;
	}else{
		return false;
	}
	
}

/**
 * @method : get_classification_array
 * @author : Hirak
 * @purpose : get all classification for search dropdown
 * 
 */
function get_classification_array(){
	
	global $db;
	
	$classification = array();
	
	
	/*
	 * Modified by : Ashutosh
	* Date : 4 Jan 2013
	* Purpose : to get instance classificatoins
	*/
	$stGetInstanceClssSQL = "SELECT value FROM config WHERE name ='target_classifications' AND category ='instance'";
	$rsGetInstanceClssSQL = $db->query ( $stGetInstanceClssSQL );
	$arGetInstanceClssSQL = $db->fetchByAssoc ( $rsGetInstanceClssSQL );
	
	if (isset ( $arGetInstanceClssSQL ['value'] ) && trim ( $arGetInstanceClssSQL ['value'] ) != '') {
		$arClsficationIds = json_decode ( base64_decode ( $arGetInstanceClssSQL ['value'] ) );
		//$sql = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters` uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE `filter_type`='classification' AND uf.`deleted`=0";
		$stClasficatoinIds = (count($arClsficationIds) > 0)? ' c.id IN ("'.implode('","',$arClsficationIds).'")':'';
		$sql = "SELECT `id`, `name` FROM oss_classification c WHERE $stClasficatoinIds   AND  c.`deleted`=0";
				
	}else{
		$sql = "SELECT `id`, `name` FROM `oss_classification` WHERE `deleted`=0";
		
	}
	
	$result = $db->query ( $sql );
	$i = 0;
	
	while($row = $db->fetchByAssoc($result)){		
			$classification[$row['id']] = $row['name'];
			
		}
	
	return $classification;
}
/**
 * Function to check if a uploaded document is 
 * supported by the system.
 * @param string $stMimeType
 * @return bool 
 */
function isSupportedDocument($stMimeType){

	global $sugar_config;
	require_once $sugar_config['master_config_path'] ;// '/vol/certificate/master_config.php';
	require_once 'custom/include/master_db/mysql.class.php';
	
	if($stMimeType == ''){
		return false;
	}
	// Connect to Central Schedule DB
	$obDb = new MasterMySQL ( MASTER_HOST, MASTER_USER, MASTER_PASS, MASTER_DB, true );
	$stSql = 'SELECT name FROM oss_documenttypes WHERE deleted=0 AND description = "'.$stMimeType.'"';
	$rsResult = $obDb->query($stSql);
	$iRowcount = $obDb->num_rows($rsResult);
	
	return $iRowcount;
	
}

/**
 * Function to check if a uploaded document is
 * supported by the system.
 * @param string $stMimeType
 * @return bool
 */
function getSupportedDocumentsMIME(){
	 global $sugar_config;
	require_once $sugar_config['master_config_path'] ;// '/vol/certificate/master_config.php';
	require_once 'custom/include/master_db/mysql.class.php';
	
	// Connect to Central Schedule DB
	$obDb = new MasterMySQL ( MASTER_HOST, MASTER_USER, MASTER_PASS, MASTER_DB, true );
	$stSql = 'SELECT name,description FROM oss_documenttypes WHERE deleted=0 ';
	$rsResult = $obDb->query($stSql);
	while($arRow = $obDb->fetch_assoc($rsResult)){
		$arRows[] =$arRow; 
		
	}

	return $arRows;

}

function updateClientsFirstClassification($stClientId){
	
	$stSQL = "SELECT oss_classification .id
    					,oss_classification.description
				 FROM oss_classifion_accounts_c
				 INNER JOIN oss_classification ON oss_classi48bbication_ida  =oss_classification.id AND oss_classification.deleted=0
				WHERE  oss_classid41cccounts_idb = '".$stClientId."' and oss_classifion_accounts_c.deleted =0
				ORDER BY oss_classification .description ASC  LIMIT 1";
	
	$rsResult = $GLOBALS['db']->query($stSQL);
	$arData = $GLOBALS['db']->fetchByAssoc($rsResult);
	 
	if(isset($arData['description'])){
		$stUpdateSQL = 'UPDATE accounts set first_classification = "'.$arData['description'].'" WHERE id= "'.$stClientId.'"';
	}else{
		$stUpdateSQL = 'UPDATE accounts set first_classification = NULL WHERE id= "'.$stClientId.'"';
	}
	$GLOBALS['db']->query($stUpdateSQL);
	
}

function proview_url($params){
	$proview_icon = '';
	if(!empty($params['url'])){

		if (preg_match('/^[^:\/]*:\/\/.*/', $params['url'])) {
			$url= $params['url'];
		} else {
			$url = 'http://' . $params['url'];
		}

		if(preg_match('/bluebook/',$url)){
			if(isset($params['website'])){
				$proview_icon .= "Proview : ";
			}
			$proview_icon .= '<a href="javascript:void(0)"  onclick="window.open(\''.$url.'\',\'\',\'width=925,height=600,scrollbars=yes\')"  ><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
		}else{
			if(isset($params['website'])){
				$proview_icon = $url;
			}
		}
	}

	return $proview_icon;
}

function formatPhoneNumber($messy_phone_no)
{

	$rawField = clean_ph_no($messy_phone_no);
	$len_rawField =strlen($rawField);

	if( $len_rawField >= 7){

		$area =  substr($rawField,0,3);
		$prefix = substr($rawField,3,3);
		$ext = substr($rawField,6,$len_rawField);
		$new_field = $area."-".$prefix."-".$ext;

	}else if($len_rawField >= 4 && $len_rawField < 7){

		$area =  substr($rawField,0,3);
		$ext = substr($rawField,3,$len_rawField);
		$new_field = $area."-".$ext;

	}else{
		$new_field = $rawField;
	}

	return $new_field;

}
function clean_ph_no($text)
{
	$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=',' ','-');
	$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','','','-');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
}

/**
 * Active Change Log Flag for Project Lead
 * @param string $lead_id
 * @param string $db
 */

function changeLogFlag($lead_id,$db){
	
	$sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$lead_id."' AND deleted = 0";
	$db->query($sql);
	//Check for De-duped Project Lead
	$pl_sql = "SELECT parent_lead_id FROM leads WHERE id='".$lead_id."' AND deleted = 0";
	$pl_query = $db->query($pl_sql);
	$pl_result = $db->fetchByAssoc($pl_query);
	if(!empty($pl_result['parent_lead_id'])){
		$sql="SELECT change_log_flag FROM leads WHERE id='".$pl_result['parent_lead_id']."' AND deleted = 0";
		$query = $db->query($sql);
		$result = $db->fetchByAssoc($query);
		if($result['change_log_flag']== 0){
			$sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$pl_result['parent_lead_id']."' AND deleted = 0";
			$db->query($sql);
		}
	}

}

function getCurrentInstanceAccountNo(){
	
	require_once 'modules/Administration/Administration.php';
	$obAdmin = new Administration ();
	$obAdmin->disable_row_level_security = true;
	$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
	$account_no = $arAdminData->settings['instance_account_name'];
	return $account_no;
}

function getRemoteData($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000000000);
	curl_setopt($ch, CURLOPT_TIMEOUT ,3000000000);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

/**
 * Function to Add entry into Change log table.
 * @param string $db
 * @param string $module
 * @param string $bean_id
 * @param string $old_value
 * @param string $new_value
 * @param string $field_name
 * @param string $field_type
 * @param string $modified_by
 */


function insertChangeLog($db, $module, $bean_id, $old_value, $new_value, $field_name, $field_type,$modified_by) {
	$module = strtolower($module);
	if ($old_value != $new_value) {
		$insert_sql = "INSERT INTO ".$module."_audit(`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`)
				VALUES(UUID(),'" . $bean_id . "',UTC_TIMESTAMP(),'" . $modified_by . "','".$field_name."','".$field_type."','" . $old_value . "','" . $new_value . "') ";
		$db->query ( $insert_sql );
	}
}


/**
 * Function to return version from proposal sent count
 * @param int $sent_count
 * @return decimal $version
 */
function create_proposal_version($sent_count = 0){
	
	$first_char = 1;
	if(strlen($sent_count) > 1){
		$first_char =  substr($sent_count,0,1);
	}
	
	$last_char = ($sent_count) * 0.1;
	$version = $first_char + $last_char;

	if(strlen($version) < 3){
		$version = $version.".0";
	}

	return $version;
}

/**
 * check existing client for xml import - new logic
 * @param - string name
 * @param - string phone_no
 * @param - string fax_no
 * @param - string email_address
 */
function checkExistingClientForXMLImport( $name, $phone_no, $fax_no, $email_address ){
	
	global $db;
	$name = addslashes(trim($name));
	$phone_no = addslashes(trim($phone_no));
	$fax_no = addslashes(trim($fax_no));
	$email_address = addslashes(trim($email_address));
	$eight_char_of_name = substr($name, 0, 8);
	
	if(empty($name) && empty($phone_no) && empty($fax_no) && empty($email_address)){
		return;
	}
	
	$client_sql = " SELECT accounts.id, accounts.name FROM accounts ";
	$emailJoin = '';
	$emailWhere = '';
	$phoneFaxWhere = '';
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$emailJoin .= " LEFT JOIN email_addr_bean_rel ear
				ON  ear.bean_id = accounts.id AND  ear.bean_module = 'Accounts'
				AND ear.deleted = 0
				LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id
				AND ea.deleted = 0 ";
		
		$emailWhere .= " WHERE (ea.email_address = '" . $email_address . "') ";
	}
	
	if( !empty($phone_no)  && !empty($fax_no) ){		
		$phoneFaxWhere .= " AND (accounts.phone_office = '" . $phone_no . "' 
								AND  accounts.phone_fax = '" . $fax_no . "') ";
	}	
	
	if($emailJoin != '' || $phoneFaxWhere != ''){
	    if (isset ( $email_address ) && ! empty ( $email_address )) {
	        $query = $client_sql . $emailJoin. $emailWhere." AND accounts.deleted = 0 
	                UNION ".$client_sql . $emailJoin . " WHERE accounts.deleted = 0 ".$phoneFaxWhere;
	    }else{
	        $query = $client_sql . " WHERE accounts.deleted = 0 ". $phoneFaxWhere;
	    }
	    
		$client_result = $db->query ( $query );
		//$GLOBALS['log']->fatal(print_r($query,true));
		$client_row = $db->fetchByAssoc ( $client_result );
		$existing_client_id = $client_row ['id'];
		if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
			return $existing_client_id;
		}
	}

	if( !empty($phone_no) ){
		$client_sql_part = '';
		$client_sql_part .= " WHERE (  accounts.phone_office = '" . $phone_no . "' ) ";
		$query = $client_sql . $client_sql_part. " AND accounts.deleted = 0 ";
		$client_result = $db->query ( $query );
		$client_row_count = $db->getRowCount($client_result);
		if($client_row_count > 1){
			$client_sql_part = '';
			$client_sql_part .= " WHERE (  accounts.phone_office = '" . $phone_no . "' ) 
					AND (accounts.name LIKE '".$eight_char_of_name."%'  ) 
							ORDER BY accounts.date_entered ";
			$query = $client_sql . $client_sql_part. " AND accounts.deleted = 0 ";
			$client_result = $db->query ( $query );
			$client_row = $db->fetchByAssoc ( $client_result );
			$existing_client_id = $client_row ['id'];
			if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
				return $existing_client_id;
			}
		}
		else{
			$client_row = $db->fetchByAssoc ( $client_result );
			$existing_client_id = $client_row ['id'];
			if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
				return $existing_client_id;
			}
		}
		
	}
	
	if( !empty($fax_no) ){
		$client_sql_part = '';
		$client_sql_part .= " WHERE (  accounts.phone_fax = '" . $fax_no . "' ) ";
		$query = $client_sql . $client_sql_part. " AND accounts.deleted = 0 ";
		$client_result = $db->query ( $query );
		$client_row_count = $db->getRowCount($client_result);
		if($client_row_count > 1){
			$client_sql_part = '';
			$client_sql_part .= " WHERE (  accounts.phone_fax = '" . $fax_no . "' )
					AND (accounts.name LIKE '".$eight_char_of_name."%'  ) 
							ORDER BY accounts.date_entered ";
			$query = $client_sql . $client_sql_part. " AND accounts.deleted = 0 ";
			$client_result = $db->query ( $query );
			$client_row = $db->fetchByAssoc ( $client_result );
			$existing_client_id = $client_row ['id'];
			if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
				return $existing_client_id;
			}	
		}
		else{
			$client_row = $db->fetchByAssoc ( $client_result );
			$existing_client_id = $client_row ['id'];
			if(!empty($existing_client_id)  || trim($existing_client_id)!=''){
				return $existing_client_id;
			}
		}
	
	}
	return '';
}

/**
 * check existing client for xml import - new logic
 * @param - string name
 * @param - string phone_no
 * @param - string fax_no
 * @param - string email_address
 */
function checkExistingClientContactForXMLImport( $name, $phone_no, $fax_no, $email_address ){

	global $db;
	$name = addslashes(trim($name));
	$phone_no = addslashes(trim($phone_no));
	$fax_no = addslashes(trim($fax_no));
	$email_address = addslashes(trim($email_address));
	$eight_char_of_name = substr($name, 0, 8);
	
	if(empty($name) && empty($phone_no) && empty($fax_no) && empty($email_address)){
		return;
	}
	
	$client_contact_sql = " SELECT contacts.id FROM contacts ";
	$client_contact_sql_part = '';
	$emailJoin = '';
	$emailWhere = '';
	$phoneFaxWhere = '';
	
	if (isset ( $email_address ) && ! empty ( $email_address )) {
		$emailJoin .= " LEFT JOIN email_addr_bean_rel ear
			ON  ear.bean_id = contacts.id AND  ear.bean_module = 'Contacts'
			 AND ear.deleted = 0
			LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id
				AND ea.deleted = 0 ";
		
		$emailWhere .= " WHERE ( ea.email_address = '" . $email_address . "' ) ";
	}
	
	if( !empty($phone_no)  && !empty($fax_no) ){		
		$phoneFaxWhere .= " AND (  contacts.phone_work = '" . $phone_no . "'
								AND  contacts.phone_fax = '" . $fax_no . "' ) ";
	}
	
	if($emailJoin != '' || $phoneFaxWhere != ''){
	    if (isset ( $email_address ) && ! empty ( $email_address )) {
	        $query = $client_contact_sql . $emailJoin. $emailWhere." AND contacts.deleted = 0 
	                UNION ".$client_contact_sql . $emailJoin . " WHERE contacts.deleted = 0 ".$phoneFaxWhere;
	    }else{
	        $query = $client_contact_sql . " WHERE contacts.deleted = 0 ". $phoneFaxWhere;
	    }
	    
		$client_contact_result = $db->query ( $query );
		//$GLOBALS['log']->fatal(print_r($query,true));
		$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
		$existing_client_contact_id = $client_contact_row ['id'];	
		if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
			return $existing_client_contact_id;
		}
	}
	
	if( !empty($phone_no) ){
		$client_contact_sql_part = '';
		$client_contact_sql_part .= " WHERE ( contacts.phone_work = '" . $phone_no . "' ) ";
		$query = $client_contact_sql . $client_contact_sql_part. " AND contacts.deleted = 0 ";
		$client_contact_result = $db->query ( $query );
		$client_contact_row_count = $db->getRowCount($client_contact_result);
		if($client_contact_row_count > 1){
			$client_contact_sql_part = '';
			$client_contact_sql_part .= " WHERE (  contacts.phone_work = '" . $phone_no . "' )
					AND ( TRIM(CONCAT_WS(' ', contacts.first_name, contacts.last_name)) LIKE '".$eight_char_of_name."%'  )
							ORDER BY contacts.date_entered ";
			$query = $client_contact_sql . $client_contact_sql_part. " AND contacts.deleted = 0 ";
			$client_contact_result = $db->query ( $query );
			$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
			$existing_client_contact_id = $client_contact_row ['id'];	
			if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
				return $existing_client_contact_id;
			}
		}
		else{
			$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
			$existing_client_contact_id = $client_contact_row ['id'];	
			if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
				return $existing_client_contact_id;
			}
		}
	}
	
	if( !empty($fax_no) ){
		$client_contact_sql_part = '';
		$client_contact_sql_part .= " WHERE ( contacts.phone_fax = '" . $fax_no . "' ) ";
		$query = $client_contact_sql . $client_contact_sql_part. " AND contacts.deleted = 0 ";
		$client_contact_result = $db->query ( $query );
		$client_contact_row_count = $db->getRowCount($client_contact_result);
		if($client_contact_row_count > 1){
			$client_contact_sql_part = '';
			$client_contact_sql_part .= " WHERE (  contacts.phone_fax = '" . $fax_no . "' )
					AND ( TRIM(CONCAT_WS(' ', contacts.first_name, contacts.last_name)) LIKE '".$eight_char_of_name."%'  )
							ORDER BY contacts.date_entered ";
			$query = $client_contact_sql . $client_contact_sql_part. " AND contacts.deleted = 0 ";
			$client_contact_result = $db->query ( $query );
			$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
			$existing_client_contact_id = $client_contact_row ['id'];
			if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
				return $existing_client_contact_id;
			}
		}
		else{
			$client_contact_row = $db->fetchByAssoc ( $client_contact_result );
			$existing_client_contact_id = $client_contact_row ['id'];
			if(!empty($existing_client_contact_id) || trim($existing_client_contact_id)!=''){
				return $existing_client_contact_id;
			}
		}
	}
	
	return '';
}



function custom_get_emails_by_assign_or_link($params)
{
	$relation = $params['link'];
	$bean = $GLOBALS['app']->controller->bean;
	if(empty($bean->$relation)) {
		$bean->load_relationship($relation);
	}
	if(empty($bean->$relation)) {
		$GLOBALS['log']->error("Bad relation '$relation' for bean '{$bean->object_name}' id '{$bean->id}'");
		return array();
	}
	$rel_module = $bean->$relation->getRelatedModuleName();
	$rel_join = $bean->$relation->getJoin(array(
			'join_table_alias' => 'link_bean',
			'join_table_link_alias' => 'linkt',
	));
	$rel_join = str_replace("{$bean->table_name}.id", "'{$bean->id}'", $rel_join);
	$return_array['select']='SELECT DISTINCT emails.id ';
	$return_array['from'] = "FROM emails ";
	$return_array['join'] = " INNER JOIN (".
	// directly assigned emails
	"select eb.email_id, 'direct' source FROM emails_beans eb where";
	if (empty($bean->parent_opportunity_id)) {
	    $return_array['join'] .= " eb.bean_module IN ( '{$bean->module_dir}', 'Leads' ) AND eb.bean_id IN ( '{$bean->id}', '{$bean->project_lead_id}' )";	    
	} else {
	    $return_array['join'] .= " eb.bean_module = '{$bean->module_dir}' AND eb.bean_id = '{$bean->id}'";
	}
	$return_array['join'] .= " AND eb.deleted=0 ";
	$return_array['join'] .= " UNION ";
	// Assigned to contacts
	/*$return_array['join'] .= " select DISTINCT eb.email_id, 'contact' source FROM emails_beans eb
	$rel_join AND link_bean.id = eb.bean_id
	where eb.bean_module = '$rel_module' AND eb.deleted=0".
	" UNION ";*/
	// Related by directly by email
	$return_array['join'] .= " select DISTINCT eear.email_id, 'relate' source  from emails_email_addr_rel eear INNER JOIN email_addr_bean_rel eabr";
	if (empty($bean->parent_opportunity_id)) {
	    $return_array['join'] .= " ON eabr.bean_id IN ('{$bean->id}', '{$bean->project_lead_id}') AND eabr.bean_module IN ('{$bean->module_dir}', 'Leads')";	    
	} else {
	   $return_array['join'] .= " ON eabr.bean_id ='{$bean->id}' AND eabr.bean_module = '{$bean->module_dir}'";
	}	
	$return_array['join'] .= " AND
	eabr.email_address_id = eear.email_address_id and eabr.deleted=0 where eear.deleted=0".
	/*" UNION ".
	// Related by email to linked contact
	"select DISTINCT eear.email_id, 'relate_contact' source FROM emails_email_addr_rel eear INNER JOIN email_addr_bean_rel eabr
	ON eabr.email_address_id=eear.email_address_id AND eabr.bean_module = '$rel_module' AND eabr.deleted=0
	$rel_join AND link_bean.id = eabr.bean_id
	where eear.deleted=0".*/
	") email_ids ON emails.id=email_ids.email_id ";
	$return_array['where']=" WHERE emails.deleted=0 ";

	//$return_array['join'] = '';
	$return_array['join_tables'][0] = '';

        if($bean->object_name == "Case" && !empty($bean->case_number)) {
        		$where = str_replace("%1", $bean->case_number, 	$bean->getEmailSubjectMacro());
        		$return_array["where"] .= "\n AND (email_ids.source = 'direct' OR emails.name LIKE '%$where%')";
        }

       return $return_array;
}

/**
 * Get Directory Size of a Directory in bytes
 */
function getDirectorySize($directory)
{
	$dirSize=0;

	if(!$dh=opendir($directory))
	{
		return false;
	}

	while($file = readdir($dh))
	{
		if($file == "." || $file == "..")
		{
			continue;
		}
			
		if(is_file($directory."/".$file))
		{
			$dirSize += filesize($directory."/".$file);
		}
			
		if(is_dir($directory."/".$file))
		{
			$dirSize += getDirectorySize($directory."/".$file);
		}
	}

	closedir($dh);

	return $dirSize;
}

/**
 * Set Update Previous Bid To Flag
 */
function setPreviousBidToUpdate(){
    global $db;
    require_once 'modules/Administration/Administration.php';
    $obAdmin = new Administration ();
    $obAdmin->disable_row_level_security = true;
    $arAdminData = $obAdmin->saveSetting('instance', 'update_prev_bid_to', '1');
}

/**
 * get subpanel data for history 
 * @author Mohit Kumar Gupta
 * @date 26-06-2014
 * @param array $params
 * @return array
 */
function custom_get_history_by_assign_or_link($params = array()){
    $tableName = $params['link'];
    $bean = $GLOBALS['app']->controller->bean;
    $return_array['select']='SELECT DISTINCT '.$tableName.'.id ';
    $return_array['from'] = "FROM ".$tableName." ";
    
    //if record belongs to project opportunity
    if (empty($bean->parent_opportunity_id)) {  
        //meeting and calls also have a separate table for relationship with leads
        //along with own parent id and parent type      
        if ($tableName == 'meetings') {
            $return_array['join'] = " LEFT JOIN meetings_leads ON meetings.id=meetings_leads.meeting_id AND meetings_leads.lead_id='".$bean->project_lead_id."' ";
            $return_array['where'] = "  
            WHERE  (meetings.status='Held' OR meetings.status='Not Held')  AND meetings.deleted =0 AND COALESCE(meetings_leads.deleted,0) =0  
            AND CASE WHEN meetings.parent_type <> 'Leads' AND meetings.parent_id <> '".$bean->project_lead_id."' Then 
            (meetings.parent_type = 'Opportunities' AND meetings.parent_id = '".$bean->id."') OR 
            (meetings_leads.deleted =0 AND meetings_leads.lead_id = '".$bean->project_lead_id."')
            ELSE 
            meetings.parent_id IN ('".$bean->id."', '".$bean->project_lead_id."') 
            AND meetings.parent_type IN ('Opportunities', 'Leads') 
            END";                                                
        } else if ($tableName == 'calls') {
            $return_array['join'] = " LEFT JOIN calls_leads ON calls.id=calls_leads.call_id AND calls_leads.lead_id='".$bean->project_lead_id."' ";
            $return_array['where'] = "  
            WHERE  (calls.status='Held' OR calls.status='Not Held')  AND calls.deleted =0 AND COALESCE(calls_leads.deleted,0) =0  
            AND CASE WHEN calls.parent_type <> 'Leads' AND calls.parent_id <> '".$bean->project_lead_id."' Then 
            (calls.parent_type = 'Opportunities' AND calls.parent_id = '".$bean->id."') OR 
            (calls_leads.deleted =0 AND calls_leads.lead_id = '".$bean->project_lead_id."')
            ELSE 
            calls.parent_id IN ('".$bean->id."', '".$bean->project_lead_id."') 
            AND calls.parent_type IN ('Opportunities', 'Leads') 
            END";
        } else {
            $return_array['where']=" WHERE ( ".$tableName.".parent_id IN ('{$bean->id}', '{$bean->project_lead_id}') AND ".$tableName.".parent_type IN ('{$bean->module_dir}', 'Leads') AND ".$tableName.".deleted=0 )";
        }
    } else {
        $return_array['where']=" WHERE ".$tableName.".parent_id ='{$bean->id}' AND ".$tableName.".parent_type = '{$bean->module_dir}' AND ".$tableName.".deleted=0 ";
    }
    //echo $return_array['select'].$return_array['from'].$return_array['join'].$return_array['where'];
    $return_array['join_tables'][0] = '';
    return $return_array;
}
?>
