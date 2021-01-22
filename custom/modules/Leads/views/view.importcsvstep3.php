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
require_once('modules/Import/ImportDuplicateCheck.php');
require_once('modules/Import/Forms.php');

require_once('include/upload_file.php');

class LeadsViewImportcsvstep3 extends SugarView
{
    protected $currentFormID = 'importcsvstep3';
    protected $previousAction = 'importcsvstep2';
    protected $nextAction = 'importcsvfinal';
    
    function __construct(){
        parent::SugarView();
    }
    
    function display(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db;
        
        //module title
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle() );
        //instruction
        $this->ss->assign("CONFIRM_MAPPING_INSTRUCTION", $mod_strings['LBL_CONFIRM_MAPPING_INSTRUCTION'] );
        
        $has_header = ( isset( $_REQUEST['bidder_has_header']) ? 1 : 0 );
        
        if(empty($_REQUEST['lead_file_name'])){
            $this->nextAction = 'importcsvconfirm';
        }
        
        $this->ss->assign("NEXT_ACTION", $this->nextAction );
        $this->ss->assign("PREVIOUS_ACTION", $this->previousAction );
        $this->ss->assign("CURRENT_STEP", $this->currentFormID );
        
        // attempt to lookup a preexisting field map
        // use the custom one if specfied to do so in step 1
        $mapping_file = new ImportMap();
        
        $bidders_field_map = $mapping_file->set_get_import_wizard_fields();
        
        $bidders_default_values = array();
        $accounts_default_values = array();
        $contacts_default_values = array();
        $leads_default_values = array();
        
        $bidders_ignored_fields = array();
        $accounts_ignored_fields = array();
        $contacts_ignored_fields = array();
        $leads_ignored_fields = array();
        
        
        $delimiter = $this->getRequestDelimiter();
        
        
        if ( !empty( $_REQUEST['import_source']))
        {
            $GLOBALS['log']->fatal("Loading import map properties.");
            $sqlImportMap = "SELECT id FROM import_maps WHERE name = '".$_REQUEST['import_source']."' AND deleted = 0";
            $resultImportMap = $db->query($sqlImportMap);
            $rowImportMap = $db->fetchByAssoc($resultImportMap);
            
            if(!empty($rowImportMap['id'])){
                
                $mapping = new ImportMap();
                $mapping->retrieve($rowImportMap['id'], false);
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
                
                $field_map = array();
                if(!empty($bidder_mapping)){
                    $mapping->content = $bidder_mapping;
                    $field_map = $mapping->getMapping();
                }
                
            }
        }
        
        
        
        if(isset($field_map)){
        
        
            $classname = $this->getMappingClassName(ucfirst('csv'));
            
            //Set the $_REQUEST['source'] to be 'other' for ImportMapOther special case
            if($classname == 'ImportMapOther')
            {
                $_REQUEST['source'] = 'other';
            }
            
            if (class_exists($classname))
            {
                $mapping_file = new $classname;
                
                $bidder_ignored_fields = $mapping_file->getIgnoredFields('oss_LeadClientDetail');
                $accounts_ignored_fields = $mapping_file->getIgnoredFields('Accounts');
                $bidder_ignored_fields = $mapping_file->getIgnoredFields('Contacts');
                $bidder_ignored_fields = $mapping_file->getIgnoredFields('Leads');
                
                $bidders_field_map2 = $mapping_file->getMapping('oss_LeadClientDetail');
                $bidders_field_map = array_merge($bidders_field_map,$bidders_field_map2);
                
                $accounts_field_map = $mapping_file->getMapping('Accounts');
                $contacts_field_map = $mapping_file->getMapping('Contacts');
                $leads_field_map = $mapping_file->getMapping('Leads');
            }
        
        
        }
        
        $this->ss->assign("CUSTOM_DELIMITER", $delimiter);
        $this->ss->assign("CUSTOM_ENCLOSURE",  ( !empty($_REQUEST['custom_enclosure']) ? $_REQUEST['custom_enclosure'] : "" ) );
        
        
        //populate import locale  values from import mapping if available, these values will be used througout the rest of the code path
        
