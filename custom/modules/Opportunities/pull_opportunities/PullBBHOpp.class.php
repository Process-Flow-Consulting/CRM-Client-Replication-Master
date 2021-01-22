<?php
// ini_set('display_errors',1);
// ini_set('memory_limit','1024M');
/**
 * Class for Pull Project Leads from Blue Book Hub to insert or update project
 * lead and data for others module direct through query.
 * @author Mohit Kumar Gupta
 * 
 */
require_once 'custom/modules/Opportunities/pull_opportunities/fields_mapping.php';
require_once 'custom/include/common_functions.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/include/dynamic_dropdown.php';
require_once 'custom/modules/Users/filters/instancePackage.class.php';
require_once 'custom/modules/Leads/views/view.save_opportunity.php';
require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';
require_once 'custom/modules/Users/role_config.php';
require_once 'custom/modules/Leads/views/view.convert_to_opportunity.php';
require_once 'custom/modules/Opportunities/views/view.assigneduser.php';
require_once 'modules/Configurator/Configurator.php';

class PullBBHOpp {
	public  $db;
	private $sugar_config;
	private $account_no;
	public  $limit;
	private $session;
	private $userData;
	private $totalLeads = 0;
	private $insertedLeads = 0;
	private $updatedLeads = 0;
	private $insertedProjectOpp = 0;
	private $updatedProjectOpp = 0;
	private $insertedClientOpp = 0;
	private $updatedClientOpp = 0;
	private $lock_file;
	private $status_file;
	
	function __construct($account_no, $userId, $projectOppId) {
		global $db, $sugar_config;
		$process_path = 'upload/process/';
		$this->db = $db;
		$this->sugar_config = $sugar_config;
		$this->account_no = $account_no;
		$this->limit = 100;
		$this->session = time ();
		$this->lock_file = $process_path.'opp_import_process_lock';
		$this->status_file = $process_path.'opp_import_import_status';
		if (!empty($projectOppId)) {
			$this->lock_file = $process_path.'opp_import_process_lock_'.$projectOppId;
	        $this->status_file = $process_path.'opp_import_import_status_'.$projectOppId;
		}
		PullBBH :: authenticate ();
		$this->userData = $this->getUserData($userId);
	}
			
	/**
	 * get user data
	 */
	private function getUserData($userId = '1') {
		$userId = (!empty($userId)) ? $userId : '1';
		$obj = new User();
		$obj->retrieve($userId);		
		return $obj;
	}
			
	/**
	 * Get saved roles from Users.
	 * 
	 */
	private function getUsersSavedRoles() {
		$rolesArray = '';	
		$stGetInstanceClssSQL = "SELECT value FROM config WHERE name ='global_roles' AND category ='instance'";
		$rsGetInstanceClssSQL = $this->db->query ( $stGetInstanceClssSQL );
		$arGetInstanceClssSQL = $this->db->fetchByAssoc ( $rsGetInstanceClssSQL );
		
		if (isset ( $arGetInstanceClssSQL ['value'] ) && trim ( $arGetInstanceClssSQL ['value'] ) != '') {		
			$rolesArray = json_decode ( base64_decode ( $arGetInstanceClssSQL ['value'] ) );						
		}		
		return $rolesArray;
	}
	
	/**
	 * Get Project Opportunities and related data from hub
	 * 
	 */
	private function getProjectOpportunitiesData($type = 'New',$projectNumber  = '', $sequence = 0) {
		
		//Get request type
		$reqType = 'reqtype=getOpsByEmail';
		$projectId = '';
		
		//Create email Parameter url
		$userEmail = "&email=".$this->userData->email1;	

		//get last pull date of opportunity
		$lpd = "&lpd=" .urlencode($this->getOpportunityLastPullDate($this->userData->id));
		if($type == 'Update') {
			$reqType = 'reqtype=getProjectUpdate';
			if(!empty($projectNumber)) {
			    $leadSql = "SELECT mi_lead_id FROM leads where deleted='0' AND id=".$this->db->quoted($projectNumber);
			    $leadQuery = $this->db->query($leadSql);
			    $leadData = $this->db->fetchByAssoc ( $leadQuery );
			    $projectId = '&projno='.$leadData['mi_lead_id'];
			    if (empty($leadData['mi_lead_id'])) {
			        $GLOBALS ['log']->fatal ('Project id does not exist for project update.');
			        sugar_die ( 'Project id does not exist for project update.' );
			    }
			} else {
				$GLOBALS ['log']->fatal ('Project id does not passed for project update.');
				sugar_die ( 'Project id does not passed for project update.' );	
			}
			$lpd = '';
			$userEmail = "&email=";
		}

		//Create current month Parameter url
		$currentMonth = date("n");
		
		//Create account number Parameter url
		$accountNumber = "&account=".$this->account_no;				
		
		$checksum = "&cks=checksum - ".md5($this->account_no. "_" .$sequence."_".$currentMonth."bidpipe2015");
						
		$seq = "&seq=".$sequence;		
								
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
		
		//create role list 
		$roleList = "";		
		$rolesArray = $this->getUsersSavedRoles();
		if(!empty($rolesArray)) {
			$roleList = "&rolelist=".urlencode(implode("|",$rolesArray));			
		}
				
		//Get All Target Classification			
		$class_url = "";
		$classifications = PullBBH :: getUsersClassifications ();
		if (! empty ( $classifications )) {
			$class_url = "&targetClass=$classifications";
		}
		
		//Pull Data from Bluebook Hub
		$url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/wopipeline_getops.p?";
		//TODO change data server from QA to production
		//$url = "http://qa.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/wopipeline_getops.p?";
		$data = $reqType. $accountNumber . $seq. $projectId. $userEmail. $lpd . $roleList. $class_url . $limit_url . $checksum . $session;
		$GLOBALS ['log']->fatal ( $url.$data);
		//$GLOBALS ['log']->fatal ( " Session:" . $this->session );
		//$GLOBALS ['log']->fatal ( $url );
        //$GLOBALS ['log']->fatal ( $data);	
		$pulledData = PullBBH :: getPostData($url, $data);
		//TODO comment this file put content and file path
		//$file = "custom/modules/Opportunities/pull_opportunities/opportunities.json";
		//file_put_contents($file, $pulledData);
		
		//update last pull date to config file
		if($type != 'Update') {
		  $this->setOpportunityLastPullDate($this->userData->id);
		}
		
		return $pulledData;
		//return $content = file_get_contents($file);
		
	}
		
