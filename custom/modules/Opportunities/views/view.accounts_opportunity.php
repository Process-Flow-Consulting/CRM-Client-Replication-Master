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
/**
 * Added New View to create opportunity from accounts.
 * @author Mohit Kumar Gupta
 * @date 08-Nov-2013
 */
require_once('custom/include/common_functions.php');
require_once ('custom/modules/Users/filters/instancePackage.class.php');
include_once 'custom/modules/Leads/views/view.convert_to_opportunity.php';
class CustomOpportunitiesViewAccounts_opportunity extends ViewEdit
{
    /*
     * Constructor for the View
     */
    function CustomOpportunitiesViewAccounts_opportunity()
    {
        parent::ViewEdit();
    }
    /**
     * display method to render the view
     * 
     * @see ViewList::display()
     *
     */
    function display()
    {
        global $timedate, $app_list_strings, $db, $current_user;
        $admin=new Administration();
        $admin_settings = $admin->retrieveSettings('instance', true);
        $geo_filter = $admin->settings ['instance_geo_filter'];
        
        $obPackage = new instancePackage ();
        if ($obPackage->validateOpportunities ()) {
        	sugar_die ( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
        }
        $arSelectIds = (isset($_REQUEST['uid']) && trim($_REQUEST['uid']) != '') ? explode(',', $_REQUEST['uid']) : $_REQUEST['mass'];
		if (empty($arSelectIds)) {
			$arSelectIds = explode(",",$_REQUEST['clientIds']);
		}        
        $stCompareIds = "'" . implode("','", $arSelectIds) . "'";
        $arFilters = Array ();        
        $params = array (
                'favorites' => 1 
        );
        $obAccount = new Account();
        $accountObj = clone $obAccount;        
        $order = (isset($_REQUEST['odr'])) ? $_REQUEST['odr'] : 'ASC';
        
        $orderBy = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : 'name';
        $first_order = $orderBy . ' ' . $order;
        
        $second_order = ', accounts.date_entered ASC ';
        
        $arSQL = $obAccount->create_new_list_query($first_order . $second_order, " accounts.id IN ({$stCompareIds}) ", $arFilters, $params, false, '', 1);               
        $stSQL = $arSQL['select'] . ' ' . $arSQL['from'] . ' ' . $arSQL['where'] . ' ' . $arSQL['order_by'];
        $rsResult = $db->query($stSQL, false, '', true, true);
        $countClientResult = $db->getRowCount($rsResult);
        
        //get saved roles classifications start
        //@modified by Mohit Kumar Gupta
        //@date 18-nov-2013
        $rolesClassificationsArr = getRolesClassifications();
        $countRolesClassifications = count($rolesClassificationsArr);
        if ($countRolesClassifications == 0) {
        	setRolesClassifications();
        	$rolesClassificationsArr = getRolesClassifications();
        	$countRolesClassifications = count($rolesClassificationsArr);
        }
        //get saved roles classifications end
        
        //get saved target Classifications start
        //@modified by Mohit Kumar Gupta
        //@date 18-nov-2013
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
        $arSavedRolesTargetClassifications = array(''=>'None');
        foreach($arSavedTargetClass as $obSavedClass){
        	$arSavedRolesTargetClassifications[$obSavedClass->id] = $obSavedClass->description ;
        }
        //get saved roles and targetClassifications end
        
        while ( $arResult = $db->fetchByAssoc ( $rsResult ) ) {
        	$arResultData = ( array ) $arResult;
        	/* 
        	$classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters` uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE `filter_type`='classification'  AND uf.assigned_user_id = '".$GLOBALS['current_user']->id."' AND uf.`deleted`=0";
        	$classification_filter_result = $db->query($classification_filter_query);
        	$classification_filter_count = $db->getRowCount($classification_filter_result);
        		
        	if($classification_filter_count > 0){
        		while($classification_filter_row = $db->fetchByAssoc($classification_filter_result)){
        			$classification_filter_array[$classification_filter_row['id']] = $classification_filter_row['name'];
        		}
        			
        		$classification_filter = implode("','", $classification_filter_array);
        		$stGetAccountsClassifications = " SELECT group_concat(oc.description ORDER BY FIELD(oc.name, '$classification_filter') DESC SEPARATOR '#$#') as classifications ";
        	}else{
        		$stGetAccountsClassifications = ' SELECT group_concat(oc.description SEPARATOR "#$#") as classifications ';
        	}
        		
        	$stGetAccountsClassifications .= ' FROM oss_classifion_accounts_c  oca
			LEFT JOIN oss_classification oc ON oc.id=oss_classi48bbication_ida AND oc.deleted=0
			WHERE  oca.oss_classid41cccounts_idb = '.$db->quoted($arResultData['id']).'  AND oca.deleted = 0
			GROUP BY oca.oss_classid41cccounts_idb  ';
        		
        	$queryResult = $db->query($stGetAccountsClassifications);
        	$arAccountClsfication = $db->fetchByAssoc($queryResult);
        	$arResultData['customClassifications'] = explode("#$#",$arAccountClsfication['classifications']); 
        	*/

        	//get deafult selected classification id start    	
        	$opportunityClassificationId = '';
        	
        	//get classification of an accounts
        	$AccountCassificationArr = getAccountClassifications($arResultData['id']);
        	$countAccountClassificationArr = count($AccountCassificationArr);
        	
        	//if classification of an accounts and target classifications exists
        	if ($countAccountClassificationArr >0 && $countSavedTargetClassifications >0) {
        		//if target classification matches to client classification
        		//update alphabetically first target classification to classification id
        		foreach ($arSavedTargetClassifications as $classificationId) {
        			if (in_array($classificationId,$AccountCassificationArr)) {
        				$opportunityClassificationId = $classificationId;
        				break;
        			}
        		}
        	
        	}
        	//get deafult selected classification id end
        	
        	$customClassificationsList = get_select_options_with_id($arSavedRolesTargetClassifications, $opportunityClassificationId);
        	$arResultData['customClassificationsList'] = $customClassificationsList;        	
        	
        	
        	//auto fill contact if it is default client contact for that client
        	//or this client have only single client contact
        	$defaultContactData = getDefaultFirstAccountContact($arResultData['id']);
        	$arResultData['contact_id'] = $defaultContactData['contact_id'];
        	$arResultData['contact_name'] = $defaultContactData['contact_name'];        	
        	
        	//if geo location is client location change assigned user else assigned user is current user
        	$arResultData['assigned_user_id'] = $current_user->id;
        	$arResultData['assigned_user_name'] = $current_user->name;
        	if ($geo_filter == 'client_location') {
        		$accountObj->disable_row_level_security = true;
        		$accountObj->retrieve($arResultData['id']);
        		$accountObj->load_relationship ( 'oss_classifation_accounts' );
        		$classIds = $accountObj->oss_classifation_accounts->get ();
        		$assigned_user = LeadsViewConvert_to_opportunity::getAssignedUser('Accounts',$arResultData['billing_address_state'],$arResultData['county_id'],$arResultData['billing_address_postalcode'],NULL,$classIds);
        		$arResultData['assigned_user_id'] = $assigned_user['id'];
        		$arResultData['assigned_user_name'] = $assigned_user['name'];
        	}
        	
        	$arData [] = $arResultData;
        }
        $order = ($order == 'ASC') ? 'DESC' : 'ASC';
        $this->ss->assign('order', $order);
        $this->ss->assign('orderBy', $orderBy);
        $this->ss->assign('timedate', $timedate);
        $this->ss->assign('ST_ASSIGN_IDS', implode(',', $arSelectIds));
        $this->ss->assign('account_list', $arData);
        $this->ss->assign('countClientResult', $countClientResult);
        $this->ss->assign('geoFilter', $geo_filter);
        $this->ss->assign('customClassificationsListAll', $customClassificationsList);
        $this->ss->display('custom/modules/Opportunities/tpls/convert_accounts_to_opportunity.tpl');  
    }   
}