<?php

/**
 * *
 * @org    : Osscube Solutions
 * @desc   : Mapped Fields of XML to Fields of Database tables.
 * @author : Basudeba Rath.
 * @date   : 17 Dec 2012.
 * @class  : ImportXML
 **/

//ini_set('display_errors','1');

require_once 'custom/modules/Leads/import_xml/onvia_fields_map.php';
require_once 'custom/include/common_functions.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/include/dynamic_dropdown.php';
require_once ('modules/Import/ImportFile.php');
require_once ('modules/Import/ImportFileSplitter.php');
require_once ('modules/Import/ImportCacheFiles.php');
require_once ('modules/Import/ImportDuplicateCheck.php');
require_once ('include/upload_file.php');
class ImportXML {
	private $db;
	private $sugar_config;
	private $current_user;
	private $data;
	private $import_module;
	private $importObj;
	private $fname;
	private $list_value; 
	/**
	 * *
	 * @org : Osscube Solutions
	 * @author : Basudeba Rath.
	 * @date : 17 Dec 2012.
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
	}
	/**
	 * @org : Osscube Solutions 
	 * @author : Basudeba Rath.
	 * @date : 21 Dec 2012.
	 * @function : insertData()
	 * @desc : Build xml array, call and pass xml data to different method to import.
	 **/
	function insertData() {
		$importObj = new ImportFile($this->fname);
		
		foreach ( $this->data as $record ) {
			
			$lead_id = $this->insertProjectLead ( $record,$importObj );
			$buyer_first_name = isset ( $record ['BuyerFirstName'] ) ? $record ['BuyerFirstName'] : "";
			$buyer_last_name = isset ( $record ['BuyerLastName'] ) ? $record ['BuyerLastName'] : "";
			$buyer_name = $buyer_first_name . $buyer_last_name;
			$client_id = $this->insertClient ( $record ,$importObj);
			if (! empty ( $record ['BuyerID'] ) && ! empty ( $buyer_name )) {
				$client_contact_id = $this->insertClientContact ( $record, $client_id,$importObj );
			}
			else{
				$client_contact_id = "";
			}
			$this->insertBidderList ( $lead_id, $client_id, $client_contact_id, $record,$importObj );
			$parent_lead_id = $this->getParentLeadId ( $lead_id );
			
			// Bidder list logic hooks
			$this->updateNewTotalBidderCountBBH ( $parent_lead_id );
			$this->updateLeadVersionBidDueDateBBH ( $parent_lead_id );
			
			// Update online plans
			updateOnlineCount ( $lead_id );		
		}
		
		//set update prev bid to flag
		setPreviousBidToUpdate();
		
		//write csv status file to display new records of after imported.
		$importObj->writeStatus ();
		
		// Update Dropdown values of industry dom.
		for ($i = 0; $i<sizeof($this->list_value); $i++){
			$this->editDropdownList ( "industry_dom", $this->list_value[$i] );
		}
	}
	/**
	 * @org      : Osscube Solutions
	 * @author   : Basudeba Rath.
	 * @date     : 21 Dec 2012.
	 * @function : insertProjectLead()
	 * @param    : record array
	 * @desc     : Insert & Update Project Leads.
	 */
	function insertProjectLead($record,$importObj) {
		global $pl_notes, $pl_fields, $app_list_strings, $timedate;
		
		$bid_due_timezone = "";
		$data_type = "";
		//$module_name = 'Lead';
		$_REQUEST ['import_module'] = 'Leads';
		$xml_lead_id = $record ['ProjectID'];
		//$project_title = isset ( $record ['ProjectTitle'] ) ? $record ['ProjectTitle'] : "";
		// $project_title = htmlspecialchars_decode($project_title,ENT_QUOTES);
		//$project_title = addslashes ( $project_title );
		// Check Existing Lead including deleted leads
		$leadSql = "SELECT `id` FROM `leads` WHERE `project_lead_id` = '" . $xml_lead_id . "' AND `deleted` = 0";
		
		$plsql = "INSERT INTO leads SET ";
		$pl_audit_sql = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
		$leadQuery = $this->db->query ( $leadSql );
		$existingLead = $this->db->fetchByAssoc ( $leadQuery );
		$pl_is_update = false;
		$newLeadRecord = true;
		$pl_status = 'New';
		if (! empty ( $existingLead )) {
			$pl_is_update = true;
			$newLeadRecord = false;
			$lead_id = $existingLead ['id'];
			$locallead = $this->getLocalProjectLead ( $lead_id );
		} else {
			$lead_id = create_guid ();
			//if new lead add own id to parent_lead_id
			$plsql .= " parent_lead_id = '".$lead_id."', ";
		}
		$plsql .= "`id` = '" . $lead_id . "',
					`date_entered` = UTC_TIMESTAMP(),
					`date_modified` = UTC_TIMESTAMP(),
					`modified_user_id` = '1',
					`created_by` = '" . $this->current_user->id . "',
					`deleted` = '0',
					`assigned_user_id` = '" . $this->current_user->id . "',
					`team_id` = '1',
					`team_set_id` = '1',
					`last_name` = '" . addslashes ( $record ['ProjectTitle'] ) . "',
					`status` = '" . $pl_status . "',
					`onvia_id` = '" . $xml_lead_id . "'";
		
		$plupdate = " ON DUPLICATE KEY UPDATE
					`date_modified` = VALUES(`date_modified`),
					`last_name` = VALUES(`last_name`),
					`status` = VALUES(`status`)";
		
		$pl_notes_str = '';
		$p = 0;
		
		foreach ( $record as $key => $value ) {
		
			// Create Project Lead Notes Field Value
			if (in_array ( $key, $pl_notes ) && isset ( $value ) && ! empty ( $value )) {
				$pl_notes_str .= $key . ": " . addslashes (htmlspecialchars_decode( $value )) . "\n";
			}
			
			// Build Query For Project Lead
			if (array_key_exists ( $key, $pl_fields ) && isset ( $value )) {
				$pl_field_name = $pl_fields [$key];
				$pl_field_value = $value;
				//$pl_field_value = addslashes($pl_field_value);
				$pl_field_value = htmlspecialchars_decode($pl_field_value,ENT_QUOTES);
				$pl_field_value = htmlspecialchars($pl_field_value,ENT_QUOTES);
				
				/*if ($pl_field_name == 'scope_of_work') {
					$search_string = array ( "'", '"' );
					$rep_string = array ( "`", "`" );
					$pl_field_value = str_replace ( $search_string, $rep_string, $pl_field_value );
				}
				if($pl_field_name == 'project_title'){
					
					
					//$pl_field_value = htmlspecialchars($value,HTML_SPECIALCHARS);
					//$pl_field_value = htmlspecialchars_decode($pl_field_value,HTML_SPECIALCHARS);
					//$pl_field_value = addslashes($pl_field_value);
					//echo $pl_field_value;echo "<br>";
				}*/
				if ($pl_field_name == 'pre_bid_meeting') {
					
					$pre_bid_meeting_date = date ( 'Y-m-d', strtotime ( $pl_field_value ) );
					$pre_bid_meeting_date .= " 00:00:00";
					$pl_field_value = $pre_bid_meeting_date;
				}
				if ($pl_field_name == 'city') {
					
					$project_city = explode ( ";", $pl_field_value );
					$project_city = $project_city [0];
					$pl_field_value = $project_city;
				}
				if ($pl_field_name == 'county_id') {
					
					$county_id = $this->getCounty ( $pl_field_value );
					if (! empty ( $county_id ) && isset ( $county_id )) {
						$pl_field_value = $county_id;
					}
				}
				if ($pl_field_name == 'state') {
					
					$project_state = explode ( ";", $pl_field_value );
					$project_state = $project_state [0];
					$pl_field_value = $project_state;
				}
				if ($pl_field_name == 'zip_code') {
					
					$proj_county_fips_code = explode ( ";", $pl_field_value );
					$proj_county_fips_code = $proj_county_fips_code [0];
					$pl_field_value = $proj_county_fips_code;
				}
				if ($pl_field_name == 'bids_due' && isset ( $project_state )) {
					
					$state_name = $app_list_strings ['state_dom'] [$project_state];
					$bid_due_timezone = $app_list_strings ['state_tz_dom'] [$state_name];
					
					// $pl_field_value = $this->makeMysqlDate($pl_field_value);
					$stBidsDue = (trim ( $pl_field_value ) != '') ? strtotime ( $pl_field_value ) : '';
					$db_date_time = date ( 'Y-m-d H:i:s', $stBidsDue );
					$userDateTime = $timedate->to_display_date_time ( $db_date_time, true, false );
					$oss_timedate = new OssTimeDate ();
					$pl_field_value = $oss_timedate->convertDateForDB ( $userDateTime, $bid_due_timezone );
				}
				// $pl_field_value = htmlspecialchars_decode($pl_field_value);
				// $pl_field_value = addslashes ( $pl_field_value );
				$plsql .= ", `" . $pl_field_name . "` = '" . $pl_field_value . "'";
				$plupdate .= ", `" . $pl_field_name . "` =  VALUES (`" . $pl_field_name . "`)";
				
				// Prepare Query For Change Log
				//print_r($locallead );die;
				if ($pl_is_update == true) {
					if($pl_field_name == 'bids_due' ){	
						$stBidsDue_local = (trim ( $pl_field_value ) != '') ? strtotime ( $locallead [$pl_field_name] ) : '';
				        $db_date_time_local = date ( 'Y-m-d H:i:s', $stBidsDue_local );
						$locallead [$pl_field_name] = $db_date_time_local;
					}
					
					
					if ($locallead [$pl_field_name] != stripslashes($pl_field_value)) {
						/*echo '<h1>'.$locallead['id'].'</h1>' .$locallead [$pl_field_name];echo "<br><br><br>";
						echo $pl_field_value;echo "-------------------<br><br><br><br>";*/
						$is_audit = true;
						if ($p > 0) {
							$pl_audit_sql .= ",";
						}
						//set field data type
						if ($pl_field_name == 'bids_due') {
							$data_type = 'datetimecombo';
						}
						elseif($pl_field_name == 'state'){
							
							$data_type = 'enum';
							
						}else{
							
							$data_type = '';
						}
						
						$lead_audit_id = create_guid ();
						$pl_audit_sql .= " ('" . $lead_audit_id . "','" . $lead_id . "',UTC_TIMESTAMP(),'Onvia','" . $pl_field_name . "','" . $data_type . "','" . $locallead [$pl_field_name] . "','" . $pl_field_value . "') ";
						$p ++;
					}
				} // EOC of lead audit.
			}
		}
		
		if(($pl_is_update == true) && ($locallead ['description'] != $pl_notes_str )){
			$is_audit = true;
			$data_type = '';
			$p = $p+1;
			$lead_audit_id = create_guid ();
			$pl_audit_sql .= ", ('" . $lead_audit_id . "','" . $lead_id . "',UTC_TIMESTAMP(),'Onvia','" . 'description' . "','" . $data_type . "','" . $locallead ['description'] . "','" . $pl_notes_str . "') ";
		}
		$plsql .= ", `lead_source` = 'onvia'";
		$plsql .= ", `description` = '" . $pl_notes_str . "'";
		$plupdate .= ", `description` = VALUES (`description`) ";
				
		$plsql .= ", `bid_due_timezone` = '" . $bid_due_timezone . "'";
		$plupdate .= ", `bid_due_timezone` = VALUES (`bid_due_timezone`) ";	
		
		$sql = $plsql . $plupdate;			
		$this->db->query ( $sql );
		
		if ($p > 0) {	
			$this->db->query ( $pl_audit_sql );
			changeLogFlag($lead_id,$this->db);
			$is_audit = false;
		}
		
		$importObj->markRowAsImported ( $newLeadRecord );
		if ($newLeadRecord){
			$importObj->writeRowToLastImport ( $_REQUEST ['import_module'],'Lead', $lead_id );
		}
		return ($lead_id);
	}
	
