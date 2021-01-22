<?php
//ini_set('display_errors','1');
require_once 'custom/include/common_functions.php';
require_once 'custom/include/OssTimeDate.php';
require_once 'custom/include/dynamic_dropdown.php';
require_once('modules/Import/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/ImportCacheFiles.php');
require_once('modules/Import/ImportDuplicateCheck.php');
require_once('include/upload_file.php');

class ImportDodge
{
    private $db;
    private $sugar_config;
    private $current_user;
    private $data;
    private $import_module;
    private $fname;
    private $list_value;
    private $leadField;
    private $clientField;
    private $contactField;
    private $fileNameTime;
    private $iTotalLeadsPulledCount = 0;
    private $dtStartDateTime;
    private $iTotalInsertedLeads = 0;
    private $iTotalUpdatedLeads = 0;

    /**
     * @function : __construct()
     * @desc: Initialize all the variables.
     */
    function __construct($xmlObj, $importFileName)
    {
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
        $this->leadField = array(
            'project_title' => 'title',
            'valuation' => 'est-low',
            'address' => 'p-addr-line1',
            'city' => 'p-city-name',
            'state' => 'p-state-id',
            'county_id' => 'p-county-name',
            'zip_code' => 'p-zip-code5',
            //'structure' => 'work-type',
            //'project_status' => 'status-text',
            //'type' => 'proj-type',
            //'owner' => 'owner-class',
            'custom_field_1' => 'work-type',
            'custom_field_4' => 'status-text',
            'custom_field_2' => 'proj-type',
            'custom_field_3' => 'owner-class',
            'contact_no' => 'contract-nbr',
            'start_date' => 'target-start-date',
            'scope_of_work' => 'title-code',
            'last_name' => 'title',
        );

        $this->clientField = array(
            'name' => 'firm-name',
            'address1' => 'c-addr-line1',
            'address2' => 'c-addr-line2',
            'billing_address_city' => 'c-city-name',
            'billing_address_state' => 'c-state-id',
            'billing_address_postalcode' => 'c-zip-code5',
            'county_id' => 'c-county-name',
            'website' => 'www-url',
            'phone_office' => 'phone-office',
            'phone_fax' => 'phone-fax'
        );

        $this->contactField = array(
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'title' => 'contact-title',
            'phone_work' => 'phone-work',
            'phone_fax' => 'phone-fax'
        );
        $obDateTime = new SugarDateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
        $obDateTime->setTimezone(new DateTimeZone('US/Eastern'));
        $stDateTime = $obDateTime->format('m/d/Y H:i:s');
        $this->dtStartDateTime = $stDateTime;

        /**
         * unset the custom fields of type,structure,status and owner
         * if these are not exists in leads table
         */
        $leads = new Lead();
        if (!in_array('custom_field_1', $leads->column_fields)) {
            unset($this->leadField['custom_field_1']);
        }

        if (!in_array('custom_field_2', $leads->column_fields)) {
            unset($this->leadField['custom_field_2']);
        }

        if (!in_array('custom_field_3', $leads->column_fields)) {
            unset($this->leadField['custom_field_3']);
        }

        if (!in_array('custom_field_4', $leads->column_fields)) {
            unset($this->leadField['custom_field_4']);
        }
    }

    /**
     * @function : insertData()
     * @desc : Build xml array, call and pass xml data to different method to import.
     **/
    function insertData()
    {

        $this->fileNameTime = strtotime("now");
        foreach ($this->data as $record) {
            $onlinePlanQuery = '';
            //create / update leads
            $lead_id = $this->insertProjectLead($record);

            //Lead URL Plans
            if (isset($record['project-url']) && !empty($record['project-url'])) {
                $url = htmlspecialchars_decode($record ['project-url'], ENT_QUOTES);
                //check if same online plan exist
                $sql = "SELECT id FROM oss_onlineplans WHERE lead_id='" . $lead_id . "' AND description='" . $url . "' AND deleted = 0";
                $query = $this->db->query($sql);
                $result = $this->db->fetchByAssoc($query);

                //if same online plan doesn't exist create a new record
                if (empty($result['id'])) {
                    $onlinePlanQuery .= " ( UUID(), UTC_TIMESTAMP(), UTC_TIMESTAMP(),
                        1, 1, '" . addslashes($url) . "', 0, 1, 'Other',
                        'Dodge', '" . $lead_id . "' ) ";
                }
            }

            if (isset($record['cn-project-url']) && !empty($record['cn-project-url'])) {
                $url = htmlspecialchars_decode($record ['cn-project-url'], ENT_QUOTES);
                //check if same online plan exist
                $sql = "SELECT id FROM oss_onlineplans WHERE lead_id='" . $lead_id . "' AND description='" . $url . "' AND deleted = 0";
                $query = $this->db->query($sql);
                $result = $this->db->fetchByAssoc($query);

                //if same online plan doesn't exist create a new record
                if (empty($result['id'])) {
                    $onlinePlanQuery .= !empty($onlinePlanQuery) ? ", " : "";
                    $onlinePlanQuery .= " ( UUID(), UTC_TIMESTAMP(), UTC_TIMESTAMP(),
                        1, 1, '" . addslashes($url) . "', 0, 1, 'Other',
                        'Dodge', '" . $lead_id . "' ) ";
                }
            }

