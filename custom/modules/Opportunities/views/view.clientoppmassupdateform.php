<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/master-subscription-agreement
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
* by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
require_once('custom/include/common_functions.php');
/**
 * Create mass update form for client opportunity
 * @author Mohit Kumar Gupta
 * @date 02-04-2014
 */
class OpportunitiesViewClientoppmassupdateform extends ViewEdit{

    /**
     * default constructor
     */
    function OpportunitiesViewClientoppmassupdateform(){
        parent::SugarView();
    }
    
    /**
     * display function to display mass update form for opportunity
     * @see ViewEdit::display()
     */
    function display(){

        global $app_list_strings;
        if( !isset($_REQUEST['uid']) || empty($_REQUEST['uid']) ){
            $GLOBALS['log']->error('Error!! No Client Opportunity ID.');
            exit('Error!! No Client Opportunity ID.');
        }
        if( !isset($_REQUEST['parentId']) || empty($_REQUEST['parentId']) ){
			$GLOBALS['log']->error('Error!! No Project Opportunity ID.');
			exit('Error!! No Project Opportunity ID.');
		}
        $oppIdArray = array_filter(explode(",", $_REQUEST['uid']));
        $oppIdStr = implode(",", $oppIdArray);
        $parentId = $_REQUEST['parentId'];        
        
        //get saved roles classifications start        
        $rolesClassificationsArr = getRolesClassifications();
        $countRolesClassifications = count($rolesClassificationsArr);
        if ($countRolesClassifications == 0) {
            setRolesClassifications();
            $rolesClassificationsArr = getRolesClassifications();
            $countRolesClassifications = count($rolesClassificationsArr);
        }
        //get saved roles classifications end
        
        //get saved target Classifications start       
        $arSavedTargetClass = getTargetClassifications();
        $arSavedTargetClassifications = array();
        foreach($arSavedTargetClass as $obSavedClass){
            $arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->id ;
        }
        $countSavedTargetClassifications = count($arSavedTargetClassifications);
        //get saved target Classifications end
        
        //get saved roles and targetClassifications start
        $roleTargetClassificationArr = array_unique(array_merge(array_values($rolesClassificationsArr), array_values($arSavedTargetClassifications)));
        $obTargetClass = new oss_Classification();
        $arSavedTargetClass = $obTargetClass->get_full_list(' description ASC',' oss_classification.id IN ("'.implode('","',$roleTargetClassificationArr).'")');
        $arSavedRolesTargetClassifications = array(''=>'-none-');
        foreach($arSavedTargetClass as $obSavedClass){
            $arSavedRolesTargetClassifications[$obSavedClass->id] = $obSavedClass->description ;
        }
        //get saved roles and targetClassifications end
        
        $salesStage = $app_list_strings['client_sales_stage_dom'];        
        $clientBidStatus = $app_list_strings['client_bid_status_dom'];
        $this->ss->assign('salesStage', get_select_options_with_id($salesStage, ''));
        $this->ss->assign('clientBidStatus', get_select_options_with_id($clientBidStatus, ''));
        $this->ss->assign('customClassificationsListAll', get_select_options_with_id($arSavedRolesTargetClassifications, ''));
        $this->ss->assign("PARENTID",$parentId);
        $this->ss->assign("OPPIDSTR",$oppIdStr);
        $this->ss->display('custom/modules/Opportunities/tpls/clientoppmassupdate.tpl');
    }    
}