	/**
	 * @function: insertClient
	 * @param   : record array.
	 * @desc    : Insert & Update Client records.       	
	 */
	function insertClient($record,$importObj) {
		global $client_fields, $client_comment;
		$_REQUEST ['import_module'] = 'Leads';
		$module_name = 'Account';
		$client_comment_str = "";
		$xml_client_id = isset ( $record ['OwnerID'] ) ? $record ['OwnerID'] : "";
		$c_name = isset ( $record ['OwnerName'] ) ? $record ['OwnerName'] : "";
		
		$existingClient = $this->checkExistingRecord ( 'accounts', 'onvia_id', $xml_client_id );
		if (empty ( $existingClient )) {
			$existingClient = checkExistingClientForXMLImport ( $c_name, "", "", "" );
		}
		
		if (empty ( $existingClient )) {
			$client_id = create_guid ();
			$newAccountRecord = true;
		} else {
			$client_id = $existingClient;
			$newAccountRecord = false;
			$localClient = $this->getClient($client_id, $this->db);
		}
		
		$insertSQL = "INSERT INTO accounts SET
		        `id` = '" . $client_id . "',				
				`date_entered` = UTC_TIMESTAMP(),
				`date_modified` = UTC_TIMESTAMP(),
				`modified_user_id` = '" . $this->current_user->id . "',
				`created_by` = '" . $this->current_user->id . "',
				`deleted` = '0',
				`visibility` = '0',
				`team_id` = '1',
				`team_set_id` = '1',
				`lead_source` = 'onvia'";
		$updateSQL = "UPDATE accounts SET `date_modified` = UTC_TIMESTAMP() ";
		$fieldSQL = '';
		foreach ( $record as $key => $value ) {
			
		    /** if exisiting client and localy modified
		     *  then only update the blank values
		     */
		    if(!empty( $existingClient ) ){
		         
		        if( $localClient['is_modified'] && !empty($value)){
		            continue;
		        }
		    }
		    
			// Create Client Comments Field Value
			if (in_array ( $key, $client_comment ) && isset ( $value ) && ! empty ( $value )) {
				$client_comment_str .= $key . ": " . addslashes ( $value ) . "\n";
			}
			if (array_key_exists ( $key, $client_fields ) && isset ( $value )) {
				$client_field_name = $client_fields [$key];
				$client_field_value = $value;
				if ($client_field_name == 'industry' && ! empty ( $client_field_value )) {
					$industry_dom_value = '["' . $this->clean_text ( $client_field_value ) . '","' . $client_field_value . '"]';
					if (! in_array ( $industry_dom_value, $this->list_value )) {
						$existing_dom_val = checkExistingValueFromDom ( 'industry_dom', $industry_dom_value );
						if (! isset ( $existing_dom_val ) && empty ( $existing_dom_val )) {
							$this->list_value [] = $industry_dom_value;
						}
					}
					$client_field_value = $this->clean_text ( $client_field_value );
				}
				$client_field_value = htmlspecialchars_decode ( $client_field_value, ENT_QUOTES );
				//Maintain Change Log
				if (! empty ( $existingClient )) {
					
					$field_type = '';
					if ($client_field_name == 'billing_address_state') {
						$field_type = 'enum';
					}
					insertChangeLog ( $this->db, 'accounts', $existingClient, $localClient[$client_field_name], $client_field_value, $client_field_name, $field_type, $this->current_user->id );
				}
				
				$client_field_value = addslashes ( $client_field_value );
				$fieldSQL .= ", `" . $client_field_name . "` = '" . $client_field_value . "'";
			}
		}
		
		if( $localClient['is_modified'] && trim($client_comment_str) == ''){
		   $client_comment_str = $localClient['description'];
		}
		$fieldSQL .= ", `description` = '" . $client_comment_str . "'";
		
		
			
		if (! empty ( $existingClient )) {
			
			if($localClient['description'] != $client_comment_str){
				insertChangeLog ( $this->db, 'accounts', $existingClient, $localClient['description'], $client_comment_str, 'description', 'text', $this->current_user->id );
			}
			
			$fieldSQL .= " WHERE `id`= '" . $existingClient . "' AND `deleted` = '0'";
			$sql = $updateSQL . $fieldSQL;
		} else {
			$sql = $insertSQL . $fieldSQL;
		}
		
		// echo $sql."<br>=======================<br>";
		$result = $this->db->query ( $sql );
		$importObj->markRowAsImported ( $newAccountRecord );
		if ($newAccountRecord)
			$importObj->writeRowToLastImport ( $_REQUEST ['import_module'], $module_name, $client_id );
		
		return $client_id;
	}
	
