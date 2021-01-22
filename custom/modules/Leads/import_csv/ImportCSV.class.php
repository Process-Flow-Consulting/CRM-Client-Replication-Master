<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
* By installing or using this file, You have unconditionally agreed to the
* terms and conditions of the License, and You may not use this file except in
* compliance with the License.  Under the terms of the license, You shall not,
* among other things: 1) sublicense, resell, rent, lease, redistribute, assign
* or otherwise transfer Your rights to the Software, and 2) use the Software
* for timesharing or service bureau purposes such as hosting the Software for
* commercial gain and/or for the benefit of a third party.  Use of the Software
* may be subject to applicable fees and any use of the Software without first
* paying applicable fees is strictly prohibited.  You do not have the right to
* remove SugarCRM copyrights from the source code or user interface.
*
* All copies of the Covered Code must include on each user interface screen:
*  (i) the "Powered by SugarCRM" logo and
*  (ii) the SugarCRM copyright notice
* in the same form as they appear in the distribution.  See full license for
* requirements.
*
* Your Warranty, Limitations of liability and Indemnity are expressly stated
* in the License.  Please refer to the License for the specific language
* governing these rights and limitations under the License.  Portions created
* by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

/*********************************************************************************
* Description: View Import CSV Step2
********************************************************************************/
require_once('include/MVC/View/SugarView.php');
require_once('modules/Import/sources/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/ImportCacheFiles.php');
require_once('modules/Import/ImportFieldSanitize.php');
require_once('modules/Import/ImportDuplicateCheck.php');
require_once('modules/Import/CsvAutoDetect.php');
require_once('include/upload_file.php');
require_once ('custom/include/common_functions.php');
require_once 'custom/include/OssTimeDate.php';

class ImportCSV
{
    private $db;
	private $sugar_config;
	private $current_user;
	private $data;
	private $import_module;
	private $importObj;
	private $fname;
	private $list_value; 
    
    function __construct(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db, $timedate, $sugar_config;
        $this->db = $db;
        $list_value = array();
        $this->sugar_config = $sugar_config;
        $this->current_user = $current_user;
        $this->import_module = 'Leads';
        $this->is_bidder_modified = false;
        $this->bidder_row_count = 0;
        $this->_errorCount = 0;
		$this->_createdCount = 0;
		$this->_updateCount = 0;
        $this->ifs = $this->getFieldSanitizer();
    }
    
