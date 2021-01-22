<?php
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */
/**
 * *******************************************************************************
 * Description: View Import CSV Step1
 * *******************************************************************************
 */
require_once ('include/MVC/View/SugarView.php');
class AdministrationViewManage_import extends SugarView
{
    /**
     * Constructor
     */
    function AdministrationViewManage_import()
    {
        parent::SugarView();
    }
    /**
     * Display method to rendet the view
     *
     * @see SugarView::display()
     */
    function display()
    {
        global $db;
        
        if ($_POST['select_option'] == 'delete') {
            $arImportIds = $_POST['mapps'];
            
            foreach ($arImportIds as $stIds) {
                $stMarkDelSQL = 'UPDATE import_maps SET deleted =1 WHERE id=' . $db->quoted($stIds);
                $db->query($stMarkDelSQL);
            }
        }
        if ($_POST['select_option'] == 'create') {
            // redirect to create mapping screen
            SugarApplication::redirect('index.php?module=Leads&action=importcsvstep1');
        }
        
        $importOptions = array ();
        
        $sqlImportMap = " SELECT id, name FROM import_maps WHERE module = 'Bidders' AND source = 'csv' AND deleted = 0";
        $resultImportMap = $db->query($sqlImportMap);
        
        while ($rowImportMap = $db->fetchByAssoc($resultImportMap)) {
            $importOptions[$rowImportMap['id']] = $rowImportMap['name'];
        }
        
        $this->ss->assign('IMPORT_SOURCE_OPTIONS', get_select_options_with_id($importOptions, ''));
        
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle());
        $this->ss->display('custom/modules/Administration/tpls/manage_import.tpl');
    }
    
    /**
     * Function to set the heading
     * 
     * @see SugarView::getModuleTitle()
     */
    public function getModuleTitle($show_help = true)
    {
        global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $action;
        
        $theTitle = "<div class='moduleTitle'>\n";
        
        $module = preg_replace("/ /", "", $this->module);
        
        $params = $this->_getModuleTitleParams();
        $params[] = translate('LBL_MANAGE_MAPPING');
        $index = 0;
        
        if (SugarThemeRegistry::current()->directionality == "rtl") {
            $params = array_reverse($params);
        }
        if (count($params) > 1) {
            array_shift($params);
        }
        $count = count($params);
        $paramString = '';
        
        foreach ($params as $parm) {
            $index++;
            $paramString .= $parm;
            if ($index < $count) {
                $paramString .= $this->getBreadCrumbSymbol();
            }
        }
        
        if (!empty($paramString)) {
            $theTitle .= "<h2> $paramString </h2>\n";
        }
        $theTitle .= "<span class='utils'>";
        $createImageURL = SugarThemeRegistry::current()->getImageURL('create-record.gif');
        $url = ajaxLink("index.php?module=$module&action=EditView&return_module=$module&return_action=DetailView");
        $theTitle .= <<<EOHTML
&nbsp;
<a id="create_image" href="{$url}" class="utilsLink">
<img src='{$createImageURL}' alt='{$GLOBALS['app_strings']['LNK_CREATE']}'></a>
<a id="create_link" href="{$url}" class="utilsLink">
{$GLOBALS['app_strings']['LNK_CREATE']}
</a>
EOHTML;
        
        $theTitle .= "</span><div class='clear'></div></div>\n";
        return $theTitle;
    }
}