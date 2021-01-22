<?php
//ini_set('display_errors','1');
require_once 'custom/include/common_functions.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/include/dynamic_dropdown.php';
require_once ('modules/Import/ImportFile.php');
require_once ('modules/Import/ImportFileSplitter.php');
require_once ('modules/Import/ImportCacheFiles.php');
require_once ('modules/Import/ImportDuplicateCheck.php');
require_once ('include/upload_file.php');

class ImportReed {
	private $db;
	private $sugar_config;
	private $current_user;
	private $data;
	private $import_module;
	private $importObj;
	private $fname;
	private $list_value; 
	
	/**
	 * @function : __construct()
	 * @desc: Initialize all the variables.
	 */
	function __construct($xmlObj,$importFileName) {
		global $db, $sugar_config, $current_user;
		$this->db = $db;
		$list_value = array();
		$this->sugar_config = $sugar_config;
		$this->current_user = $current_user;
		$this->data = $xmlObj;
		$this->import_module = 'Leads';
		$this->fname = $importFileName;
		$this->is_bidder_modified = false;
		$this->bidder_row_count = 0;
	}
	
	/**
	 * @function : insertData()
	 * @desc : Build xml array, call and pass xml data to different method to import.
	 **/
	function insertData(){
		
		$importObj = new ImportFile($this->fname);
		
		foreach ( $this->data as $record ) {
			
			//create / update leads
			$lead_id = $this->insertProjectLead ( $record, $importObj );
			
			//Lead URL Plans
			if(isset($arValue['ProjectURL']) && !empty($record['ProjectURL'])){
				$onine_plans = new oss_OnlinePlans();
				//check if the online plan
				$existing_online_plans = $onine_plans->retrieve_by_string_fields(array('description' => $record['ProjectURL'], 'lead_id' => $lead_id));
			
				if (empty($existing_online_plans->id)) {		
					$onine_plans->plan_type = 'Other';
					$onine_plans->plan_source = 'Reed';
					$onine_plans->lead_id = $lead_id;
					$onine_plans->description = $record['ProjectURL'];
					$onine_plans->save();
				}
			}
			
			//create  / update clients
			foreach ( $record ['bidders'] as $bidder ) {
				
				//create / update client
				$client_id = $this->insertClient ( $bidder, $importObj );
				
				//create / update client contact
				if( isset( $bidder ['ContactName'] ) && !empty ( $bidder ['ContactName'] ) ) {
					$contact_id = $this->insertClientContact ( $bidder, $client_id, $importObj );
				}else{
					$contact_id = '';
				}
				
				//create / update bidders list
				$this->insertBidderList ( $lead_id, $client_id, $contact_id, $bidder, $importObj );
				
				//get parent lead id if any
				$parent_lead_id = $this->getParentLeadId ( $lead_id );
				
				// Bidder list logic hooks
				$this->updateNewTotalBidderCountBBH ( $parent_lead_id );
				$this->updateLeadVersionBidDueDateBBH ( $parent_lead_id );
				
				// Update online plans
				updateOnlineCount ( $lead_id );	
			}
			
			//if bidder modified add ebtry to change log
			if($this->is_bidder_modified && ($this->bidder_row_count != 0) ){
				$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='".$lead_id."' AND `field_name`='Bidders List Modification'";
				$query = $this->db->query($sql);
				$result = $this->db->fetchByAssoc($query);
				$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'".$lead_id."',UTC_TIMESTAMP(),'".$current_user->id."','Bidders List Modification','datetimecombo','".$result['before_date']."',UTC_TIMESTAMP());";
				$this->db->query($insertSQL);
			}
		
		}
		
		//set update prev bid to flag
		setPreviousBidToUpdate();
		
