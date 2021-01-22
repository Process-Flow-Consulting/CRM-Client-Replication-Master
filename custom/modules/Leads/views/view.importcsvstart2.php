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

class LeadsViewImportcsvstart2 extends SugarView
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
    }
    
    function display(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db, $timedate, $sugar_config;
        
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
        
        // Clear out this user's last import
        $seedUsersLastImport = new UsersLastImport();
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();
        
        
        $uploadLeadFileName = $_REQUEST['lead_file_name'];
        $uploadBidderFileName = $_REQUEST['bidder_file_name'];
        
        //if both file not uploaded show error
        if( empty($uploadLeadFileName) && empty($uploadBidderFileName) )
        {
            $this->_showImportError('Import Error: No File Uploaded.', 'Leads', 'importcsvbidders', false, null , true);
            die;
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
        
        

        if(empty($uploadLeadFileName)){
            $uploadLeadFileName = 'none';
        }
        if(empty($uploadBidderFileName)){
            $uploadBidderFileName = 'none';
        }
        
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvstart2.tpl');
        
        $status_filename = ImportCacheFiles::getStatusFileName();
        
        $cmd = "/usr/local/zend/bin/php -f cmdscripts/ImportCSV.php $uploadLeadFileName  $uploadBidderFileName $importSourceMap $current_user->id > /dev/null 2>&1 & echo $!;";
        $GLOBALS['log']->fatal($cmd);
        $process_path = 'upload/process/';
        $lock_file = $process_path. $current_user->id. '_import_csv_process_lock';
        	
        //Check lock file is exists
        if(file_exists($lock_file)){
            //check pid
            $pid = file_get_contents($lock_file);
        
            if(posix_kill($pid,0)){
                //echo "Process already running";
                //echo 'running';
            }else{
                //Run the command and write pid in a file.
                $this->runCommand($cmd,$lock_file);
                //echo 'start';
            }
        } else {
            // Run the command and write pid in a file.
            $this->runCommand ( $cmd, $lock_file );
            //echo 'start1';
        }
        
        echo <<<EOQ
		<script type = "text/javascript">
        var interval_id = '';
        function getProcessStatus(){
            var error_pattern = new RegExp("^error_");
            $.ajax({
                type: 'POST',
        		url : 'cmdscripts/import_csv_process_status.php',
        		data: "&current_user=$current_user->id",
                beforeSend:function (){
                },
                success:function (data){
                    if(trim(data) == 'finished'){
                        clearInterval(interval_id);
                        window.location.href = "index.php?module=Leads&action=finishcsvimport";
                        return false;
                    }else if(error_pattern.test(trim(data))){
                        clearInterval(interval_id);
                        document.getElementById('ajax_content').innerHTML = '<center>'+data.replace("error_","")+'<br>Please Go Back and Try again Resolving the Error.</center>';
                        return false;
                    }
                },
                error:function(data){
                    clearInterval(interval_id);
                    //window.location.href = "index.php?module=Leads&action=index";
                    return false;
                },
                cache: false,
                async:true
            });
        }
        $(document).ready(function() {
            interval_id=setInterval( function() { getProcessStatus(); }, 5000 );
        });
        </script>
EOQ;
        
    }
    
    function runCommand($cmd,$lock_file){
        $pid =  exec ( $cmd, $output );
        $fp=fopen($lock_file,"w");
        fwrite($fp,$pid);
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
}