            if ($onlinePlanQuery != '') {
                $db_query = " INSERT INTO oss_onlineplans (id, date_entered, date_modified,
                    modified_user_id, created_by, description, deleted, assigned_user_id, plan_type,
                    plan_source, lead_id ) VALUES ";
                $db_query .= $onlinePlanQuery;
                $this->db->query($db_query);
            }

            //create  / update clients
            foreach ($record ['bidders'] as $bidder) {

                //create / update client
                $client_id = $this->insertClient($bidder, $record['dr-nbr']);

                //create / update client contact
                if (isset($bidder ['contact-name']) && !empty ($bidder ['contact-name'])) {
                    $contact_id = $this->insertClientContact($bidder, $client_id, $record['dr-nbr']);
                } else {
                    $contact_id = '';
                }

                //create / update bidders list
                $this->insertBidderList($lead_id, $client_id, $contact_id, $bidder);

            }

            //get parent lead id if any
            $parent_lead_id = $this->getParentLeadId($lead_id);

            // Bidder list logic hooks
            $this->updateNewTotalBidderCountBBH($parent_lead_id);
            $this->updateLeadVersionBidDueDateBBH($parent_lead_id);

            // Update online plans
            updateOnlineCount($lead_id);

            //if bidder modified add entry to change log
            if ($this->is_bidder_modified && ($this->bidder_row_count != 0)) {
                $sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $lead_id . "' AND `field_name`='Bidders List Modification'";
                $query = $this->db->query($sql);
                $result = $this->db->fetchByAssoc($query);
                $insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'" . $lead_id . "',UTC_TIMESTAMP(),'" . $current_user->id . "','Bidders List Modification','datetimecombo','" . $result['before_date'] . "',UTC_TIMESTAMP());";
                $this->db->query($insertSQL);
            }
            $GLOBALS['log']->fatal('Lead and all related data inserted/updated with dodge id', $record['dr-nbr']);
            $GLOBALS['log']->fatal('Insert/update Lead count = ' . $this->iTotalInsertedLeads . '/' . $this->iTotalUpdatedLeads);
        }

        //set update prev bid to flag
        setPreviousBidToUpdate();

        //$this->addDropDownFromFile();
        $this->sendImportCompleteEmail();
    }

    /**
     * @function: insertProjectLead()
     * @param: array record
     * @param: object importfile.
     * @return: string lead id
     */
    function insertProjectLead($record)
    {

        global $app_list_strings, $timedate, $db;
        $oss_timedate = new OssTimeDate ();

        $dodge_id = trim($record['dr-nbr']);
        $date_entered = '';

        //Check if the dodge_id alread exists then assign lead to mofify
        $leadId = $this->checkExistingRecord('leads', 'dodge_id', $dodge_id);
        $pl_is_update = false;
        $newLeadRecord = true;
        $pl_status = 'New';
        $leadFields = $this->leadField;
        $assignedUserId = '1';
        if (!empty($this->current_user->id)) {
            $assignedUserId = $this->current_user->id;
        }

        //if lead already in system update the record
        if (!empty($leadId)) {
            $pl_is_update = true;
            $newLeadRecord = false;
            $locallead = $this->getLocalProjectLead($leadId);
            if ($locallead ['status'] == 'Converted') {
                $pl_status = $locallead ['status'];
            }

            $date_entered = $locallead ['date_entered'];

            $sqlLeadClientCount = "SELECT count(1) as row_count FROM  oss_leadclientdetail WHERE deleted = '0' AND lead_id='" . $leadId . "'";
            $res_count = $this->db->query($sqlLeadClientCount);
            $row_count = $this->db->fetchByAssoc($res_count);
            $this->bidder_row_count = $row_count['row_count'];
            if (count($record ['bidders']) > $row_count['row_count']) {
                $this->is_bidder_modified = true;
            }
            $plSqlUpdate = "UPDATE leads SET 
			        `date_modified`=UTC_TIMESTAMP(),
			        `status` = '" . $pl_status . "'
			";
            $pl_audit_sql = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES ";
        } else {
            $leadId = create_guid();
            // Query for Project Lead
            $plsqlColumns = "INSERT INTO leads (
		        `id`,
		        `date_entered`,
		        `date_modified`,
		        `modified_user_id`,
		        `created_by`,
		        `assigned_user_id`,
		        `team_id`,
		        `team_set_id`,
		        `status`,
		        `lead_source`,
		        `dodge_id`,
		        `project_lead_id`,
		        `parent_lead_id`
	        ";
            $plsqlValues = " VALUES (
		        '" . $leadId . "',
		        UTC_TIMESTAMP(),
		        UTC_TIMESTAMP(),
		        '" . $assignedUserId . "',
		        '" . $assignedUserId . "', 
                '" . $assignedUserId . "',
                '1',
                '1',
                '" . $pl_status . "',
		        'dodge',  
                '" . addslashes($dodge_id) . "', 
                '" . addslashes($record['dr-nbr']) . "',
                '" . $leadId . "'                                           		                                              
		    ";
        }


        $pl_counter = 0;
        $is_audit = false;
        $leadState = substr($record['p-state-id'], 0, 2);

        foreach ($leadFields as $dbKey => $xmlKey) {
            $data_type = '';
            /* if ($dbKey == 'structure') {
                 //Structure Type
                 $leadStructre = $this->clean_text($record[$xmlKey]);
                 $list_value = '["'.$leadStructre.'","'.addslashes($record[$xmlKey]).'"]';
                 $this->addDropDownToFile ('structure_non_building',$list_value);
                 $dataValue = $leadStructre;
                 $data_type = 'enum';
             } else if ($dbKey == 'project_status') {
                 //Project Status
                 $projectStatus = $this->clean_text($record[$xmlKey]);
                 $list_value = '["'.$projectStatus.'","'.addslashes($record[$xmlKey]).'"]';
                 $this->addDropDownToFile ('project_status_dom',$list_value);
                 $dataValue = $projectStatus;
                 $data_type = 'enum';
             } else if ($dbKey == 'owner') {
                 //Project Lead Owner
                 $leadOwner = $this->clean_text($record[$xmlKey]);
                 $list_value = '["'.$leadOwner.'","'.addslashes($record[$xmlKey]).'"]';
                 $this->addDropDownToFile ('owner_dom',$list_value);
                 $dataValue = $leadOwner;
                 $data_type = 'enum';
             } else if ($dbKey == 'type') {
                 //Project Lead Type
                 $leadType = $this->clean_text($record[$xmlKey]);
                 $list_value = '["'.$leadType.'","'.addslashes($record[$xmlKey]).'"]';
                 $this->addDropDownToFile ('project_type_dom',$list_value);
                 $dataValue = $leadType;
                 $data_type = 'enum';
             } else*/
            if ($dbKey == 'state') {
                $dataValue = $leadState;
            } else {
                if ($dbKey == 'county_id') {

                    if ($record['p-fips-county']) {
                        $dataValue = $this->getCounty(array(
                            'fips' => $record['p-fips-county'],
                            'name' => trim(str_ireplace(" COUNTY", "", $record[$xmlKey]))
                        ), $leadState, 1);
                    } else {
                        $dataValue = $this->getCounty(array(
                            'name' => trim(str_ireplace(" COUNTY", "", $record[$xmlKey]))
                        ), $leadState);
                    }

                } else {
                    if ($dbKey == 'start_date') {
                        $dataValue = $this->makeMysqlDate($record[$xmlKey]);
                    } else {
                        $dataValue = htmlspecialchars_decode($record[$xmlKey], ENT_QUOTES);
                        $dataValue = addslashes($dataValue);
                    }
                }
            }

            // Prepare Query For Change Log
            if ($pl_is_update == true) {
                if ($locallead [$dbKey] != $dataValue) {
                    $is_audit = true;
                    if ($pl_counter > 0) {
                        $pl_audit_sql .= ",";
                    }

                    $lead_audit_id = create_guid();
                    $pl_audit_sql .= " ('" . $lead_audit_id . "','" . $leadId . "',UTC_TIMESTAMP(),'Dodge','" . $dbKey . "','" . $data_type . "','" . $locallead [$dbKey] . "','" . $dataValue . "') ";
                    $pl_counter++;
                }
                $plSqlUpdate .= ", `" . $dbKey . "` =  '" . $dataValue . "'";
            } else {
                $plsqlColumns .= ", `" . $dbKey . "`";
                $plsqlValues .= ", '" . $dataValue . "'";
            }
        }


        //Bid Due Date
        $bid_date = $this->makeMysqlDate($record['bid-date']);//get mysql format
        $bid_time = $record['bid-time']; //time
        $bidZone = substr(trim($record['bid-zone']), 0, 3);//time zone

        //set bid due timezone according to state if bid zone is empty
        if (!empty($bidZone)) {

            $arTimeZoneMap = array(
                'CT' => 'Central',
                'ET' => 'Eastern',
                'MT' => 'Mountain',
                'PT' => 'Pacific',
                'CST' => 'Central',
                'EST' => 'Eastern',
                'MST' => 'Mountain',
                'PST' => 'Pacific',
                'CDT' => 'Central',
                'EDT' => 'Eastern',
                'MDT' => 'Mountain',
                'PDT' => 'Pacific'
            );

            if (array_key_exists($bidZone, $arTimeZoneMap)) {
                $bidZone = $arTimeZoneMap[$bidZone];
            }
        } else {
            $state = $app_list_strings['state_dom'][$leadState];
            $bidZone = $app_list_strings['state_tz_dom'][$state];
        }


        if ($pl_is_update == true) {
            $plSqlUpdate .= ", `bid_due_timezone` =  '" . $bidZone . "'";
        } else {
            $plsqlColumns .= ", `bid_due_timezone`";
            $plsqlValues .= ", '" . $bidZone . "'";
        }

        if ($pl_is_update == true && $locallead ['bid_due_timezone'] != $bidZone) {
            //add lead change log
            $is_audit = true;
            if ($pl_counter > 0) {
                $pl_audit_sql .= ",";
            }

            $pl_audit_sql .= " ('" . create_guid() . "','" . $leadId . "',UTC_TIMESTAMP(),'Dodge',' bid_due_timezone ','','" . $locallead ['bid_due_timezone'] . "','" . $bidZone . "') ";
            $pl_counter++;
        }

        $stBidsDueDate = (trim($bid_date) != '') ? strtotime($bid_date) : '';
        $stBidsDueTime = (trim($bid_time) != '') ? strtotime($bid_time) : '';

        if (trim($stBidsDueDate) != '') {
            $db_time = date('H:i:s', $stBidsDueTime);
            $db_date_time = $bid_date . ' ' . $db_time;
            $userDateTime = $timedate->to_display_date_time($db_date_time, true, false);
            //convert bid due date to db format
            $gmt_time = $oss_timedate->convertDateForDB($userDateTime, $bidZone);

            if ($pl_is_update == true) {
                $plSqlUpdate .= ", `bids_due` =  '" . $gmt_time . "'";
            } else {
                $plsqlColumns .= ", `bids_due`";
                $plsqlValues .= ", '" . $gmt_time . "'";
            }

            if ($pl_is_update == true && $locallead ['bids_due'] != $gmt_time) {
                //add lead change log
                $is_audit = true;
                if ($pl_counter > 0) {
                    $pl_audit_sql .= ",";
                }

                $pl_audit_sql .= " ('" . create_guid() . "','" . $leadId . "',UTC_TIMESTAMP(),'Dodge',' bids_due ',' datetimecombo','" . $locallead ['bids_due'] . "','" . $gmt_time . "') ";
                $pl_counter++;
            }
        }

        // Insert Update Project Lead
        if ($pl_is_update == true) {
            $this->iTotalUpdatedLeads++;
            $plsql = $plSqlUpdate . " WHERE id='" . $leadId . "'";
        } else {
            $this->iTotalInsertedLeads++;
            $plsql = $plsqlColumns . " )" . $plsqlValues . " )";
        }

        $this->db->query($plsql);
        // Insert Lead Change Log Query
        if ($is_audit == true) {
            $this->db->query($pl_audit_sql);
            changeLogFlag($leadId, $this->db);
            $is_audit = false;
        }
        //save import info
        ImportFile::markRowAsImported($newLeadRecord);
        if ($newLeadRecord) {
            ImportFile::writeRowToLastImport($this->import_module, 'Lead', $leadId);
        }
        /****************************************************************/
        return $leadId;
    }

    /**
     * check an existiing record in crm
     * @param string $table
     * @param string $field_name
     * @param string $field_value
     * @return string result
     */
    function checkExistingRecord($table, $field_name, $field_value)
    {
        $sql = "SELECT id FROM " . $table . " WHERE " . $field_name . " = '" . $field_value . "' AND deleted=0";
        $query = $this->db->query($sql);
        $result = $this->db->fetchByAssoc($query);
        if (!empty ($result)) {
            return $result ['id'];
        }
        return '';
    }

    /**
     * Get Project Lead Information
     * @param lead id
     * @return array leads
     */
    function getLocalProjectLead($id)
    {
        $sql = "SELECT * from `leads` WHERE id='" . $id . "' AND `deleted`=0";
        $query = $this->db->query($sql);
        $result = $this->db->fetchByAssoc($query);
        return $result;
    }

    /**
     * @param array $countyValue
     * @param string $state
     * @param bool $isFipsSearch
     * @return string - UUID of the county
     */
    function getCounty($countyValue, $state = '', $isFipsSearch)
    {
        $countyId = "";
        if ($isFipsSearch) {

            $t = preg_replace('/[^0-9]/', '', $countyValue['fips']);
            $county_number = ltrim($t, 0);
            $county_abbr = preg_replace('/[0-9]/', '', $countyValue['fips']);

            $countSQL = "SELECT id FROM oss_county WHERE county_number='{$county_number}' AND county_abbr='{$county_abbr}' AND deleted = '0' ";
            $countSQL .= (!empty($state)) ? " AND county_abbr='" . $state . "'" : "";

            $resCounty = $this->db->query($countSQL);
            $rowCounty = $this->db->fetchByAssoc($resCounty);
            if (!empty ($rowCounty)) {
                return $rowCounty ['id'];
            }
        }

        $proj_county = explode(";", $countyValue['name']);
        $proj_county = $proj_county [0];

        if (!empty ($proj_county)) {
            $countSQL = "SELECT id FROM oss_county WHERE LOWER(name) LIKE '" . strtolower($proj_county) . "%' AND deleted = '0' ";

            $countSQL .= (!empty($state)) ? " AND county_abbr='" . $state . "'" : "";

            $resCounty = $this->db->query($countSQL);
            $rowCounty = $this->db->fetchByAssoc($resCounty);
            if (!empty ($rowCounty)) {
                $countyId = $rowCounty ['id'];
            }
        }
        return $countyId;
    }

    /**
     * create mysql date format
     * @param: string $datestring
     * @return: string mysql date format
     */
    function makeMysqlDate($datestring)
    {
        $day = substr($datestring, -2);
        $month = substr($datestring, 4, 2);
        $year = substr($datestring, 0, 4);
        return $year . '-' . $month . '-' . $day;
    }

    /**
     * @function: insertClient
     * @param: array bidder
     * @param: string dodge unique id
     * @param: object importfile
     * @return: string client id
     */
    function insertClient($bidder, $dodge_id)
    {

        $clientFields = $this->clientField;
        // get the phone area code and marge it to ph no
        $ph_area_code = ($bidder ['area-code'] != '') ? '(' . $bidder ['area-code'] . ')' : '';
        $ph_nbr = $bidder ['phone-nbr'];
        $bidder['phone-office'] = $this->ph_field_clean_text($ph_area_code . $ph_nbr);

        // get the phone area code and marge it to fax no
        $fax_area_code = ($bidder ['fax-area-code'] != '') ? '(' . $bidder ['fax-area-code'] . ')' : '';
        $fax_nbr = $bidder ['fax-nbr'];
        $bidder['phone-fax'] = $this->ph_field_clean_text($fax_area_code . $fax_nbr);

        //check for existing client
        $existing_client_id = checkExistingClientForXMLImport(
            $bidder ['firm-name'],
            $bidder['phone-office'],
            $bidder['phone-fax'],
            $bidder ['email-id']
        );

        if (!empty ($existing_client_id)) {

            $client_id = $existing_client_id;
            $newAccountRecord = false;
            $localClient = $this->getClient($client_id, $this->db);
        } else {
            $client_id = create_guid();
            $newAccountRecord = true;
        }

        if ($newAccountRecord == true) {
            $insertSqlColumns = "INSERT INTO accounts ( 
		        `id`,
		        `date_entered`,
		        `date_modified`,
		        `modified_user_id`,
		        `created_by`,
		        `team_id`,
		        `team_set_id`,
		        `lead_source`,
		        `visibility`,
		        `dodge_id`
			";
            $insertSqlValues = " VALUES(
			    '" . $client_id . "',  
	            UTC_TIMESTAMP(),
	            UTC_TIMESTAMP(),
	            '" . $this->current_user->id . "',       
                '" . $this->current_user->id . "',
                '1',
                '1',
                'dodge',
                '0',
                '" . $dodge_id . "'
			";
        } else {
            $updateSQL = "UPDATE accounts SET 
		        `date_modified` = UTC_TIMESTAMP(), 
		        `dodge_id` = '" . $dodge_id . "'  
		    ";
        }

        foreach ($clientFields as $dbKey => $xmlKey) {
            $dbValue = htmlspecialchars_decode($bidder[$xmlKey], ENT_QUOTES);
            $dbValue = addslashes($dbValue);

            if ($dbKey == 'county_id') {
                $countyName = trim(str_ireplace(" COUNTY", "", $dbValue));
                if (!empty($bidder['c-fips-county-id'])) {
                    $dbValue = $this->getCounty(array('fips' => $bidder['c-fips-county-id'], 'name' => $countyName),
                        $bidder['c-state-id'], 1);
                } else {
                    $dbValue = $this->getCounty(array('name' => $countyName, $bidder['c-state-id']));
                }
            }

            if (!empty($existing_client_id)) {
                $field_type = '';

                //flag for locally modified
                $bClientLoacallyModified = $localClient['is_modified'];
                //if this client is linked with bluebook
                //then only balnk fileds will be updated
                if (trim($localClient['mi_account_id']) != '') {
                    $bClientLoacallyModified = true;
                }
                //if it is localally modifed then update only blank fields
                if ($bClientLoacallyModified && !empty($localClient[$dbKey])) {
                    continue;
                }

                if ($dbKey == 'billing_address_state') {
                    $field_type = 'enum';
                }
                insertChangeLog($this->db, 'accounts', $existing_client_id, $localClient[$dbKey], $dbValue, $dbKey,
                    $field_type, $this->current_user->id);
                $updateSQL .= ", `" . $dbKey . "` = '" . $dbValue . "'";

            } else {
                $insertSqlColumns .= ", `" . $dbKey . "`";
                $insertSqlValues .= ", '" . $dbValue . "'";
            }
        }

        if (!empty ($existing_client_id)) {
            $sql = $updateSQL . " WHERE `id`= '" . $existing_client_id . "'";
        } else {
            $sql = $insertSqlColumns . " )" . $insertSqlValues . " )";
        }

        $emailIds = array_filter(explode(";", strtolower($bidder ['email-id'])));
        if (count($emailIds) > 0) {
            $this->insertUpdateEmailAddress('Accounts', $client_id, $emailIds);
        }

        $result = $this->db->query($sql);

        //save import info
        ImportFile::markRowAsImported($newAccountRecord);
        if ($newAccountRecord) {
            ImportFile::writeRowToLastImport($this->import_module, 'Account', $client_id);
        }

        return $client_id;
    }

    /**
     * clean special characters from phone string
     * @param: string phone no
     * @return: string phone no
     */
    function ph_field_clean_text($text)
    {
        $code_entities_match = array(
            '&quot;',
            '&quot; ',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '+',
            '{',
            '}',
            ':',
            '"',
            '<',
            '>',
            '?',
            '[',
            ']',
            '\\',
            ';',
            "'",
            "' ",
            ',',
            '*',
            '+',
            '~',
            '`',
            '=',
            ' ',
            '-'
        );
        $code_entities_replace = array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '-'
        );
        $text = str_replace($code_entities_match, $code_entities_replace, $text);
        return $text;
    }

    /**
     * Get Client Information
     * @param string client id
     * @param objejct db
     * @return array cleint
     */
    function getClient($client_id, $db)
    {
        $sql = "SELECT accounts.*,ea.email_address as email1 FROM accounts
				LEFT JOIN email_addr_bean_rel eabr ON eabr.bean_id = accounts.id AND eabr.deleted = 0 AND eabr.primary_address = 1
				LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0
				WHERE accounts.id = '" . addslashes($client_id) . "' AND accounts.deleted = 0";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);
        return $result;
    }

    /**
     * Insert Email Address
     *
     * @param
     *            string - $module - Module Name
     * @param
     *            char - $bean_id - Id of the client / contact
     * @param
     *            email id - $email_address - Email Address
     * @param
     *            boolean - $is_new - Parent Insert or Update
     */

    function insertUpdateEmailAddress($module, $bean_id, $email_addresses)
    {
        //get Existing Email Addresses of related bean
        $email_addresses = array_unique($email_addresses);
        $sql = "SELECT ea.email_address
				FROM email_addresses ea
        		INNER JOIN email_addr_bean_rel eabr ON eabr.email_address_id = ea.id AND eabr.bean_id='" . $bean_id . "' AND eabr.bean_module='" . $module . "' AND eabr.deleted = 0
				WHERE ea.deleted = 0";
        $query = $this->db->query($sql);
        $emails_from_db = array();
        while ($result = $this->db->fetchByAssoc($query)) {
            $emails_from_db[] = $result['email_address'];
        }

        $insertSQL = "INSERT INTO email_addresses (id, email_address, email_address_caps, invalid_email, opt_out, date_created, date_modified, deleted) VALUES";
        $insertBeanRel = "INSERT INTO email_addr_bean_rel (id, email_address_id, bean_id, bean_module, reply_to_address, date_created, date_modified, deleted ) VALUES";

        $i = 0;

        foreach ($email_addresses as $email_address) {

            if (!in_array($email_address, $emails_from_db)) {
                //Insert Email Address
                $email_address_id = create_guid();
                $email_addr_bean_rel_id = create_guid();
                if ($i > 0) {
                    $insertSQL .= ", ";
                    $insertBeanRel .= ", ";
                }
                $insertSQL .= " ('" . $email_address_id . "', '" . $email_address . "', '" . strtoupper($email_address) . "', 0, 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(), 0) ";
                $insertBeanRel .= " ('" . $email_addr_bean_rel_id . "', '" . $email_address_id . "','" . $bean_id . "', '" . $module . "', 0, UTC_TIMESTAMP(), UTC_TIMESTAMP(),0) ";
                $i++;
            }
        }

        if ($i > 0) {
            $this->db->query($insertSQL);
            $this->db->query($insertBeanRel);
        }

        foreach ($emails_from_db as $db_email) {
            if (!in_array($db_email, $email_addresses)) {
                //Query for set flag deleted = 0
                $sql = "UPDATE email_addr_bean_rel eabr, email_addresses ea SET eabr.deleted = 1, ea.deleted=1
						WHERE ea.email_address='" . $db_email . "' AND eabr.bean_id = '" . $bean_id . "' AND eabr.bean_module='" . $module . "' AND eabr.deleted = 0 AND ea.id = eabr.email_address_id";
                $this->db->query($sql);
            }
        }

        //Reset Primary Email
        if (count($email_addresses) > 0) {
            $primary_sql = "SELECT id FROM email_addr_bean_rel WHERE bean_id='" . $bean_id . "' AND bean_module='" . $module . "' AND deleted=0 ORDER BY date_created asc LIMIT 0,1";
            $query = $this->db->query($primary_sql);
            $result = $this->db->fetchByAssoc($query);
            if (!empty($result['id'])) {
                $update_primary_sql = "UPDATE email_addr_bean_rel SET primary_address=1 WHERE id='" . $result['id'] . "'";
                $this->db->query($update_primary_sql);
            }
        }
    }

    /**
     * @function: insertClientContact
     * @param: array bidder.
     * @param: string client id
     * @param: string dodge uinque id
     * @param: object importfile
     * @return: string contact id
     */
    function insertClientContact($bidder, $client_id, $dodge_id)
    {

        $contactFields = $this->contactField;
        // get the phone area code and marge it to ph no
        $ph_area_code = ($bidder ['area-code'] != '') ? '(' . $bidder ['area-code'] . ')' : '';
        $ph_nbr = $bidder ['phone-nbr'];
        $bidder['phone-work'] = $this->ph_field_clean_text($ph_area_code . $ph_nbr);

        // get the phone area code and marge it to fax no
        $fax_area_code = ($bidder ['fax-area-code'] != '') ? '(' . $bidder ['fax-area-code'] . ')' : '';
        $fax_nbr = $bidder ['fax-nbr'];
        $bidder['phone-fax'] = $this->ph_field_clean_text($fax_area_code . $fax_nbr);

        //check for the existing client contact
        $existing_client_contact_id = checkExistingClientContactForXMLImport(
            $bidder ['contact-name'],
            $bidder['phone-work'],
            $bidder['phone-fax'],
            $bidder ['email-id']
        );
        $emailIds = array_filter(explode(";", strtolower($bidder ['email-id'])));
        $email = addslashes($emailIds [0]);

        list($bidder['first_name'], $bidder['last_name']) = $this->splitName($bidder ['contact-name']);

        if (!empty ($existing_client_contact_id)) {
            $contact_id = $existing_client_contact_id;
            $newContactRecord = false;
            $contactRes = $this->getClientContact($contact_id, $this->db);
            $updateSQL = "UPDATE contacts SET
		        `date_modified` = UTC_TIMESTAMP(),
		        `dodge_id` = '" . $dodge_id . "'
		    ";
        } else {
            $contact_id = create_guid();
            $newContactRecord = true;

            $insertSqlColumns = "INSERT INTO contacts (
		        `id`,
		        `date_entered`,
		        `date_modified`,
		        `modified_user_id`,
		        `created_by`,
		        `team_id`,
		        `team_set_id`,
		        `lead_source`,
		        `visibility`,
		        `dodge_id`
			";
            $insertSqlValues = " VALUES(
			    '" . $contact_id . "',
	            UTC_TIMESTAMP(),
	            UTC_TIMESTAMP(),
	            '" . $this->current_user->id . "',
                '" . $this->current_user->id . "',
                '1',
                '1',
                'dodge',
                '0',
                '" . $dodge_id . "'
			";
        }

        foreach ($contactFields as $dbKey => $xmlKey) {
            $dbValue = htmlspecialchars_decode($bidder[$xmlKey], ENT_QUOTES);
            $dbValue = addslashes($dbValue);
            if (!empty($existing_client_contact_id)) {

                //check for locally modified
                $bContactLocallyModified = $contactRes['is_modified'];

                //if this client Contact is linked with bluebook
                //then only balnk fileds will be updated
                if (trim($contactRes['mi_contact_id']) != '') {
                    $bContactLocallyModified = true;
                }

                //if it is localally modifed then update only blank fields
                if ($bContactLocallyModified && !empty($contactRes[$dbKey])) {
                    continue;
                }
                insertChangeLog($this->db, 'contacts', $existing_client_contact_id, $contactRes[$dbKey], $dbValue,
                    $dbKey, '', $this->current_user->id);
                $updateSQL .= ", `" . $dbKey . "` = '" . $dbValue . "'";
            } else {
                $insertSqlColumns .= ", `" . $dbKey . "`";
                $insertSqlValues .= ", '" . $dbValue . "'";
            }
        }

        if (!empty ($existing_client_contact_id)) {
            if ($contactRes['email1'] != $email) {
                insertChangeLog($this->db, 'contacts', $existing_client_contact_id, $contactRes['email1'], $email,
                    'email1', 'varchar', $this->current_user->id);
            }
            $sql = $updateSQL . " WHERE `id`= '" . $existing_client_contact_id . "'";
        } else {
            $sql = $insertSqlColumns . " )" . $insertSqlValues . " )";
        }

        // Insert or Update email address to client contact.
        if (count($emailIds) > 0) {
            $this->insertUpdateEmailAddress('Contacts', $contact_id, $emailIds);
        }

        $this->db->query($sql);

        // Create Relationship between Client and Client Contact
        if (!empty ($client_id)) {
            $this->insertAccountContactRel($contact_id, $client_id);
        }

        //save import info
        ImportFile::markRowAsImported($newContactRecord);
        if ($newContactRecord) {
            ImportFile::writeRowToLastImport($this->import_module, 'Contact', $newContactRecord);
        }
        /********************************************************************/
        return $contact_id;
    }

    /**
     * split full name to first name and last name
     * @param unknown $name
     * @return array first name and last name
     */
    function splitName($name)
    {

        $names = explode(' ', $name);
        $firstname = '';
        if (count($names) > 1) {
            $firstname = $names[0];
            unset($names[0]);
            $lastname = implode(' ', $names);
        } else {
            $lastname = $name;
        }
        return array($firstname, $lastname);
    }

    /**
     * Get Client Contact Information
     * @param string contact id
     * @param objejct db
     * @return array cleint contact
     */
    function getClientContact($contact_id, $db)
    {
        $sql = "SELECT contacts.*,ea.email_address as email1 FROM contacts
		LEFT JOIN email_addr_bean_rel eabr ON eabr.bean_id = contacts.id AND eabr.deleted = 0 AND eabr.primary_address = 1
		LEFT JOIN email_addresses ea ON ea.id = eabr.email_address_id AND ea.deleted = 0
		WHERE contacts.id = '" . addslashes($contact_id) . "' AND contacts.deleted = 0";
        $query = $db->query($sql);
        $result = $db->fetchByAssoc($query);
        return $result;
    }

    /**
     * Insert Account Contact Relationship
     */
    function insertAccountContactRel($ci_contact_id, $ci_account_id)
    {
        if (!empty ($ci_account_id) && !empty ($ci_contact_id)) {
            $sql = "SELECT id FROM accounts_contacts WHERE contact_id = '" . $ci_contact_id . "' AND account_id = '" . $ci_account_id . "' AND deleted = 0";
            $query = $this->db->query($sql);
            $result = $this->db->fetchByAssoc($query);
            if (empty ($result ['id'])) {
                $insertRel = "INSERT INTO `accounts_contacts` (`id`,`contact_id`,`account_id`,`date_modified`,`deleted`)
						VALUES(UUID(),'" . $ci_contact_id . "','" . $ci_account_id . "',UTC_TIMESTAMP(),0)";
                $this->db->query($insertRel);
            }
        }
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
    function insertBidderList($lead_id, $client_id, $contact_id, $bidder)
    {

        // get the phone area code and marge it to ph no
        $ph_area_code = ($bidder ['area-code'] != '') ? '(' . $bidder ['area-code'] . ')' : '';
        $ph_nbr = $bidder ['phone-nbr'];
        $bidder['phone-work'] = $this->ph_field_clean_text($ph_area_code . $ph_nbr);

        // get the phone area code and marge it to fax no
        $fax_area_code = ($bidder ['fax-area-code'] != '') ? '(' . $bidder ['fax-area-code'] . ')' : '';
        $fax_nbr = $bidder ['fax-nbr'];
        $bidder['phone-fax'] = $this->ph_field_clean_text($fax_area_code . $fax_nbr);

        // Store data into variables to save Bidders List
        if (empty($bidder ['s-contact-role'])) {
            $bidder['s-contact-role'] = 'Sub Contractor';
        }

        $bidder['s-contact-role'] = htmlspecialchars_decode($bidder['s-contact-role'], ENT_QUOTES);
        $bidder['s-contact-role'] = addslashes($bidder['s-contact-role']);
        //check for existing biider list
        $sql = "SELECT id FROM oss_leadclientdetail WHERE deleted = '0'";
        $sql .= !empty($lead_id) ? " AND lead_id='" . $lead_id . "'" : "";
        $sql .= !empty($client_id) ? " AND account_id='" . $client_id . "'" : "";
        $sql .= !empty($contact_id) ? " AND contact_id='" . $contact_id . "'" : "";
        $query = $this->db->query($sql);
        $existing_lead_client_id = $this->db->fetchByAssoc($query);

        $assignedUserId = '1';
        if (!empty($current_user->id)) {
            $assignedUserId = $current_user->id;
        }

        if (!empty ($existing_lead_client_id['id'])) {
            $leadClientId = $existing_lead_client_id['id'];
            $bidderSQL = "UPDATE oss_leadclientdetail SET  
			    `date_modified` = UTC_TIMESTAMP(),
			    `contact_email` = '" . $bidder['email-id'] . "', 
		        `contact_phone_no` = '" . $bidder['phone-work'] . "', 
		        `role` = '" . $bidder['s-contact-role'] . "', 
		        `contact_fax` = '" . $bidder['phone-fax'] . "', 
		        `contact_id` = '" . $contact_id . "', 
		        `lead_id` = '" . $lead_id . "', 
		        `account_id` = '" . $client_id . "' 
                WHERE id='" . $leadClientId . "'                		                
			";
        } else {
            $leadClientId = create_guid();
            $bidderSQL = "INSERT INTO `oss_leadclientdetail` (
			    `id`,
			    `date_entered`, 
		        `date_modified`,
		        `modified_user_id`, 
		        `created_by`, 
		        `team_id`, 
		        `team_set_id`, 
		        `assigned_user_id`, 
		        `contact_email`, 
		        `contact_phone_no`, 
		        `role`, 
		        `contact_fax`, 
		        `lead_source`, 
		        `contact_id`, 
		        `lead_id`, 
		        `account_id`
		    ) VALUES (
			    '" . $leadClientId . "', 
	            UTC_TIMESTAMP(), 
	            UTC_TIMESTAMP(), 
	            '" . $assignedUserId . "', 
                '" . $assignedUserId . "', 
                '1', 
                '1', 
                '" . $assignedUserId . "', 
                '" . $bidder['email-id'] . "', 
                '" . $bidder['phone-work'] . "', 
                '" . $bidder['s-contact-role'] . "', 
                '" . $bidder['phone-fax'] . "', 
                'dodge', 
                '" . $contact_id . "', 
                '" . $lead_id . "', 
                '" . $client_id . "'
			)
			";
        }
        $this->db->query($bidderSQL);
        return $leadClientId;
    }

    /**
     * Get Parent lead id from client instance
     * @param: string lead id
     * @return: string parent lead id
     */
    function getParentLeadId($lead_id)
    {
        if (!empty ($lead_id)) {
            $sql = "SELECT id,parent_lead_id FROM leads WHERE id = '" . $lead_id . "' AND deleted = 0";
            $query = $this->db->query($sql);
            $result = $this->db->fetchByAssoc($query);
            if (!empty ($result ['parent_lead_id'])) {
                return $result ['parent_lead_id'];
            } else {
                return $result ['id'];
            }
        }
    }

    /**
     * update new - total bidder count for project lead
     * @param string project lead id
     */
    function updateNewTotalBidderCountBBH($stProjectLeadId)
    {
        //check look up table for existance
        $arChkData = $this->checkLookupLeadExistsBBH($stProjectLeadId);

        // SQL to get the count of new and total bidders for this project lead/parent project lead
        $stGetBidderCount = "SELECT COALESCE(leads.parent_lead_id,leads.id) ldgrpid,
				sum(if(bidders.is_viewed = 0,1,0)) newbidders,count(bidders.id) total_bidders
				FROM leads LEFT JOIN oss_leadclientdetail bidders on leads.id = lead_id 
				AND bidders.deleted =0
				WHERE COALESCE(leads.parent_lead_id,leads.id) ='" . $stProjectLeadId . "' 
				AND leads.deleted=0 GROUP BY coalesce(parent_lead_id,leads.id)";
        $rsResult = $this->db->query($stGetBidderCount);
        $arCountData = $this->db->fetchByAssoc($rsResult);

        if (isset ($arChkData ['id']) && trim($arChkData ['id']) != '') {
            //if project lead already in look up table update record
            $stReplace = "UPDATE project_lead_lookup  
					SET new_bidder ='" . $arCountData ['newbidders'] . "',
					total_bidder = '" . $arCountData ['total_bidders'] . "'
					WHERE project_lead_id = '" . $arCountData ['ldgrpid'] . "'	";
            $this->db->query($stReplace);
        } else {
            //if project lead not in look up table insert record
            $stReplace = "INSERT INTO 
					project_lead_lookup(id,project_lead_id,new_bidder,total_bidder) 
					VALUES (UUID(),'" . $arCountData ['ldgrpid'] . "',
							'" . $arCountData ['newbidders'] . "',
							'" . $arCountData ['total_bidders'] . "')";
            $this->db->query($stReplace);
        }
    }

    /**
     * check project lead existance on lookup table
     * @param string $stProjectLeadId
     * @return array lookup data
     */
    function checkLookupLeadExistsBBH($stProjectLeadId)
    {
        $stCheckSQL = 'SELECT project_lead_id id FROM  project_lead_lookup WHERE project_lead_id ="' . $stProjectLeadId . '"';
        $rsChkResult = $this->db->query($stCheckSQL);
        $arChkData = $this->db->fetchByAssoc($rsChkResult);
        return $arChkData;
    }

    /**
     * update lead version
     * @param string lead id
     */
    function updateLeadVersionBidDueDateBBH($stParentLeadId)
    {
        //check look up table for existance
        $arChkData = $this->checkLookupLeadExistsBBH($stParentLeadId);

        //check converted lead count
        $stLeadVerBidDueSQL = 'SELECT count(leads.id) countt, 
				coalesce(parent_lead_id,leads.id) leadid, min(bids_due) bids_due_grops,
				GROUP_CONCAT( CONCAT(bids_due," {} ",bid_due_timezone ) 
				ORDER BY bids_due ASC SEPARATOR "$$") bids_due_grops_timezone
				FROM leads WHERE leads.deleted =0
				AND coalesce(parent_lead_id,leads.id)="' . $stParentLeadId . '"
				GROUP BY coalesce(parent_lead_id,leads.id)';
        $rsResult = $this->db->query($stLeadVerBidDueSQL);
        $arData = $this->db->fetchByAssoc($rsResult);

        if (isset ($arData ['bids_due_grops_timezone'])) {
            // get the timezone of min date
            $arTmp = explode("$$", $arData ['bids_due_grops_timezone']);
            $arTmpMinDateZone = explode(' {} ', $arTmp [0]);
            $arData ['bids_due_grops_timezone'] = $arTmpMinDateZone [1];
        }

        if (isset ($arChkData ['id']) && trim($arChkData ['id']) == $stParentLeadId) {
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
        $this->db->query($stReplace);
    }

    function sendImportCompleteEmail()
    {
        $this->iTotalLeadsPulledCount = $this->iTotalInsertedLeads + $this->iTotalUpdatedLeads;
        $GLOBALS['log']->fatal('process complete , Total Leads:' . $this->iTotalLeadsPulledCount);
        require_once('include/SugarPHPMailer.php');
        //send email to Matt and rommel
        $stSubject = '[127871] Project Leads Pulled from XML on ' . date('m/d/Y');
        $obDateTime = new SugarDateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
        $obDateTime->setTimezone(new DateTimeZone('US/Eastern'));
        $stDateTime = $obDateTime->format('m/d/Y H:i:s');
        $stEmailHTML = '<br/>The import leads from XML process is completed, below are the details :'
            . '<br/><b>Started on </b>: ' . $this->dtStartDateTime . ' (EST).'
            . '<br/><b>Completed on </b>:' . $stDateTime . ' (EST).'
            . '<br/><b>Leads Inserted </b>:' . $this->iTotalInsertedLeads
            . '<br/><b>Leads Updated </b>:' . $this->iTotalUpdatedLeads
            . '<br/><b>Total Number of leads pulled </b>: ' . $this->iTotalLeadsPulledCount . ' <br>'
            . '<br/> ';
        $arEmailIds = array('mohit@osscube.com');
        //$arEmailIds = array('mmoyers@mail.thebluebook.com','lyee@mail.thebluebook.com');
        $GLOBALS['log']->fatal(' Send Email to PP:', $stEmailHTML);
        $emailObj = new Email ();
        $defaults = $emailObj->getSystemDefaultEmail();
        $mail = new SugarPHPMailer ();
        $mail->setMailerForSystem();

        $mail->From = $defaults ['email'];
        $mail->FromName = $defaults ['name'];
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->Subject = from_html($stSubject);

        $mail->Body_html = from_html($stEmailHTML);
        $mail->Body = from_html($stEmailHTML);
        $mail->IsHTML(true);


        $mail->prepForOutbound();
        $hasRecipients = false;

        //this email should go to a common email
        $mail->AddBCC('mohit@osscube.com');
        $mail->AddBCC('gaurav.tyagi@osscube.com');
        $mail->AddBCC('rahul.bhandari@osscube.com');

        foreach ($arEmailIds as $itemail) {
            $mail->AddAddress($itemail);
        }

        $hasRecipients = true;

        $success = false;
        if ($hasRecipients) {
            $success = $mail->Send();

        }
    }

    /**
     * match type dropdown list
     * @param string driopdown value
     * @return string driopdown value
     */
    function matchTypeDom($stMatchValue)
    {
        global $app_list_strings;
        $arAllType = $app_list_strings['project_type_dom'];
        foreach ($arAllType as $stKey => $stValue) {
            $arTmp[str_replace(" ", '', strtolower($stKey))] = $stKey;
        }
        $tmpKey = str_replace(" ", '', strtolower($stMatchValue));
        return (isset($arTmp[$tmpKey])) ? $arTmp[$tmpKey] : '';

    }

    /**
     * clean special characters from a string
     * @param: string text
     * @return: string clean text
     */
    function clean_text($text)
    {
        $code_entities_match = array(
            '&quot;',
            '&quot; ',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '+',
            '{',
            '}',
            ':',
            '"',
            '<',
            '>',
            '?',
            '[',
            ']',
            '\\',
            ';',
            "'",
            "' ",
            ',',
            '*',
            '+',
            '~',
            '`',
            '='
        );
        $code_entities_replace = array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );
        $text = str_replace($code_entities_match, $code_entities_replace, $text);
        return $text;
    }

    function addDropDownToFile($fileName = '', $value = '')
    {
        if ($fileName != '' && $value != '') {
            $process_path = 'upload/process/' . $fileName . "_" . $this->current_user->id . "_" . $this->fileNameTime;
            $fp1 = fopen($process_path, "a");
            $value .= "||\n";
            fwrite($fp1, $value);
            fclose($fp1);
        }
    }

    //add non existing drop down value to temprary file.

    function addDropDownFromFile()
    {
        global $app_list_strings;
        $process_path = 'upload/process/';
        $seperator = "||";

        //update lead owner drop down
        $ownerFileName = $process_path . "owner_dom_" . $this->current_user->id . "_" . $this->fileNameTime;
        if (file_exists($ownerFileName)) {
            $fp1 = fopen($ownerFileName, "r");
            $ownerDataStr = file_get_contents($ownerFileName);
            $ownerData = array_unique(explode($seperator, $ownerDataStr));
            $arrCompare = $app_list_strings['owner_dom'];
            foreach ($ownerData as $list_value) {
                $leadOwnerValue = explode(",", $list_value);
                $leadOwnerValue = str_replace(array("'", '[', '"'), array('', '', ''), trim($leadOwnerValue[0]));
                if (!array_key_exists($leadOwnerValue, $arrCompare) && !empty($leadOwnerValue)) {
                    $this->editDropdownList("owner_dom", $list_value);
                }
            }
            fclose($fp1);
            unlink($ownerFileName);
        }

        //update lead structure drop down
        $leadStructreFileName = $process_path . "structure_non_building_" . $this->current_user->id . "_" . $this->fileNameTime;
        if (file_exists($leadStructreFileName)) {
            $fp1 = fopen($leadStructreFileName, "r");
            $structureDataStr = file_get_contents($leadStructreFileName);
            $structureData = array_unique(explode($seperator, $structureDataStr));
            foreach ($structureData as $list_value) {
                $leadStructreValue = explode(",", $list_value);
                $leadStructreValue = str_replace(array("'", ']', '"'), array('', '', ''), trim($leadStructreValue[1]));
                if (!empty($leadStructreValue)) {
                    $leadStructre = $this->matchStructureDom($leadStructreValue);
                    if (empty($leadStructre)) {
                        $this->editDropdownList("structure_non_building", $list_value);
                    }
                }
            }
            fclose($fp1);
            unlink($leadStructreFileName);
        }

        //update lead type drop down
        $leadTypeFileName = $process_path . "project_type_dom_" . $this->current_user->id . "_" . $this->fileNameTime;
        if (file_exists($leadTypeFileName)) {
            $fp1 = fopen($leadTypeFileName, "r");
            $leadTypeDataStr = file_get_contents($leadTypeFileName);
            $leadTypeData = array_filter(array_unique(explode($seperator, $leadTypeDataStr)));
            $arrCompare = $app_list_strings['project_type_dom'];
            foreach ($leadTypeData as $list_value) {
                $leadTypeValue = explode(",", $list_value);
                $leadTypeValue = str_replace(array("'", '[', '"'), array('', '', ''), trim($leadTypeValue[0]));
                if (!array_key_exists($leadTypeValue, $arrCompare) && !empty($leadTypeValue)) {
                    $this->editDropdownList("project_type_dom", $list_value);
                }
            }
            fclose($fp1);
            unlink($leadTypeFileName);
        }

        //update lead status drop down
        $projectStatusFileName = $process_path . "project_status_dom_" . $this->current_user->id . "_" . $this->fileNameTime;
        if (file_exists($projectStatusFileName)) {
            $fp1 = fopen($projectStatusFileName, "r");
            $statusDataStr = file_get_contents($projectStatusFileName);
            $statusData = array_unique(explode($seperator, $statusDataStr));
            $arrCompare = $app_list_strings['project_status_dom'];
            foreach ($statusData as $list_value) {
                $leadStatusValue = explode(",", $list_value);
                $leadStatusValue = str_replace(array("'", '[', '"'), array('', '', ''), trim($leadStatusValue[0]));
                if (!array_key_exists($leadStatusValue, $arrCompare) && !empty($leadStatusValue)) {
                    $this->editDropdownList("project_status_dom", $list_value);
                }
            }
            fclose($fp1);
            unlink($projectStatusFileName);
        }
    }

    //add non existing drop down value from temprary file to drop down.

    /**
     * add dynamic dropdown
     * @param string $dropdwon_name
     * @param array $new_list_value
     */
    function editDropdownList($dropdwon_name, $new_list_value)
    {
        require_once 'custom/include/dynamic_dropdown.php';
        editDropdownList($dropdwon_name, $new_list_value);

        return;
        /*
         * Buggy code : replicates the dropdown value
        global $app_list_strings;

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
     * match structure dropdown list
     * @param string driopdown value
     * @return string driopdown value
     */
    function matchStructureDom($stMatchValue)
    {
        global $app_list_strings;
        $arAllStructere = array_merge($app_list_strings['structure_residential'],
            $app_list_strings['structure_non_residential'], $app_list_strings['structure_non_building']);

        foreach ($arAllStructere as $stKey => $stValue) {
            $arTmp[str_replace(" ", '', strtolower($stKey))] = $stKey;
        }
        $tmpKey = str_replace(" ", '', strtolower($stMatchValue));

        return (isset($arTmp[$tmpKey])) ? $arTmp[$tmpKey] : '';

    }
}

?>
