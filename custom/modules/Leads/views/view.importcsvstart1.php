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

class LeadsViewImportcsvstart1 extends SugarView
{

	const SAMPLE_ROW_SIZE = 3;
    protected $errorScript = "";
    protected $currentFormID = 'importcsvstart1';
    protected $previousAction = 'importcsvbidders';
    protected $nextAction = 'importcsvstart2';
    
    function __construct(){
        parent::SugarView();
    }
    
    function display(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db, $timedate, $sugar_config;
           
        
        /**************File Upload Limit Check ***********************/
        $admin=new Administration();
        $admin_settings = $admin->retrieveSettings('instance', true);
        	
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
        
        //instruction
        $this->ss->assign("CONFIRM_CSV_INSTRUCTION", $mod_strings['LBL_CONFIRM_CSV_INSTRUCTION'] );
        
        
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
        
        
        $this->ss->assign("LEAD_FILE_NAME", $uploadLeadFileName);
        $this->ss->assign("BIDDER_FILE_NAME", $uploadBidderFileName);
        
        
        $this->ss->assign("CUSTOM_DELIMITER", $delimiter);
        $this->ss->assign("CUSTOM_ENCLOSURE", htmlentities($enclosure, ENT_QUOTES) );
        
        
        if(!empty($uploadLeadFileName)){
                     
            $_REQUEST['import_module'] = 'Leads';
            
            // Now parse the file and look for errors
            $importLeadFile = new ImportFile( $uploadLeadFileName, $delimiter,html_entity_decode($enclosure,ENT_QUOTES), FALSE);
            if( $this->shouldAutoDetectProperties($importSource) )
            {
                $GLOBALS['log']->debug("Auto detecing csv properties...");
                $autoDetectOk = $importLeadFile->autoDetectCSVProperties();
                $importLeadFileMap = array();
                if($autoDetectOk === FALSE)
                {
                    //show error only if previous mime type check has passed
                    if($mimeTypeOk){
                        $this->ss->assign("AUTO_DETECT_ERROR",  $mod_strings['LBL_AUTO_DETECT_ERROR']);
                    }
                }
                else
                {
                    $dateFormat = $importLeadFile->getDateFormat();
                    $timeFormat = $importLeadFile->getTimeFormat();
                    if ($dateFormat) {
                        $importLeadFileMap['importlocale_dateformat'] = $dateFormat;
                    }
                    if ($timeFormat) {
                        $importLeadFileMap['importlocale_timeformat'] = $timeFormat;
                    }
                }
            }
            else
            {
                $importLeadMapSeed = $this->getImportMap($importSource);
                $importLeadFile->setImportFileMap($importLeadMapSeed);
                $importLeadFileMap = $importLeadMapSeed->getMapping('Leads');
            }
            
            $delimeterLead = $importLeadFile->getFieldDelimeter();
            $enclosureLead = $importLeadFile->getFieldEnclosure();
            $hasHeaderLead = $importLeadFile->hasHeaderRow();
            
            if ( !$importLeadFile->fileExists() ) {
                $this->_showImportError("Can't Open Leads CSV File",'Leads','importcsvstep1', false, null, true);
                return;
            }
            
            //Retrieve a sample set of data
            $rowsLead = $this->getSampleSet($importLeadFile);
            $this->ss->assign('LEAD_COLUMN_COUNT', $this->getMaxColumnsInSampleSet($rowsLead) );
            $this->ss->assign('LEAD_HAS_HEADER', $importLeadFile->hasHeaderRow(FALSE) );            
            $this->ss->assign("SAMPLE_LEAD_ROWS",$rowsLead);
            unset($_REQUEST['import_module']);
        }
        
        if(!empty($uploadBidderFileName)){
            
            $_REQUEST['import_module'] = 'Leads';
            
            // Now parse the file and look for errors
            $importBidderFile = new ImportFile( $uploadBidderFileName, $delimiter,html_entity_decode($enclosure,ENT_QUOTES), FALSE);
            if( $this->shouldAutoDetectProperties($importSource) )
            {
                $GLOBALS['log']->debug("Auto detecing csv properties...");
                $autoDetectOk = $importBidderFile->autoDetectCSVProperties();
                $importBidderFileMap = array();
                if($autoDetectOk === FALSE)
                {
                    //show error only if previous mime type check has passed
                    if($mimeTypeOk){
                        $this->ss->assign("AUTO_DETECT_ERROR",  $mod_strings['LBL_AUTO_DETECT_ERROR']);
                    }
                }
                else
                {
                    $dateFormat = $importBidderFile->getDateFormat();
                    $timeFormat = $importBidderFile->getTimeFormat();
                    if ($dateFormat) {
                        $importBidderFileMap['importlocale_dateformat'] = $dateFormat;
                    }
                    if ($timeFormat) {
                        $importBidderFileMap['importlocale_timeformat'] = $timeFormat;
                    }
                }
            }
            else
            {
                $importBidderMapSeed = $this->getImportMap($importSource);
                $importBidderFile->setImportFileMap($importBidderMapSeed);
                $importBidderFileMap = $importBidderMapSeed->getMapping('Leads');
            }
        
            $delimeterBidder = $importBidderFile->getFieldDelimeter();
            $enclosureBidder = $importBidderFile->getFieldEnclosure();
            $hasHeaderBidder = $importBidderFile->hasHeaderRow();
            //if ISQFT then set header true
            if($mapping->name == 'ISQFT'){
                $importBidderFile->setHeaderRow(1);
            }
            if ( !$importBidderFile->fileExists() ) {
                $this->_showImportError("Can't Open Bidder CSV File",'Leads','importcsvstep1', false, null, true);
                return;
            }
            
            //Retrieve a sample set of data
            $rowsBidder = $this->getSampleSet($importBidderFile);
            $this->ss->assign('BIDDER_COLUMN_COUNT', $this->getMaxColumnsInSampleSet($rowsBidder) );
            $this->ss->assign('BIDDER_HAS_HEADER', $importBidderFile->hasHeaderRow(FALSE) );
            $this->ss->assign("SAMPLE_BIDDER_ROWS",$rowsBidder);
            unset($_REQUEST['import_module']);
        }
        
        
        $this->ss->assign("NEXT_ACTION", $this->nextAction );
        $this->ss->assign("PREVIOUS_ACTION", $this->previousAction );
        $this->ss->assign("CURRENT_STEP", $this->currentFormID );
        
        
        $this->ss->assign("JAVASCRIPT", $this->_getJS());
        
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvstart1.tpl');
        
    }
    
