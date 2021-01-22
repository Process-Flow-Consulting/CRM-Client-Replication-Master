<?php
// ini_set('display_errors',1);
// ini_set('memory_limit','1024M');
/**
 * Class for Pull Project Leads from Blue Book Hub to insert or update project
 * lead and data for others module direct through query.
 * @author Satish Gupta
 * 
 */
require_once 'custom/modules/Leads/pull_project_lead/fields_mapping.php';
require_once 'custom/include/common_functions.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/include/dynamic_dropdown.php';
require_once 'custom/modules/Users/filters/instancePackage.class.php';
class PullBBH {
	private $db;
	private $sugar_config;
	private $account_no;
	public $limit;
	private $session;
	function __construct($account_no) {
		global $db, $sugar_config;
		$this->db = $db;
		$this->sugar_config = $sugar_config;
		$this->account_no = $account_no;
		$this->limit = 50;
		$this->session = time ();
		$this->authenticate ();
	}
	/**
	 * Check Instance Validity
	 */
	
	public function authenticate() {
		global $app_strings;
		$obinstancePackage = new instancePackage ();
		$arPackage = $obinstancePackage->getPacakgeDetails ();
		if (strtotime ( $arPackage ['expiry_date'] ) < strtotime ( date ( "Y-m-d" ) )) {
			sugar_die ( $app_strings ['MSG_PACKAGE_EXPIRED_PULL_LEADS'] );
			$GLOBALS ['log']->fatal( $app_strings ['MSG_PACKAGE_EXPIRED_PULL_LEADS'] );
		}
	}
	
	/**
	 * Get Project Leads from Blue Book HUB
	 */
	private function getProjectLeads() {
		//Get All Target Classification
		$class_url = "";
		$classifications = $this->getUsersClassifications ();
		if (! empty ( $classifications )) {
			$class_url = "&targetclass=$classifications";
		}
		
		//Add Limit URL if limit is set
		$limit_url = "";
		if (isset ( $this->limit )) {
			$limit_url = "&max=$this->limit";
		}
		
		//Create Session Parameter url
		$session = "";
		if (isset ( $this->session )) {
			$session = "&sessionid=$this->session";
		}
		
		//Get Last Pull Date From Config
		$sql = "SELECT value FROM config WHERE `category`='instance' AND `name`='last_pull_date'";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		$last_pull_date = $result ['value'];
		$last_pull_date = date ( 'm/d/Y H:i:s', strtotime ( $last_pull_date ) );
		$lpd = "";
		if (trim ( $last_pull_date ) != '') {
			$lpd = "&lpd=" . urlencode ( $last_pull_date );
		}
		
		//Pull Data from Bluebook Hub
		// "http://65.206.121.202/wsnsa.dll/WService=wsbbhub/bb_hub/blgetleads_json.p?account=".$this->account_no.$class_url.$limit_url;
		$url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetleads_json.p";
		$data = "account=" . $this->account_no . $class_url . $limit_url . $lpd . $session;
		$GLOBALS ['log']->fatal ( "Last Pull Date: " . $last_pull_date . " Session:" . $this->session );
		$GLOBALS ['log']->fatal ( $url );
                $GLOBALS ['log']->fatal ( $data);		
		return $this->getPostData($url, $data);
		/*
		$file = "custom/modules/Leads/pull_project_lead/5178840_leads.json";
		return $content = file_get_contents($file);
		*/
	}
	
	/**
	 * Get Clients from BB HUB.
	 */
	private function getClients() {
		//Get Instance Account No.
		$sugarcrm_account = $this->getInstanceAccountNo ();
		//Get the Client Data from Bluebook Hub.		
		$url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetclient_json.p?client_id=" . $this->account_no . "&sugarcrm_account=" . $sugarcrm_account;				
		return $this->getData ( $url );
		
		//$file = "custom/modules/Leads/pull_project_lead/278529-0_json.txt";
		//return $content = file_get_contents($file);
	}
	/**
	 * Get Client Contact from BB HUB.
	 */
	private function getContacts() {
		//Get Instance Account No.
		$sugarcrm_account = $this->getInstanceAccountNo ();
		
		//Get the Client Contact Data from Bluebook Hub.
		// "http://65.206.121.202/wsnsa.dll/WService=wsbbhub/bb_hub/blgetcontact_json.p?contact_bb_id=".$this->account_no."&sugarcrm_account=".$sugarcrm_account;
		$url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetcontact_json.p?contact_bb_id=" . $this->account_no . "&sugarcrm_account=" . $sugarcrm_account;
		return $this->getData ( $url );
	}
	