	/**
	 * @function: insertClientContact
	 * @param   : record and clientid.
	 * @desc    : Insert & Update Client Contacts.      	
	 */
	function insertClientContact($record, $client_id,$importObj) {
		global $contact_fields;
		$buyer_email = array ();
		$email = "";
		$fax = "";
		$phone_no = "";
		$_REQUEST ['import_module'] = 'Leads';
		$module_name = 'Contact';
		$xml_contact_id = isset ( $record ['BuyerID'] ) ? $xml_contact_id = $record ['BuyerID'] : $xml_contact_id = '';
		$fname = isset ( $record ['BuyerFirstName'] ) ? $fname = $record ['BuyerFirstName'] : $fname = '';
		$lname = isset ( $record ['BuyerLastName'] ) ? $lname = $record ['BuyerLastName'] : $lname = '';
		$name = $fname . " " . $lname;
		$buyer_address1 = isset($record ['BuyerAddress1'])? $record ['BuyerAddress1'] : "";
		$buyer_address2 = isset($record ['BuyerAddress2'])? $record ['BuyerAddress2'] : "";
		$buyer_address = rtrim($buyer_address1 . " , " . $buyer_address2," , ");
				
		if( isset ( $record ['BuyerBusinessPhone']) && ! empty ( $record ['BuyerBusinessPhone'] )){
			$phone1 = explode ( ";", $record ['BuyerBusinessPhone'] );
			$phone2 = explode ( "x", $phone1[0] );
			$phone_no = $this->ph_field_clean_text ( $phone2[0] );
		}
		if( isset ( $record ['BuyerFax'] ) && ! empty ( $record ['BuyerFax'] )){
			$fax1 = explode ( ";", $record ['BuyerFax'] );
			$fax2 = explode ( "x", $fax1 [0] );
			$fax = $this->ph_field_clean_text ($fax2 [0] );
		}
		if (isset ( $record ['BuyerEmail'] ) && ! empty ( $record ['BuyerEmail'] )) {
			$buyer_email = explode ( ";", strtolower ( $record ['BuyerEmail'] ) );
			$email = addslashes ( $buyer_email [0] );
		}
		
		// Check Existing Contact by xml id
		$existingContact = $this->checkExistingRecord ( 'contacts', 'onvia_id', $xml_contact_id );
		if (empty ( $existingContact )) {
			$existingContact = checkExistingClientContactForXMLImport ( $name, $phone_no, $fax, $email );
		}
		
		if (empty ( $existingContact )) {
			$contact_id = create_guid ();
			$newContactRecord = true;
		} else {
			$contact_id = $existingContact;
			$newContactRecord = false;
			$contactRes = $this->getClientContact($contact_id, $this->db);
		}
		$insertSQL = "INSERT INTO contacts SET 
                	`id` = '" . $contact_id . "',
					`date_entered` = UTC_TIMESTAMP(),
					`date_modified` = UTC_TIMESTAMP(),
					`modified_user_id` = '1',
					`team_id` = '1',
					`team_set_id` = '1',
					`created_by` = '" . $this->current_user->id . "',
					`deleted` = '0',
					`visibility` = '0',";
		
		$updateSQL = "UPDATE contacts SET ";
		$fieldSQL = '';
		$i = 0;
		foreach ( $record as $key => $value ) {
		    
		    /** if exisiting client contact and localy modified
		     *  then only update the blank values
		     */ 
		    if(!empty( $existingContact ) ){
		        		    	
		      if( $contactRes['is_modified'] && !empty($field_value)){
		          continue;		      	
		      }		        
		    }
			
			if (array_key_exists ( $key, $contact_fields ) && isset ( $value )) {
				$field_name = $contact_fields [$key];
				$field_value = htmlspecialchars_decode ( $value, ENT_QUOTES );
				$field_value = addslashes ( $field_value );
				
				if ($field_name == 'phone_work') {	
					$phone1 = explode ( ";", $field_value );
			        $buyer_phone = explode ( 'x', $phone1 [0] );
					$field_value = $this->ph_field_clean_text ( $buyer_phone [0] );
				}
				if ($field_name == 'phone_fax') {	
					$phone_fax1 = explode ( ";", $field_value );
			        $buyer_phone_fax = explode ( 'x', $phone_fax1 [0] );
					$field_value = $this->ph_field_clean_text ( $buyer_phone_fax [0] );
				}
				if ($field_name == 'primary_address_state') {	
					$buyer_state = explode ( ";", $field_value );
					$field_value = $buyer_state [0];
				}
				
				if ($i > 0) {
					$fieldSQL .= ", ";
				}
				
				//Maintain Change Log
				if (! empty ( $existingContact )) {					
					$field_type = '';
					if ($field_name == 'primary_address_state' || $field_name == 'alt_address_state') {
						$field_type = 'enum';
					}
					
					insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes [$field_name], $field_value, $field_name, $field_type, $this->current_user->id );
				}
				
				$fieldSQL .= " `$field_name` = '" . $field_value . "'";
				$i ++;
			}
		}
		$fieldSQL .= ", `primary_address_street` = '" . $buyer_address . "'";
				
		
		if (! empty ( $existingContact )) {
			
			if($contactRes['primary_address_street'] != $buyer_address){
				insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes ['primary_address_street'], $buyer_address, 'primary_address_street', 'varchar', $this->current_user->id );
			}
			