        $uploadFileName = $_REQUEST['bidder_file_name'];
        
        // Now parse the file and look for errors
        $importFile = new ImportFile( $uploadFileName, $delimiter, html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES), FALSE);
        
        if ( !$importFile->fileExists() ) {
            $this->_showImportError("Can't Open Leads CSV", 'Leads','importcsvstep1');
            return;
        }
        
        $charset = $importFile->autoDetectCharacterSet();
        
        // retrieve first 3 rows
        $rows = array();
        
        //Keep track of the largest row count found.
        $maxFieldCount = 0;
        for ( $i = 0; $i < 3; $i++ )
        {
            $rows[] = $importFile->getNextRow();
            $maxFieldCount = $importFile->getFieldCount() > $maxFieldCount ?  $importFile->getFieldCount() : $maxFieldCount;
        }
        $ret_field_count = $maxFieldCount;
        
        // Bug 14689 - Parse the first data row to make sure it has non-empty data in it
        $isempty = true;
        if ( $rows[(int)$has_header] != false ) {
            foreach ( $rows[(int)$has_header] as $value ) {
                if ( strlen(trim($value)) > 0 ) {
                    $isempty = false;
                    break;
                }
            }
        }
        
        if ($isempty || $rows[(int)$has_header] == false) {
            $this->_showImportError($mod_strings['LBL_NO_LINES'], 'Leads','importcsvstep1');
            return;
        }
        
        // save first row to send to step 4
        $this->ss->assign("FIRSTROW", base64_encode(serialize($rows[0])));
        
        // Now build template
        $this->ss->assign("TMP_FILE", $uploadFileName );
        
        // we export it as email_address, but import as email1
        $field_map['accounts_email_address'] = 'email1';
        $field_map['contacts_email_address'] = 'email1';
        
        // build each row; row count is determined by the the number of fields in the import file
        $columns = array();
        $mappedFields = array();
        
        // this should be populated if the request comes from a 'Back' button click
        $importColumns = $this->getImportColumns();
        $column_sel_from_req = false;
        if (!empty($importColumns)) {
            $column_sel_from_req = true;
        }
        
        for($field_count = 0; $field_count < $ret_field_count; $field_count++) {
            
            // See if we have any field map matches
            $defaultValue = "";
            // Bug 31260 - If the data rows have more columns than the header row, then just add a new header column
            if ( !isset($rows[0][$field_count]) )
                $rows[0][$field_count] = '';
            // See if we can match the import row to a field in the list of fields to import
            $firstrow_name = trim(str_replace(":","",$rows[0][$field_count]));
            if ($has_header && isset( $field_map[$firstrow_name] ) ) {
                 $defaultValue = $field_map[$firstrow_name];
            }
            elseif (isset($field_map[$field_count])) {
                $defaultValue = $field_map[$field_count];
            }else  {
                $defaultValue = trim($rows[0][$field_count]);
            }
            
            
            // build string of options
                  
            $options = array();
            $required = array();
            $defaultField = '';
            $selected = '';
            $req_class = '';
            $req_mark = '';
            global $current_language;
            
            
            $account = new Account();
            $fields  = $account->get_importable_fields();
            $moduleStrings = return_module_language($current_language, $account->module_dir);
            $options[] = '<optgroup label="Accounts">';
            foreach ( $fields as $fieldname => $properties ) {
                
                // get field name
                if (!empty($moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)]) )
                {
                    $displayname = str_replace(":","", $moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)] );
                }
                else if (!empty ($properties['vname']))
                {
                    $displayname = str_replace(":","",translate($properties['vname'] ,$account->module_dir));
                }
                else
                    $displayname = str_replace(":","",translate($properties['name'] ,$account->module_dir));
                
                // see if this is required
                $req_mark  = "";
                $req_class = "";
                if ( array_key_exists($fieldname, $account->get_import_required_fields()) ) {
                    $req_mark  = ' ' . $app_strings['LBL_REQUIRED_SYMBOL'];
                    $req_class = ' class="required" ';
                }
                
                $req_mark  = "";
                $req_class = "";
                
                $selected = '';
                if ( !empty($defaultValue) && !in_array('account_'.$fieldname,$mappedFields)
                    && !in_array('account_'.$fieldname,$ignored_fields) )
                    {
                        if ( strtolower('account_'.$fieldname) == strtolower($defaultValue)
                        || strtolower('account_'.$fieldname) == str_replace(" ","_",strtolower($defaultValue))
                         )
                        {
                            $selected = ' selected="selected" ';
                            $defaultField = 'account_'.$fieldname;
                            $mappedFields[] = 'account_'.$fieldname;
                        }
                    }
                
                // get field type information
                $fieldtype = '';
                if ( isset($properties['type'])
                && isset($mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])]) )
                    $fieldtype = ' [' . $mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])] . '] ';
                if ( isset($properties['comment']) )
                    $fieldtype .= ' - ' . $properties['comment'];
                $options['account_'.$displayname.$fieldname] = '<option value="account_'.$fieldname.'" title="'. $displayname . htmlentities($fieldtype) . '"'
                        . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }
            
            // get list of required fields
            foreach ( array_keys($account->get_import_required_fields()) as $name ) {
                $properties = $account->getFieldDefinition($name);
                if (!empty ($properties['vname']))
                    $required['account_'.$name] = str_replace(":","",translate($properties['vname'] ,$account->module_dir));
                else
                    $required['account_'.$name] = str_replace(":","",translate($properties['name'] ,$account->module_dir));
            }
            
            
            $contact = new Contact();
            $fields  = $contact->get_importable_fields();
            $moduleStrings = return_module_language($current_language, $contact->module_dir);
            $options[] = '<optgroup label="Contacts">';
            foreach ( $fields as $fieldname => $properties ) {
            
                // get field name
                if (!empty($moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)]) )
                {
                    $displayname = str_replace(":","", $moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)] );
                }
                else if (!empty ($properties['vname']))
                {
                    $displayname = str_replace(":","",translate($properties['vname'] ,$contact->module_dir));
                }
                else
                    $displayname = str_replace(":","",translate($properties['name'] ,$contact->module_dir));
            
                // see if this is required
                $req_mark  = "";
                $req_class = "";
                if ( array_key_exists($fieldname, $contact->get_import_required_fields()) ) {
                    $req_mark  = ' ' . $app_strings['LBL_REQUIRED_SYMBOL'];
                    $req_class = ' class="required" ';
                }
            
                $req_mark  = "";
                $req_class = "";
                
                $selected = '';
                if ( !empty($defaultValue) && !in_array('contact_'.$fieldname,$mappedFields)
                    && !in_array('contact_'.$fieldname,$ignored_fields) )
                    {
                        if ( strtolower('contact_'.$fieldname) == strtolower($defaultValue)
                        || strtolower('contact_'.$fieldname) == str_replace(" ","_",strtolower($defaultValue))
                         )
                        {
                            $selected = ' selected="selected" ';
                            $defaultField = 'contact_'.$fieldname;
                            $mappedFields[] = 'contact_'.$fieldname;
                        }
                    }
                
                // get field type information
                $fieldtype = '';
                if ( isset($properties['type'])
                && isset($mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])]) )
                    $fieldtype = ' [' . $mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])] . '] ';
                if ( isset($properties['comment']) )
                    $fieldtype .= ' - ' . $properties['comment'];
                $options['contact_'.$displayname.$fieldname] = '<option value="contact_'.$fieldname.'" title="'. $displayname . htmlentities($fieldtype) . '"'
                        . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }
            
            // get list of required fields
            foreach ( array_keys($contact->get_import_required_fields()) as $name ) {
                $properties = $contact->getFieldDefinition($name);
                if (!empty ($properties['vname']))
                    $required['contact_'.$name] = str_replace(":","",translate($properties['vname'] ,$contact->module_dir));
                else
                    $required['contact_'.$name] = str_replace(":","",translate($properties['name'] ,$contact->module_dir));
            }
            
            $lead = new Lead();
            $fields  = $lead->get_importable_fields();
            $moduleStrings = return_module_language($current_language, $lead->module_dir);
            $options[] = '<optgroup label="Leads">';
            foreach ( $fields as $fieldname => $properties ) {
            
                // get field name
                if (!empty($moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)]) )
                {
                    $displayname = str_replace(":","", $moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)] );
                }
                else if (!empty ($properties['vname']))
                {
                    $displayname = str_replace(":","",translate($properties['vname'] ,$lead->module_dir));
                }
                else
                    $displayname = str_replace(":","",translate($properties['name'] ,$lead->module_dir));
            
                // see if this is required
                $req_mark  = "";
                $req_class = "";
                if ( array_key_exists($fieldname, $lead->get_import_required_fields()) ) {
                    $req_mark  = ' ' . $app_strings['LBL_REQUIRED_SYMBOL'];
                    $req_class = ' class="required" ';
                }
                
                $req_mark  = "";
                $req_class = "";
                
                $selected = '';
                if ( !empty($defaultValue) && !in_array('lead_'.$fieldname,$mappedFields)
                    && !in_array('lead_'.$fieldname,$ignored_fields) )
                    {
                        if ( strtolower('lead_'.$fieldname) == strtolower($defaultValue)
                        || strtolower('lead_'.$fieldname) == str_replace(" ","_",strtolower($defaultValue))
                         )
                        {
                            $selected = ' selected="selected" ';
                            $defaultField = 'lead_'.$fieldname;
                            $mappedFields[] = 'lead_'.$fieldname;
                        }
                    }
            
                // get field type information
                $fieldtype = '';
                if ( isset($properties['type'])
                && isset($mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])]) )
                    $fieldtype = ' [' . $mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])] . '] ';
                if ( isset($properties['comment']) )
                    $fieldtype .= ' - ' . $properties['comment'];
                $options['lead_'.$displayname.$fieldname] = '<option value="lead_'.$fieldname.'" title="'. $displayname . htmlentities($fieldtype) . '"'
                        . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }
            // get list of required fields
            foreach ( array_keys($lead->get_import_required_fields()) as $name ) {
                $properties = $lead->getFieldDefinition($name);
                if (!empty ($properties['vname']))
                    $required['lead_'.$name] = str_replace(":","",translate($properties['vname'] ,$lead->module_dir));
                else
                    $required['lead_'.$name] = str_replace(":","",translate($properties['name'] ,$lead->module_dir));
            }
            
            $bidder = new oss_LeadClientDetail();
            $fields  = $bidder->get_importable_fields();
            $moduleStrings = return_module_language($current_language, $bidder->module_dir);
            $options[] = '<optgroup label="Bidders">';
            foreach ( $fields as $fieldname => $properties ) {
            
                // get field name
                if (!empty($moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)]) )
                {
                    $displayname = str_replace(":","", $moduleStrings['LBL_EXPORT_'.strtoupper($fieldname)] );
                }
                else if (!empty ($properties['vname']))
                {
                    $displayname = str_replace(":","",translate($properties['vname'] ,$bidder->module_dir));
                }
                else
                    $displayname = str_replace(":","",translate($properties['name'] ,$bidder->module_dir));
            
                // see if this is required
                $req_mark  = "";
                $req_class = "";
                if ( array_key_exists($fieldname, $lead->get_import_required_fields()) ) {
                    $req_mark  = ' ' . $app_strings['LBL_REQUIRED_SYMBOL'];
                    $req_class = ' class="required" ';
                }
            
                $req_mark  = "";
                $req_class = "";
                
                $selected = '';
                if ( !empty($defaultValue) && !in_array($fieldname,$mappedFields)
                    && !in_array($fieldname,$ignored_fields) )
                    {
                        if ( strtolower($fieldname) == strtolower($defaultValue)
                        || strtolower($fieldname) == str_replace(" ","_",strtolower($defaultValue))
                        || strtolower($displayname) == strtolower($defaultValue)
                        || strtolower($displayname) == str_replace(" ","_",strtolower($defaultValue)) )
                        {
                            $selected = ' selected="selected" ';
                            $defaultField = $fieldname;
                            $mappedFields[] = $fieldname;
                        }
                    }
                
                // get field type information
                $fieldtype = '';
                if ( isset($properties['type'])
                && isset($mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])]) )
                    $fieldtype = ' [' . $mod_strings['LBL_IMPORT_FIELDDEF_' . strtoupper($properties['type'])] . '] ';
                if ( isset($properties['comment']) )
                    $fieldtype .= ' - ' . $properties['comment'];
                $options['bidder_'.$displayname.$fieldname] = '<option value="bidder_'.$fieldname.'" title="'. $displayname . htmlentities($fieldtype) . '"'
                        . $selected . $req_class . '>' . $displayname . $req_mark . '</option>\n';
            }
            // get list of required fields
            foreach ( array_keys($bidder->get_import_required_fields()) as $name ) {
                $properties = $bidder->getFieldDefinition($name);
                if (!empty ($properties['vname']))
                    $required['bidder_'.$name] = str_replace(":","",translate($properties['vname'] ,$bidder->module_dir));
                else
                    $required['bidder_'.$name] = str_replace(":","",translate($properties['name'] ,$bidder->module_dir));
            }
            
            // Bug 27046 - Sort the column name picker alphabetically
            //ksort($options);
        
            // to be displayed in UTF-8 format
            if (!empty($charset) && $charset != 'UTF-8') {
                if (isset($rows[1][$field_count])) {
                    $rows[1][$field_count] = $locale->translateCharset($rows[1][$field_count], $charset);
                }
            }
            
            $defaultFieldHTML = '';
        
            $cellOneData = isset($rows[0][$field_count]) ? $rows[0][$field_count] : '';
            $cellTwoData = isset($rows[1][$field_count]) ? $rows[1][$field_count] : '';
            $cellThreeData = isset($rows[2][$field_count]) ? $rows[2][$field_count] : '';
            $columns[] = array(
                    'field_choices' => implode('',$options),
                    'default_field' => $defaultFieldHTML,
                    'cell1'         => strip_tags($cellOneData),
                    'cell2'         => strip_tags($cellTwoData),
                    'cell3'         => strip_tags($cellThreeData),
                    'show_remove'   => false,
            );
        }  
        
        
        $this->ss->assign("COLUMNCOUNT",$ret_field_count);
        $this->ss->assign("rows",$columns);
        
        $this->ss->assign("HAS_HEADER",($has_header ? 'on' : 'off' ));
        
        $required = array();
        
        $this->ss->assign("JAVASCRIPT", $this->_getJS($required));
 
        $this->ss->assign('required_fields',implode(', ',$required));
                
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvstep3.tpl');
        
       
    }
    
    protected function getRequestDelimiter()
    {
        $delimiter = !empty($_REQUEST['custom_delimiter']) ? $_REQUEST['custom_delimiter'] : ",";
    
        switch ($delimiter)
        {
            case "other":
                $delimiter = $_REQUEST['custom_delimiter_other'];
                break;
            case '\t':
                $delimiter = "\t";
                break;
        }
        return $delimiter;
    }
    
    
    protected function _getJS($required){

        global $mod_strings;
        
        $print_required_array = "";
        foreach ($required as $name=>$display) {
            $print_required_array .= "required['$name'] = '". sanitize($display) . "';\n";
        }
        $sqsWaitImage = SugarThemeRegistry::current()->getImageURL('sqsWait.gif');
        
        $javascript = <<<EOQ
document.getElementById('goback').onclick = function()
{
    document.getElementById('{$this->currentFormID}').action.value = '{$this->previousAction}';
    return true;
}

document.getElementById('gonext').onclick = function()
{
    if( ImportView.validateMappings() )
    {
        document.getElementById('{$this->currentFormID}').action.value = '{$this->nextAction}';
        return true;
    }else{
        return false;
    }
}
ImportView = {

	    validateMappings : function()
	    {
	        // validate form
	        clear_all_errors();
	        var form = document.getElementById('{$this->currentFormID}');
	        var hash = new Object();
	        var required = new Object();
	        $print_required_array
	        var isError = false;
	        for ( i = 0; i < form.length; i++ ) {
	            if ( form.elements[i].name.indexOf("bidder_colnum",0) == 0) {
	                if ( form.elements[i].value == "-1") {
	                    continue;
	                }
	                if ( hash[ form.elements[i].value ] == 1 ) {
	                    isError = true;
	                    add_error_style('{$this->currentFormID}',form.elements[i].name,"{$mod_strings['ERR_MULTIPLE']}");
	                }
	                hash[form.elements[i].value] = 1;
	            }
	        }    
	        // check for required fields
	        for(var field_name in required) {
	            // contacts hack to bypass errors if full_name is set
	            if (field_name == 'last_name' &&
	                    hash['full_name'] == 1) {
	                    continue;
	            }
	            if ( hash[ field_name ] != 1 ) {
	                isError = true;
	                add_error_style('{$this->currentFormID}',form.bidder_colnum_0.name,
	                    "{$mod_strings['ERR_MISSING_REQUIRED_FIELDS']} " + required[field_name]);
	            }
	        }
           
	        // return false if we got errors
	        if (isError == true) {
	            return false;
	        }


	        return true;

	    }

	}
EOQ;
        
        return $javascript;
        
    }
    
    /**
     * getMappingClassName
     *
     * This function returns the name of a mapping class used to generate the mapping of an import source.
     * It first checks to see if an equivalent custom source map exists in custom/modules/Imports/maps directory
     * and returns this class name if found.  Searches are made for sources with a ImportMapCustom suffix first
     * and then ImportMap suffix.
     *
     * If no such custom file is found, the method then checks the modules/Imports/maps directory for a source
     * mapping file.
     *
     * Lastly, if a source mapping file is still not located, it checks in
     * custom/modules/Import/maps/ImportMapOther.php file exists, it uses the ImportMapOther class.
     *
     * @see display()
     * @param string $source String name of the source mapping prefix
     * @return string name of the mapping class name
     */
    protected function getMappingClassName($source)
    {
        // Try to see if we have a custom mapping we can use
        // based upon the where the records are coming from
        // and what module we are importing into
        $name = 'ImportMap' . $source;
        $customName = 'ImportMapCustom' . $source;
    
        if (file_exists("custom/modules/Import/maps/{$customName}.php"))
        {
            require_once("custom/modules/Import/maps/{$customName}.php");
            return $customName;
        } else if (file_exists("custom/modules/Import/maps/{$name}.php")) {
            require_once("custom/modules/Import/maps/{$name}.php");
        } else if (file_exists("modules/Import/maps/{$name}.php")) {
            require_once("modules/Import/maps/{$name}.php");
        } else if (file_exists('custom/modules/Import/maps/ImportMapOther.php')) {
            require_once('custom/modules/Import/maps/ImportMapOther.php');
            return 'ImportMapOther';
        }
    
        return $name;
    }
    
    
    
    protected function getImportColumns()
    {
        $importColumns = array();
        foreach ($_REQUEST as $name => $value)
        {
            // only look for var names that start with "fieldNum"
            if (strncasecmp($name, "bidder_colnum_", 14) != 0)
                continue;
    
            // pull out the column position for this field name
            $pos = substr($name, 14);
    
            // now mark that we've seen this field
            $importColumns[$pos] = $value;
        }
    
        return $importColumns;
    }
    
    /**
     * overwrite function
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle()
    {
    
        $theTitle = "<div class='moduleTitle'>\n";
    
        $theTitle .= "<h2> Step 3: Confirm Field Mappings ( Bidders ) </h2>\n";
        
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
    
        $browserTitle = 'Step 3: Confirm Field Mappings ( Bidders )';
    
        return $browserTitle;
    }
    
    /**
     * Displays the Smarty template for an error
     *
     * @param string $message error message to show
     * @param string $module what module we were importing into
     * @param string $action what page we should go back to
     */
    public function _showImportError($message,$module = 'Leads',$action = 'importcsvstep1',$showCancel = false, $cancelLabel = null, $display = false)
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
    
}