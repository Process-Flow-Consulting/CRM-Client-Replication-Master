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

class LeadsViewImportcsvfinal extends SugarView
{
    protected $currentFormID = 'importcsvfinal';
    protected $previousAction = 'importcsvstep3';
    protected $nextAction = 'importcsvmapping';
    
    function __construct(){
        parent::SugarView();
    }
    
    function display(){
        
        global $mod_strings, $app_list_strings, $app_strings, $current_user;
        
        //module title
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle() );
        //instruction
        $this->ss->assign("CONFIRM_SAVE_INSTRUCTION", $mod_strings['LBL_CONFIRM_SAVE_INSTRUCTION'] );
        
        if(empty($_REQUEST['bidder_file_name'])){
            $this->nextAction = 'importcsvstep2';
        }else if(empty($_REQUEST['lead_file_name'])){
            $this->nextAction = 'importcsvconfirm';
        }
        
        $this->ss->assign("NEXT_ACTION", $this->nextAction );
        $this->ss->assign("PREVIOUS_ACTION", $this->previousAction );
        $this->ss->assign("CURRENT_STEP", $this->currentFormID );

        
        $delimiter = $this->getRequestDelimiter();
        
        $this->ss->assign("CUSTOM_DELIMITER", $delimiter);
        $this->ss->assign("CUSTOM_ENCLOSURE",  ( !empty($_REQUEST['custom_enclosure']) ? $_REQUEST['custom_enclosure'] : "" ) );
        
        $uploadLeadFileName = $_REQUEST['lead_file_name'];
        $uploadBiodderFileName = $_REQUEST['bidder_file_name'];
        
        $lead_firstrow = base64_decode($_REQUEST['lead_firstrow']);
        $bidder_firstrow = base64_decode($_REQUEST['bidder_firstrow']);
        
        $this->ss->assign("JAVASCRIPT", $this->_getJS());
                
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvfinal.tpl'); 
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
    
    
    protected function _getJS(){

        global $mod_strings;
        
        $javascript = <<<EOQ
document.getElementById('goback').onclick = function()
{
    document.getElementById('{$this->currentFormID}').action.value = '{$this->previousAction}';
    return true;
}

document.getElementById('gonext').onclick = function()
{
    document.getElementById('{$this->currentFormID}').action.value = '{$this->nextAction}';
    return true;
}
EOQ;
        
        return $javascript;
        
    }
    
    
    /**
     * overwrite function
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle()
    {
    
        $theTitle = "<div class='moduleTitle'>\n";
    
        $theTitle .= "<h2> Step 4: Save Mapping </h2>\n";
    
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
    
        $browserTitle = 'Step 3: Save Mapping';
    
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