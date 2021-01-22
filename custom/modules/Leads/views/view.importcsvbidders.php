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
class LeadsViewImportcsvbidders extends SugarView
{
    function __construct(){
        parent::SugarView();
    }
    
    function display(){
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $db;
        
        //module title
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle() );
        
        $importOptions = array();
        $importOptions[''] = '--none--';
        $sqlImportMap = " SELECT id, name FROM import_maps WHERE module = 'Bidders' AND source = 'csv' AND deleted = 0";
        $resultImportMap = $db->query($sqlImportMap);
        while( $rowImportMap = $db->fetchByAssoc($resultImportMap) ){
            $importOptions[$rowImportMap['id']] = $rowImportMap['name'];  
        }
        
        $this->ss->assign('IMPORT_SOURCE_OPTIONS', get_select_options_with_id($importOptions, '') );
        
        //tpl file
        $this->ss->display('custom/modules/Leads/tpls/importcsvbidders.tpl');
    }
    
    /**
     * overwrite function
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle()
    {
    
        $theTitle = "<div class='moduleTitle'>\n";
    
        $theTitle .= "<h2> Step 1: Upload CSV file </h2>\n";
        
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
    
        $browserTitle = 'Step 1: Upload CSV file';
    
        return $browserTitle;
    }
    
}