    public function getMaxColumnsInSampleSet ($sampleSet)
    {
        $maxColumns = 0;
        foreach ($sampleSet as $v) {
            if (count($v) > $maxColumns)
                $maxColumns = count($v);
            else
                continue;
        }
        
        return $maxColumns;
    }

    public function getSampleSet ($importFile)
    {
        $rows = array();
        // flag to not get into infinite loop
        $bInfiniteLoopBreak = 0;
        $iNonEmptyCols = 0;
        for ($i = 0; $i < self::SAMPLE_ROW_SIZE; $i ++) {
            $arRow = $importFile->getNextRow();
            // itrate through the rows and check non empty values
            foreach ($arRow as $iIndex => $stValue) {
                if (trim($stValue) != '') {
                    $iNonEmptyCols ++;
                }
            }
            if ($iNonEmptyCols <= 1) {
                $i --;
                // increase the counter allow only 20 itration
                $bInfiniteLoopBreak ++;
                if ($bInfiniteLoopBreak > 20) {
                    // stop here do not get into infinte loop
                    break;
                }
                
                continue;
            }
            $rows[] = $arRow;
        }
        
        if (! $importFile->hasHeaderRow(FALSE)) {
            array_unshift($rows, array_fill(0, 1, ''));
        }
        
        // to be displayed in UTF-8 format
        global $locale;
        $encoding = $importFile->autoDetectCharacterSet();
        if (! empty($encoding) && $encoding != 'UTF-8') {
            foreach ($rows as &$row) {
                if (is_array($row)) {
                    foreach ($row as &$val) {
                        $val = $locale->translateCharset($val, $encoding);
                    }
                }
            }
        }
        
        foreach ($rows as &$row) {
            if (is_array($row)) {
                foreach ($row as &$val) {
                    $val = strip_tags($val);
                }
            }
        }
        
        return $rows;
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
    
        $theTitle .= "<h2> Step 2: Confirm Import File Properties </h2>\n";
    
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
    
        $browserTitle = 'Step 2: Confirm Import File Properties';
    
        return $browserTitle;
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
    
    protected function _getJS(){
        
        $javascript = <<<EOQ
document.getElementById('goback').onclick = function()
{
    document.getElementById('importcsvstart1').action.value = '{$this->previousAction}';
    return true;
}

document.getElementById('gonext').onclick = function()
{
    document.getElementById('importcsvstart1').action.value = '{$this->nextAction}';
    document.getElementById('upload_content').style.display = 'none';
    document.getElementById('ajax_content').style.display = 'block';
    document.getElementById('mod_title').innerHTML = '<h2>Importing...</h2>';
    return true;
}
EOQ;
        return $javascript;
        
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
}