		//write csv status file to display new records of after imported.
		$importObj->writeStatus ();
	}
	
	/**
	 * @function: insertProjectLead()
	 * @param: array record
	 * @param: object importfile.
	 * @return: string lead id
	 */
	function insertProjectLead($record,$importObj){

		global $app_list_strings, $timedate, $db;
		
		$lead = new Lead ();
		$reed_id = trim ( $record ['ProjectID'] );
		
		$publish_date = $record['UpdateDate'];
		
		$lead->reed_id = $reed_id;
		$newLeadRecord = true;
		
		// check if the mi_lead_id alread exist then assign lead to modify
		$existing_lead_id = $this->checkExistingRecord( 'leads', 'reed_id', $reed_id );
		
		if (!empty($existing_lead_id)) {
			$lead->id = $existing_lead_id;
			$newLeadRecord = false;
			$lead->retrieve($lead->id);
			$date_entered = $timedate->to_db($lead->date_entered);
			
			$sqlLeadClientCount = "SELECT count(1) as row_count FROM  oss_leadclientdetail WHERE deleted = '0' AND lead_id='".$existing_lead_id."'";
			$res_count = $this->db->query($sqlLeadClientCount);
			$row_count = $this->db->fetchByAssoc($res_count);
			$this->bidder_row_count = $row_count['row_count'];
			if(count($record ['bidders']) > $row_count['row_count']){
				$this->is_bidder_modified = true;
			}
		}
			
		if(empty($existing_lead_id) || !empty( $date_entered ) 
												|| ($date_entered < $publish_date )){
		
		
			if(!empty($existing_lead_id) && !empty( $date_entered ) 
										&& ($publish_date > $date_entered )){
				$lead->id = $existing_lead_id;
				$newLeadRecord = false;
			}
		
			//Project Lead Info
			$lead->project_title = $record ['Title'];
			$lead->last_name = $record ['Title'];
			$lead->project_lead_id = $record ['ProjectID'];
			$lead->lead_source = 'reed';
			$lead->valuation = $record ['Value'];
			
			// Project Lead Address
			$lead->address = $record ['AddressLine1'];
			
			//Find county id
			$lead->county_id = $this->getCounty($record ['County'], $record ['StateProvince']);
			
			$lead->city = $record ['City'];
			$lead->state = $record ['StateProvince'];
			$lead->zip_code = $record ['ZipPostalCode'];
			
			// Project Lead Status
			$lead->project_status = $record ['ContractType'];	
			if (! empty ( $lead->project_status )) {
				$arrCompare = $app_list_strings ['project_status_dom'];
				if (! array_key_exists ( $this->clean_text ( $lead->project_status ), $arrCompare )) {
					$list_value = '["' . $this->clean_text ( $lead->project_status ) . '","' . $lead->project_status . '"]';
					$lead->project_status = $this->clean_text ( $lead->project_status );
					$this->editDropdownList ( "project_status_dom", $list_value );
				}
			}
			$lead->project_status = $this->clean_text ( $lead->project_status );
			
			// set bid due timezone according to state
			$state = $app_list_strings ['state_dom'] [$lead->state];
			$lead->bid_due_timezone = $app_list_strings ['state_tz_dom'] [$state];
			
			// convert to db date
			$bid_date = $record ['StageComment'];
			$bid_due_date = str_replace ( 'Contract #:  Bid Due Date: ', '', $bid_date );
			
			$stBidsDue = (trim ( $bid_due_date ) != '') ? strtotime ( $bid_due_date ) : '';
				
			if (trim ( $stBidsDue ) != '') {
				global $timedate;
				$db_date_time = date ( 'Y-m-d H:i:s', $stBidsDue );
				$userDateTime = $timedate->to_display_date_time ( $db_date_time, true, false );
			
				$oss_timedate = new OssTimeDate ();
				$gmt_time = $oss_timedate->convertDateForDB ( $userDateTime, $lead->bid_due_timezone );
				$lead->bids_due = $gmt_time;
			}
			
			//Project Lead Owner
			$lead->owner = $record ['Ownership'];
			if (! empty ( $lead->owner )) {
				$arrCompare = $app_list_strings ['owner_dom'];
				if (! array_key_exists ( $this->clean_text ( $lead->owner ), $arrCompare )) {
					$list_value = '["' . $this->clean_text ( $lead->owner ) . '","' . $lead->owner . '"]';
					$lead->owner = $this->clean_text ( $lead->owner );
					$this->editDropdownList ( "owner_dom", $list_value );
				}
			}
			$lead->owner = $this->clean_text ( $lead->owner );
			
			//Project Lead Type
			$lead->type = $this->matchTypeDom ( $record ['WorkType'] );
			
			if(empty($lead->type) && (!empty($record['WorkType'])) ){
				$arrCompare = $app_list_strings ['project_type_dom'];
				if (! array_key_exists ( $this->clean_text ($record['WorkType'] ), $arrCompare )) {
					$list_value = '["' . $this->clean_text ($record['WorkType'] ) . '","' . $record['WorkType'] . '"]';
					$lead->type = $this->clean_text ( $record['WorkType'] );
					$this->editDropdownList ( "project_type_dom", $list_value );
				}
			}
			$lead->type = $this->clean_text ( $lead->type );
			
			//Project Lead Structure
			$lead->structure = $this->matchStructureDom ( $record ['SubCategory'] );
			if (empty ( $lead->structure ) && ! empty ( $record ['SubCategory'] )) {
				$list_value = '["' . $this->clean_text ( $record ['SubCategory'] ) . '","' . $record ['SubCategory'] . '"]';
				$lead->structure = $this->clean_text ( $record ['SubCategory'] );
				$this->editDropdownList ( "structure_non_building", $list_value );
			}

			// Additional Info
			$lead->scope_of_work = $record ['DetailText'];
			$record_date = $record ['RecordDate'];
			$lead->received = date ( 'Y-m-d', strtotime ( $record_date ) );
			
			//create / update leads
			/******************************************************************/
			$lead->populateDefaultValues();
			if ( !isset($lead->assigned_user_id) || $lead->assigned_user_id == '' && $newLeadRecord ) {
				$lead->assigned_user_id = $this->current_user->id;
			}
			if ( !isset($lead->team_id) || $lead->team_id == '' && $newLeadRecord ) {
				$lead->team_id = 1;
			}
			
			$lead->save();
		
			//save import info
			ImportFile::markRowAsImported($newLeadRecord);
			if ( $newLeadRecord)
				ImportFile::writeRowToLastImport($this->import_module,$lead->object_name,$lead->id);
			/****************************************************************/
		}

		return ($lead->id);
	}
	
	/**
	 * @function: insertClient
	 * @param: array bidder
	 * @param: string dodge unique id
	 * @param: object importfile
	 * @return: string client id
	 */
	function insertClient($bidder, $importObj) {
		
		
		
		global $app_list_strings, $timedate, $db;
		
		// other then ownner all are bidders
		$client = new Account ();
		$new_client = true;
		$reed_id = $bidder ['CompanyID'];
		
		//ph no and fax no
		$bidder ['PhoneNumber'] = $this->ph_field_clean_text($bidder ['PhoneNumber']);
		$bidder ['FaxNumber'] = $this->ph_field_clean_text($bidder ['FaxNumber']);

		
		//check for existing client
		$existing_client_id = $this->checkExistingRecord('accounts', 'reed_id', $reed_id);
		
		if(empty($existing_client_id)){
			$existing_client_id = checkExistingClientForXMLImport(
					$bidder ['CompanyName'],
					$bidder ['PhoneNumber'],
					$bidder ['FaxNumber'],
					$bidder ['email-id']
			);
		}
		
		
		if (!empty ( $existing_client_id )) {

			$new_client = false;
			
			$client->id = $existing_client_id;
			$client->retrieve ( $existing_client_id );
			$client->reed_id = $reed_id;
			
			//flag for locally modified
			$bClientLoacallyModified = $client->is_modified;
			
			//if this client is linked with bluebook 
			//then only balnk fileds will be updated 
			if(trim($client->mi_account_id) != ''){			    
			    $bClientLoacallyModified = true;
			}
			//if name empty in crm update name 
			if(empty($client->name) || !$bClientLoacallyModified ) $client->name = $bidder ['CompanyName'];
			//if address1 empty in crm update address1
			if(empty($client->address1)  || !$bClientLoacallyModified  ) $client->address1 = $bidder ['AddressLine1'];
			//if address2 empty in crm update address2
			if(empty($client->address2) || !$bClientLoacallyModified ) $client->address2 = $bidder ['AddressLine2'];
			//if city empty in crm update city
			if(empty($client->billing_address_city) || !$bClientLoacallyModified ) $client->billing_address_city = $bidder ['City'];
			//if state empty in crm update state
			if(empty($client->billing_address_state) || !$bClientLoacallyModified ) $client->billing_address_state = $bidder ['StateProvince'];
			//if zip code empty in crm update zip code
			if(empty($client->billing_address_postalcode) || !$bClientLoacallyModified ) $client->billing_address_postalcode = $bidder ['ZipPostalCode'];
			//if phone empty in crm update phone
			if(empty($client->phone_office) || !$bClientLoacallyModified ) $client->phone_office = $bidder['PhoneNumber'];
			//if fax empty in crm update fax
			if(empty($client->phone_fax) || !$bClientLoacallyModified ) $client->phone_fax = $bidder['FaxNumber'];
		
			//update client
			$client->save ();
			
		}else{
			
			$client->name = $bidder ['CompanyName'];
			$client->address1 = $bidder ['AddressLine1'];
			$client->address2 = $bidder ['AddressLine2'];
			$client->billing_address_city = $bidder ['City'];
			$client->billing_address_state = $bidder ['StateProvince'];
			$client->billing_address_postalcode = $bidder ['ZipPostalCode'];
			$client->phone_office = $bidder['PhoneNumber'];
			$client->phone_fax = $bidder['FaxNumber'];
			// additional info
			$client->reed_id = $reed_id;
			$client->lead_source = 'reed'; 
			
			//create client
			/******************************************************************/
			$client->populateDefaultValues ();
			if (! isset ( $client->assigned_user_id ) || $client->assigned_user_id == '' && $new_client) {
				$client->assigned_user_id = $current_user->id;
			}
			if (! isset ( $client->team_id ) || $client->team_id == '' && $new_client) {
				$client->team_id = 1;
			}
			
			$client->assigned_user_id = '';
			$client->team_id = '';
			$client->team_set_id = '';
			//do not show the client
			$client->visibility = 0;
			
			$client->save ();
			
			//save import info
			ImportFile::markRowAsImported ( $new_client );
			if ($new_client)
				ImportFile::writeRowToLastImport ( $this->import_module, $client->object_name, $client->id );
			/******************************************************************/
		}
		
		return ($client->id);
	}
	
	/**
	 * @function: insertClientContact
	 * @param: array bidder.
	 * @param: string client id
	 * @param: string dodge uinque id 
	 * @param: object importfile
	 * @return: string contact id
	 */
	function insertClientContact($bidder, $client_id, $importObj) {
		
		global $app_list_strings, $timedate, $db;

		$contact = new Contact();
		$new_contact = true;
		
		$reed_id = $bidder ['CompanyID']; //no unique contact id so we are using client unique id
		
		//ph no and fax no
		$bidder ['PhoneNumber'] = $this->ph_field_clean_text($bidder ['PhoneNumber']);
		$bidder ['FaxNumber'] = $this->ph_field_clean_text($bidder ['FaxNumber']);

		//check for the existing client contact
		$existing_client_contact_id = checkExistingClientContactForXMLImport(
				$bidder ['ContactName'], 
				$bidder['PhoneNumber'], 
				$bidder['FaxNumber'], 
				$bidder ['email-id']
		);
		
		list($bidder['first_name'], $bidder['last_name']) = $this->splitName($bidder ['ContactName']);

		if( !empty ( $existing_client_contact_id ) ){
			$new_contact = false;
			
			$contact->id = $existing_client_contact_id;
			$contact->retrieve($existing_client_contact_id);
			$contact->reed_id = $reed_id;
			
			//check for locally modified
			$bContactLocallyModified = $contact->is_modified;
			
			//if this client Contact is linked with bluebook
			//then only balnk fileds will be updated
			if(trim($contact->mi_contact_id) != ''){
			    $bContactLocallyModified = true;
			}
			//if first name empty in crm update first name
			if(empty($contact->first_name) || !$bContactLocallyModified ) $contact->first_name = $bidder ['first_name'];
			//if last name empty in crm update last name
			if(empty($contact->last_name) || !$bContactLocallyModified) $contact->last_name = $bidder ['last_name'];
			//if phone empty in crm update phone
			if(empty($contact->phone_work) || !$bContactLocallyModified) $contact->phone_work = $bidder['PhoneNumber'];
			//if fax empty in crm update fax
			if(empty($contact->phone_fax) || !$bContactLocallyModified) $contact->phone_fax = $bidder['FaxNumber'];
			//if email empty in crm update email
			if(empty($contact->email1) || !$bContactLocallyModified) $contact->email1 = $bidder ['email-id'];
			//if related acocunt empty in crm update related account
			if(empty($contact->account_id) || !$bContactLocallyModified) $contact->account_id = $client_id;
			
			$contact->save ();

		}else{
			
			$contact->first_name = $bidder ['first_name'];
			$contact->last_name = $bidder ['last_name'];
			$contact->phone_work = $bidder['PhoneNumber'];
			$contact->phone_fax = $bidder['FaxNumber'];
			$contact->email1 = $bidder ['email-id'];
			$contact->account_id = $client_id;
			//additional info
			$contact->reed_id = $reed_id;
			$contact->lead_source = 'reed';

			//update client contact
			/*********************************************************************/
			$contact->populateDefaultValues ();
			if (! isset ( $contact->assigned_user_id ) || $contact->assigned_user_id == '' && $new_contact) {
				$contact->assigned_user_id = $current_user->id;
			}
			if (! isset ( $contact->team_id ) || $contact->team_id == '' && $new_contact) {
				$contact->team_id = 1;
			}
			
			//blank assignment
			$contact->assigned_user_id = '';
			$contact->team_id = '';
			$contact->team_set_id = '';
			
			//do not show the client contact
			$contact->visibility = 0;
			
			$contact->save ();

			//save import info
			ImportFile::markRowAsImported ( $new_contact );
			if ($new_contact)
				ImportFile::writeRowToLastImport ( $this->import_module, $contact->object_name, $contact->id );
			/********************************************************************/
		}
		
		return ($contact->id);
	}
	
	/**
	 * @function: insertBidderList
	 * @param: string lead id
	 * @param: string client id
	 * @param: string client contact id
	 * @param: array bidder
	 * @param: object import file
	 * @return: string bidder list id
	 */
	function insertBidderList($lead_id, $client_id, $contact_id, $bidder, $importObj) {
	
		global $app_list_strings, $timedate, $db;
		
		$lead_client_detail = new oss_LeadClientDetail ();
		$new_lead_client_detail = true;
		
		//bidders relation
		$lead_client_detail->lead_id = $lead_id;
		$lead_client_detail->account_id = $client_id;
		$lead_client_detail->contact_id = $contact_id;
	
		//ph no and fax no
		$bidder ['PhoneNumber'] = $this->ph_field_clean_text($bidder ['PhoneNumber']);
		$bidder ['FaxNumber'] = $this->ph_field_clean_text($bidder ['FaxNumber']);
		
		// Store data into variables to save Bidders List
		if ($bidder ['Role'] == 'Project Owner') {
			$bidder ['Role'] = 'Owner';
		} else {
			$bidder ['Role'] = 'Sub Contractor';
		}
	
		//bidders info
		$lead_client_detail->contact_phone_no = $bidder['PhoneNumber'];
		$lead_client_detail->contact_fax = $bidder['FaxNumber'];
		$lead_client_detail->contact_email = $bidder['email-id'];
		$lead_client_detail->role = $bidder['Role'];
		$lead_client_detail->lead_source = 'reed';
		
		$bidder_list_array = array();
		if(!empty($lead_id)) $bidder_list_array['lead_id'] = $lead_id;
		if(!empty($client_id)) $bidder_list_array['account_id'] = $client_id;
		if(!empty($contact_id))  $bidder_list_array['contact_id'] = $contact_id;
	
		//check for existing biider list
		$existing_lead_client_id = $lead_client_detail->retrieve_by_string_fields (
				$bidder_list_array,
				false
		);
		if (! empty ( $existing_lead_client_id->id )) {
			$lead_client_detail->id = $existing_lead_client_id->id;
			$new_lead_client_detail = false;
		}
	
		//create / update bidders list
		/******************************************************************/
		$lead_client_detail->populateDefaultValues ();
		if (! isset ( $lead_client_detail->assigned_user_id ) || $lead_client_detail->assigned_user_id == '' && $new_lead_client_detail) {
			$lead_client_detail->assigned_user_id = $current_user->id;
		}
		if (! isset ( $lead_client_detail->team_id ) || $lead_client_detail->team_id == '' && $new_lead_client_detail) {
			$lead_client_detail->team_id = 1;
		}
		
		$lead_client_detail->save ();
		
		//save import info
		ImportFile::markRowAsImported ( $new_lead_client_detail );
		if ($new_lead_client_detail)
			ImportFile::writeRowToLastImport ( $this->import_module, $lead_client_detail->object_name, $lead_client_detail->id );
		/******************************************************************/
		
		return ($lead_client_detail->id);
	
	}
	
	/**
	 * match structure dropdown list
	 * @param string driopdown value
	 * @return string driopdown value
	 */
	function matchStructureDom($stMatchValue){
		global  $app_list_strings;
		$arAllStructere = array_merge($app_list_strings['structure_residential'],$app_list_strings['structure_non_residential'],$app_list_strings['structure_non_building']);
	
		foreach($arAllStructere as $stKey => $stValue){
			$arTmp[str_replace(" ",'',strtolower($stKey))] = $stKey;
		}
		$tmpKey = str_replace(" ",'',strtolower($stMatchValue));
	
		return (isset($arTmp[$tmpKey]))?$arTmp[$tmpKey]: '';
	
	}
	
	/**
	 * match type dropdown list
	 * @param string driopdown value
	 * @return string driopdown value
	 */
	function matchTypeDom($stMatchValue){
		global  $app_list_strings;
		$arAllType = $app_list_strings['project_type_dom'];
		foreach($arAllType as $stKey => $stValue){
			$arTmp[str_replace(" ",'',strtolower($stKey))] = $stKey;
		}
		$tmpKey = str_replace(" ",'',strtolower($stMatchValue));
		return (isset($arTmp[$tmpKey]))?$arTmp[$tmpKey]: '';
	
	}
	
	/**
	 * add dynamic dropdown
	 * @param string $dropdwon_name
	 * @param array $new_list_value
	 */
	function editDropdownList($dropdwon_name, $new_list_value) {

        require_once 'custom/include/dynamic_dropdown.php';
        editDropdownList($dropdwon_name, $new_list_value);

		return ;

	    /*
		 *
		 *Buggy code : replicates the dropdown value
		 * global $app_list_strings;
		
		$GLOBALS ['log']->info ( 'edit dropdown: ' . $dropdwon_name );
		
		$arrPrevList = $app_list_strings [$dropdwon_name];
		
		foreach ( $arrPrevList as $Key => $Value ) {
			$list_value [] = '["' . $Key . '","' . $Value . '"]';
		}
		
		$list_value = implode ( ',', $list_value );
		
		if ($list_value)
			$list_value = '[' . $list_value . ',' . $new_list_value . ']';
		else
			$list_value = '[' . $new_list_value . ']';
		
		$arrParse ['to_pdf'] = true;
		$arrParse ['sugar_body_only'] = 1;
		$arrParse ['action'] = 'savedropdown';
		$arrParse ['view_package'] = 'studio';
		$arrParse ['dropdown_name'] = $dropdwon_name;
		$arrParse ['dropdown_lang'] = 'en_us';
		$arrParse ['list_value'] = $list_value;
		$arrParse ['module'] = 'ModuleBuilder';
		$_REQUEST ['view_package'] = 'studio';
        $arrParse['use_push'] = 'true';
		
		$GLOBALS ['log']->info ( $arrParse );
		
		require_once 'custom/include/customParserDropdown.php';
		$parser = new customParserDropdown ();
		$parser->saveDropDown ( $arrParse );*/
	}
	
	/**
	 * check project lead existance on lookup table
	 * @param string $stProjectLeadId
	 * @return array lookup data
	 */
	function checkLookupLeadExistsBBH($stProjectLeadId) {
		$stCheckSQL = 'SELECT project_lead_id id FROM  project_lead_lookup WHERE project_lead_id ="' . $stProjectLeadId . '"';
		$rsChkResult = $this->db->query ( $stCheckSQL );
		$arChkData = $this->db->fetchByAssoc ( $rsChkResult );
		return $arChkData;
	}
	
	/**
	 * update new - total bidder count for project lead
	 * @param string project lead id 
	 */
	function updateNewTotalBidderCountBBH($stProjectLeadId) {
		//check look up table for existance
		$arChkData = $this->checkLookupLeadExistsBBH ( $stProjectLeadId );
		
		// SQL to get the count of new and total bidders for this project lead/parent project lead
		$stGetBidderCount = "SELECT COALESCE(leads.parent_lead_id,leads.id) ldgrpid,
				sum(if(bidders.is_viewed = 0,1,0)) newbidders,count(bidders.id) total_bidders
				FROM leads LEFT JOIN oss_leadclientdetail bidders on leads.id = lead_id 
				AND bidders.deleted =0
				WHERE COALESCE(leads.parent_lead_id,leads.id) ='" . $stProjectLeadId . "' 
				AND leads.deleted=0 GROUP BY coalesce(parent_lead_id,leads.id)";
		$rsResult = $this->db->query ( $stGetBidderCount );
		$arCountData = $this->db->fetchByAssoc ( $rsResult );
		
		if (isset ( $arChkData ['id'] ) && trim ( $arChkData ['id'] ) != '') {
			//if project lead already in look up table update record
			$stReplace = "UPDATE project_lead_lookup  
					SET new_bidder ='" . $arCountData ['newbidders'] . "',
					total_bidder = '" . $arCountData ['total_bidders'] . "'
					WHERE project_lead_id = '" . $arCountData ['ldgrpid'] . "'	";
			$this->db->query ( $stReplace );
		} else {
			//if project lead not in look up table insert record
			$stReplace = "INSERT INTO 
					project_lead_lookup(id,project_lead_id,new_bidder,total_bidder) 
					VALUES (UUID(),'" . $arCountData ['ldgrpid'] . "',
							'" . $arCountData ['newbidders'] . "',
							'" . $arCountData ['total_bidders'] . "')";
			$this->db->query ( $stReplace );
		}
	}
	
	/**
	 * update lead version
	 * @param string lead id
	 */
	function updateLeadVersionBidDueDateBBH($stParentLeadId) {
		//check look up table for existance
		$arChkData = $this->checkLookupLeadExistsBBH ( $stParentLeadId );
		
		//check converted lead count 
		$stLeadVerBidDueSQL = 'SELECT count(leads.id) countt, 
				coalesce(parent_lead_id,leads.id) leadid, min(bids_due) bids_due_grops,
				GROUP_CONCAT( CONCAT(bids_due," {} ",bid_due_timezone ) 
				ORDER BY bids_due ASC SEPARATOR "$$") bids_due_grops_timezone
				FROM leads WHERE leads.deleted =0
				AND coalesce(parent_lead_id,leads.id)="' . $stParentLeadId . '"
				GROUP BY coalesce(parent_lead_id,leads.id)';
		$rsResult = $this->db->query ( $stLeadVerBidDueSQL );
		$arData = $this->db->fetchByAssoc ( $rsResult );
		
		if (isset ( $arData ['bids_due_grops_timezone'] )) {
			// get the timezone of min date
			$arTmp = explode ( "$$", $arData ['bids_due_grops_timezone'] );
			$arTmpMinDateZone = explode ( ' {} ', $arTmp [0] );
			$arData ['bids_due_grops_timezone'] = $arTmpMinDateZone [1];
		}
		
		if (isset ( $arChkData ['id'] ) && trim ( $arChkData ['id'] ) == $stParentLeadId) {
			//if project lead already in look up table update record
			$stReplace = "UPDATE project_lead_lookup 
			SET lead_version = '" . $arData ['countt'] . "',
			first_bid_due_date = '" . $arData ['bids_due_grops'] . "',
			first_bid_due_timezone = '" . $arData ['bids_due_grops_timezone'] . "'
			WHERE  project_lead_id = '" . $stParentLeadId . "' ";
		} else {
			//if project lead not in look up table insert record
			$stReplace = "INSERT INTO project_lead_lookup
			(project_lead_id,lead_version,first_bid_due_date,first_bid_due_timezone)
			 VALUES ('" . $stParentLeadId . "','" . $arData ['countt'] . "',
			 		'" . $arData ['bids_due_grops'] . "',
			 				'" . $arData ['bids_due_grops_timezone'] . "')";
		}	
		$this->db->query ( $stReplace );
	}
	
	/**
	 * Get County by County Number and State Abbr
	 * @param: string county name
	 * @param: strimg county abbr
	 * @return: string couunty id 
	 */
	function getCounty($county, $stateProvince) {
		$countyId = "";
		
		if (! empty ( $county )) {
			$countSQL = "SELECT id FROM oss_county WHERE name LIKE '" .trim($county) . "' AND county_abbr = '".trim($stateProvince)."'  AND deleted = '0' ";
			$resCounty = $this->db->query ( $countSQL );
			$rowCounty = $this->db->fetchByAssoc ( $resCounty );
			if (! empty ( $rowCounty )) {
				$countyId = $rowCounty ['id'];
			}
		}
		return $countyId;
	}
	
	/**
	 * check an existiing record in crm
	 * @param string $table
	 * @param string $field_name
	 * @param string $field_value
	 * @return string result
	 */
	function checkExistingRecord($table, $field_name, $field_value) {
		$sql = "SELECT id FROM " . $table . " WHERE " . $field_name . " = '" . $field_value . "' AND deleted=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		if (! empty ( $result )) {
			return $result ['id'];
		}
		return;
	}
	
	/**
	 * Get Client Information
	 * @param string client id
	 * @param objejct db
	 * @return array cleint
	 */
	function getClient($client_id, $db) {
		$sql = "SELECT accounts.*,ea.email_address as email1 FROM accounts
				LEFT JOIN email_addr_bean_rel eabr ON eabr.bean_id = accounts.id AND eabr.deleted = 0 AND eabr.primary_address = 1
				LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0
				WHERE accounts.id = '" . addslashes ( $client_id ) . "' AND accounts.deleted = 0";
		$query = $db->query ( $sql );
		$result = $db->fetchByAssoc ( $query );
		return $result;
	}
	
	/**
	 * Get Client Contact Information
	 * @param string contact id
	 * @param objejct db
	 * @return array cleint contact
	 */
	function getClientContact($contact_id, $db) {
		$sql = "SELECT contacts.*,ea.email_address as email1 FROM contacts
		LEFT JOIN email_addr_bean_rel eabr ON eabr.bean_id = contacts.id AND eabr.deleted = 0 AND eabr.primary_address = 1
		LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0
		WHERE contacts.id = '" . addslashes ( $contact_id ) . "' AND contacts.deleted = 0";
		$query = $db->query ( $sql );
		$result = $db->fetchByAssoc ( $query );
		return $result;
	}
	
	/**
	 * Get Project Lead Information
	 * @param lead id
	 * @return array leads
	 */
	function getLocalProjectLead($id) {
		$sql = "SELECT * from `leads` WHERE id='" . $id . "' AND `deleted`=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		return $result;
	}
	
	/**
	 * Get Parent lead id from client instance
	 * @param: string lead id
	 * @return: string parent lead id
	 */
	function getParentLeadId($lead_id) {
		if (! empty ( $lead_id )) {
			$sql = "SELECT id,parent_lead_id FROM leads WHERE id = '" . $lead_id . "' AND deleted = 0";
			$query = $this->db->query ( $sql );
			$result = $this->db->fetchByAssoc ( $query );
			if (! empty ( $result ['parent_lead_id'] )) {
				return $result ['parent_lead_id'];
			} else {
				return $result ['id'];
			}
		}
	}
	
	/**
	 * clean special characters from a string
	 * @param: string text
	 * @return: string clean text
	 */
	function clean_text($text){
		$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=');
		$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}
	
	/**
	 * clean special characters from phone string
	 * @param: string phone no
	 * @return: string phone no
	 */
	function ph_field_clean_text($text){
		$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=',' ','-');
		$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','','','-');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}
	
	/**
	 * create mysql date format
	 * @param: string $datestring
	 * @return: string mysql date format
	 */
	function makeMysqlDate($datestring){
		$day = substr($datestring,-2);
		$month = substr($datestring,4,2);
		$year = substr($datestring, 0,4);
		return $year.'-'.$month.'-'.$day;
	}
	/**
	 * split full name to first name and last name
	 * @param unknown $name
	 * @return array first name and last name
	 */
	function splitName($name){
	
		$names = explode(' ', $name);
		$firstname = '';
		if(count($names) >  1){
			$firstname = $names[0];
			unset($names[0]);
			$lastname = implode(' ', $names);
		}else{
			$lastname = $name;
		}
		return array($firstname, $lastname);
	}
}
?>