	/**
	 * Get Target Classification from Users.
	 * 
	 */
	public function getUsersClassifications() {
		$class_str = '';
		/*
		 * Modified by : Ashutosh Date : 1 Jan 2013 Purpose : to get instance
		 * classificatoins
		 */
		$stGetInstanceClssSQL = "SELECT value FROM config WHERE name ='target_classifications' AND category ='instance'";
		$rsGetInstanceClssSQL = $this->db->query ( $stGetInstanceClssSQL );
		$arGetInstanceClssSQL = $this->db->fetchByAssoc ( $rsGetInstanceClssSQL );
		
		if (isset ( $arGetInstanceClssSQL ['value'] ) && trim ( $arGetInstanceClssSQL ['value'] ) != '') {
			
			$arClsficationIds = json_decode ( base64_decode ( $arGetInstanceClssSQL ['value'] ) );
			// $sql = "SELECT DISTINCT(c.category_no) FROM `oss_user_filters` uf
			// INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE
			// `filter_type`='classification' AND uf.`deleted`=0";
			$stClasficatoinIds = ' c.id IN ("' . implode ( '","', $arClsficationIds ) . '")';
			$sql = "SELECT DISTINCT(c.category_no) FROM oss_classification c WHERE $stClasficatoinIds   AND  c.`deleted`=0";
			
			$query = $this->db->query ( $sql );
			$i = 0;
			while ( $row = $this->db->fetchByAssoc ( $query ) ) {
				if ($i > 0) {
					$class_str .= "|";
				}
				$class_str .= $row ['category_no'];
				$i ++;
			}
		}
		
		return $class_str;
	}
	/**
	 * Get Local Project Lead detail by Id
	 * 
	 */
	public function getLocalProjectLead($id) {
		$sql = "SELECT * from `leads` WHERE id='" . $id . "' AND `deleted`=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		return $result;
	}
	/**
	 * Get Id from modules by dynamic field and value
	 * @param string $table
	 * @param string $field_name
	 * @param string $field_value
	 * @return void|string
	 */
	public function checkExistingRecord($table, $field_name, $field_value) {
		$sql = "SELECT id FROM " . $table . " WHERE " . $field_name . " = '" . $field_value . "' AND deleted=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		if (! empty ( $result )) {
			return $result ['id'];
		}
		return;
	}
	
	/**
	 * Get Local BI Information by BB BI Id.
	 * @param string $bb_id
	 * @return string|boolean
	 */
	
	private function getLocalBIInfo($bb_id){
		$bi_sql = "SELECT `id`,`name`,`description`,`type_order`,`image_description`,`image_url`,`sort_order` FROM oss_businessintelligence WHERE `mi_oss_businessintelligence_id`='".$bb_id."' AND `deleted`=0";
		$bi_query = $this->db->query($bi_sql);
		$bi_result = $this->db->fetchByAssoc($bi_query);
		if(!empty($bi_result)){
			return $bi_result;
		}
		return false;
	}
	
	/**
	 * Insert Update Project Leads
	 */
	public function insertUpdateProjectLeads() {
		global $pl_fields;
		$process_path = 'upload/process/';
		$oss_timedate = new OssTimeDate ();
		
		$restricted_fields = array (
				'pl_bb_id',
				'undef',
				'proj_stage',
				'Bidder List',
				'OnlinePlans',
				'Classification',
				'bidscope' 
		);
		
		$data_json = $this->getProjectLeads ();
		
		// print_r($data_json);die;
		
		if (empty ( $data_json )) {
			sugar_die ( 'No new or modified project leads are available.' );
		}
		
		$data = json_decode ( $data_json, true );
		/*
		 * echo "<pre>"; print_r($data); echo "</pre>";die;
		 */
		
		$total_leads = count ( $data ['response'] ['Project'] );
		
		if ($total_leads > 0) {
			
			$lock_file = $process_path . 'process_lock';
			$content = file_get_contents ( $lock_file );
			$content = explode ( "_", $content );
			$pid = $content [0];
			
			$text = $pid . "_" . time ();
			
			$fp1 = fopen ( $lock_file, "w" );
			fwrite ( $fp1, $text );
			fclose ( $fp1 );
		}
		$inserted_lead = 0;
		$updated_lead = 0;
		foreach ( $data ['response'] ['Project'] as $pl ) {
			// Check Existing Lead including deleted leads
			$leadSql = "SELECT `id` FROM `leads` WHERE `mi_lead_id` = '" . $pl ['pl_bb_id'] . "'";
			$leadQuery = $this->db->query ( $leadSql );
			$existingLead = $this->db->fetchByAssoc ( $leadQuery );
			$pl_is_update = false;
			$pl_status = 'New';
			
			// Query for Project Lead
			$plsql = "INSERT INTO leads SET ";
			$pl_audit_sql = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
			
			
			if (! empty ( $existingLead )) {
				$pl_is_update = true;
				$lead_id = $existingLead ['id'];
				$locallead = $this->getLocalProjectLead ( $lead_id );
				if ($locallead ['status'] == 'Converted') {
					$pl_status = $locallead ['status'];
				}
				// print_r($locallead); die;
			} else {
				$lead_id = create_guid ();
				//if new lead add own id to parent_lead_id
				$plsql .= " parent_lead_id = '".$lead_id."', ";
			}
			
			$plsql .= "`id` = '" . $lead_id . "', 
					`date_entered` = UTC_TIMESTAMP(), 
					`date_modified` = UTC_TIMESTAMP(), 
					`modified_user_id` = '1',
					`created_by` = '1',
					`deleted` = '0',
					`assigned_user_id` = '1',
					`team_id` = '1',
					`team_set_id` = '1',
					`status` = '" . $pl_status . "',
					`last_name` = '" . addslashes ( $pl ['proj_title'] ) . "',
					`mi_lead_id` = '" . addslashes ( $pl ['pl_bb_id'] ) . "',
					`project_lead_id` = '" . $pl ['pl_bb_id'] . "'";
			$plupdate = " ON DUPLICATE KEY UPDATE 
					`date_modified` = VALUES(`date_modified`),
					`last_name` = VALUES(`last_name`),
					`status` = VALUES(`status`)";
			$pl_counter = 0;
			
			// Query For Bidders List
			$bidderSQL = "INSERT INTO `oss_leadclientdetail` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `team_id`, `team_set_id`, `assigned_user_id`, `contact_email`, `contact_phone_no`, `role`, `mi_oss_leadclientdetail_id`, `contact_fax`, `lead_source`, `contact_id`, `lead_id`, `account_id`,`bid_status`) VALUES ";
			$bidderSQL1 = " ON DUPLICATE KEY UPDATE `name`  = VALUES(`name`), `date_modified` = VALUES(`date_modified`), `contact_email` = VALUES(`contact_email`), `contact_phone_no` = VALUES(`contact_phone_no`), `role` = VALUES(`role`), `contact_fax` = VALUES(`contact_fax`), `contact_id` = VALUES(`contact_id`),  `lead_id` = VALUES(`lead_id`), `account_id` = VALUES(`account_id`), `bid_status` = VALUES(`bid_status`);";
			$classInsertSQL = " INSERT INTO oss_classifcation_leads_c (`id`, `date_modified`, `deleted`,`oss_classi4427ication_ida`, `oss_classi7103dsleads_idb`) VALUES ";
			$is_insert_class = false;
			$is_audit = false;
			$lcd_bid_status = array ();
			foreach ( $pl as $key => $data ) {
				// Create Query for insert or update project lead
				if (! in_array ( $key, $restricted_fields )) {
					
					$field_name = $pl_fields [$key];
					if (trim ( $field_name ) == '') {
						continue;
					}
					$field_value = $data;
					$field_value = htmlspecialchars ( $field_value, ENT_QUOTES );
					$field_value = htmlspecialchars_decode ( $field_value, ENT_QUOTES );
					$field_value = addslashes ( $field_value );
					
					$data_type = '';
					
					if ($field_name == 'start_date' || $field_name == 'end_date') {
						$date_arr = explode ( "/", $field_value );
						$field_value = date ( "Y-m-d", mktime ( 0, 0, 0, $date_arr [0], $date_arr [1], $date_arr [2] ) );
					}
					
					if ($field_name == 'bids_due') {
						if ($pl ['asap'] == 'true') {
							$field_value = "0000-00-00 00:00:00";
						} else {
							$date = date ( $field_value );
							$timezone = 'Eastern';
							if (isset ( $pl ['timezone'] ) && ! empty ( $pl ['timezone'] )) {
								$timezone = $pl ['timezone'];
							}
							$gmt_date = $oss_timedate->convertDateForDB ( $date, $timezone, false, true );
							$field_value = $gmt_date;
						}
					}
					
					if ($field_name == 'county_id') {
						$county = $this->getCounty ( $field_value, $pl ['state'] );
						$field_value = $county ['id'];
					}
					
					if ($field_name == 'union_c' || $field_name == 'non_union' || $field_name == 'prevailing_wage' || $field_name == 'asap') {
						
						if ($field_value == 'true') {
							$field_value = '1';
						} else {
							$field_value = '0';
						}
					}
					
					if ($field_name == 'structure') {
						$existing_val = checkExistingValueFromDom ( 'structure', $field_value );
						if (empty ( $existing_val )) {
							$new_list_value = '["' . $field_value . '","' . $field_value . '"]';
							editDropdownList ( 'structure_non_building', $new_list_value );
						} else {
							$field_value = $existing_val;
						}
						$data_type = 'enum';
					}
					
					if ($field_name == 'type') {
						$existing_val = checkExistingValueFromDom ( 'project_type_dom', $field_value );
						if (empty ( $existing_val )) {
							$new_list_value = '["' . $field_value . '","' . $field_value . '"]';
							editDropdownList ( 'project_type_dom', $new_list_value );
						} else {
							$field_value = $existing_val;
						}
						$data_type = 'enum';
					}
					
					if ($field_name == 'project_status') {
						// $field_value = clean_string($field_value);
						$existing_val = checkExistingValueFromDom ( 'project_status_dom', $field_value );
						if (empty ( $existing_val )) {
							$new_list_value = '["' . $field_value . '","' . $field_value . '"]';
							editDropdownList ( 'project_status_dom', $new_list_value );
						}
						$data_type = 'enum';
					}
					
					if ($field_name == 'lead_source') {
						if ($field_value == 'BB') {
							$field_value = strtolower ( $field_value );
						}
						$existing_val = checkExistingValueFromDom ( 'lead_source_list', $field_value );
						if (empty ( $existing_val )) {
							$new_list_value = '["' . $field_value . '","' . $field_value . '"]';
							editDropdownList ( 'lead_source_list', $new_list_value );
						}
						$data_type = 'enum';
					}
					
					/*
					 * if($field_name=='scope_of_work'){ $field_value =
					 * html_entity_decode($field_value); $field_value =
					 * htmlspecialchars($field_value,ENT_NOQUOTES); echo
					 * $locallead [$field_name]."<br>"; echo
					 * $field_value."<br>=======<br>"; }
					 */
					
					if ($field_name == 'asap') {
						if ($field_value == 'true') {
							if (! isset ( $pl ['bids_due'] )) {
								$plsql .= ", `bids_due` = '0000-00-00 00:00:00' ";
								$plupdate .= ", `bids_due` = VALUES(`bids_due`) ";
							}
						}
					}
					
					// Prepare Query For Change Log
					if ($pl_is_update == true) {
						if ($locallead [$field_name] != $field_value) {
							$is_audit = true;
							if ($pl_counter > 0) {
								$pl_audit_sql .= ",";
							}
							if ($field_name == 'bids_due') {
								$data_type = 'datetimecombo';
							}
							
							$lead_audit_id = create_guid ();
							$pl_audit_sql .= " ('" . $lead_audit_id . "','" . $lead_id . "',UTC_TIMESTAMP(),'Blue Book','" . $field_name . "','" . $data_type . "','" . $locallead [$field_name] . "','" . $field_value . "') ";
							$pl_counter ++;
						}
					}
					
					// Prepare Query for Insert or Update Project Lead
					$plsql .= ", `" . $field_name . "` = '" . $field_value . "'";
					$plupdate .= ", `" . $field_name . "` =  VALUES (`" . $field_name . "`)";
				}
				
				// insert update Bidders List
				if ($key == 'Bidder List') {
					$bidder_counter = 0;
					foreach ( $data as $bidder ) {
						// Check Existing Client
						if ($bidder_counter > 0) {
							$bidderSQL .= ", ";
						}
						$bidderSQL .= $this->insertUpdateBidders ( $bidder, $lead_id );
						$lcd_bid_status [$bidder ['bidder_bb_id']] = $bidder ['bid_status'];
						$bidder_counter ++;
					}
					$bidderSQL .= $bidderSQL1;
				}
				
				// insert update Online Plans
				if ($key == 'OnlinePlans') {
					// get the count of online plans by lead id
					$is_op_modified = false;
					if ($pl_is_update) {
						$sql = "SELECT COUNT(1) opctn FROM oss_onlineplans WHERE lead_id = '" . $lead_id . "' AND deleted = 0";
						$query = $this->db->query ( $sql );
						$result = $this->db->fetchByAssoc ( $query );
						$total_op_local = $result ['opctn'];
						$total_op_json = count ( $pl ['OnlinePlans'] );
						if ($total_op_json > $total_op_local) {
							$is_op_modified = true;
						}
					}
					
					foreach ( $data as $online_plan ) {
						$this->insertUpdateOnlinePlans ( $online_plan, $lead_id, $pl_is_update, $is_op_modified );
					}
				}
				
				// insert update Classification
				if ($key == 'Classification') {					
					$is_class_modified = false;
					foreach ( $data as $class ) {
						$cat_no = $class ['classification_id'];						
						$classInsertSQL1 = $this->insertUpdateLeadClassRel ( $lead_id, $cat_no, $pl_is_update );
						if ($classInsertSQL1) {
							$is_class_modified = true;
						}
						
					}
					// Insert Classification change log data
					if ($is_class_modified == true) {
						// Maintain Change log for Project Lead
						$lead_audit_id = create_guid ();
						$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $lead_id . "' AND `field_name`='Project Class Modification'";
						$query = $this->db->query ( $sql );
						$result = $this->db->fetchByAssoc ( $query );
						$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'" . $lead_id . "',UTC_TIMESTAMP(),'Blue Book','Project Class Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP());";
						$this->db->query ( $insertSQL );
						changeLogFlag ( $lead_id, $this->db );
					}
				}
			}
			
			$plsql .= $plupdate;
			
			// Insert Update Project Lead
			$pl_query = $this->db->query ( $plsql );
			if ($pl_query) {
				if ($pl_is_update) {
					$updated_lead += 1;
				} else {
					$inserted_lead += 1;
				}
				
				$file_text = $inserted_lead . "|" . $updated_lead;
				$status_file = $process_path . 'import_status';
				$fp = fopen ( $status_file, "w" );
				fwrite ( $fp, $file_text );
				fclose ( $fp );
				
				// Write time after streaming
			}
			
			// Insert Bidders List
			if (isset ( $pl ['Bidder List'] )) {
				// Check the bid status changes and no of bidders
				$is_bidder_modify = false;
				if ($pl_is_update == true) {
					// Check No of Existing Bidders
					$sql = "SELECT count(1) total_bidders FROM oss_leadclientdetail WHERE lead_id='" . $lead_id . "' AND deleted=0";
					$query = $this->db->query ( $sql );
					$result = $this->db->fetchByAssoc ( $query );
					
					$json_bidder_count = count ( $pl ['Bidder List'] );
					$local_bidder_count = $result ['total_bidders'];
					if ($json_bidder_count > $local_bidder_count) {
						$is_bidder_modify = true;
					}
					
					if ($is_bidder_modify == false) {
						$sql = "SELECT mi_oss_leadclientdetail_id, bid_status FROM oss_leadclientdetail WHERE lead_id='" . $lead_id . "' AND deleted=0";
						$query = $this->db->query ( $sql );
						$local_lcd_bid_status = array ();
						while ( $row = $this->db->fetchByAssoc ( $query ) ) {
							$local_lcd_bid_status [$row ['mi_oss_leadclientdetail_id']] = $row ['bid_status'];
						}
						
						foreach ( $local_lcd_bid_status as $key => $value ) {
							if ($lcd_bid_status [$key] != $value) {
								$is_bidder_modify = true;
							}
						}
					}
					if ($is_bidder_modify == true) {
						$lead_audit_id = create_guid ();
						if ($is_audit == true) {
							$pl_audit_sql .= ",";
						}
						$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $lead_id . "' AND `field_name`='Bidders List Modification'";
						$query = $this->db->query ( $sql );
						$result = $this->db->fetchByAssoc ( $query );
						$pl_audit_sql .= " ('" . $lead_audit_id . "','" . $lead_id . "',UTC_TIMESTAMP(),'Blue Book','Bidders List Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP()) ";
					}
				}
				
				$this->db->query ( $bidderSQL );
			}
			
			// Insert New Total Bidders Count
			// Update bidder count
			$parent_lead_id = $this->getParentLeadId ( $lead_id );
			
			// Bidder list logic hooks
			$this->updateNewTotalBidderCountBBH ( $parent_lead_id );
			
			$this->updateLeadVersionBidDueDateBBH ( $parent_lead_id );
			
			// Update online plans
			updateOnlineCount ( $lead_id );
			
			// Insert Lead Change Log Query
			if ($is_audit == true || $is_bidder_modify == true) {
				// echo $pl_audit_sql;
				$this->db->query ( $pl_audit_sql );
				changeLogFlag ( $lead_id, $this->db );
				$is_audit = false;
				$is_bidder_modify = false;
			}
			
			// Upadte Current time into process lock file
			$lock_file = $process_path . 'process_lock';
			$content = file_get_contents ( $lock_file );
			$content = explode ( "_", $content );
			$pid = $content [0];
			
			$text = $pid . "_" . time ();
			
			$fp1 = fopen ( $lock_file, "w" );
			fwrite ( $fp1, $text );
			fclose ( $fp1 );
		}
		
		// Save Last Pulled Date
		$sql = "UPDATE config SET `value`= CONVERT_TZ(UTC_TIMESTAMP(),'GMT','US/Eastern') WHERE `category`='instance' AND `name`='last_pull_date'";
		$this->db->query ( $sql );
		
		//set update prev bid to flag
		setPreviousBidToUpdate();
		
		if ($total_leads >= $this->limit) {
			$GLOBALS ['log']->fatal ( 'Next ' . $this->limit );
			$status_file = $process_path . 'import_status';
			$fp = fopen ( $status_file, "w" );
			fwrite ( $fp, "0|0" );
			fclose ( $fp );
			$this->insertUpdateProjectLeads ();
		}
	}
	
	/**
	 * Get County by County Number and State Abbr
	 */
	function getCounty($county_no, $state) {
		$sql = "SELECT `id`,`name` FROM oss_county WHERE county_number = '" . $county_no . "' AND county_abbr = '" . $state . "' AND deleted=0";
		$query = $this->db->query ( $sql );
		$result = $this->db->fetchByAssoc ( $query );
		return $result;
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
	 * Insert Lead and Classification Relationship Data
	 *
	 * @param
	 *        	id - $mi_lead_id - lead id in master
	 * @param
	 *        	id - $lead_id - lead id in master
	 * @return boolean
	 */
	function insertUpdateLeadClassRel($lead_id, $classification_id, $pl_is_update) {
		$existing_sql = "SELECT oc.id FROM oss_classification oc Inner JOIN oss_classifcation_leads_c ocl on oc.id=ocl.oss_classi4427ication_ida  AND ocl.deleted = 0 WHERE oc.category_no =  '" . $classification_id . "' AND ocl.oss_classi7103dsleads_idb = '" . $lead_id . "' AND oc.deleted = 0";
		$query = $this->db->query ( $existing_sql );
		$res = $this->db->fetchByAssoc ( $query );
		if (empty ( $res ['id'] )) {
			$classification_id = $this->checkExistingRecord ( 'oss_classification', 'category_no', $classification_id );
			if (! empty ( $classification_id )) {
				$insertSQL = "INSERT INTO oss_classifcation_leads_c (`id`, `date_modified`, `deleted`,`oss_classi4427ication_ida`, `oss_classi7103dsleads_idb`) 
						VALUES (UUID(),UTC_TIMESTAMP(),'0','" . $classification_id . "','" . $lead_id . "')";
				$this->db->query ( $insertSQL );
				if ($pl_is_update == true) {
					return true;
				}
			}
		}
	}
	
	/**
	 * Insert Update Bidders
	 */
	function insertUpdateBidders($bidder, $lead_id) {
		// insert client
		$client_id = $this->insertClient ( $bidder );
		
		// Update First Classification for the client
		// updateClientsFirstClassification($client_id);
		// Insert client contact
		$contact_id = $this->insertContact ( $bidder );
		
		// Insert Client and Client Contact Relationship
		$this->insertAccountContactRel ( $contact_id, $client_id );
		
		// Add Bidders Role if does not exists
		$bidders_role = addslashes ( $bidder ['role'] );
		if (isset ( $bidders_role ) && ! empty ( $bidders_role )) {
			$existing_val = checkExistingValueFromDom ( 'role_dom', $bidders_role );
			if (empty ( $existing_val )) {
				$new_list_value = '["' . $bidders_role . '","' . $bidders_role . '"]';
				editDropdownList ( 'role_dom', $new_list_value );
			}
		}
		
		// Add Bidders Bid Status dynamic dropdown
		$bid_status = addslashes ( $bidder ['bid_status'] );
		if (isset ( $bid_status ) && ! empty ( $bid_status )) {
			$existing_val = checkExistingValueFromDom ( 'client_bid_status_dom', $bid_status );
			if (empty ( $existing_val )) {
				$new_list_value = '["' . $bid_status . '","' . $bid_status . '"]';
				editDropdownList ( 'client_bid_status_dom', $new_list_value );
			}
		}
		

		//retrieve local client contact details
		$ciContact = $this->getClientContact ( $contact_id, $this->db );
		
		$cc_fax = '';
		if (isset ( $bidder ['cc_fax'] )) {
			$cc_fax = addslashes ( $bidder ['cc_fax'] );
		}else{
		    $cc_fax = $ciContact['phone_fax'];												
		}
		
		$c_name = '';
		if (isset ( $bidder ['c_name'] )) {
			$c_name = addslashes ( $bidder ['c_name'] );
		}
		$cc_email = '';
		if (isset ( $bidder ['cc_email'] )) {
			$cc_email = addslashes ( $bidder ['cc_email'] );
		}else{
			$cc_email = $ciContact['email1']; 
		}
		$cc_phone = '';
		if (isset ( $bidder ['cc_phone'] )) {
			$cc_phone = addslashes ( $bidder ['cc_phone'] );
		}else{
		    $cc_phone = $ciContact['phone_work'];		
		}

		$sql = " ( UUID(), '" . $c_name . "', UTC_TIMESTAMP(), UTC_TIMESTAMP(), '1', '1', '0', '1', '1', '1', '" . $cc_email . "', '" . $cc_phone . "', '" . $bidders_role . "', '" . addslashes ( $bidder ['bidder_bb_id'] ) . "', '" . $cc_fax . "', 'bb', '" . $contact_id . "', '" . $lead_id . "', '" . $client_id . "','" . $bid_status . "') ";
		return $sql;
	}
	
	/**
	 */
	public function insertClient($bidder) {
		// Check Existing Client by master id
		isset ( $bidder ['c_name'] ) ? $c_name = $bidder ['c_name'] : $c_name = '';
		isset ( $bidder ['c_phone'] ) ? $c_phone = addslashes ( $bidder ['c_phone'] ) : $c_phone = '';
		isset ( $bidder ['c_fax'] ) ? $c_fax = addslashes ( $bidder ['c_fax'] ) : $c_fax = '';
		isset ( $bidder ['c_email'] ) ? $c_email = addslashes ( $bidder ['c_email'] ) : $c_email = '';
		isset ( $bidder ['c_county'] ) ? $c_county = addslashes ( $bidder ['c_county'] ) : $c_county = '';
		isset ( $bidder ['c_state'] ) ? $c_state = addslashes ( $bidder ['c_state'] ) : $c_state = '';
		isset ( $bidder ['proview_url'] ) ? $proview_url = $bidder ['proview_url'] : $proview_url = '';
		isset ( $bidder ['c_city'] ) ? $c_city = addslashes ( $bidder ['c_city'] ) : $c_city = '';
		isset ( $bidder ['c_state'] ) ? $c_state = addslashes ( $bidder ['c_state'] ) : $c_state = '';
		isset ( $bidder ['c_zip'] ) ? $c_zip = addslashes ( $bidder ['c_zip'] ) : $c_zip = '';
		isset ( $bidder ['class'] ) ? $class = addslashes ( $bidder ['class'] ) : $class = array ();
		isset ( $bidder ['client_bb_id'] ) ? $client_bb_id = $bidder ['client_bb_id'] : $client_bb_id = '';
		
		$c_name = htmlspecialchars_decode ( $c_name, ENT_QUOTES );
		$c_name = addslashes ( $c_name );
		$proview_url = htmlspecialchars_decode ( $proview_url, ENT_QUOTES );
		
		$existingClient = $this->checkExistingRecord ( 'accounts', 'mi_account_id', $client_bb_id );
		if (! empty ( $existingClient )) {
			if ($c_name == '') {
				return $existingClient;
			}
		}
		if (empty ( $existingClient )) {
			$existingClient = checkExistingClient ( $c_name, $c_phone, $c_fax, $c_email );
		}
		if (empty ( $existingClient )) {
			$id = create_guid ();
			// Get County Id by County Code
			$county = $this->getCounty ( $c_county, $c_state );
			
			//get county name for international clients
			//@modified by Mohit Kumar Gupta 30-07-2014
			$countyName = $this->getInternationalCountyName($county ['id']);
			
			
			$sql = "INSERT INTO accounts (`id`,`name`,`date_entered`,`date_modified`,`modified_user_id`, `created_by`, `deleted`,`team_id`,`team_set_id`,`phone_office`,`phone_fax`,`proview_url`,`visibility`,`mi_account_id`,`lead_source`,`billing_address_city`,`billing_address_state`,`billing_address_postalcode`,`county_name`,`county_id`) 
					VALUES ('" . $id . "','" . $c_name . "',UTC_TIMESTAMP(),UTC_TIMESTAMP(),'1','1','0','1','1','" . $c_phone . "','" . $c_fax . "','" . addslashes ( $proview_url ) . "','0','" . addslashes ( $bidder ['client_bb_id'] ) . "','bb','" . $c_city . "','" . $c_state . "','" . $c_zip . "','" . addslashes ( $countyName ) . "','" . addslashes ( $county ['id'] ) . "') ";
			$this->db->query ( $sql );
			
			// Insert Email Id
			if (isset ( $bidder ['c_email'] ) && ! empty ( $bidder ['c_email'] )) {
				$this->insertUpdateEmailAddress ( 'Accounts', $id, $c_email);
			}
			
			// Add Classification
			$class_arr = explode ( ",", $bidder ['class'] );
			foreach ( $class_arr as $class_cat_no ) {
				// $classification_id =
				// $this->checkExistingRecord('oss_classification',
				// 'category_no', $class_cat_no);
				$this->insertAccountClassRel ( $class_cat_no, $id );
			}
			return $id;
		} else {
			// Get Client
			$clientRes = $this->getClient ( $existingClient, $this->db );
			
			// Update Client
			$client_field_array = array (
					'c_name' => 'name',
					'c_phone' => 'phone_office',
					'c_fax' => 'phone_fax',
					'proview_url' => 'proview_url',
					'c_city' => 'billing_address_city',
					'c_state' => 'billing_address_state',
					'c_zip' => 'billing_address_postalcode',
					'c_county' => 'county_id',
					'client_bb_id' => 'mi_account_id',
			);
			$updateSQL = "UPDATE accounts SET ";
			$updateSQL .= "`date_modified` = UTC_TIMESTAMP() ";
			if ($clientRes ['is_modified'] == 1) {
				// update client based on modification
				$client_counter = 0;
				foreach ( $client_field_array as $key => $value ) {
					if (empty ( $clientRes [$value] )) {
						$field_name = $client_field_array [$key];
						$field_value = '';
						if (isset ( $bidder [$key] )) {
							$field_value = htmlspecialchars_decode ( $bidder [$key], ENT_QUOTES );
							$field_value = addslashes ( $field_value );
						}
						
						if ($field_name == 'county_id') {
							$county = $this->getCounty ( $field_value, $c_state );
							$field_value = $county ['id'];
							//get county name for international clients
							//@modified by Mohit Kumar Gupta 30-07-2014
							$countyName = $this->getInternationalCountyName($county ['id']);
							$updateSQL .= ",`county_name` = '" . $countyName . "' ";
						}
						if (trim ( $field_value ) != '') {
							//Maintain Change Log							
							$field_type = '';
							if($field_name == 'billing_address_state'){
								$field_type = 'enum';
							}
							
							insertChangeLog($this->db, 'accounts', $existingClient, '', $field_value, $field_name, $field_type, 'Blue Book');
							$updateSQL .= ",`$field_name` = '" . $field_value . "' ";
							$client_counter ++;
						}
					}
				}
			} else {
				$client_counter = 0;
				foreach ( $client_field_array as $key => $value ) {
					$field_name = $client_field_array [$key];
					$field_value = '';
					if (isset ( $bidder [$key] )) {
						$field_value = htmlspecialchars_decode ( $bidder [$key], ENT_QUOTES );
						$field_value = addslashes ( $field_value );
					}
					
					if ($field_name == 'county_id') {
						$county = $this->getCounty ( $field_value, $c_state );
						$field_value = $county ['id'];
						//get county name for international clients
						//@modified by Mohit Kumar Gupta 30-07-2014
						$countyName = $this->getInternationalCountyName($county ['id']);
						$updateSQL .= ",`county_name` = '" . $countyName . "' ";
					}
					
					if (trim ( $field_value ) != '') {
						
						// Maintain Change Log
						if ($clientRes ['visibility'] == 1) {
							$field_type = '';
							if ($field_name == 'billing_address_state') {
								$field_type = 'enum';
							}
							
							insertChangeLog ( $this->db, 'accounts', $existingClient, $clientRes [$field_name], $field_value, $field_name, $field_type, 'Blue Book');
						}
						
						$updateSQL .= ",`$field_name` = '" . $field_value . "' ";
						$client_counter ++;
					}
				}
			}
			$updateSQL .= "WHERE `id` = '" . $existingClient . "'";
			if ($client_counter > 0) {
				$this->db->query ( $updateSQL );
			}
			// Update Email Id
			if (isset ( $bidder ['c_email'] ) && ! empty ( $bidder ['c_email'] )) {
				$this->insertUpdateEmailAddress ( 'Accounts', $existingClient, $bidder ['c_email'],true, $clientRes);
			}
			// insert update classification
			if (count ( $class ) > 0) {
				$class_arr = explode ( ",", $class );
				foreach ( $class_arr as $class_cat_no ) {					
					$this->insertAccountClassRel ( $class_cat_no, $existingClient );
				}
			}
			return $existingClient;
		}
	}
	
	/**
	 */
	public function insertContact($bidder) {
		isset ( $bidder ['cc_fname'] ) ? $cc_fname = $bidder ['cc_fname'] : $cc_fname = '';
		isset ( $bidder ['cc_lname'] ) ? $cc_lname = $bidder ['cc_lname'] : $cc_lname = '';
		isset ( $bidder ['cc_phone'] ) ? $cc_phone = addslashes ( $bidder ['cc_phone'] ) : $cc_phone = '';
		isset ( $bidder ['cc_fax'] ) ? $cc_fax = addslashes ( $bidder ['cc_fax'] ) : $cc_fax = '';
		isset ( $bidder ['cc_email'] ) ? $cc_email = addslashes ( $bidder ['cc_email'] ) : $cc_email = '';
		isset ( $bidder ['contact_bb_id'] ) ? $contact_bb_id = $bidder ['contact_bb_id'] : $contact_bb_id = '';
		
		// Check Existing Contact by master id
		$existingContact = $this->checkExistingRecord ( 'contacts', 'mi_contact_id', $contact_bb_id );
		if (empty ( $existingContact )) {
			$name = $cc_fname . " " . $cc_lname;
			$name = htmlspecialchars_decode ( $name, ENT_QUOTES );
			$name = addslashes ( $name );
			$existingContact = checkExistingClientContact ( $name, $cc_phone, $cc_fax, $cc_email );
		}
		if (empty ( $existingContact )) {
			$id = create_guid ();
			$sql = "INSERT INTO contacts (`id`,`first_name`,`last_name`,`date_entered`,`date_modified`,`modified_user_id`, `created_by`, `deleted`,`team_id`,`team_set_id`,`phone_work`,`phone_fax`,`mi_contact_id`,`visibility`,`lead_source`)
					VALUES ('" . $id . "','" . $cc_fname . "','" . $cc_lname . "',UTC_TIMESTAMP(),UTC_TIMESTAMP(),'1','1','0','1','1','" . $cc_phone . "','" . $cc_fax . "','" . addslashes ( $contact_bb_id ) . "','0','bb') ";
			$this->db->query ( $sql );
			// Insert Email Address
			if (isset ( $bidder ['cc_email'] ) && ! empty ( $bidder ['cc_email'] )) {
				$this->insertUpdateEmailAddress ( 'Contacts', $id, $bidder ['cc_email'] );
			}
			return $id;
		} else {
			// Get Client Contact
			$contactRes = $this->getClientContact ( $existingContact, $this->db );
			
			// update contact
			$contact_field_array = array (
					'cc_fname' => 'first_name',
					'cc_lname' => 'last_name',
					'cc_phone' => 'phone_work',
					'cc_fax' => 'phone_fax',
					'contact_bb_id' => 'mi_contact_id'
			);
			$updateSQL = "UPDATE contacts SET ";
			$updateSQL .= "`date_modified` = UTC_TIMESTAMP() ";
			if ($contactRes ['is_modified'] == 1) {
				// update contact based on modified field
				foreach ( $contact_field_array as $key => $value ) {
					if (empty ( $contactRes [$value] )) {
						if (isset ( $bidder [$key] )) {
							if (trim ( $bidder [$key] ) != '') {
								$field_name = $contact_field_array[$key];
								$field_value = htmlspecialchars_decode ( $bidder [$key], ENT_QUOTES );
								
								$field_type = '';
								if($field_name == 'primary_address_state' || $field_name == 'alt_address_state'){
									$field_type = 'enum';
								}								
								insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes [$field_name], $field_value, $field_name, $field_type, 'Blue Book');
								$updateSQL .= ",`$field_name` = '" . addslashes ( $field_value ) . "' ";
							}
						}
					}
				}
			} else {
				foreach ( $contact_field_array as $key => $value ) {
					if (isset ( $bidder [$key] )) {
						if (trim ( $bidder [$key] ) != '') {
							$field_name = $contact_field_array [$key];
							$field_value = htmlspecialchars_decode ( $bidder [$key], ENT_QUOTES );
							
							if ($contactRes ['visibility'] == 1) {
								$field_type = '';
								if ($field_name == 'primary_address_state' || $field_name == 'alt_address_state') {
									$field_type = 'enum';
								}
								insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes [$field_name], $field_value, $field_name, $field_type, 'Blue Book' );
							}
							$updateSQL .= ",`$contact_field_array[$key]` = '" . addslashes ( $field_value ) . "' ";
						}
					}
				}
			}
			$updateSQL .= "WHERE `id` = '" . $existingContact . "'";

			$this->db->query ( $updateSQL );
			// update email Address
			if (isset ( $bidder ['cc_email'] ) && ! empty ( $bidder ['cc_email'] )) {
				$this->insertUpdateEmailAddress ( 'Contacts', $existingContact, $bidder ['cc_email'],true, $contactRes);
			}
			
			if(trim($existingContact) != ''){
					//get local client details
					$ciContact = $this->getClientContact ( $existingContact, $this->db );
					
					$stSQL = 'UPDATE oss_leadclientdetail 
							SET  contact_fax = "'.$ciContact['phone_fax'].'" 
							,contact_email="'.$ciContact['email1'].'" 
							,contact_phone_no="'.$ciContact['phone_work'].'"
							 WHERE contact_id ="'.$existingContact.'" ';
							 
					$this->db->query ( $stSQL );	
			}
			return $existingContact;
		}
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
	/*function insertEmailAddress($module, $bean_id, $email_address) {
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
			
			$sql_update_email_bean = " UPDATE email_addr_bean_rel SET email_address_id = '" . $email_address_id . "' WHERE bean_id = '" . $bean_id . "' AND bean_module = '" . $module . "' AND primary_address = 1";
			$this->db->query ( $sql_update_email_bean );
		} else {
			
			$sql_email_bean = " INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, primary_address, reply_to_address, date_created, date_modified, deleted ) VALUES (UUID(), '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', 1, 0, NOW(), NOW(),0) ";
			$this->db->query ( $sql_email_bean );
		}
		
		return true;
	}*/
	
	/**
	 * Insert Update Clients
	 */
	public function insertUpdateClients($import_client = false) {
		global $client_fields, $app_list_strings;
		
		require_once 'custom/include/common_functions.php';
		
		// this fields will be not saved
		$restricted_fields = array (
				'Contact',
				'Region',
				'BI',
				'email',
				'year_established',
				'tps',
				'noe',
				'prev_proj' 
		);
		
		$data_json = $this->getClients (); // get json
		                                   // print_r($data_json);
		$data_array = json_decode ( $data_json, true ); // decode json to array
		/*
		 * echo "<pre>"; print_r($data_array); echo "</pre>";die;
		 */
		
		foreach ( $data_array ['response'] ['Company'] as $client ) {
			
			$account_id = create_guid (); // create id for account
			
			isset ( $client ['name'] ) ? $client_name = $client ['name'] : $client_name = '';
			isset ( $client ['phone_office'] ) ? $client_ph_office = $client ['phone_office'] : $client_ph_office = '';
			isset ( $client ['phone_fax'] ) ? $client_ph_fax = $client ['phone_fax'] : $client_ph_fax = '';
			isset ( $client ['email'] ) ? $client_email = $client ['email'] : $client_email = '';
			
			$client_name = htmlspecialchars_decode ( $client_name, ENT_QUOTES );
			$client_name = addslashes ( $client_name );
			// check if the client exist or not
			$mi_account_id = $client ['client_bb_id'];
			if (! empty ( $mi_account_id )) {
				$existingSql = " SELECT id FROM accounts WHERE mi_account_id = '" . $mi_account_id . "' AND deleted = 0";
				$existingResult = $this->db->query ( $existingSql );
				$existingRow = $this->db->fetchByAssoc ( $existingResult );
				$existingAccount = $existingRow ['id'];
				// echo "Account:".$existingAccount;die;
			}
			
			if (empty ( $existingAccount )) {
				$existingAccount = checkExistingClient ( $client_name, $client_ph_office, $client_ph_fax, $client_email );
			}
			
			// queries
			$query_1 = "INSERT INTO accounts SET ";
			$query_2 = "UPDATE accounts SET ";
			$query_3 = " id = '" . addslashes ( $account_id ) . "', date_entered = UTC_TIMESTAMP(), date_modified = UTC_TIMESTAMP(), modified_user_id = '1',
			created_by = '1', deleted = 0, team_id = '1', team_set_id = '1',  is_modified = 0, lead_source = 'bb',  ";
			$query_4 = '';
			$query_5 = '';
			
			foreach ( $client as $key => $value ) {
				// prepare the fields to be saved
				if (! in_array ( $key, $restricted_fields )) {
					$field_name = $client_fields [$key];
					$field_value = htmlspecialchars_decode ( $value, ENT_QUOTES );
					$field_value = addslashes ( $field_value );
					if (trim ( $field_name ) == '') {
						continue;
					}			
					
					if ($field_name == 'county_id') {
						$county = $this->getCounty ( $field_value, $client ['state'] );
						$field_value = $county ['id'];
						//get county name for international clients
						//@modified by Mohit Kumar Gupta 30-07-2014
						$countyName = $this->getInternationalCountyName($county ['id']);
						$query_4 .= " county_name = '" . $countyName . "', ";
					}
					
					if($field_value == 'true'){
						$field_value = 1;
					}
					
					if($field_value == 'false'){
						$field_value = 0;
					}
					
					//Maintain Change Log for Clients
					if ($existingAccount) {
						$ciClient = $this->getClient ( $existingAccount, $this->db );
						if ($ciClient ['visibility'] == 1) {
							$old_value = $ciClient [$field_name];
								
							$field_type = '';
							if($field_name == 'billing_address_state' || $field_name == 'industry'){
								$field_type = 'enum';
							}
							insertChangeLog($this->db, 'accounts', $existingAccount, $old_value, $field_value, $field_name, $field_type, 'Blue Book');
						}
					}
					
					//if ($field_value == 'true' || $field_value == 'false') {
					//	$query_4 .= " $field_name = " . $field_value . ", ";
					//} else {
						$query_4 .= " $field_name = '" . $field_value . "', ";
					//}
				}
			}
			
			// remove last ", " from the query string
			! empty ( $query_4 ) ? ($query_4 = substr ( trim ( $query_4 ), 0, - 1 )) : ($query_4 = '');
			
			if ($existingAccount) {
				$ciClient = $this->getClient ( $existingAccount, $this->db );
				$account_id = $ciClient ['id'];
				
				if ($ciClient ['is_modified'] == 1 || $ciClient ['lead_source'] != 'bb') {
					$account_fields = $this->db->get_columns ( 'accounts' );
					$i = 0;
					
					foreach ( $account_fields as $account_column => $val ) {
						// echo $account_column."<br>";
						$client_fields_reverse = array_flip ( $client_fields );
						
						if (in_array ( $account_column, $client_fields )) {
							
							if ((empty ( $ciClient [$account_column] ) || trim ( $ciClient [$account_column] ) == '') && (! empty ( $client [$client_fields_reverse [$account_column]] ))) {
								
								$field_name = $account_column;
								$field_value = $client [$client_fields_reverse [$account_column]];
								$field_value = htmlspecialchars_decode ( $field_value, ENT_QUOTES );
								$field_value = addslashes ( $field_value );
								
								if ($i != 0) {
									$query_5 .= ", ";
								}
								
								if ($field_name == 'county_id') {
									$county = $this->getCounty ( $field_value, $client ['state'] );
									$field_value = $county ['id'];
								}
								
								if($field_value == 'true'){
									$field_value = 1;
								}
									
								if($field_value == 'false'){
									$field_value = 0;
								}
								
								//if ($field_value == 'true' || $field_value == 'false') {
								//	$query_5 .= " $field_name = " . $field_value . " ";
								//} else {
									$query_5 .= " $field_name = '" . $field_value . "' ";
								//}
								
								//Maintain Chagne Log
								$field_type = '';
								if($field_name == 'billing_address_state' || $field_name == 'industry'){
									$field_type = 'enum';
								}
								insertChangeLog($this->db, 'accounts', $existingAccount, '', $field_value, $field_name, $field_type, 'Blue Book');								
								$i ++;
							}
						}
					}
					
					if ($i > 0) {
						
						if (! empty ( $query_5 )) {
							$query_5 .= ", ";
						}
						$query_5 .= " visibility = 1,pulled_date = UTC_TIMESTAMP() ";
						
						$query = $query_2 . $query_5 . " WHERE id = '" . $account_id . "' ";
						$this->db->query ( $query );						
						
						$this->insertUpdateEmailAddress ( 'Accounts', $account_id, $client_email,true,$ciClient);
					}
				} else {
					
					if (! empty ( $query_4 )) {
						$query_4 .= ", ";
					}
					
					$query_4 .= " visibility = 1,pulled_date = UTC_TIMESTAMP() ";
					
					$query = $query_2 . $query_4 . " WHERE id = '" . $account_id . "' ";
					$this->db->query ( $query );
					$this->insertUpdateEmailAddress ( 'Accounts', $account_id, $client_email, true,$ciClient);
				}
			} else {
				if ($import_client) {
					$query_3 .= " visibility = 1,pulled_date = UTC_TIMESTAMP(), ";
				} else {
					$query_3 .= " visibility = 0, ";
				}
				$query = $query_1 . $query_3 . $query_4;
				$this->db->query ( $query );
				$this->insertUpdateEmailAddress ( 'Accounts', $account_id, $client_email );
			}
			
			foreach ( $client as $key => $value ) {
				
				// save region data and accounts classification relationship
				if ($key == 'Region') {
					$this->insertUpdateRegion ( $account_id, $value );
				}
				
				// save BI data
				if ($key == 'BI') {
					$this->insertUpdateBI ( $account_id, $value );
				}
				
				// save Contact data
				if ($key == 'Contact') {
					foreach ( $value as $contact ) {
						$this->insertUpdateContacts ( $account_id, $contact, $import_client );
					}
				}
			}
		}
		
		if (empty ( $data_json )) {
			sugar_die ( "No Clients Available" );
		}
	}
	
	/**
	 * Insert / update Region data
	 */
	public function insertUpdateRegion($account_id, $region_data) {
		global $client_fields, $app_list_strings;
		require_once 'custom/include/common_functions.php';
		
		$db_query = " INSERT INTO oss_region ( id, name, date_entered, date_modified, modified_user_id,
				created_by, deleted, team_id, team_set_id, assigned_user_id, account_id_c, oss_classification_id_c, mi_region_id) VALUES ";
		
		$classification_query = " INSERT INTO oss_classifion_accounts_c (id, date_modified, deleted, oss_classi48bbication_ida, oss_classid41cccounts_idb) VALUES  ";
		
		$i = 0;
		$arClsficationAccounts = array ();
		foreach ( $region_data as $region ) {
			
			// get the classification id from the category no
			$class_sql = " SELECT id FROM oss_classification WHERE category_no = '" . addslashes ( $region ['classification_id'] ) . "' ";
			$class_result = $this->db->query ( $class_sql );
			$class_row = $this->db->fetchByAssoc ( $class_result );
			
			if (! empty ( $class_row ['id'] )) {
				
				$classification_id = $class_row ['id'];
				
				($i != 0) ? $db_query .= ", " : "";
				
				$db_query .= "(UUID(), '" . addslashes ( $region ['region_id'] ) . "', UTC_TIMESTAMP(), UTC_TIMESTAMP(), 1,
							1, 0, 1, 1, 1, '" . addslashes ( $account_id ) . "', '" . addslashes ( $classification_id ) . "', '" . addslashes ( $region ['region_bb_id'] ) . "' )";
				
				$existing_sql = "SELECT id FROM oss_classifion_accounts_c WHERE oss_classi48bbication_ida = '" . addslashes ( $classification_id ) . "'
									 AND oss_classid41cccounts_idb = '" . $account_id . "' AND deleted = 0";
				$existing_query = $this->db->query ( $existing_sql );
				$existing_result = $this->db->fetchByAssoc ( $existing_query );
				
				// var_dump($existing_query);
				if (($existing_query->num_rows == 0) && ! in_array ( $classification_id, $arClsficationAccounts )) {
					($i != 0) ? $classification_query .= ", " : "";
					$classification_query .= " ( UUID(), UTC_TIMESTAMP(), 0, '" . addslashes ( $classification_id ) . "', '" . addslashes ( $account_id ) . "') ";
					$i ++;
				}
				// add to classification accounts array
				$arClsficationAccounts [] = $classification_id;
			}
		}
		
		$db_query .= " ON DUPLICATE KEY UPDATE name = VALUES(name) , account_id_c = VALUES(account_id_c),
						oss_classification_id_c = VALUES(oss_classification_id_c) ";
		if ($i > 0) {
			$this->db->query ( $classification_query ); // accounts classsification
			                                            // query
			$this->db->query ( $db_query ); // region query
		}
		
		return true;
	}
	
	/**
	 * Insert Account and Classification Relationship
	 */
	function insertAccountClassRel($class_id, $account_id) {
		$existing_sql = "SELECT oc.id FROM oss_classification oc Inner JOIN oss_classifion_accounts_c ocl on oc.id=ocl.oss_classi48bbication_ida  AND ocl.deleted = 0 WHERE oc.category_no =  '" . $class_id . "' AND ocl.oss_classid41cccounts_idb = '" . $account_id . "' AND oc.deleted = 0";
		$existing_query = $this->db->query ( $existing_sql );
		$existing_result = $this->db->fetchByAssoc ( $existing_query );
		if (empty ( $existing_result )) {
			$classification_id = $this->checkExistingRecord ( 'oss_classification', 'category_no', $class_id );
			if (! empty ( $classification_id )) {
				$insertSql = "INSERT INTO oss_classifion_accounts_c (`id`,`date_modified`,`oss_classi48bbication_ida`,`oss_classid41cccounts_idb`) VALUES (UUID(),UTC_TIMESTAMP(),'" . $classification_id . "','" . $account_id . "')";
				$this->db->query ( $insertSql );
			}
		}
	}
	
	/**
	 * Insert / Update BI data
	 */
	public function insertUpdateBI($account_id, $bi_data) {
		global $client_fields, $app_list_strings;
		require_once 'custom/include/common_functions.php';
		$is_bi_update = false;
		//Get BI Count for this account
		$bi_count_sql = "SELECT COUNT(1) bi_count FROM oss_businessintelligence WHERE account_id='".$account_id."' AND deleted=0";
		$bi_count_query = $this->db->query($bi_count_sql);
		$bi_count_result = $this->db->fetchByAssoc($bi_count_query);
		
		$total_local_bi = $bi_count_result['bi_count'];
		$total_json_bi = count($bi_data);
		if($total_local_bi != $total_json_bi){
			$is_bi_update = true;
		}		
		
		$db_query = " INSERT INTO oss_businessintelligence
					(id, name, date_entered, date_modified, modified_user_id, created_by, description,
					deleted, team_id, team_set_id, assigned_user_id, source, type_order, account_id,
							 image_description, image_url, sort_order, mi_oss_businessintelligence_id)
					VALUES  ";
		
		$i = 0;
		

		
		foreach ( $bi_data as $bi ) {
			$image_desc = '';
			$image_url = '';
			$sort_order = '';
			$description = '';			
			if (isset ( $bi ['image_description'] )) {
				$image_desc = $bi ['image_description'];
			}
			
			if (isset ( $bi ['image_url'] )) {
				$image_url = $bi ['image_url'];
			}
			
			if (isset ( $bi ['sort_order'] )) {
				$sort_order = $bi ['sort_order'];
			}
			
			if (isset ( $bi ['description'] )) {
				$description = $bi ['description'];
			}			
			// get the BI type order
			$type_order = $app_list_strings ['bi_type_order_dom'] [$bi ['name']];
			
			//Get Local BI Information by bluebook bi id
			$local_bi = $this->getLocalBIInfo($bi ['bi_bb_id']);
			if(!empty($local_bi['id'])){
				foreach ($bi as $bi_key => $bi_value){					
					if ($bi_key != 'bi_bb_id') {
						if ($local_bi [$bi_key] != $bi_value) {
							$is_bi_update = true;							
						}
					} 	
				}
			}		
			
			
			($i != 0) ? $db_query .= ", " : "";
			
			$db_query .= " (UUID(), '" . addslashes ( ucwords ( strtolower ( $bi ['name'] ) ) ) . "', UTC_TIMESTAMP(), UTC_TIMESTAMP(), 1, 1,
							'" . addslashes ( $description ) . "',0, 1, 1, 1, 'Blue Book', '" . addslashes ( $type_order ) . "',
							'" . addslashes ( $account_id ) . "', '" . addslashes ( $image_desc ) . "',
							'" . addslashes ( $image_url ) . "','" . addslashes ( $sort_order ) . "', '" . addslashes ( $bi ['bi_bb_id'] ) . "' ) ";
			
			$i ++;
		}
		
		$db_query .= " ON DUPLICATE KEY UPDATE name = VALUES(name) , description = VALUES(description),
						source = VALUES(source), type_order = VALUES(type_order), account_id = VALUES(account_id),
						image_description =  VALUES(image_description), image_url = VALUES(image_url),
						sort_order = VALUES(sort_order) ";
		
		
		if($is_bi_update){
			$bi_audit_id = create_guid ();
			$bi_audit_sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `accounts_audit` WHERE `parent_id`='" . $account_id . "' AND `field_name`='Business Intelligence Modification'";
			$query = $this->db->query ( $bi_audit_sql );
			$result = $this->db->fetchByAssoc ( $query );
			$insertSQL = "INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'" . $account_id . "',UTC_TIMESTAMP(),'Blue Book','Business Intelligence Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP());";
			$this->db->query ( $insertSQL );
		}
		
		if ($i > 0) {
			$this->db->query ( $db_query );
		}
		
		return true;
	}
	
	/**
	 * Insert Update Contacts
	 */
	public function insertUpdateContacts($account_id, $contact, $import_client = false) {
		global $contact_fields, $app_list_strings;
		require_once 'custom/include/common_functions.php';
		
		// this fields will be not saved
		$restricted_fields = array (
				'cust_acct',
				'sequence',
				'contact_no',
				'client_id',
				'email1' 
		);
		$bClientChangeLogFlag = false;
		// foreach ($contact_data as $contact){
		
		$ci_contact_id = create_guid ();
		
		$contact_name = $contact ['first_name'] . " " . $contact ['last_name'];
		isset ( $contact ['phone_work'] ) ? ($contact_ph_work = $contact ['phone_work']) : ($contact_ph_work = '');
		isset ( $contact ['phone_fax'] ) ? ($contact_ph_fax = $contact ['phone_fax']) : ($contact_ph_fax = '');
		isset ( $contact ['email1'] ) ? ($contact_email = $contact ['email1']) : ($contact_email = '');
		isset ( $contact ['contact_bb_id'] ) ? $contact_bb_id = $contact ['contact_bb_id'] : $contact_bb_id = '';
		
		$contact_name = htmlspecialchars_decode ( $contact_name, ENT_QUOTES );
		$contact_name = addslashes ( $contact_name );
		
		$mi_contact_id = $contact_bb_id;
		
		if (! empty ( $mi_contact_id )) {
			$existingSql = " SELECT id FROM contacts WHERE mi_contact_id = '" . $mi_contact_id . "' AND deleted = 0";
			$existingResult = $this->db->query ( $existingSql );
			$existingRow = $this->db->fetchByAssoc ( $existingResult );
			$existingContact = $existingRow ['id'];
		}
		
		if (empty ( $existingContact )) {
			$existingContact = checkExistingClientContact ( $contact_name, $contact_ph_work, $contact_ph_fax, $contact_email );
		}
		
		$query_1 = "INSERT INTO `contacts` SET ";
		$query_2 = "UPDATE `contacts` SET ";
		$query_3 = " `id` = '" . addslashes ( $ci_contact_id ) . "', `date_entered` = UTC_TIMESTAMP(), `date_modified` = UTC_TIMESTAMP(), `modified_user_id` = '1', `created_by` = '1', `deleted` = 0, `team_id` = '1', `team_set_id` = '1', `is_modified` = 0, `lead_source` = 'bb', `visibility` = 1, ";
		$query_4 = '';
		
		foreach ( $contact as $key => $value ) {
			// prepare the fields to be saved
			if (! in_array ( $key, $restricted_fields )) {
				$field_name = $contact_fields [$key];
				$field_value = htmlspecialchars_decode ( $value, ENT_QUOTES );
				$field_value = addslashes ( $field_value );
				if (trim ( $field_name ) == '') {
					continue;
				}
				//Maintain Change Log for Client
				if($existingContact) {
					$ciContact = $this->getClientContact ( $existingContact, $this->db );
					if($ciContact['visibility']==1){
						$field_type = '';
						if($field_name == 'primary_address_state' || $field_name == 'alt_address_state'){
							$field_type = 'enum';
						}
											
						insertChangeLog ( $this->db, 'contacts', $existingContact, $ciContact[$field_name], $field_value, $field_name, $field_type, 'Blue Book');
						/* added by Ashutosh */
						if(!$bClientChangeLogFlag && ($existingContact != $ciContact[$field_name])){
						    $bClientChangeLogFlag = true;
						}
					}					
				}				
				
				$query_4 .= " $field_name  = '" . $field_value . "',";
			}
		}
		
		// remove last ", " from the query string
		! empty ( $query_4 ) ? ($query_4 = substr ( trim ( $query_4 ), 0, - 1 )) : ($query_4 = '');
		
		if ($existingContact) {
			
			$ciContact = $this->getClientContact ( $existingContact, $this->db );
			
			$ci_contact_id = $ciContact ['id'];
			
			if ($ciContact ['is_modified'] == 1 || $ciContact ['lead_source'] != 'bb') {
				
				$contact_fields_db = $this->db->get_columns ( 'contacts' );
				$i = 0;
				foreach ( $contact_fields_db as $contact_column => $val ) {
					
					$contact_fields_reverse = array_flip ( $contact_fields );
					
					if (in_array ( $contact_column, $contact_fields )) {
						if ((empty ( $ciContact [$contact_column] ) || trim ( $ciContact [$contact_column] ) == '') && (! empty ( $contact [$contact_fields_reverse [$contact_column]] ))) {
							
							if ($i != 0) {
								$query_5 .= ", ";
							}
							
							
							$field_name = $contact_column;
							$field_value = htmlspecialchars_decode ( $contact [$contact_fields_reverse [$contact_column]], ENT_QUOTES );
							
							//Maintain Change Log for Contact
							$field_type = '';
							if($field_name == 'primary_address_state' || $field_name == 'alt_address_state'){
								$field_type = 'enum';
							}
							insertChangeLog ( $this->db, 'contacts', $existingContact, $ciContact[$field_name], $field_value, $field_name, $field_type, 'Blue Book');
							/* added by Ashutosh */
							if(!$bClientChangeLogFlag && ($existingContact != $ciContact[$field_name])){
							    $bClientChangeLogFlag = true;
							}
							$query_5 .= " $field_name = '" . addslashes ( $field_value ) . "' ";
							
							$i ++;
						}
					}
				}
				
				if ($i > 0) {
					
					if (! empty ( $query_5 )) {
						$query_5 .= ", ";
					}
					$query_5 .= " visibility = 1 ";
					
					$query = $query_2 . $query_5 . " WHERE id = '" . $ci_contact_id . "' ";
					$res = $this->db->query ( $query );					
					$this->insertUpdateEmailAddress ( 'Contacts', $ci_contact_id, $contact_email,true,$ciContact );
				}
			} else {
				
				if (! empty ( $query_4 )) {
					$query_4 .= ", ";
				}
				
				$query_4 .= " visibility = 1 ";
				
				$query = $query_2 . $query_4 . " WHERE id = '" . $ci_contact_id . "' ";
				$res = $this->db->query ( $query );
				$this->insertUpdateEmailAddress ( 'Contacts', $ci_contact_id, $contact_email, true,$ciContact);
			}
		} else {
			
			$query = $query_1 . $query_3 . $query_4;
			$res = $this->db->query ( $query );
			$this->insertUpdateEmailAddress ( 'Contacts', $ci_contact_id, $contact_email );
			/**
			 * Added by Ashutosh 
			 * For New contacts mark change log on related client
			 * */
			$bClientChangeLogFlag = true;
			
		}
		
		// echo $query; die;
		
		if (! empty ( $account_id )) {
			$this->insertAccountContactRel ( $ci_contact_id, $account_id );
		}
		/**
		 * Added BY Ashutosh 
		 * mark a change log in Client
		 */
		if($bClientChangeLogFlag){
		    		    
		    $cc_audit_sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) 
		            before_date FROM `accounts_audit` WHERE `parent_id`='" . $account_id 
		        . "' AND `field_name`='Client Contact Modification'";
		    $query = $this->db->query ( $cc_audit_sql);
		    $result = $this->db->fetchByAssoc ( $query );
		    $insertSQL = "INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`
		                        ,`created_by`,`field_name`,`data_type`,`before_value_string`
		                        ,`after_value_string`) VALUES (UUID(),'" . $account_id . "'
		                          ,UTC_TIMESTAMP(),'Blue Book','Client Contact Modification'
		                                ,'datetimecombo','" . $result ['before_date'] 
		                        . "',UTC_TIMESTAMP());";
		    $this->db->query ( $insertSQL );
		}
		if(trim($ci_contact_id) != ''){
		//get local client details
		$ciContact = $this->getClientContact ( $ci_contact_id, $this->db );
		
		$stSQL = 'UPDATE oss_leadclientdetail 
				SET  contact_fax = "'.$ciContact['phone_fax'].'" 
				,contact_email = "'.$ciContact['email1'].'" 
				,contact_phone_no = "'.$ciContact['phone_work'].'"
				 WHERE contact_id = "'.$ci_contact_id.'" ';
				 
		$this->db->query ( $stSQL );	
	   }	 
		// }
		return true;
	}
	public function updateContacts() {
		$data_json = $this->getContacts ();
		$data_array = json_decode ( $data_json, true ); // decode json to array
		
		if (empty ( $data_json )) {
			sugar_die ( "No Contacts Available" );
		} else {
			
			foreach ( $data_array ['response'] ['Contact'] as $contact ) {
				
				isset ( $contact ['contact_bb_id'] ) ? $contact_bb_id = $contact ['contact_bb_id'] : $contact_bb_id = '';
				
				$cBidderSQL = "SELECT id FROM accounts WHERE mi_account_id = '" . addslashes ( $contact_bb_id ) . "' AND deleted=0";
				$cBidderQuery = $this->db->query ( $cBidderSQL );
				$cBidderRes = $this->db->fetchByAssoc ( $cBidderQuery );
				$account_id = $cBidderRes ['id'];
				
				$this->insertUpdateContacts ( $account_id, $contact );
			}
		}
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
	private function getData($url) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 3000000000 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 3000000000 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
	}
	
	public function getPostData($url, $data) {
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 3000000000 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 3000000000 );
		curl_setopt ( $ch, CURLOPT_POST, 1);
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
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
	function insertUpdateEmailAddress($module, $bean_id, $email_addresses,$is_update=false,$local_data='') {
		
	    if (! is_array ( $email_addresses )) {
			$email_addresses = array (
					$email_addresses 
			);
		}
		/**
		 * modified : by Ashutosh 
		 * date : 28 Feb 2013
		 * purpose : To not allow empty emails  
		 */
		foreach($email_addresses as $iKey => $stEmail){
		    
			if(trim($stEmail) == ''){
				unset($email_addresses[$iKey]);
			} 
		}
		
		if (count ( $email_addresses ) == 0) {
			return;
		}
		$email_addresses = array_unique ( $email_addresses );
		
		//Maintain Change Log
		if($is_update){
			if($local_data['email1'] != $email_addresses[0]){
				insertChangeLog($this->db, $module, $bean_id, $local_data['email1'], $email_addresses[0], 'email1', 'varchar', 'Blue Book');
			}
		}
		
		// get Existing Email Addresses of related bean
		$sql = "SELECT ea.email_address
				FROM email_addresses ea
        		INNER JOIN email_addr_bean_rel eabr ON eabr.email_address_id = ea.id AND eabr.bean_id='" . $bean_id . "' AND eabr.bean_module='" . $module . "' AND eabr.deleted = 0
				WHERE ea.deleted = 0";
		$query = $this->db->query ( $sql );
		$emails_from_db = array ();
		while ( $result = $this->db->fetchByAssoc ( $query ) ) {
			$emails_from_db [] = $result ['email_address'];
		}
		
		$insertSQL = "INSERT INTO email_addresses (id, email_address, email_address_caps, invalid_email, opt_out, date_created, date_modified, deleted) VALUES";
		$insertBeanRel = "INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, reply_to_address, date_created, date_modified, deleted ) VALUES";
		
		$counter = 0;
		
		foreach ( $email_addresses as $email_address ) {
			
			if (! in_array ( $email_address, $emails_from_db )) {
				// Insert Email Address
				$email_address_id = create_guid ();
				$email_addr_bean_rel_id = create_guid ();
				if ($counter > 0) {
					$insertSQL .= ", ";
					$insertBeanRel .= ", ";
				}
				$insertSQL .= " ('" . $email_address_id . "', '" . $email_address . "', '" . strtoupper ( $email_address ) . "', 0, 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(), 0) ";
				$insertBeanRel .= " ('" . $email_addr_bean_rel_id . "', '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(),0) ";
				$counter ++;
			}
		}
		if ($counter > 0) {
			$this->db->query ( $insertSQL );
			$this->db->query ( $insertBeanRel );
		}
		
		foreach ( $emails_from_db as $db_email ) {
			if (! in_array ( $db_email, $email_addresses )) {
				// Query for set flag deleted = 0
				$sql = "UPDATE email_addr_bean_rel eabr, email_addresses ea SET eabr.deleted = 1, ea.deleted=1
						WHERE ea.email_address='" . $db_email . "' AND eabr.bean_id = '" . $bean_id . "' AND eabr.bean_module='" . $module . "' AND eabr.deleted = 0 AND ea.id = eabr.email_address_id";
				$this->db->query ( $sql );
			}
		}
		
		// Reset Primary Email
		if (count ( $email_addresses ) > 0) {
			$primary_sql = "SELECT id FROM email_addr_bean_rel WHERE bean_id='" . $bean_id . "' AND bean_module='" . $module . "' AND deleted=0 ORDER BY date_created asc LIMIT 0,1";
			$query = $this->db->query ( $primary_sql );
			$result = $this->db->fetchByAssoc ( $query );
			if (! empty ( $result ['id'] )) {
				$update_primary_sql = "UPDATE email_addr_bean_rel SET primary_address=1 WHERE id='" . $result ['id'] . "'";
				$this->db->query ( $update_primary_sql );
			}
		}
	}
	
	/**
	 * Insert Update Online Plans
	 */
	function insertUpdateOnlinePlans($online_plan, $lead_id, $pl_is_update, $is_op_modified) {
		$name = '';
		$type = '';
		$source = '';
		$url = '';
		if (isset ( $online_plan ['name'] )) {
			$name = htmlspecialchars_decode ( $online_plan ['name'], ENT_QUOTES );
			$name = addslashes ( $name );
		}
		if (isset ( $online_plan ['type'] )) {
			$type = htmlspecialchars_decode ( $online_plan ['type'], ENT_QUOTES );
			$type = addslashes ( $type );
		}
		if (isset ( $online_plan ['source'] )) {
			$source = htmlspecialchars_decode ( $online_plan ['source'], ENT_QUOTES );
			$source = addslashes ( $source );
		}
		if (isset ( $online_plan ['url'] )) {
			$url = htmlspecialchars_decode ( $online_plan ['url'], ENT_QUOTES );
			$url = addslashes ( $url );
		}
		if (isset ( $online_plan ['op_bb_id'] )) {
			$op_bb_id = htmlspecialchars_decode ( $online_plan ['op_bb_id'], ENT_QUOTES );
			$op_bb_id = addslashes ( $op_bb_id );
		}
		
		if ($pl_is_update == true) {
			// Get local online plans for compare
			if ($is_op_modified == false) {
				$sql = "SELECT name,description,plan_type,plan_source FROM oss_onlineplans WHERE mi_oss_onlineplans_id='" . $op_bb_id . "' AND deleted = 0";
				$query = $this->db->query ( $sql );
				$result = $this->db->fetchByAssoc ( $query );
				/*
				 * echo $result['name']." : ".$result['description']." :
				 * ".$result['plan_type']." : ".$result['plan_source']; echo
				 * "====="; echo $name." : ".$url." : ".$type." : ".$source;
				 * echo "<br>";
				 */
				if ($result ['name'] != $name || $result ['description'] != $url || $result ['plan_type'] != $type || $result ['plan_source'] != $source) {
					$is_op_modified = true;
				}
			}
			
			if ($is_op_modified == true) {
				$lead_audit_id = create_guid ();
				$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $lead_id . "' AND `field_name`='Online Plan Modification'";
				$query = $this->db->query ( $sql );
				$result = $this->db->fetchByAssoc ( $query );
				$insert_sql = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
				$insert_sql .= " ('" . $lead_audit_id . "','" . $lead_id . "',UTC_TIMESTAMP(),'Blue Book','Online Plan Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP()) ";
				$this->db->query ( $insert_sql );
				changeLogFlag ( $lead_id, $this->db );
			}
		}
		
		$db_query = " INSERT INTO oss_onlineplans (id, name, date_entered, date_modified,
                    modified_user_id, created_by, description, deleted, assigned_user_id, plan_type,
                    plan_source, lead_id, mi_oss_onlineplans_id ) VALUES ";
		
		$db_query .= " ( UUID(), '" . $name . "', UTC_TIMESTAMP(), UTC_TIMESTAMP(),
                    1, 1, '" . $url . "', 0, 1, '" . $type . "',
                    '" . $source . "', '" . $lead_id . "', '" . $op_bb_id . "' ) ";
		
		$db_query .= " ON DUPLICATE KEY UPDATE name = VALUES(name) , description = VALUES(description),
                        plan_type = VALUES(plan_type), plan_source = VALUES(plan_source), lead_id = VALUES(lead_id) ";
		
		$this->db->query ( $db_query );
	}
	
	
	/**
	 * Update local or imported non bb client if matches to BB Hub 
	 * @param string $client_json
	 * @param id $client_id
	 */
	
	function updateExistingNonBBClient($client_json, $client_id){
		
		global $client_fields;
	
		// this fields will be not saved
		$restricted_fields = array (
				'Contact','Region','BI','email','year_established','tps','noe','prev_proj'
		);
		
		$data_array = json_decode ( $client_json, true );
		
		$query = "UPDATE accounts SET ";
	
		$ciClient = $this->getClient($client_id, $this->db);
	
		//echo '<pre>'; print_r($client); echo '<pre>';
	
		foreach ( $data_array['response']['Company'] as $client ) {
			
			
			$account_query = " SELECT count(*) c FROM accounts WHERE mi_account_id = '".$client['client_bb_id']."'  AND deleted = 0 ";
			$account_result = $this->db->query($account_query);
			$account_row = $this->db->fetchByAssoc($account_result);
			
			if( $account_row['c'] > 0){
				$GLOBALS['log']->info('Import has been skipped. ' .$client['client_bb_id']. ' already exist.');
				continue;
			}
			
			$client_email = isset( $client['email'] ) ?  $client['email'] : '';
	
			$account_fields = $this->db->get_columns ( 'accounts' );
			$i = 0;
			
			foreach ( $account_fields as $account_column => $val ) {
				
				
					
				$client_fields_reverse = array_flip ( $client_fields );
	
				if (in_array ( $account_column, $client_fields )) {
						
						
					if ((empty ( $ciClient [$account_column] ) || 
							trim ( $ciClient [$account_column] ) == '') 
								&& (! empty ( $client [$client_fields_reverse [$account_column]] ))) 
					{
							
						$field_name = $account_column;
						$field_value = $client [$client_fields_reverse [$account_column]];
						$field_value = htmlspecialchars_decode ( $field_value, ENT_QUOTES );
						$field_value = addslashes ( $field_value );
	
						if ($i != 0) {
							$query .= ", ";
						}
	
						if ($field_name == 'county_id') {
							$county = $this->getCounty ( $field_value, $client ['state'] );
							$field_value = $county ['id'];
						}
	
						if($field_value == 'true'){
							$field_value = 1;
						}
	
						if($field_value == 'false'){
							$field_value = 0;
						}
							
						$query .= " $field_name = '" . $field_value . "' ";
	
						//Maintain Chagne Log
						$field_type = '';
						if($field_name == 'billing_address_state' || $field_name == 'industry'){
							$field_type = 'enum';
						}
							
						insertChangeLog($this->db, 'accounts', $client_id, '', $field_value, $field_name, $field_type, 'Blue Book');
						$i ++;
					}
				}
			}
				
			if ($i > 0) {
				
				$query .= ", visibility = 1,pulled_date = UTC_TIMESTAMP(), show_update_icon = 0 WHERE id = '".$client_id."' ";
				
				$GLOBALS['log']->info($query);
				
				$this->db->query ( $query );
	
				$this->insertUpdateEmailAddress ( 'Accounts', $client_id, $client_email, true, $ciClient);
			}
			
			foreach ( $client as $key => $value ) {
			
				// save region data and accounts classification relationship
				if ($key == 'Region') {
					$this->insertUpdateRegion ( $client_id, $value );
				}
			
				// save BI data
				if ($key == 'BI') {
					$this->insertUpdateBI ( $client_id, $value );
				}
			
				// save Contact data
				if ($key == 'Contact') {
					foreach ( $value as $contact ) {
						$this->insertUpdateContacts ( $client_id, $contact, true );
					}
				}
			}
	
		}
		
		return true;
	}
	
	/**
	 * Update local or imported non bb client contact if matches to BB Hub
	 * @param string $client_contact_json
	 * @param id $client_id
	 */
	function updateExistingNonBBClientContact($client_contact_json, $client_contact_id){
		
		global $contact_fields;
		
		// this fields will be not saved
		$restricted_fields = array (
				'cust_acct','sequence','contact_no','client_id','email1'
		);
		
		$bClientChangeLogFlag = false;
		
		$data_array = json_decode($client_contact_json , true);
		
		$query = "UPDATE contacts SET ";
		
		$ciContact = $this->getClientContact($client_contact_id, $this->db);
		
		//echo '<pre>'; print_r($client); echo '<pre>';
		
		foreach ( $data_array['response']['Company'] as $contact ) {
			
			$contact_query = " SELECT count(*) c FROM contacts WHERE mi_contact_id = '".$contact['contact_bb_id']."' AND deleted = 0 ";
			$contact_result = $this->db->query($contact_query);
			$contact_row = $this->db->fetchByAssoc($contact_result);
				
			if( $contact_row['c'] > 0){
				$GLOBALS['log']->info('Import has been skipped. ' .$contact['contact_bb_id']. ' already exist.');
				continue;
			}
			
			$contact_email = isset ( $contact ['email1'] ) ? $contact ['email1'] : '';
			
			$contact_fields_db = $this->db->get_columns ( 'contacts' );
			
			$i = 0;
			
			foreach ( $contact_fields_db as $contact_column => $val ) {
			
				$contact_fields_reverse = array_flip ( $contact_fields );
			
				if (in_array ( $contact_column, $contact_fields )) {
					
					if ((empty ( $ciContact [$contact_column] ) 
							|| trim ( $ciContact [$contact_column] ) == '') 
							&& (! empty ( $contact [$contact_fields_reverse [$contact_column]] ))) 
					{
			
						if ($i != 0) {
							$query .= ", ";
						}
						
						$field_name = $contact_column;
						$field_value = htmlspecialchars_decode ( $contact [$contact_fields_reverse [$contact_column]], ENT_QUOTES );
			
						//Maintain Change Log for Contact
						$field_type = '';
						if($field_name == 'primary_address_state' || $field_name == 'alt_address_state'){
							$field_type = 'enum';
						}
						insertChangeLog ( $this->db, 'contacts', $client_contact_id, $ciContact[$field_name], $field_value, $field_name, $field_type, 'Blue Book');
						 
						if(!$bClientChangeLogFlag && ($client_contact_id != $ciContact[$field_name])){
							$bClientChangeLogFlag = true;
						}
						
						$query .= " $field_name = '" . addslashes ( $field_value ) . "' ";
			
						$i ++;
					}
				}
			}
			
			if ($i > 0) {
				
				$query .= ", visibility = 1 WHERE id = '" . $client_contact_id . "' ";
				
				$GLOBALS['log']->info($query);
				
				$res = $this->db->query ( $query );
				
				$this->insertUpdateEmailAddress ( 'Contacts', $client_contact_id, $contact_email, true, $ciContact );
			}
			
		}
		
		if($bClientChangeLogFlag){

			$account_query = " SELECT account_id FROM accounts_contacts WHERE contact_id = '".$client_contact_id."' AND deleted = 0 ORDER BY date_modified LIMIT 0,1"; 
			$account_result = $this->db->query($account_query);
			$account_row = $this->db->fetchByAssoc($account_result);
			
			$account_id = $account_row['account_id'];
			
			if(!empty($account_id)){
				
				$cc_audit_sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP())
			            before_date FROM `accounts_audit` WHERE `parent_id`='" . $account_id
					            . "' AND `field_name`='Client Contact Modification'";
				$query = $this->db->query ( $cc_audit_sql);
				$result = $this->db->fetchByAssoc ( $query );
				$insertSQL = "INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`
			                        ,`created_by`,`field_name`,`data_type`,`before_value_string`
			                        ,`after_value_string`) VALUES (UUID(),'" . $account_id . "'
			                          ,UTC_TIMESTAMP(),'Blue Book','Client Contact Modification'
			                                ,'datetimecombo','" . $result ['before_date']
					                                . "',UTC_TIMESTAMP());";
				$this->db->query ( $insertSQL );
			}
		}
		
		if(trim($client_contact_id) != ''){
			
			//get local client details
			$ciContact = $this->getClientContact ( $client_contact_id, $this->db );
			$stSQL = 'UPDATE oss_leadclientdetail
				SET  contact_fax = "'.$ciContact['phone_fax'].'"
				,contact_email = "'.$ciContact['email1'].'"
				,contact_phone_no = "'.$ciContact['phone_work'].'"
				 WHERE contact_id = "'.$client_contact_id.'" ';
				
			$this->db->query ( $stSQL );
		}
		
		return true;
	}
	
	private function getInstanceAccountNo() {
		require_once 'modules/Administration/Administration.php';
		$obAdmin = new Administration ();
		$obAdmin->disable_row_level_security = true;
		$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
		$account_no = $arAdminData->settings ['instance_account_name'];
		return $account_no;
	}
	/**
	 * get the county name for international clients
	 * @author Mohit Kumar Gupta
	 * @date 30-07-2014
	 * @param string $countyId
	 * @return string $countyName
	 */
	public function getInternationalCountyName($countyId = ''){
	    $countyName = '';
	    if(!empty($countyId)){
	        $countyObj = new oss_County();
	        $countData = $countyObj->retrieve($countyId);
	        $countyName = $countData->name;
	    }
	    return $countyName;
	}
}

?>