			if($contactRes['email1'] != $email){
				insertChangeLog($this->db, 'contacts', $existingContact, $contactRes['email1'], $email, 'email1', 'varchar', $this->current_user->id);
			}
			
			$updateSQL = $updateSQL . $fieldSQL;
			$updateSQL .= " WHERE `id`= '" . $existingContact . "'";
			$sql = $updateSQL;
		} else {
			$sql = $insertSQL . $fieldSQL;
		}
		
		// Insert or Update email address to client contact.	
		if(count($buyer_email) > 0){
			$this->insertUpdateEmailAddress('Contacts', $contact_id, $buyer_email);			
		}
		/* for($i = 0; $i < sizeof ( $buyer_email ); $i ++) {
			$isPrimary = ($i==0)?1:0;		
			if (! empty ( $existingContact ) && ! empty ( $buyer_email [$i] )) {
				$this->insertUpdateEmailAddress ( 'Contacts', $contact_id, $buyer_email [$i], false,$isPrimary );
			} else if (! empty ( $buyer_email [$i] )) {
				$this->insertUpdateEmailAddress ( 'Contacts', $contact_id, $buyer_email [$i], true ,$isPrimary);
			}
		} */
		//echo $sql;echo "<br>";
		$this->db->query ( $sql );
		
		// Create Relationship between Client and Client Contact
		if (! empty ( $client_id )) {
			$this->insertAccountContactRel ( $contact_id, $client_id );
		}
		$importObj->markRowAsImported ( $newContactRecord );
		if ($newContactRecord)
			$importObj->writeRowToLastImport ($_REQUEST ['import_module'], $module_name, $contact_id );
			
		return $contact_id;
	}
	/**
	 * Get County by County Number and State Abbr
	 */
	function getCounty($countyValue) {
		$countyId = "";
		
		$proj_county = explode ( ";", $countyValue );
		$proj_county = $proj_county [0];
		
		if (! empty ( $proj_county )) {
			$countSQL = "SELECT id FROM oss_county WHERE name LIKE '" . $proj_county . "%' AND deleted = '0' ";
			$resCounty = $this->db->query ( $countSQL );
			$rowCounty = $this->db->fetchByAssoc ( $resCounty );
			if (! empty ( $rowCounty )) {
				$countyId = $rowCounty ['id'];
			}
		}
		return $countyId;
	}
	private function checkExistingRecord($table, $field_name, $field_value) {
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
	 * Insert / Update Email Address
	 *
	 * @param
	 *        	string - $module - Module Name
	 * @param
	 *        	char - $bean_id - Id of the client / contact
	 * @param
	 *        	email id - $email_address - Email Address
	 * @param
	 *        	boolean - $is_new - Parent Insert or Update
	 * @param
	 *        	boolean - $is_bb - is the destination bb ?
	 * @return boolean
	 */
	/*function insertUpdateEmailAddress($module, $bean_id, $email_address, $is_new = false,$is_primary = 0) {
		if (empty ( $email_address )) {
			return;
		}
		
		$email_address = addslashes ( $email_address );
		if ($is_new) {
			$this->insertEmailAddress ( $module, $bean_id, $email_address, $is_primary  );
			return true;
		} else {
			$sql_email_bean = " SELECT id, email_address_id FROM email_addr_bean_rel WHERE bean_id = '" . $bean_id . "' AND primary_address = '".$is_primary."' AND deleted = 0";
			$result_email_bean = $this->db->query ( $sql_email_bean );
			$row_email_bean = $this->db->fetchByAssoc ( $result_email_bean );
			
			if (! empty ( $row_email_bean ['id'] )) {
				
				$sql_email_address = " SELECT id FROM email_addresses WHERE email_address_caps = '" . strtoupper ( $email_address ) . "' AND deleted = 0 ";
				$result_email_address = $this->db->query ( $sql_email_address );
				$row_email_address = $this->db->fetchByAssoc ( $result_email_address );
				
				if (empty ( $row_email_address ['id'] )) {
					$email_address_id = create_guid ();
					$sql_email_address = " INSERT INTO email_addresses (id, email_address, email_address_caps, invalid_email, opt_out, date_created, date_modified, deleted) VALUES ('" . $email_address_id . "', '" . $email_address . "', '" . strtoupper ( $email_address ) . "', 0, 0, NOW(), NOW(), 0)";
					$this->db->query ( $sql_email_address );
				} else {
					$email_address_id = $row_email_address ['id'];
				}
				
				$sql_email_rel = " SELECT id FROM email_addr_bean_rel WHERE email_address_id = '" . $email_address_id . "' AND bean_id = '" . $bean_id . "' AND bean_module = '" . $module . "'  AND deleted = 0 ";
				$result_email_rel = $this->db->query ( $sql_email_rel );
				$row_email_rel = $this->db->fetchByAssoc ( $result_email_rel );
				
				if (! empty ( $row_email_rel ['id'] )) {
					
					$sql_update_email_bean = " UPDATE email_addr_bean_rel SET email_address_id = '" . $email_address_id . "' WHERE bean_id = '" . $bean_id . "' AND bean_module = '" . $module . "' AND primary_address = '".$is_primary."'";
					$this->db->query ( $sql_update_email_bean );
				} else {
					
					$sql_email_bean = " INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, primary_address, reply_to_address, date_created, date_modified, deleted ) VALUES (UUID(), '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', '".$is_primary."', 0, NOW(), NOW(),0) ";
					$this->db->query ( $sql_email_bean );
				}
			} else {
				$this->insertEmailAddress ( $module, $bean_id, $email_address,$is_primary );				
				return true;
			}
		}
		return false;
	} */
	/**
	 * @function: insertBidderList
	 * @param : $lead_id,$client_id,$contact_id,$record.
	 * @desc: Insert Bidder Lists. 	
	 */
	function insertBidderList($lead_id, $client_id, $contact_id, $record,$importObj) {
		$phone_no = "";
		$email = "";
		$fax = "";
		$_REQUEST ['import_module'] = "Leads";
		$buyer_id = isset ( $record ['BuyerID'] ) ? $record ['BuyerID'] : "";
		$mi_lcd_id = $record ['ProjectID'] . '-' . $record ['OwnerID'] . '-' . $buyer_id;
		
		if (isset ( $record ['BuyerBusinessPhone'] ) && ! empty ( $record ['BuyerBusinessPhone'] )) {
			$phone1 = explode ( ";", $record ['BuyerBusinessPhone'] );
			$buyer_phone = explode ( 'x', $phone1 [0] );
			$phone_no = $this->ph_field_clean_text ( $buyer_phone [0] );
		}
		if( isset ( $record ['BuyerFax'] ) && ! empty ( $record ['BuyerFax'] )){
			$fax1 = explode ( ";", $record ['BuyerFax'] );
			$fax2 = explode ( "x", $fax1 [0] );
			$fax = $this->ph_field_clean_text ( $fax2[0] );
		}
		if (isset ( $record ['BuyerEmail'] ) && ! empty ( $record ['BuyerEmail'] )) {
			$buyer_email = explode ( ";", strtolower ( $record ['BuyerEmail'] ) );
			$email = addslashes ( $buyer_email [0] );
		}
		$bidder_new_id = create_guid(); 
		// Query For Bidders List
		$bidderSQL = "INSERT INTO `oss_leadclientdetail` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `team_id`, `team_set_id`, `assigned_user_id`, `contact_email`, `contact_phone_no`, `role`, `mi_oss_leadclientdetail_id`, `contact_fax`, `lead_source`, `contact_id`, `lead_id`, `account_id`,`bid_status`) VALUES ";
		$bidderSQL .= " ( '".$bidder_new_id."', '" . $record ['OwnerID'] . "', UTC_TIMESTAMP(), UTC_TIMESTAMP(), '1', '1', '0', '1', '1', '1', '" . $email . "', '" . $phone_no . "', '', '" . addslashes ( $mi_lcd_id ) . "', '" . $fax . "', 'onvia', '" . $contact_id . "', '" . $lead_id . "', '" . $client_id . "','') ";
		$bidderSQL .= " ON DUPLICATE KEY UPDATE `name`  = VALUES(`name`), `date_modified` = VALUES(`date_modified`), `contact_email` = VALUES(`contact_email`), `contact_phone_no` = VALUES(`contact_phone_no`), `role` = VALUES(`role`), `contact_fax` = VALUES(`contact_fax`), `contact_id` = VALUES(`contact_id`),  `lead_id` = VALUES(`lead_id`), `account_id` = VALUES(`account_id`), `bid_status` = VALUES(`bid_status`);";
		$this->db->query ( $bidderSQL );
		/*$newLCDRecord = true;
		$importObj->markRowAsImported ( $newLCDRecord );
		if ($newLCDRecord){
			$importObj->writeRowToLastImport ( $_REQUEST ['import_module'],'oss_LeadClientDetail', $bidder_new_id );
		}
		*/
	}
	private function getLocalProjectLead($id) {
		$sql = "SELECT * from `leads` WHERE id='" . $id . "' AND `deleted`=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		return $result;
	}
	function editDropdownList($dropdwon_name, $new_list_value) {

        require_once 'custom/include/dynamic_dropdown.php';
        editDropdownList($dropdwon_name, $new_list_value);
		/*global $app_list_strings;
		
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
	 * Insert Email Address
	 * 
	 * @param
	 *        	string - $module - Module Name
	 * @param
	 *        	char - $bean_id - Id of the client / contact
	 * @param
	 *        	email id - $email_address - Email Address
	 * @param
	 *        	boolean - $is_new - Parent Insert or Update
	 */
	
	function insertUpdateEmailAddress($module,$bean_id,$email_addresses){
		//get Existing Email Addresses of related bean
		$email_addresses = array_unique($email_addresses);
		$sql = "SELECT ea.email_address 
				FROM email_addresses ea
        		INNER JOIN email_addr_bean_rel eabr ON eabr.email_address_id = ea.id AND eabr.bean_id='".$bean_id."' AND eabr.bean_module='".$module."' AND eabr.deleted = 0 
				WHERE ea.deleted = 0";
		$query = $this->db->query($sql);
		$emails_from_db = array();
		while($result = $this->db->fetchByAssoc($query)){
			$emails_from_db[] = $result['email_address'];
		}
				
		$insertSQL = "INSERT INTO email_addresses (id, email_address, email_address_caps, invalid_email, opt_out, date_created, date_modified, deleted) VALUES";
		$insertBeanRel = "INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, reply_to_address, date_created, date_modified, deleted ) VALUES";
		
		$i = 0;				
		
		foreach($email_addresses as $email_address){
						
			if(!in_array($email_address,$emails_from_db)){
				//Insert Email Address
				$email_address_id = create_guid();
				$email_addr_bean_rel_id = create_guid();
				if($i > 0){
					$insertSQL .= ", ";
					$insertBeanRel .= ", ";
				}								
				$insertSQL .= " ('".$email_address_id."', '" . $email_address . "', '" . strtoupper ( $email_address ) . "', 0, 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(), 0) ";
				$insertBeanRel .= " ('".$email_addr_bean_rel_id."', '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(),0) ";
				$i++;
			}			
		}		
		$this->db->query($insertSQL);
		$this->db->query($insertBeanRel);
		
		foreach($emails_from_db as $db_email){			
			if(!in_array($db_email,$email_addresses)){
				//Query for set flag deleted = 0
				$sql = "UPDATE email_addr_bean_rel eabr, email_addresses ea SET eabr.deleted = 1, ea.deleted=1 
						WHERE ea.email_address='".$db_email."' AND eabr.bean_id = '".$bean_id."' AND eabr.bean_module='".$module."' AND eabr.deleted = 0 AND ea.id = eabr.email_address_id";
				$this->db->query($sql);			
			}			
		}

		//Reset Primary Email
		if(count($email_addresses) > 0){
			$primary_sql = "SELECT id FROM email_addr_bean_rel WHERE bean_id='".$bean_id."' AND bean_module='".$module."' AND deleted=0 ORDER BY date_created asc LIMIT 0,1";
			$query = $this->db->query($primary_sql);
			$result = $this->db->fetchByAssoc($query);
			if(!empty($result['id'])){
				$update_primary_sql = "UPDATE email_addr_bean_rel SET primary_address=1 WHERE id='".$result['id']."'";
				$this->db->query($update_primary_sql);
			}
		}
	}
	/*function insertEmailAddress($module, $bean_id, $email_address, $is_primary = 0) {
		$email_address = addslashes ( $email_address );
		$sql_email_address = " SELECT id FROM email_addresses WHERE email_address = '" . $email_address . "' AND deleted = 0 ";
		$result_email_address = $this->db->query ( $sql_email_address );
		$row_email_address = $this->db->fetchByAssoc ( $result_email_address );
		
		if (empty ( $row_email_address ['id'] )) {
			$email_address_id = create_guid ();
			$sql_email_address = " INSERT INTO email_addresses (id, email_address, email_address_caps, invalid_email, opt_out, date_created, date_modified, deleted) VALUES ('" . $email_address_id . "', '" . $email_address . "', '" . strtoupper ( $email_address ) . "', 0, 0, NOW(), NOW(), 0) ";
			$this->db->query ( $sql_email_address );
		} else {
			$email_address_id = $row_email_address ['id'];
		}
		
		$sql_email_rel = " SELECT id FROM email_addr_bean_rel WHERE email_address_id = '" . $email_address_id . "' AND bean_id = '" . $bean_id . "' AND bean_module = '" . $module . "' AND deleted = 0 ";
		$result_email_rel = $this->db->query ( $sql_email_rel );
		$row_email_rel = $this->db->fetchByAssoc ( $result_email_rel );
		
		if (! empty ( $row_email_rel ['id'] )) {
			
			$sql_update_email_bean = " UPDATE email_addr_bean_rel SET email_address_id = '" . $email_address_id . "' WHERE bean_id = '" . $bean_id . "' AND bean_module = '" . $module . "' AND primary_address = '".$is_primary."'";
			$this->db->query ( $sql_update_email_bean );
		} else {
			
			$sql_email_bean = " INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, primary_address, reply_to_address, date_created, date_modified, deleted ) VALUES (UUID(), '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', '".$is_primary."', 0, NOW(), NOW(),0) ";
			$this->db->query ( $sql_email_bean );
		}
		
		return true;
	}*/
	private function checkLookupLeadExistsBBH($stProjectLeadId) {
		$stCheckSQL = 'SELECT project_lead_id id FROM  project_lead_lookup WHERE project_lead_id ="' . $stProjectLeadId . '"';
		$rsChkResult = $this->db->query ( $stCheckSQL );
		$arChkData = $this->db->fetchByAssoc ( $rsChkResult );
		return $arChkData;
	}
	private function updateNewTotalBidderCountBBH($stProjectLeadId) {
			
			// global $db;
		$arChkData = $this->checkLookupLeadExistsBBH ( $stProjectLeadId );
		
		// SQL to get the count of new and total bidders for this project
		// lead/parent project lead
		$stGetBidderCount = "SELECT COALESCE(leads.parent_lead_id,leads.id) ldgrpid
	,sum(if(bidders.is_viewed = 0,1,0)) newbidders
	,count(bidders.id) total_bidders
	FROM leads LEFT JOIN oss_leadclientdetail bidders on leads.id = lead_id AND bidders.deleted =0
	WHERE COALESCE(leads.parent_lead_id,leads.id) ='" . $stProjectLeadId . "' AND leads.deleted=0 GROUP BY coalesce(parent_lead_id,leads.id)";
		
		$rsResult = $this->db->query ( $stGetBidderCount );
		$arCountData = $this->db->fetchByAssoc ( $rsResult );
		
		if (isset ( $arChkData ['id'] ) && trim ( $arChkData ['id'] ) != '') {
			$stReplace = "UPDATE project_lead_lookup  SET new_bidder ='" . $arCountData ['newbidders'] . "'
		 				,total_bidder = '" . $arCountData ['total_bidders'] . "'
						WHERE project_lead_id = '" . $arCountData ['ldgrpid'] . "'	";
			$this->db->query ( $stReplace );
		} else {
			$stReplace = "INSERT INTO project_lead_lookup(id,project_lead_id,new_bidder,total_bidder) VALUES(UUID(),'" . $arCountData ['ldgrpid'] . "','" . $arCountData ['newbidders'] . "','" . $arCountData ['total_bidders'] . "')";
			$this->db->query ( $stReplace );
		}
	}
	function updateLeadVersionBidDueDateBBH($stParentLeadId) {
		$arChkData = $this->checkLookupLeadExistsBBH ( $stParentLeadId );
		
		$stLeadVerBidDueSQL = 'SELECT count(leads.id) countt
								 , coalesce(parent_lead_id,leads.id) leadid
								 ,min(bids_due) bids_due_grops
								 /* ,if(min(bids_due) = bids_due,bid_due_timezone,null ) bids_due_grops_timezone*/
								 ,GROUP_CONCAT( CONCAT(bids_due," {} ",bid_due_timezone )  ORDER BY bids_due ASC SEPARATOR "$$") bids_due_grops_timezone
							FROM leads WHERE leads.deleted =0
								and coalesce(parent_lead_id,leads.id)="' . $stParentLeadId . '"
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
			$stReplace = "UPDATE project_lead_lookup SET
			 lead_version = '" . $arData ['countt'] . "'
			,first_bid_due_date = '" . $arData ['bids_due_grops'] . "'
			,first_bid_due_timezone = '" . $arData ['bids_due_grops_timezone'] . "'
			WHERE  project_lead_id = '" . $stParentLeadId . "' ";
		} else {
			$stReplace = "INSERT INTO project_lead_lookup(project_lead_id,lead_version,first_bid_due_date,first_bid_due_timezone)
			 VALUES('" . $stParentLeadId . "','" . $arData ['countt'] . "','" . $arData ['bids_due_grops'] . "','" . $arData ['bids_due_grops_timezone'] . "')";
		}
		
		$this->db->query ( $stReplace );
	}
	
	/**
	 * Get Parent lead id from client instance
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
	 * Insert Account Contact Relationship
	 */
	function insertAccountContactRel($ci_contact_id, $ci_account_id) {
		if (! empty ( $ci_account_id ) && ! empty ( $ci_contact_id )) {
			$sql = "SELECT id FROM accounts_contacts WHERE contact_id = '" . $ci_contact_id . "' AND account_id = '" . $ci_account_id . "' AND deleted = 0";
			$query = $this->db->query ( $sql );
			$result = $this->db->fetchByAssoc ( $query );
			if (empty ( $result ['id'] )) {
				$insertRel = "INSERT INTO `accounts_contacts` (`id`,`contact_id`,`account_id`,`date_modified`,`deleted`)
						VALUES(UUID(),'" . $ci_contact_id . "','" . $ci_account_id . "',UTC_TIMESTAMP(),0)";
				$this->db->query ( $insertRel );
			}
		}
	}
	function clean_text($text)
	{
		$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=');
		$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}
	function ph_field_clean_text($text){
	
		$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=',' ','-');
		$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','','','-');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}
	function makeMysqlDate($datestring){
		$day = substr($datestring,-2);
		$month = substr($datestring,4,2);
		$year = substr($datestring, 0,4);
		return $year.'-'.$month.'-'.$day;
	}
}

?>
