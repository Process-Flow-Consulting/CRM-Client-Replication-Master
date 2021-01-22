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
* Description: View Import CSV Step1
********************************************************************************/
require_once('include/MVC/View/SugarView.php');
require_once('modules/Import/sources/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/ImportCacheFiles.php');
require_once('modules/Import/ImportFieldSanitize.php');
require_once('modules/Import/ImportDuplicateCheck.php');
require_once('modules/Import/CsvAutoDetect.php');
require_once('include/upload_file.php');

require_once ('custom/modules/Users/filters/instancePackage.class.php');
require_once ('custom/include/common_functions.php');

class LeadsViewImportcsvstart extends SugarView
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
        
        parent::SugarView();
        
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
    
    function display(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db, $timedate, $sugar_config;
           
        
        /**************File Upload Limit Check ***********************/
        $admin=new Administration();
        $admin_settings = $admin->retrieveSettings('instance', true);
        $geo_filter = $admin->settings ['instance_geo_filter'];
        	
        $obPackage = new instancePackage ();
        $pkgDetails = $obPackage->getPacakgeDetails();

        $current_upload_directory_size = getDirectorySize('upload/');
        $current_file_size = 0;
        
        $upload_lead_field = 'leadfile';
        if($_FILES[$upload_lead_field]['size'] > 0 ){
            $current_file_size = $current_file_size + $_FILES[$upload_lead_field]['size'];
        }
        
        $upload_bidder_field = 'bidderfile';
        if($_FILES[$upload_bidder_field]['size'] > 0 ){
            $current_file_size = $current_file_size + $_FILES[$upload_bidder_field]['size'];
        }
        
        if( ($current_upload_directory_size + $current_file_size) > $pkgDetails['upload_limit'] ){
            $GLOBALS['log']->fatal($app_strings['LBL_NOT_ENOUGH_SPACE']);
            SugarApplication::appendErrorMessage($app_strings['LBL_NOT_ENOUGH_SPACE']);
            SugarApplication::redirect('index.php?module=Leads&action=importcsvbidders&return_module=Leads&return_action=index');
            die();
        }
        /**************File Upload Limit Check ***********************/
        
        //module title
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle() );
        
        if(empty($_REQUEST['import_source'])){
            $this->_showImportError('Import Error: No Source selected.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }else{
            $importSourceMap = $_REQUEST['import_source'];
        }
        
        // Increase the max_execution_time since this step can take awhile
        ini_set("max_execution_time", max($sugar_config['import_max_execution_time'],3600));
        
        // stop the tracker
        TrackerManager::getInstance()->pause();
        
        //get Mapping Details
        $mapping = new ImportMap();
        $mapping->retrieve($importSourceMap, false);
        $importSource = $mapping->source;
        //if mapping source is not csv show error
        if($importSource != 'csv'){
            $this->_showImportError('Import Error: Wrong Source.', 'Leads', 'importcsvbidders', false, null , true);
            return;
        }
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
        
        $uploadLeadFileName = '';
        $uploadBidderFileName = '';
        
        // Clear out this user's last import
        $seedUsersLastImport = new UsersLastImport();
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();
        
        
        // handle uploaded lead file
        $uploadLeadFile = new UploadFile('leadfile');
        if (isset($_FILES['leadfile']) && $uploadLeadFile->confirm_upload())
        {
            $uploadLeadFile->final_move('IMPORT_LEAD_CSV_'.$current_user->id);
            $uploadLeadFileName = $uploadLeadFile->get_upload_path('IMPORT_LEAD_CSV_'.$current_user->id);
        }else if( !empty($_REQUEST['lead_file_name']) ){
            $uploadLeadFileName = $_REQUEST['lead_file_name'];
        }
        
        //handle uploaded bidder file
        $uploadBidderFile = new UploadFile('bidderfile');
        if (isset($_FILES['bidderfile']) && $uploadBidderFile->confirm_upload())
        {
            $uploadBidderFile->final_move('IMPORT_BIDDER_CSV_'.$current_user->id);
            $uploadBidderFileName = $uploadBidderFile->get_upload_path('IMPORT_BIDDER_CSV_'.$current_user->id);
        }else if( !empty($_REQUEST['bidder_file_name']) ){
            $uploadBidderFileName = $_REQUEST['bidder_file_name'];
        }
        
        //if both file not uploaded show error
        if( empty($uploadLeadFileName) && empty($uploadBidderFileName) )
        {
            $this->_showImportError('Import Error: No File Uploaded.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }
        
        //check the lead file size, we dont want to process an empty file
        if( isset($_FILES['leadfile']['size']) && ($_FILES['leadfile']['size'] == 0) && !empty($uploadLeadFileName) ){
            //this file is empty, throw error message
            $this->_showImportError('Import Error: Empty Project Leads CSV.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }
        
        //check the bidders file size, we dont want to process an empty file
        if( isset($_FILES['bidderfile']['size']) && ($_FILES['bidderfile']['size'] == 0) && !empty($uploadBidderFileName) ){
            //this file is empty, throw error message
            $this->_showImportError('Import Error: Empty Bidders CSV.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }
        
        $mimeTypeOk = true;
        
        
        //check to see if the lead file mime type is not a form of text or application octed streramand fire error if not
        if(isset($_FILES['leadfile']['type']) && strpos($_FILES['leadfile']['type'],'octet-stream') === false && strpos($_FILES['leadfile']['type'],'text') === false
        && strpos($_FILES['leadfile']['type'],'application/vnd.ms-excel') === false
        && !empty($uploadLeadFileName) ) {
            //this file does not have a known text or application type of mime type, issue the warning
            $error_msgs[] = 'The selected Leads file does not appear to contain a delimited list. Please check the file type. We recommend comma-delimited files (.csv).';
            $error_msgs[] = 'To proceed with importing the selected file, click OK. To upload a new file, click Try Again';
            $this->_showImportError($error_msgs, 'Leads', 'importcsvbidders', false, null , true);
            $mimeTypeOk = false;
        }
        
        //check to see if the bidder file mime type is not a form of text or application octed streramand fire error if not
        if(isset($_FILES['bidderfile']['type']) && strpos($_FILES['bidderfile']['type'],'octet-stream') === false && strpos($_FILES['bidderfile']['type'],'text') === false
        && strpos($_FILES['bidderfile']['type'],'application/vnd.ms-excel') === false
        && !empty($uploadBidderFileName) ) {
            //this file does not have a known text or application type of mime type, issue the warning
            $error_msgs[] = 'The selected Bidders file does not appear to contain a delimited list. Please check the file type. We recommend comma-delimited files (.csv).';
            $error_msgs[] = 'To proceed with importing the selected file, click OK. To upload a new file, click Try Again';
            $this->_showImportError($error_msgs, 'Leads', 'importcsvbidders', false, null , true);
            $mimeTypeOk = false;
        }
        
        if(!empty($uploadBidderFileName) && empty($lead_mapping)){
            $this->_showImportError('Import Error: No Mapping for Leads.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }
        
        if(!empty($uploadBidderFileName) && empty($bidder_mapping)){
            $this->_showImportError('Import Error: No Mapping for Bidders.', 'Leads', 'importcsvbidders', false, null , true);
            die;
        }
        
        $this->ss->assign("JAVASCRIPT", $this->_getJS());
        
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvstart.tpl');
        
        //echo '<pre>'; print_r($lead_field_mapping); echo '</pre>';
        //echo '<pre>'; print_r($bidder_field_mapping); echo '</pre>';
        
        if(!empty($uploadLeadFileName) ){
            $this->fname = $uploadLeadFileName;
        }else{
            $this->fname = $uploadBidderFileName;
        }
        $importObj = new ImportFile( $this->fname );
        
        // turn on auto-detection of line endings
        ini_set('auto_detect_line_endings', '1');
        /*************************Start Lead Importing*****************************/
        if(!empty($uploadLeadFileName)){
            $fpLead = sugar_fopen($uploadLeadFileName, 'r');
            $leadImportColumns = array();
            if($hasHeader){
                $sourceHeaderLead = fgetcsv($fpLead, 8192,$delimiter,$enclosure);
                foreach($sourceHeaderLead as $key => $mapping){
                    $leadImportColumns[$key] = $lead_field_mapping[$mapping];
                }
                //echo '<pre>'; print_r($leadImportColumns); echo '</pre>';
                $rowCountLead = 0;
                $this->_rowsCount = 0;
                while( ($sourceRowLead = fgetcsv($fpLead, 8192,$delimiter,$enclosure)) !== FALSE ){   
                    if($rowCountLead == 0){
                        $rowCountLead++;
                        continue;
                    }
                    
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
                    //echo '<pre>'; print_r($leads); echo '</pre>';
                    $lead_id = $this->insertProjectLead($leads,$importObj);
                    $rowCountLead++;
                }
                $this->writeStatus ();
            }
        }
        /*************************End Lead Importing*****************************/
        
        
        /*************************Start Bidders Importing*****************************/
        if(!empty($uploadBidderFileName)){
            $fpBidder = sugar_fopen($uploadBidderFileName, 'r');
            $bidderImportColumns = array();
            if($hasHeader){
                $sourceHeaderBidder = fgetcsv($fpBidder, 8192,$delimiter,$enclosure);
                foreach($sourceHeaderBidder as $key => $mapping){
                    if(isset($bidder_field_mapping[$mapping])){
                        $bidderImportColumns[$key] = $bidder_field_mapping[$mapping];
                    }
                }
                //echo '<pre>'; print_r($bidderImportColumns); echo '</pre>';
                $rowCountBidder = 0;
                $this->_rowsCount = 0;
                while ( ($sourceRowBidder = fgetcsv($fpBidder, 8192,$delimiter,$enclosure)) !== FALSE ){
                    if($rowCountBidder == 0){
                        $rowCountBidder++;
                        continue;
                    }
                    
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
                    if( isset($accountArray['name']) && !empty($accountArray['name']) ){
                        $client_id = $this->insertClient($accountArray, $importObj);
                    }else{
                        $account_id = '';
                    }
                    //create / update client Conatct
                    if( (isset($contactArray['last_name']) && !empty($contactArray['last_name']) )
                            || (isset($contactArray['name']) && !empty($contactArray['name'])) ){
                        $contact_id = $this->insertClientContact($contactArray, $importObj);
                    }else{
    					$contact_id = '';
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
        /*************************End Bidder Importing*****************************/
        SugarApplication::redirect('index.php?module=Leads&action=finishcsvimport');
        //SugarApplication::redirect('index.php?module=Import&action=Last&current_step=4&type=&import_module=Leads&has_header=on');
        
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
        $ignoreSanitize = array('project_status', 'owner', 'type', 'structure');
        
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
                        $value = $this->sanitizeFieldValueByType($value, $fieldDef, $defaultRowValue, $lead, $fieldTranslated);
                    }
                    if ($value === FALSE) {
                        $do_save = false;
                        continue;
                    }
                    $lead->$key = $value;
                }
            }
  
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

        global $app_list_strings, $timedate, $db, $current_user;
    
        // other then ownner all are bidders
        $client = new Account ();
        $new_client = true;
        $do_save = true;
        $unique_id = trim ( $bidder ['unique_identifier_id'] );
    
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
<script type="text/javascript">
<!--
    clear_all_errors();
-->
</script>
    
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

      /*  global $app_list_strings;
    
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