	/**
	 * Insert/update Project Opportunities and related data
	 */
	public function setProjectClientOpportunities($type='New', $projectNumber = '', $sequence = 0) {
		$data_json = $this->getProjectOpportunitiesData($type, $projectNumber, $sequence);
		$data = json_decode ( $data_json, true );
		//$GLOBALS['log']->fatal(print_r($data,true));
		$total_leads = count ( $data ['response'] ['Project'] );
		$this->totalLeads +=  $total_leads;
		
		if($total_leads > 0) {
			
			// Upadte Current time into process lock file
			$this->updateCurrentTimeToFile($this->lock_file);
			
			foreach ( $data ['response'] ['Project'] as $pl ) {
				
				$updatedDataArray = $this->insertUpdateProjectLeads($pl);
				if(isset ( $updatedDataArray ['lead_id'] ) && !empty($updatedDataArray ['lead_id'])) {
				    foreach ($pl["Emails"] as $emaildata) {
				        $projectOppId = $this->insertUpdateOpportunity($updatedDataArray,$emaildata);
				        	
				        if (empty($projectOppId)) {
				            $GLOBALS['log']->fatal('Project Opportunity not created due to some error.');
				            sugar_die('Project Opportunity not created due to some error.');
				        }
				    }					
				} else {
				    $GLOBALS['log']->fatal('Project lead not created due to some error.');
				    sugar_die('Project lead not created due to some error.');
				}	
							
				$file_text = $this->insertedProjectOpp . "|" . $this->updatedProjectOpp;
				$status_file = $this->status_file;
				$fp = fopen ( $status_file, "w" );
				fwrite ( $fp, $file_text );
				fclose ( $fp );
			}
			
			// Upadte Current time into process lock file
			$this->updateCurrentTimeToFile($this->lock_file);
			//set update prev bid to flag
			setPreviousBidToUpdate();
		} else if ($total_leads == '0' && $this->totalLeads == '0') {
			$GLOBALS ['log']->fatal ('No leads are available in JSON.');
			sugar_die ( 'No leads are available in JSON.' );
		}
		
		if ($total_leads >= $this->limit) {
			$GLOBALS ['log']->fatal ( 'Next Opportunity' . $this->limit );
			$status_file = $this->status_file;
			$fp = fopen ( $status_file, "w" );
			fwrite ( $fp, "0|0" );
			fclose ( $fp );
			$this->setProjectClientOpportunities($type, $projectNumber, ++$sequence) ;
		} else {
		    //TODO
			//send email to the matt and lenny regarding how much opportunity is pulled
			//need to disscuss before going to implement
		}
	}	
	
	/**
	 * Insert Update Project Leads
	 */
	public function insertUpdateProjectLeads($pl) {
		global $pl_fields;
		$oss_timedate = new OssTimeDate ();
		
		$restricted_fields = array (
				'pl_bb_id',
				'undef',
				'proj_stage',
				'Bidder List',
				'OnlinePlans',
				'Classification',
				'bidscope',
		        'Emails'
		);
						
		// Check Existing Lead including deleted leads
		$leadSql = "SELECT `id` FROM `leads` WHERE `mi_lead_id` = '" . $pl ['pl_bb_id'] . "'";
		$leadQuery = $this->db->query ( $leadSql );
		$existingLead = $this->db->fetchByAssoc ( $leadQuery );
		$pl_is_update = false;
		$pl_status = 'New';
		$rolesArray = array();
		// Query for Project Lead
		$plsql = "INSERT INTO leads SET ";
		$pl_audit_sql = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
		
		
		if (! empty ( $existingLead )) {
			$pl_is_update = true;
			$lead_id = $existingLead ['id'];
			$locallead = PullBBH :: getLocalProjectLead ( $lead_id );
			if ($locallead ['status'] == 'Converted') {
				$pl_status = $locallead ['status'];
			}
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
					$county = PullBBH :: getCounty ( $field_value, $pl ['state'] );
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
					if(isset($bidder['role_filter']) && !empty($bidder['role_filter'])){
					    $bidderRole = str_replace(array("'","&#039;"," "),array("&|&","&|&",""),trim(strtolower($bidder['role_filter'])));
					    $rolesArray[$bidderRole][] =  $bidder ['bidder_bb_id'];
					}					
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
					PullBBH :: insertUpdateOnlinePlans ( $online_plan, $lead_id, $pl_is_update, $is_op_modified );
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
				$this->updatedLeads += 1;
			} else {
				$this->insertedLeads += 1;
			}

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
		$parent_lead_id = PullBBH :: getParentLeadId ( $lead_id );
		
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
		$this->updateCurrentTimeToFile($this->lock_file);	
		return array('lead_id' => $lead_id, 'roles' => $rolesArray);			
	}
	
