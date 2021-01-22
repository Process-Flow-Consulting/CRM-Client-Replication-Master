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

class LeadsViewImportcsvmapping extends SugarView
{
    
    function __construct(){
        parent::SugarView();
    }
    
    function display(){
        
        global $mod_strings, $app_list_strings, $app_strings, $current_user;
        
        //module title
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle() );

        $leads_mapiing = '';
        if(!empty($_REQUEST['lead_file_name'])){
            
            $uploadLeadFile = $_REQUEST['lead_file_name'];
            if(!file_exists($uploadLeadFile)) {
                trigger_error("Can't Open Lead File",E_USER_ERROR);
            }
            
            // Open the import file
           // $importLeadSource = new ImportFile($uploadLeadFile, $_REQUEST['custom_delimiter'],html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES));
            
            //Ensure we have a valid file.
           // if ( !$importLeadSource->fileExists() )
                //trigger_error("Can't Open Lead File",E_USER_ERROR);

            $this->importColumnsLead = $this->getLeadImportColumns();
          
            $lead_firstrow    = unserialize(base64_decode($_REQUEST['lead_firstrow'])); 
            $mappingValsArrLead = $this->importColumnsLead;
            $mapping_file_lead = new ImportMap();
            if ( isset($_REQUEST['lead_has_header']) && $_REQUEST['lead_has_header'] == '1')
            {
                $header_to_field = array ();
                foreach ($this->importColumnsLead as $pos => $field_name)
                {
                    if (isset($lead_firstrow[$pos]) && isset($field_name))
                    {
                        $header_to_field[$lead_firstrow[$pos]] = $field_name;
                    }
                }
            
                $mappingValsArrLead = $header_to_field;
            }

            //set mapping
            $mapping_file_lead->setMapping($mappingValsArrLead);
            $leads_mapiing = $mapping_file_lead->content;
            //$result = $mapping_file_lead->save( $current_user->id,  $_REQUEST['import_source'], 'Leads', 'csv',
                 //   ( isset($_REQUEST['lead_has_header']) && $_REQUEST['lead_has_header'] == 'on'), $_REQUEST['custom_delimiter'], html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES)
            //);
        }
        
        $bidders_mapping ='';
        if(!empty($_REQUEST['bidder_file_name'])){
        
            $uploadBidderFile = $_REQUEST['bidder_file_name'];
            if(!file_exists($uploadBidderFile)) {
                trigger_error("Can't Open Bidder File",E_USER_ERROR);
            }
        
            // Open the import file
           // $importBidderSource = new ImportFile($uploadBidderFile, $_REQUEST['custom_delimiter'],html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES));
        
            //Ensure we have a valid file.
           // if ( !$importBidderSource->fileExists() )
               // trigger_error("Can't Open Bidder File",E_USER_ERROR);
        
            $this->importColumnsBidder = $this->getBidderImportColumns();
            $bidder_firstrow    = unserialize(base64_decode($_REQUEST['bidder_firstrow']));
            $mappingValsArrBidder = $this->importColumnsBidder;
            $mapping_file_bidder = new ImportMap();
            if ( isset($_REQUEST['bidder_has_header']) && $_REQUEST['bidder_has_header'] == '1')
            {
                $header_to_field = array ();
                foreach ($this->importColumnsBidder as $pos => $field_name)
                {
                    if (isset($bidder_firstrow[$pos]) && isset($field_name))
                    {
                        $header_to_field[$bidder_firstrow[$pos]] = $field_name;
                    }
                }
        
                $mappingValsArrBidder = $header_to_field;
            }
        
            //set mapping
            $mapping_file_bidder->setMapping($mappingValsArrBidder);
            $bidders_mapping = $mapping_file_bidder->content;
        }

        $result = '';
        
        $mapping_file = new ImportMap();
        $mapping_file->content = $leads_mapiing.'|'.$bidders_mapping;
        
        $result = $mapping_file->save( $current_user->id,  $_REQUEST['import_source'], 'Bidders', 'csv',
                ( isset($_REQUEST['bidder_has_header']) && $_REQUEST['bidder_has_header'] == 'on'), $_REQUEST['custom_delimiter'], html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES)
        );
        
        if(!empty($result)){
            $this->ss->assign("SAVE_STATUS", 'Saved Successfully.' );
        }else{
            $this->ss->assign("SAVE_STATUS", 'Error:  Please Try Again.' );
        }
        
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvmapping.tpl'); 
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
    
    /**
     * overwrite function
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle()
    {
    
        $theTitle = "<div class='moduleTitle'>\n";
    
        $theTitle .= "<h2> Mapping Done! </h2>\n";
    
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
    
        $browserTitle = 'Mapping Done';
    
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