    function insertData($uploadLeadFileName, $uploadBidderFileName, $importSourceMap){
        
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db, $timedate, $sugar_config;
        
        // Increase the max_execution_time since this step can take awhile
        ini_set("max_execution_time", max($sugar_config['import_max_execution_time'],3600));
        
        $GLOBALS['log']->fatal($uploadLeadFileName);
        $GLOBALS['log']->fatal($uploadBidderFileName);
        $GLOBALS['log']->fatal($importSourceMap);
        
        // stop the tracker
        TrackerManager::getInstance()->pause();
        
        //get Mapping Details
        $mapping = new ImportMap();
        $mapping->retrieve($importSourceMap, false);
        $importSource = $mapping->source;
        
        $this->_delimiter = $delimiter = $mapping->delimiter;
        $this->_enclosure = $enclosure = $mapping->enclosure;
        $hasHeader = $mapping->has_header;
        $importSourceName  = $mapping->name;
        $mapping_content = $mapping->content;
        $mapping_content = explode('|', $mapping_content);
        $lead_mapping = $mapping_content[0];
        $bidder_mapping = $mapping_content[1];
        
        $lead_field_mapping = array();
        if(!empty($lead_mapping)){
            $mapping->content = $lead_mapping;
            $lead_field_mapping = $mapping->getMapping();
        }
        
        $bidder_field_mapping = array();
        if(!empty($bidder_mapping)){
            $mapping->content = $bidder_mapping;
            $bidder_field_mapping = $mapping->getMapping();
        }
        
        // Clear out this user's last import
        $seedUsersLastImport = new UsersLastImport();
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();
        
        if(!empty($uploadLeadFileName) ){
            $this->fname = $uploadLeadFileName;
        }else{
            $this->fname = $uploadBidderFileName;
        }
        $importObj = new ImportFile( $this->fname .'_INFO' );
        
        // turn on auto-detection of line endings
        ini_set('auto_detect_line_endings', '1');
        /*************************Start Lead Importing*****************************/
        // Now parse the file and look for errors
        if(!empty($uploadLeadFileName)){
            $importLeadFile = new ImportFile( $uploadLeadFileName, $delimiter, html_entity_decode($enclosure,ENT_QUOTES), false);
            $isLeadHeaderAvailable = $importLeadFile->hasHeaderRow();
            if ($importSourceName == 'ISQFT') {
                $isLeadHeaderAvailable = $importLeadFile->setHeaderRow(1);
            }
            $fpLead = sugar_fopen($uploadLeadFileName, 'r');
            $leadImportColumns = array();
            if($hasHeader){
                
                if ($importSourceName == 'ISQFT') {
                    while (($sourceHeaderLead = fgetcsv($fpLead, 8192, $delimiter, html_entity_decode($enclosure, ENT_QUOTES))) !== FALSE) {
                        $bMatchedCols = 0;            
                        
                        $GLOBALS['log']->fatal($sourceHeaderLead);
                        foreach ($sourceHeaderLead as $key => $mapping) {
                            if (!$isLeadHeaderAvailable && isset($lead_field_mapping[$key])) {
                                $leadImportColumns[$key] = $lead_field_mapping[$key];
                                $bMatchedCols++;
                            } else if (isset($lead_field_mapping[$mapping])) {
                                $leadImportColumns[$key] = $lead_field_mapping[$mapping];
                                $bMatchedCols++;
                            }
                        }
                        if ($bMatchedCols >= 3) {
                            break;
                        }
                    }
                } else {
                    
                    $sourceHeaderLead = fgetcsv($fpLead, 8192, $delimiter, html_entity_decode($enclosure, ENT_QUOTES));
                    foreach ($sourceHeaderLead as $key => $mapping) {
                        if (!$isLeadHeaderAvailable && isset($lead_field_mapping[$key])) {
                            $leadImportColumns[$key] = $lead_field_mapping[$key];
                        } else if (isset($lead_field_mapping[$mapping])) {
                            $leadImportColumns[$key] = $lead_field_mapping[$mapping];
                        }
                    }
                }
                
                $rowCountLead = 0;
                $this->_rowsCount = 0;
                while( ($sourceRowLead = fgetcsv($fpLead, 8192,$delimiter,html_entity_decode($enclosure,ENT_QUOTES))) !== FALSE ){   
                   /*commented by Ashutosh - 13-Feb-2014 do not escape rows
                    *  if($rowCountLead == 0){
                        $rowCountLead++;
                        continue;
                    }*/
                    
                    $this->_rowCountedForErrors = false;
                    $this->_currentRow = $sourceRowLead;
                    $this->_rowsCount =  $rowCountLead + 1;
                    
                    $leads = array();
                    $focus = new Lead();
                    for( $fieldNum = 0; $fieldNum < count($sourceRowLead); $fieldNum++ ){
                        if(!empty($leadImportColumns[$fieldNum])){
                            // use preg_replace instead of str_replace as str_replace may cause extra lines on Windows
                            $sourceRowLead[$fieldNum] = preg_replace("[\r\n|\n|\r]", PHP_EOL, $sourceRowLead[$fieldNum]);
                            $leads[$leadImportColumns[$fieldNum]] = $sourceRowLead[$fieldNum];
                            if( $leadImportColumns[$fieldNum] == 'unique_identifier_id'){
                                $leads[$leadImportColumns[$fieldNum]] = $sourceRowLead[$fieldNum].'-'.$importSourceName;
                            }
                        }
                    }
                    // addtional fields for ISQFT
                    if ($importSourceName == 'ISQFT') {
                    
                        $leads['type'] = 'Other';
                        $leads['lead_source'] = $importSourceName;
                        //$GLOBALS['log']->fatal($leadArray);
                    }
                    $GLOBALS['log']->fatal($leads);
                    //echo '<pre>'; print_r($leads); echo '</pre>';
                    $lead_id = $this->insertProjectLead($leads,$importObj);
                    
                    
                                      
                    $rowCountLead++;
                }
                $this->writeStatus ();
            }
        }
        /*************************End Lead Importing*****************************/
        
        
        /*************************Start Bidders Importing*****************************/
        if(!empty($uploadBidderFileName)) {
            
            $importBidderFile = new ImportFile($uploadBidderFileName, $delimiter, html_entity_decode($enclosure, ENT_QUOTES), false);
            $isBidderHeaderAvailable = $importBidderFile->hasHeaderRow();
            
            if ($importSourceName == 'ISQFT') {
                $isBidderHeaderAvailable = $importBidderFile->setHeaderRow(1);
            }
            $fpBidder = sugar_fopen($uploadBidderFileName, 'r');
            $bidderImportColumns = array ();
            
            if ($hasHeader) {
                
                if ($importSourceName == 'ISQFT') {
                    
                    while (($sourceHeaderBidder = fgetcsv($fpBidder, 8192, $delimiter, html_entity_decode($enclosure, ENT_QUOTES))) !== FALSE) {
                        $bMatchedCols = 0;
                        
                        foreach ($sourceHeaderBidder as $key => $mapping) {
                            
                            if (!$isBidderHeaderAvailable && isset($bidder_field_mapping[$key])) {
                                $bidderImportColumns[$key] = $bidder_field_mapping[$key];
                                $bMatchedCols++;
                            } else if (isset($bidder_field_mapping[$mapping])) {
                                $bidderImportColumns[$key] = $bidder_field_mapping[$mapping];
                                $bMatchedCols++;
                            }
                        }
                        if ($bMatchedCols >= 3) {
                            break;
                        }
                    }
                } else {
                    
                    $sourceHeaderBidder = fgetcsv($fpBidder, 8192, $delimiter, html_entity_decode($enclosure, ENT_QUOTES));
                    foreach ($sourceHeaderBidder as $key => $mapping) {
                        
                        if (!$isBidderHeaderAvailable && isset($bidder_field_mapping[$key])) {
                            $bidderImportColumns[$key] = $bidder_field_mapping[$key];
                        } else if (isset($bidder_field_mapping[$mapping])) {
                            $bidderImportColumns[$key] = $bidder_field_mapping[$mapping];
                        }
                    }
                }
                
                
                //echo '<pre>'; print_r($bidderImportColumns); echo '</pre>';
                $rowCountBidder = 0;
                $this->_rowsCount = 0;
                
                while ( ($sourceRowBidder = fgetcsv($fpBidder, 8192,$delimiter,html_entity_decode($enclosure,ENT_QUOTES))) !== FALSE ){
                    
                    /*commented by Ashutosh - 13-Feb-2014 do not escape rows
                     * if($rowCountBidder == 0){
                        $rowCountBidder++;
                        continue;
                    }*/
                    
                    
                    $accountArray = array();
                    $contactArray = array();
                    $leadArray = array();
                    $bidderArray = array();
                    
                    $this->_rowCountedForErrors = false;
                    $this->_currentRow = $sourceRowBidder;
                    
                    //form array for all the related modules
                    for( $fieldNum = 0; $fieldNum < count($sourceRowBidder); $fieldNum++ ){
                       
                        if(!empty($bidderImportColumns[$fieldNum])){
                            $sourceRowBidder[$fieldNum] = preg_replace("[\r\n|\n|\r]", PHP_EOL, $sourceRowBidder[$fieldNum]);
                            $module_field = strpos($bidderImportColumns[$fieldNum],'_');
                           
                        	$module_name = substr($bidderImportColumns[$fieldNum], 0, $module_field);
                        	$field_name = substr($bidderImportColumns[$fieldNum], $module_field+1);
                            $arrayName = $module_name."Array";    
                            
                        	${$arrayName}[$field_name] = $sourceRowBidder[$fieldNum];
                        	if( $field_name == 'unique_identifier_id'){
                        	   ${$arrayName}[$field_name] = $sourceRowBidder[$fieldNum].'-'.$importSourceName;
                        	}
                        }
                    }
                    
                    //create / update client
                    if( isset($accountArray['name']) && !empty($accountArray['name'])  ){
                        $client_id = $this->insertClient($accountArray, $importObj,$importSourceName);
                    }else{
                        $client_id = '';
                    }
                    //create / update client Conatct
                    if( (isset($contactArray['last_name']) && !empty($contactArray['last_name']) )
                            || (isset($contactArray['name']) && !empty($contactArray['name'])) ){
                        $contact_id = $this->insertClientContact($contactArray, $client_id, $importObj);
                    }else{
    					$contact_id = '';
    				}
                        
                        // addtional fields for ISQFT
                    if ($importSourceName == 'ISQFT') {
                        
                       $leadArray['type'] = 'Other';
                       $leadArray['lead_source'] = $importSourceName;
                       //$GLOBALS['log']->fatal($leadArray);
                    }
    			    
    				//create / update Project Lead
                    $lead_id = $this->insertProjectLead($leadArray,$importObj);
                    
                    if(empty($bidderArray['contact_phone_no']) && !empty($contactArray['phone_work'])){
                       $bidderArray['contact_phone_no'] = $contactArray['phone_work'];
                    }
                    if(empty($bidderArray['contact_fax']) && !empty($contactArray['phone_fax'])){
                        $bidderArray['contact_fax'] = $contactArray['phone_fax'];
                    }
                    if(empty($bidderArray['contact_email']) && !empty($contactArray['email1'])){
                        $bidderArray['contact_email'] = $contactArray['email1'];
                    }
                    
    				//create / update bidders list
    				$this->insertBidderList ( $lead_id, $client_id, $contact_id, $bidderArray, $importObj );
					
					$rowCountBidder++;
                }
                
            }
            
        }
                
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
     * @function: insertProjectLead()
     * @param: array record
     * @param: object importfile.
     * @return: string lead id
     */
    function insertProjectLead($record,$importObj){
    
        global $app_list_strings, $timedate, $db;
    
        $lead = new Lead ();
        $unique_id = trim ( $record ['unique_identifier_id'] );
        $newLeadRecord = true;
        $do_save = true;
        $ignoreSanitize = array('project_status', 'owner', 'type', 'structure','lead_source');
        
        // check if the mi_lead_id alread exist then assign lead to modify
        if(!empty($unique_id)){
            $existing_lead_id = $this->checkExistingRecord( 'leads', 'unique_identifier_id', $unique_id );
        }else{
            $existing_lead_id = '';
        }
        
    
        if (!empty($existing_lead_id)) {
            $lead->id = $existing_lead_id;
            $newLeadRecord = false;
            $lead->retrieve($lead->id);
            $date_entered = $timedate->to_db($lead->date_entered);
        }
    
        if( empty($existing_lead_id)){
            
            foreach ( $record as $key => $value){
                
                $fieldDef = $lead->getFieldDefinition($key);
                $defaultRowValue = '';
                $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']), $lead->module_dir)." (".$fieldDef['name'].")";
                if( !empty($value) )
                {
                    $defaultRowValue = $this->populateDefaultMapValue($key, $value, $fieldDef);
                    if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($value))
                    {
                        require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                        $sugar_field = new SugarFieldTeamset('Teamset');
                        $value = implode(', ',$sugar_field->getTeamsFromRequest($field));
                    }                    
                    if(!in_array($key,$ignoreSanitize)){
                        //handle bids due date with time zone
                        if ($key == 'bids_due' && (stristr($record['unique_identifier_id'],'ISQFT')!== false)) {
                            $arTimeZoneMap = array('CT' => 'Central'
                                                   ,'ET'=> 'Eastern'
                                                ,'MT' => 'Mountain'
                                                ,'PT' => 'Pacific'
                            );
                            $arDateTime = explode(' ',$record['bids_due']);
                            
                            if(in_array($arDateTime[3],array_keys($arTimeZoneMap))){
                                $lead->bid_due_timezone = $arTimeZoneMap[$arDateTime[3]];
                                $record['bid_due_timezone'] = $lead->bid_due_timezone; 
                            } 
                           
                            
                            $record['bids_due'] = str_replace(array('CT','ET','MT','PT'),'', $record['bids_due']);
                            
                            $oss_timedate = new OssTimeDate();                          
                           
                          
                           $date = new DateTime($record['bids_due']);
                           $record['bids_due'] = $date->format($timedate->get_date_time_format());
                                                     
                           $value = $oss_timedate->convertDateForDB($record['bids_due'], $record['bid_due_timezone']);
                            
                        } else {
                            $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $lead, $fieldTranslated);
                        }
                    }
                    if ($value === FALSE) {
                        $do_save = false;
                        continue;
                    }
                    $lead->$key = $value;
                }
            }
            
            if(!empty($lead->lead_source )){
                $arrCompare = $app_list_strings ['lead_source_list'];
                if (! array_key_exists ( $this->clean_text ( $lead->lead_source ), $arrCompare )) {
            
                    $list_value = '["' . $this->clean_text ( $lead->lead_source ) . '","' . $lead->lead_source . '"]';
                    $lead->lead_source = $this->clean_text ( $lead->lead_source );
                    $this->editDropdownList ( "lead_source_list", $list_value );
            
                }
            }
            $lead->lead_source = $this->clean_text ( $lead->lead_source );
            
            if (! empty ( $lead->project_status )) {
                $arrCompare = $app_list_strings ['project_status_dom'];
                if (! array_key_exists ( $this->clean_text ( $lead->project_status ), $arrCompare )) {
                    $list_value = '["' . $this->clean_text ( $lead->project_status ) . '","' . $lead->project_status . '"]';
                    $lead->project_status = $this->clean_text ( $lead->project_status );
                    $this->editDropdownList ( "project_status_dom", $list_value );
                }
            }
            $lead->project_status = $this->clean_text ( $lead->project_status );
            
            if (! empty ( $lead->owner )) {
                $arrCompare = $app_list_strings ['owner_dom'];
                if (! array_key_exists ( $this->clean_text ( $lead->owner ), $arrCompare )) {
                    $list_value = '["' . $this->clean_text ( $lead->owner ) . '","' . $lead->owner . '"]';
                    $lead->owner = $this->clean_text ( $lead->owner );
                    $this->editDropdownList ( "owner_dom", $list_value );
                }
            }
            $lead->owner = $this->clean_text ( $lead->owner );
            
            if(! empty($lead->type) ){
                $arrCompare = $app_list_strings ['project_type_dom'];
                if (! array_key_exists ( $this->clean_text ($lead->type ), $arrCompare )) {
                    $list_value = '["' . $this->clean_text ($lead->type ) . '","' . $lead->type . '"]';
                    $lead->type = $this->clean_text ( $lead->type );
                    $this->editDropdownList ( "project_type_dom", $list_value );
                }
            }
            $lead->type = $this->clean_text ( $lead->type );
            	
            
            if (! empty ( $lead->structure )) {
                $structure = $this->matchStructureDom ( $lead->structure );
                if (empty ( $structure ) && ! empty ( $lead->structure )) {
    				$list_value = '["' . $this->clean_text ( $lead->structure ) . '","' . $lead->structure . '"]';
    				$lead->structure = $this->clean_text ( $lead->structure );
    				$this->editDropdownList ( "structure_non_building", $list_value );
    			}
            }
            
            
            if(empty($lead->project_title)){
                $do_save = false;
                $this->writeError('Empty Prject Title', 'Project Title',$lead->project_title);
            }else{
                $lead->last_name = $lead->project_title;
            }
            
            //create / update leads
            /******************************************************************/
            $lead->populateDefaultValues();
            if ( !isset($lead->assigned_user_id) || $lead->assigned_user_id == '' && $newLeadRecord ) {
                $lead->assigned_user_id = $this->current_user->id;
            }
            if ( !isset($lead->team_id) || $lead->team_id == '' && $newLeadRecord ) {
                $lead->team_id = 1;
            }
            
            if($do_save){
                $lead->save();
                $this->_createdCount++;
                //save import info
                ImportFile::markRowAsImported($newLeadRecord);
                if ( $newLeadRecord)
                    ImportFile::writeRowToLastImport($this->import_module,$lead->object_name,$lead->id);
            }else{
                return '';
            }
            /****************************************************************/
        }
        if(!empty($lead->leads_online_plans)){
        
            $obOnlinePlans = new oss_OnlinePlans();
            $arChekcFields = array('name' => $lead->leads_online_plans);
            $obOnlinePlans->retrieve_by_string_fields($arChekcFields);
        
            //if online plan doesn't exists then add
            if(empty($obOnlinePlans->id) || ($lead->id != $obOnlinePlans->lead_id)){
                $obOnlinePlans->id ='';
                $obOnlinePlans->lead_id = $lead->id;
                $obOnlinePlans->description = $lead->leads_online_plans;
                $obOnlinePlans->save();
            }
             
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
    function insertClient($bidder, $importObj,$stImportSource = '') {

        global $app_list_strings, $timedate, $db, $current_user;
    
        // other then ownner all are bidders
        $client = new Account ();
        $new_client = true;
        $do_save = true;
        $unique_id = trim ( $bidder ['unique_identifier_id'] );
        
        //if source is ISQFT and client already exists then do not import
        if($stImportSource == 'ISQFT'){
            $existing_client_id = $this->checkExistingRecord('accounts', 'name', $bidder ['name']);
            
            if(!empty($existing_client_id)){
                //do not process this record
                return $existing_client_id;	
            }        	
        }
        //check for existing client
        if(!empty($unique_id)){
            $existing_client_id = $this->checkExistingRecord('accounts', 'unique_identifier_id', $unique_id);
        }else{
            $existing_client_id = '';
        }
    
        if(empty($existing_client_id)){
            $existing_client_id = checkExistingClientForXMLImport(
                    $bidder ['name'],
                    $bidder ['phone_office'],
                    $bidder ['phone_fax'],
                    $bidder ['email1']
            );
        }
    
    
        if (!empty ( $existing_client_id )) {

			$new_client = false;
			$client->id = $existing_client_id;
			$client->retrieve ( $existing_client_id );
			
			//flag for locally modified
			$bClientLoacallyModified = $client->is_modified;
			
			//if this client is linked with bluebook 
			//then only balnk fileds will be updated 
			if(trim($client->mi_account_id) != ''){			    
			    $bClientLoacallyModified = true;
			}
			
			foreach( $bidder as $key => $value ){
			    
			    if( empty($client->$key) || !$bClientLoacallyModified ){
    			    $fieldDef = $client->getFieldDefinition($key);
                    $defaultRowValue = '';
                    $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']), $client->module_dir)." (".$fieldDef['name'].")";
                    if( !empty($value) )
                    {
                        $defaultRowValue = $this->populateDefaultMapValue($key, $value, $fieldDef);
                        if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($value))
                        {
                            require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                            $sugar_field = new SugarFieldTeamset('Teamset');
                            $value = implode(', ',$sugar_field->getTeamsFromRequest($field));
                        }
                        $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $client, $fieldTranslated);
                        if ($value === FALSE) {
                            $do_save = false;
                            continue;
                        }
                        $client->$key = $value;
                    }
			    }
			    
			} 
			
			//update client
			if($do_save){
			    $client->save ();
			}else{
			    return '';
			}
			
		}else{
			
		    foreach( $bidder as $key => $value ){
		        
	            $fieldDef = $client->getFieldDefinition($key);
                $defaultRowValue = '';
                $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']), $client->module_dir)." (".$fieldDef['name'].")";
                if( !empty($value) )
                {
                    $defaultRowValue = $this->populateDefaultMapValue($key, $value, $fieldDef);
                    if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($value))
                    {
                        require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                        $sugar_field = new SugarFieldTeamset('Teamset');
                        $value = implode(', ',$sugar_field->getTeamsFromRequest($field));
                    }
                    $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $client, $fieldTranslated);
                    if ($value === FALSE) {
                        $do_save = false;
                        continue;
                    }
                    $client->$key = $value;
                }
			} 
			
			$client->visibility = 0;
			
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
			
			if($do_save){
			    
    			$client->save ();
    			//save import info
    			ImportFile::markRowAsImported ( $new_client );
    			if ($new_client)
    				ImportFile::writeRowToLastImport ( $this->import_module, $client->object_name, $client->id );
			}else{
			    return '';
			}
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
    
        global $app_list_strings, $timedate, $db, $current_user;

        $contact = new Contact();
        $new_contact = true;
        $do_save = true;
        $unique_id = trim ( $bidder ['unique_identifier_id'] );

        //check for existing client
        if(!empty($unique_id)){
            $existing_client_contact_id = $this->checkExistingRecord('contacts', 'unique_identifier_id', $unique_id);
        }else{
            $existing_client_contact_id = '';
        }
        
        if(empty($bidder ['name'])){
            $bidder_name = trim($bidder['first_name'].' '.$bidder['last_name']);
        }else{
            $bidder_name = $bidder['name'];
            list($bidder['first_name'], $bidder['last_name']) = $this->splitName($bidder ['name']);
        }
        
        if(empty($existing_client_contact_id)){
            //check for the existing client contact
            $existing_client_contact_id = checkExistingClientContactForXMLImport(
                    $bidder_name,
                    $bidder['phone_work'],
                    $bidder['phone_fax'],
                    $bidder ['email1']
            );
        }
        
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
            
            foreach( $bidder as $key => $value ){
                 
                if( empty($contact->$key) || !$bContactLocallyModified ){
                    
                    $fieldDef = $contact->getFieldDefinition($key);
                    $defaultRowValue = '';
                    $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']), $contact->module_dir)." (".$fieldDef['name'].")";
                    if( !empty($value) )
                    {
                        $defaultRowValue = $this->populateDefaultMapValue($key, $value, $fieldDef);
                        if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($value))
                        {
                            require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                            $sugar_field = new SugarFieldTeamset('Teamset');
                            $value = implode(', ',$sugar_field->getTeamsFromRequest($field));
                        }
                        $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $contact, $fieldTranslated);
                        if ($value === FALSE) {
                            $do_save = false;
                            continue;
                        }
                        $contact->$key = $value;
                    }
                }
                 
            }
            
            if(empty($contact->account_id) && !empty($client_id) ){
                $contact->account_id = $client_id;
            }
            
            
            
            if($do_save){
                $contact->save ();
            }else{
                return '';
            }
    
        }else{
            	
            foreach( $bidder as $key => $value ){
                $fieldDef = $contact->getFieldDefinition($key);
                $defaultRowValue = '';
                $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']), $contact->module_dir)." (".$fieldDef['name'].")";
                if( !empty($value) )
                {
                    $defaultRowValue = $this->populateDefaultMapValue($key, $value, $fieldDef);
                    if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($value))
                    {
                        require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                        $sugar_field = new SugarFieldTeamset('Teamset');
                        $value = implode(', ',$sugar_field->getTeamsFromRequest($field));
                    }
                    $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $contact, $fieldTranslated);
                    if ($value === FALSE) {
                        $do_save = false;
                        continue;
                    }
                    $contact->$key = $value;
                }
			} 
			
			if(empty($contact->account_id) && !empty($client_id) ){
			    $contact->account_id = $client_id;
			}
			
			$contact->visibility = 0;
    
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
            if($do_save){
                $contact->save ();
                //save import info
                ImportFile::markRowAsImported ( $new_contact );
                if ($new_contact)
                    ImportFile::writeRowToLastImport ( $this->import_module, $contact->object_name, $contact->id );
            }else{
                return '';
            }
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
    
        global $app_list_strings, $timedate, $db, $current_user;
    
        $lead_client_detail = new oss_LeadClientDetail ();
        $new_lead_client_detail = true;
    
        //bidders relation
        $lead_client_detail->lead_id = $lead_id;
        $lead_client_detail->account_id = $client_id;
        $lead_client_detail->contact_id = $contact_id;
        
    
        //bidders info
        
        if(!empty($bidder['contact_phone_no'])) $lead_client_detail->contact_phone_no = $bidder['contact_phone_no'];
        if(!empty($bidder['contact_fax'])) $lead_client_detail->contact_fax = $bidder['contact_fax'];
        if(!empty($bidder['contact_email'])) $lead_client_detail->contact_email = $bidder['contact_email'];
        if(!empty($bidder['role'])) $lead_client_detail->role = $bidder['role'];
    
        $bidder_list_array = array();
        $isError = 1;
        if(!empty($lead_id)){
            $bidder_list_array['lead_id'] = $lead_id;
            $isError = 0;
        }
        if(!empty($client_id)){
            $bidder_list_array['account_id'] = $client_id;
            $isError = 0;
        }
        if(!empty($contact_id)){
            $bidder_list_array['contact_id'] = $contact_id;
            $isError = 0;
        }
    
        if($isError == 0){
            
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
        
        return '';
    }
    
    protected function getFieldSanitizer()
    {
        global $locale, $current_user;
        
        $ifs = new ImportFieldSanitize();
        $ifs->charset= 'UTF-8';
        $timeFormat = $current_user->getUserDateTimePreferences();
        $ifs->dateformat=$timeFormat['date'];
        $ifs->timeformat=$timeFormat['time'];
        $ifs->timezone=$current_user->getPreference('timezone');
        $ifs->currency=$locale->getPrecedentPreference('currency', $current_user);
        $ifs->default_currency_significant_digits=$locale->getPrecedentPreference('default_currency_significant_digits', $current_user);
        $ifs->num_grp_sep=$current_user->getPreference('num_grp_sep');
        $ifs->dec_sep=$current_user->getPreference('dec_sep');
        $ifs->default_locale_name_format= $locale->getLocaleFormatMacro($current_user);;
        $currency = new Currency();
        $currency->retrieve($ifs->currency);
        $ifs->currency_symbol = $currency->symbol;
    
        return $ifs;
    }
    
    protected function populateDefaultMapValue($field, $fieldValue, $fieldDef)
    {
        global $timedate, $current_user;
         
        if ( is_array($fieldValue) )
            $defaultRowValue = encodeMultienumValue($fieldValue);
        else
            $defaultRowValue = $fieldValue;
        // translate default values to the date/time format for the import file
        if( $fieldDef['type'] == 'date' && $this->ifs->dateformat != $timedate->get_date_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->dateformat, $timedate->get_date_format());
    
        if( $fieldDef['type'] == 'time' && $this->ifs->timeformat != $timedate->get_time_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->timeformat, $timedate->get_time_format());
    
        if( ($fieldDef['type'] == 'datetime' || $fieldDef['type'] == 'datetimecombo') && $this->ifs->dateformat.' '.$this->ifs->timeformat != $timedate->get_date_time_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->dateformat.' '.$this->ifs->timeformat,$timedate->get_date_time_format());
    
        if ( in_array($fieldDef['type'],array('currency','float','int','num')) && $this->ifs->num_grp_sep != $current_user->getPreference('num_grp_sep') )
            $defaultRowValue = str_replace($current_user->getPreference('num_grp_sep'), $this->ifs->num_grp_sep,$defaultRowValue);
    
        if ( in_array($fieldDef['type'],array('currency','float')) && $this->ifs->dec_sep != $current_user->getPreference('dec_sep') )
            $defaultRowValue = str_replace($current_user->getPreference('dec_sep'), $this->ifs->dec_sep,$defaultRowValue);
    
        $user_currency_symbol = $this->defaultUserCurrency->symbol;
        if ( $fieldDef['type'] == 'currency' && $this->ifs->currency_symbol != $user_currency_symbol )
            $defaultRowValue = str_replace($user_currency_symbol, $this->ifs->currency_symbol,$defaultRowValue);
    
        return $defaultRowValue;
    }
    
    protected function sanitizeFieldValueByType($rowValue, $fieldDef, $defaultRowValue, $focus, $fieldTranslated)
    {
        global $mod_strings, $app_list_strings;
        switch ($fieldDef['type'])
        {
            case 'enum':
            case 'multienum':
                if ( isset($fieldDef['type']) && $fieldDef['type'] == "multienum" )
                    $returnValue = $this->ifs->multienum($rowValue,$fieldDef);
                else
                    $returnValue = $this->ifs->enum($rowValue,$fieldDef);
                // try the default value on fail
                if ( !$returnValue && !empty($defaultRowValue) )
                {
                    if ( isset($fieldDef['type']) && $fieldDef['type'] == "multienum" )
                        $returnValue = $this->ifs->multienum($defaultRowValue,$fieldDef);
                    else
                        $returnValue = $this->ifs->enum($defaultRowValue,$fieldDef);
                }
                if ( $returnValue === FALSE )
                {
                    $this->writeError($mod_strings['LBL_ERROR_NOT_IN_ENUM'] . implode(",",$app_list_strings[$fieldDef['options']]), $fieldTranslated,$rowValue);
                    return FALSE;
                }
                else
                    return $returnValue;
    
                break;
            case 'relate':
            case 'parent':
                $returnValue = $this->ifs->relate($rowValue, $fieldDef, $focus, empty($defaultRowValue));
                if (!$returnValue && !empty($defaultRowValue))
                    $returnValue = $this->ifs->relate($defaultRowValue,$fieldDef, $focus);
                return $rowValue;
                break;
            case 'teamset':
                $this->ifs->teamset($rowValue,$fieldDef,$focus);
                return $rowValue;
                break;
            case 'fullname':
                return $rowValue;
                break;
            default:
                $fieldtype = $fieldDef['type'];
                $returnValue = $this->ifs->$fieldtype($rowValue, $fieldDef, $focus);
                // try the default value on fail
                if ( !$returnValue && !empty($defaultRowValue) )
                    $returnValue = $this->ifs->$fieldtype($defaultRowValue,$fieldDef, $focus);
                
                if ( !$returnValue )
                {
                    $this->writeError($mod_strings['LBL_ERROR_INVALID_'.strtoupper($fieldDef['type'])],$fieldTranslated,$rowValue,$focus);
                    return FALSE;
                }
                return $returnValue;
        }
    }
    
    
    /**
     * Writes the row out to the ImportCacheFiles::getErrorFileName() file
     *
     * @param $error string
     * @param $fieldName string
     * @param $fieldValue mixed
     */
    public function writeError(
            $error,
            $fieldName,
            $fieldValue
    )
    {
        $fp = sugar_fopen(ImportCacheFiles::getErrorFileName(),'a');
        fputcsv($fp,array($error,$fieldName,$fieldValue,$this->_rowsCount));
        fclose($fp);
    
        if ( !$this->_rowCountedForErrors ) {
            $this->_errorCount++;
            $this->_rowCountedForErrors = true;
            $this->writeErrorRecord();
        }
    }
    
    /**
     * Writes the row out to the ImportCacheFiles::getErrorRecordsFileName() file
     */
    public function writeErrorRecord()
    {
        $fp = sugar_fopen(ImportCacheFiles::getErrorRecordsFileName(),'a');
        if ( empty($this->_enclosure) )
            fputs($fp,implode($this->_delimiter,$this->_currentRow).PHP_EOL);
        else
            fputcsv($fp,$this->_currentRow, $this->_delimiter, $this->_enclosure);
        fclose($fp);
    }
	
	 /**
     * Writes the totals and filename out to the ImportCacheFiles::getStatusFileName() file
     */
    public function writeStatus()
    {
        $fp = sugar_fopen(ImportCacheFiles::getStatusFileName(),'a');
        fputcsv($fp,array($this->_rowsCount,$this->_errorCount,0,
            $this->_createdCount,$this->_updatedCount,$this->fname));
        fclose($fp);
    }
    
    /**
     * Returns JS used in this view
     */
    private function _getJS()
    {
        global $mod_strings;
    
        return <<<EOJAVASCRIPT
<!--
    clear_all_errors();
-->
    
EOJAVASCRIPT;
    }
    
    protected function getLeadImportColumns()
    {
        $lead = new Lead();
        $importable_fields = $lead->get_importable_fields();
        $importColumns = array();
        foreach ($_REQUEST as $name => $value)
        {
            // only look for var names that start with "fieldNum"
            if (strncasecmp($name, "colnum_", 7) != 0)
                continue;
    
            // pull out the column position for this field name
            $pos = substr($name, 7);
    
            if ( isset($importable_fields[$value]) )
            {
                // now mark that we've seen this field
                $importColumns[$pos] = $value;
            }
        }
    
        return $importColumns;
    }
    
    protected function getBidderImportColumns()
    {
        $account = new Account();
        $fields = $account->get_importable_fields();
        foreach ( $fields as $fieldname => $properties ) {
            $importable_fields['account_'.$fieldname] = $properties;
        }
    
        $contact = new Contact();
        $fields  = $contact->get_importable_fields();
        foreach ( $fields as $fieldname => $properties ) {
            $importable_fields['contact_'.$fieldname] = $properties;
        }
    
        $lead = new Lead();
        $fields= $lead->get_importable_fields();
        foreach ( $fields as $fieldname => $properties ) {
            $importable_fields['lead_'.$fieldname] = $properties;
        }
    
        $bidder = new oss_LeadClientDetail();
        $fields  = $bidder->get_importable_fields();
        foreach ( $fields as $fieldname => $properties ) {
            $importable_fields['bidder_'.$fieldname] = $properties;
        }
    
        $importColumns = array();
        foreach ($_REQUEST as $name => $value)
        {
            // only look for var names that start with "fieldNum"
            if (strncasecmp($name, "bidder_colnum_", 14) != 0)
                continue;
    
            // pull out the column position for this field name
            $pos = substr($name, 14);
    
            if ( isset($importable_fields[$value]) )
            {
                // now mark that we've seen this field
                $importColumns[$pos] = $value;
            }
        }
    
        return $importColumns;
    }
    
    /**
     * To check Import Source
     * @param string $importSource
     * @return boolean
     */
    private function shouldAutoDetectProperties($importSource)
    {
        if(empty($importSource) || $importSource == 'csv' )
            return TRUE;
        else
            return FALSE;
    }
    
    /**
     * To get Import File Map
     *
     * @param String $importSource
     * @return array
     */
    private function getImportMap ($importSource)
    {
        if (strncasecmp("custom:", $importSource, 7) == 0) {
            $id = substr($importSource, 7);
            $import_map_seed = new ImportMap();
            $import_map_seed->retrieve($id, false);
    
            $this->ss->assign("SOURCE_ID", $import_map_seed->id);
            $this->ss->assign("SOURCE_NAME", $import_map_seed->name);
            $this->ss->assign("SOURCE", $import_map_seed->source);
        } else {
            $classname = 'ImportMap' . ucfirst($importSource);
            if (file_exists("modules/Import/maps/{$classname}.php"))
                require_once ("modules/Import/maps/{$classname}.php");
            elseif (file_exists("custom/modules/Import/maps/{$classname}.php"))
            require_once ("custom/modules/Import/maps/{$classname}.php");
            else {
                require_once ("custom/modules/Import/maps/ImportMapOther.php");
                $classname = 'ImportMapOther';
                $importSource = 'other';
            }
            if (class_exists($classname)) {
                $import_map_seed = new $classname();
                $this->ss->assign("SOURCE", $importSource);
            }
        }
    
        return $import_map_seed;
    }
    
    
    
    /**
     * overwrite function
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle()
    {
    
        $theTitle = "<div class='moduleTitle'>\n";
    
        $theTitle .= "<h2> Importing... </h2>\n";
        
        $theTitle .= "<span class='utils'>";
        
        $theTitle .= "</span><div class='clear'></div></div>\n";
        
        return $theTitle;
    }
    
    /**
     * overwrite function
     * @see SugarView::getBrowserTitle()
     */
    public function getBrowserTitle()
    {
        global $app_strings;
    
        $browserTitle = 'Importing...';
    
        return $browserTitle;
    }
    
    /**
     * Displays the Smarty template for an error
     *
     * @param string $message error message to show
     * @param string $module what module we were importing into
     * @param string $action what page we should go back to
     */
    public function _showImportError($message,$module = 'Leads',$action = 'importcsvbidders',$showCancel = false, $cancelLabel = null, $display = false)
    {
        if(!is_array($message)){
            $message = array($message);
        }
        $ss = new Sugar_Smarty();
        $display_msg = '';
        foreach($message as $m){
            $display_msg .= '<p>'.htmlentities($m, ENT_QUOTES).'</p><br>';
        }
        global $mod_strings;
    
        $ss->assign("MESSAGE",$display_msg);
        $ss->assign("ACTION",$action);
        $ss->assign("IMPORT_MODULE",$module);
        $ss->assign("MOD", $GLOBALS['mod_strings']);
        $ss->assign("SOURCE","");
        $ss->assign("SHOWCANCEL",$showCancel);
        if ( isset($_REQUEST['source']) )
            $ss->assign("SOURCE", $_REQUEST['source']);
    
        if ($cancelLabel) {
            $ss->assign('CANCELLABEL', $cancelLabel);
        }
    
        $content = $ss->fetch('custom/modules/Leads/tpls/importcsverror.tpl');
    
        echo $ss->fetch('custom/modules/Leads/tpls/importcsverror.tpl');
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
    
        $GLOBALS ['log']->info ( $arrParse );
    
        require_once 'custom/include/customParserDropdown.php';
        $parser = new customParserDropdown ();
        $parser->saveDropDown ( $arrParse );*/
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