	/**
	 * insert/update opportunity
	*/
	private function insertUpdateOpportunity($bidderData =array(), $emailData=array()) {
	    global $current_user_role, $timedate, $app_strings, $projectOppFieldArray;
	    $user_id = $this->userData->id;
	    $current_user_role = OpportunitiesViewAssigneduser :: getUserRole($user_id);
	    $leadId = $bidderData['lead_id'];
	    $bidderArray = array();
	    $rolesArray = $bidderData['roles'];
	    $emailRoles = explode("|", $emailData['roles']);
	    
	    foreach ($emailRoles as $roles) {
	        $roles = str_replace(array("'","&#039;"," "),array("&|&","&|&",""),trim(strtolower($roles)));
	        if (!empty($rolesArray[$roles])) {
	        	$bidderArray = array_merge($bidderArray,$rolesArray[$roles]);
	        }	        
	    }
	    
	    if (!empty($leadId)) {	        	        	        	        
	        
	        $pullUserEmailId = $emailData['client_email'];
	        $pullUserId = $this->getUserIdByEmail($pullUserEmailId);
	        $obUser = BeanFactory::getBean('Users',$pullUserId);
	        //$pullUserTeamId = $obUser->getPrivateTeam();
	        $pullUserTeamId = '';
	        
	        $parentOppId = '';
	        $oss_timedate = new OssTimeDate();
	        $bidderSubOPPCount = 0;
	        $subOPPCount = 0;
	        $bidderData = array();                
	        $stExpectedAssignedUserId= '';
	        $stParentOppTeamId = '';
	        $stParentOppAssingedFlag = false;
	        $parentOppUserId = '';
	        $parentOppTeamId = '';
	        
	        //check existing project opportunity
	        //Modified by Mohit Kumar Gupta 23-11-2015
	        //Remove deleted='0' check for the change request of client BSI-793
	        $sql = "SELECT `id`,`name`,`sub_opp_count`,`assigned_user_id`,`deleted` FROM opportunities WHERE parent_opportunity_id is null AND project_lead_id = '" . $leadId . "' AND initial_pulled_user_email='".strtolower($pullUserEmailId)."'";	        
	        $query = $this->db->query ( $sql );
	        $parentOppDeletedFlag = 0;
	        
	        while ($result = $this->db->fetchByAssoc ( $query )) {
	            $parentOppId = $result['id'];
	            $subOPPCount = $result['sub_opp_count'];
	            $parentOppDeletedFlag = $result['deleted'];
	            if (!empty($result['assigned_user_id'])) {
	               $parentOppUserId = $result['assigned_user_id'];
	               $obOppUser = BeanFactory::getBean('Users',$parentOppUserId);
	               // $parentOppTeamId = $obOppUser->getPrivateTeam();
	               $parentOppTeamId = '';
	            } else {
	               $parentOppUserId = $pullUserId;
	               $parentOppTeamId = $pullUserTeamId;
	            }
	            break;
	        }
	        
	        if (! empty ( $parentOppId )) {
	            $projOppUpdate = true;
	            
	            //Modified by Mohit Kumar Gupta 23-11-2015
	            //if project opportunity is alreday deleted one then don't do anything: for the change request of client BSI-793
	            if ($parentOppDeletedFlag == '1') {
	            	return $parentOppId;
	            }
	            
	            $localProjectOpp = $this->getLocalProjectOpp ( $parentOppId );
	            $this->updatedProjectOpp += 1;
	        } else {
	            //if project opportunity is not exists, create new one
	            $projOppUpdate = false;
	            $parentOppId = create_guid ();
	            $this->insertedProjectOpp += 1;
	        }
	        
	        if (!empty($bidderArray)) {
	            $bidderSql = "SELECT id,name,assigned_user_id,role,mi_oss_leadclientdetail_id,lead_source,opportunity_id,contact_id,account_id,lead_id,bid_status FROM oss_leadclientdetail";
	            $bidderSql .= " WHERE deleted='0' AND lead_id=".$this->db->quoted($leadId)." AND mi_oss_leadclientdetail_id IN ('".implode("','", $bidderArray)."')";
	            $bidderQuery = $this->db->query($bidderSql);
	            	            
	            while ($bidderResult = $this->db->fetchByAssoc ( $bidderQuery )){
	                $data = array();
	                $data['id'] = $bidderResult['id'];
	                $data['name'] = $bidderResult['name'];
	                $data['assigned_user_id'] = $bidderResult['assigned_user_id'];
	                $data['role'] = $bidderResult['role'];
	                $data['mi_oss_leadclientdetail_id'] = $bidderResult['mi_oss_leadclientdetail_id'];
	                $data['lead_source'] = $bidderResult['lead_source'];
	                $data['contact_id'] = $bidderResult['contact_id'];
	                $data['account_id'] = $bidderResult['account_id'];
	                $data['lead_id'] = $bidderResult['lead_id'];
	                $data['bid_status'] = $bidderResult['bid_status'];	                
	                $assignedData = $this->getClientOppAssignedUser(
                        array(
                            'contact_id' => $bidderResult['contact_id'],
                            'client_id' => $bidderResult['account_id'],
                            'lead_id' => $bidderResult['lead_id']
                        )
	                );
	                 
	                if (!empty($assignedData)) {
	                    //if user filters is matches
	                    $data['sub_opp_assign_user_id'] = $assignedData['id'];
	                    $data['sub_opp_assign_team_id'] = $assignedData['team_id'];
	                } else if ($projOppUpdate == true) {
	                    //if user filters does not matches and client opportunities are going to link with existing project opportunity
	                    //then update PO assigned user id and assigned user private team id to this client opportunity
	                    $data['sub_opp_assign_user_id'] = $parentOppUserId;
	                    $data['sub_opp_assign_team_id'] = $parentOppTeamId;
	                } else {
	                    //if user filter does not matches and client opportunities are going to link with new project opportunity
	                    //then update pulling user id and pulling user team id to this client opportunity
	                    $data['sub_opp_assign_user_id'] = $pullUserId;
	                    $data['sub_opp_assign_team_id'] = $pullUserTeamId;
	                }
	                
	                //set assigned user and team for project opportunity
	                if(trim($stExpectedAssignedUserId) != '' && $stExpectedAssignedUserId != $data['sub_opp_assign_user_id'] ){
	                    $stParentOppAssingedFlag = true;
	                }
	                
	                $stExpectedAssignedUserId = $data['sub_opp_assign_user_id'];
	                $stParentOppTeamId = $data['sub_opp_assign_team_id'];	                
	                
	                $bidderData[] = $data;
	                $bidderSubOPPCount++;
	            }
	        }
	        	       	        
	        if ($stParentOppAssingedFlag == false && $bidderSubOPPCount > 0) {
	            $pullUserId = $stExpectedAssignedUserId;
	            $pullUserTeamId = $stParentOppTeamId;
	        } 
	         	         
	        $plead = PullBBH :: getLocalProjectLead ( $leadId );
	        $name = addslashes(implode(" ",array($plead['first_name'], $plead['last_name'])));
	         
	        $amount = 0;
	        $sales_stage = 'Qualification';      	        	        	        	        
	        
	        // Query for Project Opportunity
	        $projOppSql = "INSERT INTO opportunities ";
	        $projOppColumn = "(
                `id`,
                `date_entered`, 
                `date_modified`,
                `modified_user_id`,
                `created_by`,
                `assigned_user_id`,
                `team_id`,
                `team_set_id`,
                `amount`,
	            `amount_usdollar`,
                `sales_stage`,
                `name`,
	            `my_project_status`, 
	            `initial_pulled_user_email`,   
                `sub_opp_count`
	        ";
	        $projOppValues = "(
	             ".$this->db->quoted($parentOppId).",
	             UTC_TIMESTAMP(),
                 UTC_TIMESTAMP(),
                 ".$this->db->quoted($pullUserId).",
                 ".$this->db->quoted($pullUserId).",
                 ".$this->db->quoted($pullUserId).",
                 ".$this->db->quoted($pullUserTeamId).",
                 ".$this->db->quoted($pullUserTeamId).",
                 ".$this->db->quoted($amount).",
                 ".$this->db->quoted($amount).",
                 ".$this->db->quoted($sales_stage).",
                 ".$this->db->quoted($name).",                 
                 'Interested',  
                 ".$this->db->quoted(strtolower($pullUserEmailId)).",       
                 ".$this->db->quoted($bidderSubOPPCount);
	        
	        $projOppSqlUpdate = " UPDATE opportunities SET 
	                date_modified = UTC_TIMESTAMP(),
	                name = ".$this->db->quoted($name);
	        
	        //$projOppAuditSql = "INSERT INTO opportunities_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
	        
	        //$pl_counter = 0;
	        //$is_audit = false;
	        foreach ($projectOppFieldArray as $key => $value ){
	            $projOppColumn .= ", `".$value."`";
	            $projOppValues .= ", ".$this->db->quoted($plead[$key]);
	            $projOppSqlUpdate .= ", ".$value." = ".$this->db->quoted($plead[$key]);
	            
	            // Prepare Query For Change Log
	            /*if ($projOppUpdate == true) {
	                
	                if ($localProjectOpp [$value] != $plead[$key]) {
	                    $is_audit = true;
	                    if ($pl_counter > 0) {
	                        $projOppAuditSql .= ",";
	                    }
	                    if ($value == 'bids_due') {
	                        $data_type = 'datetimecombo';
	                    }
	                    	
	                    $proj_opp_audit_id = create_guid ();
	                    $projOppAuditSql .= " ('" . $proj_opp_audit_id . "','" . $parentOppId . "',UTC_TIMESTAMP(),'Blue Book','" . $value . "','" . $data_type . "','" . $localProjectOpp [$value] . "','" . $plead[$key] . "') ";
	                    $pl_counter ++;
	                }
	            }*/
	        }
	        
	        //create a new project opportunity
	        if ($projOppUpdate == false) {
	            
	            $obPackage = new instancePackage ();
	            if ($obPackage->validateOpportunities ()) {
	                sugar_die ( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
	                $GLOBALS['log']->fatal( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
	            }
	            
	        	$sql = $projOppSql.$projOppColumn.") VALUES ".$projOppValues.") ";
	        	$this->db->query($sql);
	        } else {
	            $sql = $projOppSqlUpdate." WHERE id=".$this->db->quoted($parentOppId);
	            $this->db->query($sql);
	           // if ($is_audit == true) {
	           // 	$this->db->query($projOppAuditSql);
	           // }
	        }
	                                 
	        //create client opportunities for project opportunities
	        if (!empty($parentOppId)) {
	            $arRelatedAccounts = array();
	            //Prepare data for Sub Opportunity
	             
	            $notification_list = array();
	             
	            //get saved target Classifications start
	            $arSavedTargetClass = getTargetClassifications();
	            $arSavedTargetClassifications = array();
	            foreach($arSavedTargetClass as $obSavedClass){
	                $arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->id ;
	            }
	            $countSavedTargetClassifications = count($arSavedTargetClassifications);
	            //get saved target Classifications end
	            
	            //get saved roles classifications start
	            $rolesClassificationsArr = getRolesClassifications();
	            $countRolesClassifications = count($rolesClassificationsArr);
	            if ($countRolesClassifications == 0) {
	                setRolesClassifications();
	                $rolesClassificationsArr = getRolesClassifications();
	                $countRolesClassifications = count($rolesClassificationsArr);
	            }
	            //get saved roles classifications end
	             
	            $insertClientOpp = '';
	            $clientOppValues = '';	            
	            //start foreach loop of sub opportunity.
	            foreach ($bidderData as $key=>$bidderValue) {
	                $account_id = $bidderValue['account_id'];
	                $contact_id = $bidderValue['contact_id'];
	                $lead_id = $bidderValue['lead_id'];
	                $lead_source = $plead['lead_source'];
	                $assigned_user_id = $bidderValue['sub_opp_assign_user_id'];
	                $assigned_team_id = $bidderValue['sub_opp_assign_team_id'];	                	                 

	                //check existing client opportunity for same bidder, assigned user and project opportunity
	                $existingOppId = $this->checkExistingSubOpportunity(
	                   array(
	                       'leadclientdetail_id' => $bidderValue['id'],
	                       'lead_id' => $leadId,
	                       'parent_opportunity_id' => $parentOppId
	                   )
	                );
	                
	                //if client opportunity already exist then continue to next bidder
	                if (!empty($existingOppId)) {
	                    $bidderSubOPPCount--;
	                	continue;
	                }
	                
	                //update classification id to client opportunity start
	                $opportunityClassificationId = '';
	                
	                //get classification of an accounts related to that bidder
	                $AccountCassificationArr = getAccountClassifications($account_id);
	                $countAccountClassificationArr = count($AccountCassificationArr);
	                
	                //if classification of an accounts and target classifications exists
	                if ($countAccountClassificationArr >0 && $countSavedTargetClassifications >0) {
	                    //if target classification matches to client classification
	                    //update alphabetically first target classification to classification id
	                    foreach ($arSavedTargetClassifications as $classificationId) {
	                        if (in_array($classificationId,$AccountCassificationArr)) {
	                            $opportunityClassificationId = $classificationId;
	                            break;
	                        }
	                    }
	                     
	                }
	                
	                //if classification id does not match from target classification and client classification
	                //then select classification id from role mapping
	                if (empty($opportunityClassificationId)) {
	                    $opportunityClassificationId = $rolesClassificationsArr[$bidderValue['role']];
	                    
	                    //@modified By Mohit Kumar Gupta 17-11-2015
	                    //if bidder role is having single quotes as special character BSI-787
	                    if (empty($opportunityClassificationId)) {
	                        $opportunityClassificationId = $rolesClassificationsArr[htmlspecialchars($bidderValue['role'],ENT_QUOTES)];
	                    }
	                    
	                }
	                
	                //Process to Create Sub Opportunity
	                $oppId = create_guid ();

	                //Modified by Mohit Kumar Gupta 02-12-2015
	                //Update initial pulled user email address to client opportunities also, those are pulling from BBHub
	                $clientData = "(
        	             ".$this->db->quoted($oppId).",
        	             UTC_TIMESTAMP(),
                         UTC_TIMESTAMP(),
                         ".$this->db->quoted($assigned_user_id).",
                         ".$this->db->quoted($assigned_user_id).",
                         ".$this->db->quoted($assigned_user_id).",
                         ".$this->db->quoted($assigned_team_id).",
                         ".$this->db->quoted($assigned_team_id).",
                         ".$this->db->quoted($amount).",
                         ".$this->db->quoted($amount).",
                         ".$this->db->quoted($sales_stage).",
                         ".$this->db->quoted($name).",
                         'Bidding',
                         ".$this->db->quoted($lead_id).",
                         ".$this->db->quoted($bidderValue['id']).",
                         ".$this->db->quoted($plead['bids_due']).",
                         ".$this->db->quoted($plead['bid_due_timezone']).",
                         ".$this->db->quoted($plead['lead_source']).",
                         ".$this->db->quoted($parentOppId).",
                         ".$this->db->quoted($opportunityClassificationId).",
                         ".$this->db->quoted(strtolower($pullUserEmailId)).",
                         ".$this->db->quoted($contact_id)
	                .")";	                	               

	                if ($clientOppValues == '') {
	                    $insertClientOpp = "INSERT INTO opportunities ";
	                    $clientOppColumn = "(
                            `id`,
                            `date_entered`,
                            `date_modified`,
                            `modified_user_id`,
                            `created_by`,
                            `assigned_user_id`,
                            `team_id`,
                            `team_set_id`,
                            `amount`,
	                        `amount_usdollar`,
                            `sales_stage`,
                            `name`,
            	            `client_bid_status`,
                            `project_lead_id`,
                            `leadclientdetail_id`,
                            `date_closed`,
                            `bid_due_timezone`,
                            `lead_source`,
                            `parent_opportunity_id`,
	                        `opportunity_classification`,
	                        `initial_pulled_user_email`,
                            `contact_id`
            	        )";
	                	$clientOppValues .= $clientData;
	                } else {
	                    $clientOppValues .=  ",".$clientData;
	                }
	                
	                
	                $account_sql = " SELECT `mi_account_id`,visibility,name FROM accounts WHERE deleted = 0 AND id ='".$account_id."' ";
	                $account_result = $this->db->query($account_sql);
	                $account_row = $this->db->fetchByAssoc($account_result);
	                $mi_account_id = $account_row['mi_account_id'];
	                $visibility = $account_row['visibility'];
	                 
	                $notification_list[$assigned_user_id][] = array(
	                        'client_name' => $account_row['name'],
	                );
	                 
	                $arRelatedAccounts[$oppId]['account_id'] = $account_id;
	                $arRelatedAccounts[$oppId]['contact_id'] = $contact_id;
	                 
	                //Save Opportunity Id.converted to opportunity flag into Bidder
	                $bidderUpdateQuery = "UPDATE oss_leadclientdetail SET converted_to_oppr='1', opportunity_id=".$this->db->quoted($oppId). " WHERE id=".$this->db->quoted($bidderValue['id']);
	                $this->db->query($bidderUpdateQuery);
	                 
	                setBidderVisibility($account_id,$contact_id);
	                	                	                
	            }//end foreach loop of sub opportunity.	      	            	            
	            
	            //if all client opportunity already exists for these bidders then return from there and skip next execution
	            if ($bidderSubOPPCount == 0) {
	            	if ($projOppUpdate == true) {
	            	    $this->updatedProjectOpp --;
	            	}
	            	return $parentOppId;
	            }
	            
	            //execute create client opportunities query
	            if ($insertClientOpp != '') {
	            	$clientSql = $insertClientOpp.$clientOppColumn." VALUES ".$clientOppValues;
	            	$this->db->query($clientSql);
	            }	            	            	            
	             
	            
	            $oppr_parent = new Opportunity();	            
	            $oppClient = new Opportunity();	            	            
	            foreach($arRelatedAccounts as $clientOppId => $relatedData){
	                //save project opportunity account
	                $relate_values = array('opportunities_accountsopportunities_ida'=>$parentOppId,
	                        'opportunities_accountsaccounts_idb' => $relatedData['account_id']);
	                $oppr_parent->set_relationship('opportunities_accounts_c', $relate_values);
	                
	                //save child opportunity account and contact relationship
	                $relate_values = array('opportunity_id'=>$clientOppId,'account_id' => $relatedData['account_id']);
	                $oppClient->set_relationship('accounts_opportunities', $relate_values);
	                
	                $relate_values = array('opportunity_id'=>$clientOppId,'contact_id' => $relatedData['contact_id']);
	                $oppClient->set_relationship('opportunities_contacts', $relate_values);
	                
	                //update client opportunity private team to it
	                //ViewSave_opportunity:: setTeams($clientOppId);

	            }
	            
	            //$oppr_parent->load_relationship('teams');
	            //$oppr_parent->teams->add(array($stParentOppTeamId));
                unset($oppr_parent);
                unset($oppClient);
	            
	            //Change Project Lead Status into Converted
	            $updateLeads = "UPDATE leads SET status='Converted', converted_date=".$this->db->quoted($timedate->to_db($timedate->now()))." WHERE id='".$leadId."'";
	            $this->db->query($updateLeads);	            	           	            

	            //update sub opportunity count to parent opportunity
	            $updateOppSql = "UPDATE opportunities SET sub_opp_count = ".$this->db->quoted($bidderSubOPPCount+$subOPPCount)." WHERE id = '".$parentOppId."'";	                                
	            $this->db->query($updateOppSql);
	            
	            
	            if($bidderSubOPPCount > 0){
	                updateProjectOpportunityTeamSet($parentOppId);
	            }

	            //send customizied notification email
	            if(count($notification_list) > 0)
	             ViewSave_opportunity::sendNotificationEmail($notification_list, $parentOppId);
	        }	        
               
            return $parentOppId;
            
	    } else {
	        $GLOBALS['log']->fatal('Project lead is not exists for project opportunity creation.');
	        sugar_die('Project lead is not exists for project opportunity creation.');
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
	private function insertUpdateLeadClassRel($lead_id, $classification_id, $pl_is_update) {
		$existing_sql = "SELECT oc.id FROM oss_classification oc Inner JOIN oss_classifcation_leads_c ocl on oc.id=ocl.oss_classi4427ication_ida  AND ocl.deleted = 0 WHERE oc.category_no =  '" . $classification_id . "' AND ocl.oss_classi7103dsleads_idb = '" . $lead_id . "' AND oc.deleted = 0";
		$query = $this->db->query ( $existing_sql );
		$res = $this->db->fetchByAssoc ( $query );
		if (empty ( $res ['id'] )) {
			$classification_id = PullBBH :: checkExistingRecord ( 'oss_classification', 'category_no', $classification_id );
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
	 * insert/update client in PP
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
		
		$proview_url = htmlspecialchars_decode ( $proview_url, ENT_QUOTES );
		
		$existingClient = PullBBH :: checkExistingRecord ( 'accounts', 'mi_account_id', $client_bb_id );
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
			$county = PullBBH :: getCounty ( $c_county, $c_state );
			
			//get county name for international clients
			//@modified by Mohit Kumar Gupta 30-07-2014
			$countyName = PullBBH :: getInternationalCountyName($county ['id']);
			
			
			$sql = "INSERT INTO accounts (`id`,`name`,`date_entered`,`date_modified`,`modified_user_id`, `created_by`, `deleted`,`team_id`,`team_set_id`,`phone_office`,`phone_fax`,`proview_url`,`visibility`,`mi_account_id`,`lead_source`,`billing_address_city`,`billing_address_state`,`billing_address_postalcode`,`county_name`,`county_id`) 
					VALUES ('" . $id . "','" . addslashes ( $c_name ) . "',UTC_TIMESTAMP(),UTC_TIMESTAMP(),'1','1','0','1','1','" . $c_phone . "','" . $c_fax . "','" . addslashes ( $proview_url ) . "','0','" . addslashes ( $bidder ['client_bb_id'] ) . "','bb','" . $c_city . "','" . $c_state . "','" . $c_zip . "','" . addslashes ( $countyName ) . "','" . addslashes ( $county ['id'] ) . "') ";
			$this->db->query ( $sql );
			
			// Insert Email Id
			if (isset ( $bidder ['c_email'] ) && ! empty ( $bidder ['c_email'] )) {
				PullBBH :: insertUpdateEmailAddress ( 'Accounts', $id, $c_email);
			}
			
			// Add Classification
			$class_arr = explode ( ",", $bidder ['class'] );
			foreach ( $class_arr as $class_cat_no ) {
				// $classification_id =
				// PullBBH :: checkExistingRecord('oss_classification',
				// 'category_no', $class_cat_no);
				$this->insertAccountClassRel ( $class_cat_no, $id );
			}
			return $id;
		} else {
			// Get Client
			$clientRes = $this->getClient ( $existingClient, $this->db );
			
			// Update Client
			global $client_field_array;
			
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
							$field_value = addslashes ( $bidder [$key] );
						}
						
						if ($field_name == 'county_id') {
							$county = PullBBH :: getCounty ( $field_value, $c_state );
							$field_value = $county ['id'];
							//get county name for international clients
							//@modified by Mohit Kumar Gupta 30-07-2014
							$countyName = PullBBH :: getInternationalCountyName($county ['id']);
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
						$field_value = addslashes ( $bidder [$key] );
					}
					
					if ($field_name == 'county_id') {
						$county = PullBBH :: getCounty ( $field_value, $c_state );
						$field_value = $county ['id'];
						//get county name for international clients
						//@modified by Mohit Kumar Gupta 30-07-2014
						$countyName = PullBBH :: getInternationalCountyName($county ['id']);
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
				PullBBH :: insertUpdateEmailAddress ( 'Accounts', $existingClient, $bidder ['c_email'],true, $clientRes);
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
	 * insert/update contact in PP
	 */
	public function insertContact($bidder) {
		isset ( $bidder ['cc_fname'] ) ? $cc_fname = $bidder ['cc_fname'] : $cc_fname = '';
		isset ( $bidder ['cc_lname'] ) ? $cc_lname = $bidder ['cc_lname'] : $cc_lname = '';
		isset ( $bidder ['cc_phone'] ) ? $cc_phone = addslashes ( $bidder ['cc_phone'] ) : $cc_phone = '';
		isset ( $bidder ['cc_fax'] ) ? $cc_fax = addslashes ( $bidder ['cc_fax'] ) : $cc_fax = '';
		isset ( $bidder ['cc_email'] ) ? $cc_email = addslashes ( $bidder ['cc_email'] ) : $cc_email = '';
		isset ( $bidder ['contact_bb_id'] ) ? $contact_bb_id = $bidder ['contact_bb_id'] : $contact_bb_id = '';
		
		// Check Existing Contact by master id
		$existingContact = PullBBH :: checkExistingRecord ( 'contacts', 'mi_contact_id', $contact_bb_id );
		if (empty ( $existingContact )) {
			$name = $cc_fname . " " . $cc_lname;
			//$name = htmlspecialchars_decode ( $name, ENT_QUOTES );
			//$name = addslashes ( $name );
			$existingContact = checkExistingClientContact ( $name, $cc_phone, $cc_fax, $cc_email );
		}
		if (empty ( $existingContact )) {
			$id = create_guid ();
			$sql = "INSERT INTO contacts (`id`,`first_name`,`last_name`,`date_entered`,`date_modified`,`modified_user_id`, `created_by`, `deleted`,`team_id`,`team_set_id`,`phone_work`,`phone_fax`,`mi_contact_id`,`visibility`,`lead_source`)
					VALUES ('" . $id . "','" . addslashes($cc_fname) . "','" . addslashes($cc_lname) . "',UTC_TIMESTAMP(),UTC_TIMESTAMP(),'1','1','0','1','1','" . $cc_phone . "','" . $cc_fax . "','" . addslashes ( $contact_bb_id ) . "','0','bb') ";
			$this->db->query ( $sql );
			// Insert Email Address
			if (isset ( $bidder ['cc_email'] ) && ! empty ( $bidder ['cc_email'] )) {
				PullBBH :: insertUpdateEmailAddress ( 'Contacts', $id, $bidder ['cc_email'] );
			}
			return $id;
		} else {
			// Get Client Contact
			$contactRes = $this->getClientContact ( $existingContact, $this->db );
			
			// update contact
			global $contact_field_array;
			$updateSQL = "UPDATE contacts SET ";
			$updateSQL .= "`date_modified` = UTC_TIMESTAMP() ";
			if ($contactRes ['is_modified'] == 1) {
				// update contact based on modified field
				foreach ( $contact_field_array as $key => $value ) {
					if (empty ( $contactRes [$value] )) {
						if (isset ( $bidder [$key] )) {
							if (trim ( $bidder [$key] ) != '') {
								$field_name = $contact_field_array[$key];
								$field_value = addslashes ($bidder [$key]);
								
								$field_type = '';
								if($field_name == 'primary_address_state' || $field_name == 'alt_address_state'){
									$field_type = 'enum';
								}								
								insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes [$field_name], $field_value, $field_name, $field_type, 'Blue Book');
								$updateSQL .= ",`$field_name` = '" . $field_value . "' ";
							}
						}
					}
				}
			} else {
				foreach ( $contact_field_array as $key => $value ) {
					if (isset ( $bidder [$key] )) {
						if (trim ( $bidder [$key] ) != '') {
							$field_name = $contact_field_array [$key];
							$field_value = addslashes ($bidder [$key]);
							
							if ($contactRes ['visibility'] == 1) {
								$field_type = '';
								if ($field_name == 'primary_address_state' || $field_name == 'alt_address_state') {
									$field_type = 'enum';
								}
								insertChangeLog ( $this->db, 'contacts', $existingContact, $contactRes [$field_name], $field_value, $field_name, $field_type, 'Blue Book' );
							}
							$updateSQL .= ",`$contact_field_array[$key]` = '" . $field_value . "' ";
						}
					}
				}
			}
			$updateSQL .= "WHERE `id` = '" . $existingContact . "'";

			$this->db->query ( $updateSQL );
			// update email Address
			if (isset ( $bidder ['cc_email'] ) && ! empty ( $bidder ['cc_email'] )) {
				PullBBH :: insertUpdateEmailAddress ( 'Contacts', $existingContact, $bidder ['cc_email'],true, $contactRes);
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
	 * Insert Account and Classification Relationship
	 */
	function insertAccountClassRel($class_id, $account_id) {
		$existing_sql = "SELECT oc.id FROM oss_classification oc Inner JOIN oss_classifion_accounts_c ocl on oc.id=ocl.oss_classi48bbication_ida  AND ocl.deleted = 0 WHERE oc.category_no =  '" . $class_id . "' AND ocl.oss_classid41cccounts_idb = '" . $account_id . "' AND oc.deleted = 0";
		$existing_query = $this->db->query ( $existing_sql );
		$existing_result = $this->db->fetchByAssoc ( $existing_query );
		if (empty ( $existing_result )) {
			$classification_id = PullBBH :: checkExistingRecord ( 'oss_classification', 'category_no', $class_id );
			if (! empty ( $classification_id )) {
				$insertSql = "INSERT INTO oss_classifion_accounts_c (`id`,`date_modified`,`oss_classi48bbication_ida`,`oss_classid41cccounts_idb`) VALUES (UUID(),UTC_TIMESTAMP(),'" . $classification_id . "','" . $account_id . "')";
				$this->db->query ( $insertSql );
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
	
	/**
	 * update current time with process id in status file
	 * @author Mohit Kumar Gupta
	 * @date 01-07-2015
	 * @param string $lock_file
	 */
	private function updateCurrentTimeToFile($lock_file = 'upload/process/opp_import_process_lock') {
		// Upadte Current time into process lock file
		$content = file_get_contents ( $lock_file );
		$content = explode ( "_", $content );
		$pid = $content [0];
		
		$text = $pid . "_" . time ();
		
		$fp1 = fopen ( $lock_file, "w" );
		fwrite ( $fp1, $text );
		fclose ( $fp1 );
	}	
    /**
     * get local opportunity data
     * @param string $oppId
     * @return array
     */
	function getLocalProjectOpp ($oppId) {
	    $sql = "SELECT * from `opportunities` WHERE id='" . $oppId . "' AND `deleted`=0";
	    $query = $this->db->query ( $sql );
	    $result = $this->db->fetchByAssoc ( $query );
	    return $result;
	}
	
	/**
	 * get assigned user and their private team based on user filters
	 * @param array $dataArray
	 */
	function getClientOppAssignedUser($dataArray = array()){
		$assignmentData = array();
		
	    if (!empty($dataArray)) {
	        // Get Georaphical Filter Value
	        $config_sql = "SELECT `value` FROM config WHERE `name`='geo_filter' AND `category`='instance'";
	        $config_query = $this->db->query ( $config_sql );
	        $config_result = $this->db->fetchByAssoc ( $config_query );
	        $geo_filter = 'client_location';
	        if (!empty($config_result ['value'])) {
	            $geo_filter = $config_result ['value'];
	        }
	        
	        
	        //1. suggest assigned user based on contact id
	        if(isset($dataArray['contact_id']) && !empty($dataArray['contact_id'])){
	            
	            $contactSql = "SELECT assigned_user_id from contacts where deleted='0' AND id=".$this->db->quoted($dataArray['contact_id']);
	            $contactQuery = $this->db->query ( $contactSql );
	            $contactResult = $this->db->fetchByAssoc ( $contactQuery );
	            	
	            $user_role = OpportunitiesViewAssigneduser :: getUserRole($contactResult['assigned_user_id']);
	            
	            if(isset($contactResult['assigned_user_id']) && !empty($contactResult['assigned_user_id']) && ( $user_role != 'lead_reviewer' ) ){
	        
	                // $private_team_id = User::getPrivateTeam($contactResult['assigned_user_id']);
	                $private_team_id = '';
	                
	                $assignmentData = array(
                        'id' => $contactResult['assigned_user_id'],
                        'team_id' => $private_team_id
	                );
	                return $assignmentData;
	            }
	        }
	        
	        //2. suggest assigned user based on client id	        
	        if(isset($dataArray['client_id']) && !empty($dataArray['client_id'])){
	             
	            $accountSql = "SELECT assigned_user_id from accounts where deleted='0' AND id=".$this->db->quoted($dataArray['client_id']);
	            $accountQuery = $this->db->query ( $accountSql );
	            $accountResult = $this->db->fetchByAssoc ( $accountQuery );
	        
	            $user_role = OpportunitiesViewAssigneduser :: getUserRole($accountResult['assigned_user_id']);
	             
	            if(isset($accountResult['assigned_user_id']) && !empty($accountResult['assigned_user_id']) && ( $user_role != 'lead_reviewer' ) ){
	                 
	                $private_team_id = User::getPrivateTeam($accountResult['assigned_user_id']);
	                 
	                $assignmentData = array(
                        'id' => $accountResult['assigned_user_id'],
                        'team_id' => $private_team_id
	                );
	                return $assignmentData;
	            }
	        }
	        
	        
	        //3. suggest assigned user based on geo location if client contact or client
	        //does not have any assigned user
            //third suggest assigned user based on location  
                                  
            //get assigned to user if client location is set
            if($geo_filter == 'client_location' && !empty($dataArray['client_id'])){
                $accountSql2 = "SELECT billing_address_state,county_id,billing_address_postalcode from accounts where deleted='0' AND id=".$this->db->quoted($dataArray['client_id']);
                $accountQuery2 = $this->db->query ( $accountSql2 );
                $accountResult2 = $this->db->fetchByAssoc ( $accountQuery2 );
                
                $classIds = array();
                $classificationSql = "SELECT oss_classi48bbication_ida from oss_classifion_accounts_c where deleted='0' AND oss_classid41cccounts_idb=".$this->db->quoted($dataArray['client_id']);
                $classificationQuery = $this->db->query ( $classificationSql );
                while ($classificationResult = $this->db->fetchByAssoc ( $classificationQuery )) {
                    $classIds [] = $classificationResult['oss_classi48bbication_ida'];
                }
                

                $assigned_user = LeadsViewConvert_to_opportunity::getAssignedUser('Accounts',$accountResult2['billing_address_state'],$accountResult2['county_id'],$accountResult2['billing_address_postalcode'],NULL,$classIds);
        
                if(isset($assigned_user['id']) && !empty($assigned_user['id'])){
                    	        
                    $private_team_id = User::getPrivateTeam($assigned_user['id']);
                    $assignmentData = array(
                        'id' => $assigned_user['id'],
                        'team_id' => $private_team_id
                    );                    
                	                   
                    return $assignmentData;
                }
            }else if($geo_filter == 'project_location' && !empty($dataArray['lead_id']) ){
                //get assigned to user if project location is set 
                $leadSql = "SELECT state,county_id,zip_code,type,union_c,non_union,prevailing_wage from leads where deleted='0' AND id=".$this->db->quoted($dataArray['lead_id']);
                $leadQuery = $this->db->query ( $leadSql );
                $leadResult = $this->db->fetchByAssoc ( $leadQuery );
                
                $classIds = array();
                $classificationSql2 = "SELECT oss_classi4427ication_ida from oss_classifcation_leads_c where deleted='0' AND oss_classi7103dsleads_idb=".$this->db->quoted($dataArray['lead_id']);
                $classificationQuery2 = $this->db->query ( $classificationSql2 );
                while ($classificationResult2 = $this->db->fetchByAssoc ( $classificationQuery2 )) {
                    $classIds [] = $classificationResult2['oss_classi4427ication_ida'];
                }

                $lead_labor = array();
    
                if($leadResult['union_c'] == 1)
                    $lead_labor[] = 'union_c';
    
                if($leadResult['non_union'] == 1)
                    $lead_labor[] = 'non_union';
    
                if($leadResult['prevailing_wage'] == 1)
                    $lead_labor[] = 'prevailing_wage';
    
                $assigned_user =LeadsViewConvert_to_opportunity::getAssignedUser('Leads',$leadResult['state'],$leadResult['county_id'],$leadResult['zip_code'],$leadResult['type'],$leadClassIds,$lead_labor);
                               
                if(isset($assigned_user['id']) && !empty($assigned_user['id'])){
                     
                    $private_team_id = User::getPrivateTeam($assigned_user['id']);
                    $assignmentData = array(
                            'id' => $assigned_user['id'],
                            'team_id' => $private_team_id
                    );
                
                    return $assignmentData;
                }                                
            }
        }
		return $assignmentData;
	}
	/**
	 * return user id based on email
	 * @param string $emailId
	 */
	function getUserIdByEmail($emailId) {
	    $userId = '1';
	    $userRole = 'lead_reviewer';
		if (!empty($emailId)){
			$emailSql = "SELECT users.id 	 
                FROM users 
                LEFT JOIN email_addr_bean_rel ear 
                ON  ear.bean_id = users.id AND  ear.bean_module = 'Users'
                AND ear.deleted = 0 AND ear.primary_address = 1
                LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id 
                AND ea.deleted = 0 
                WHERE ea.email_address_caps =".$this->db->quoted(strtoupper($emailId));
			$emailQuery = $this->db->query ( $emailSql );
			$userData = $this->db->fetchByAssoc ( $emailQuery );
			if (isset($userData['id']) && !empty($userData['id'])) {
				$userId = $userData['id'];
				$userRole = OpportunitiesViewAssigneduser :: getUserRole($userId);
			}
		}
		
		//if bidscope user is lead reviewer then opp assigned user should be admin
		if ($userRole == 'lead_reviewer') {
			$userId = '1';
		}
		return $userId;
	}
	
	/**
	 * return existing client opportunity id
	 */
	function checkExistingSubOpportunity($dataArray = array()){
	    $oppId = '';
	    if (!empty($dataArray)){	
	        //Modified by Mohit Kumar Gupta 23-11-2015
	        //Remove deleted='0' check for the change request of client BSI-793
	        $oppSql = "SELECT id FROM opportunities WHERE 
	                parent_opportunity_id =".$this->db->quoted($dataArray['parent_opportunity_id'])." AND 
	                leadclientdetail_id =".$this->db->quoted($dataArray['leadclientdetail_id'])." AND 
	                project_lead_id =".$this->db->quoted($dataArray['lead_id']);
	        $oppQuery = $this->db->query ( $oppSql );
	        $oppData = $this->db->fetchByAssoc ( $oppQuery );
	        if (isset($oppData['id']) && !empty($oppData['id'])) {
	            $oppId = $oppData['id'];
	        }
	    }
	    
	    return $oppId;
	}
	
	/**
	 * get saved pull opportunity last pull date, if not found then return one day in past
	 * opportunity pull date will be unique for each user in case of insert opportunity but same for each user in case of opportunity/project update
	 * @param current user id $userId
	 */
	function getOpportunityLastPullDate($userId ='1') {
	    if (isset($GLOBALS['sugar_config']['opp_last_pull_date']['opp_insert_date'][$userId])) {
	        $pullOppLastPullDate = $GLOBALS['sugar_config']['opp_last_pull_date']['opp_insert_date'][$userId];
	        
	    } else {
	        $bbDate = new SugarDateTime(date('m/d/Y H:i:s'));
	        $bbDate->setTimezone(new DateTimeZone('US/Eastern'));
	        $dateFormat = $bbDate->format('m/d/Y H:i:s');
	        
	        //get default one day in past from current est time
	        $pullOppLastPullDate = date('m/d/Y H:i:s',strtotime($dateFormat) - 3600*24);
	    }
	    
	    //TODO remove this if not needed on production: past the value by 15 minutes because production server is 11 minutes advance
	    $pullOppLastPullDate = date('m/d/Y H:i:s',strtotime($pullOppLastPullDate) - 900); 
	    return $pullOppLastPullDate; 
	}
	
	/**
	 * get saved pull opportunity last pull date, if not found then return one day in past
	 * * opportunity pull date will be unique for each user in case of insert opportunity but same for each user in case of opportunity/project update
	 * @param current user id $userId
	 */
	function setOpportunityLastPullDate($userId = '1') {
	    $pullOppLastPullDate = array();
	    if (isset($GLOBALS['sugar_config']['opp_last_pull_date'])) {
	        $pullOppLastPullDate = $GLOBALS['sugar_config']['opp_last_pull_date'];
	    } 
        $bbDate = new SugarDateTime(date('m/d/Y H:i:s'));
        $bbDate->setTimezone(new DateTimeZone('US/Eastern'));
        $dateFormat = $bbDate->format('m/d/Y H:i:s');        
        $pullOppLastPullDate ['opp_insert_date'][$userId] = $dateFormat;	 
        
        $GLOBALS['sugar_config']['opp_last_pull_date'] = '';
        $conf = new Configurator();
        $conf->config['opp_last_pull_date'] = $pullOppLastPullDate;
        $conf->saveConfig(); 	
	}
}
?>
