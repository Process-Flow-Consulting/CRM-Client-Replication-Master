<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
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
 * ****************************************************************************** */
require_once 'custom/include/common_functions.php';
class SaveData {
	
	static $arPreviousValues = array();
	
	
	function SaveDataAccounts(&$focus) {
		
		//set static var for previous values
		self::$arPreviousValues = $focus->fetched_row;
		
		//change the emailaddress class for this module 
		//require_once 'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php';				
		//$focus->emailAddress = new CustomSugarEmailAddress(); 
				
		if (isset ( $_REQUEST ['action'] )) {
			if ('Save' == $_REQUEST ['action']) {
				$focus->union_c = isset ( $_REQUEST ['union_c'] ) ? $_REQUEST ['union_c'] : '0';
				$focus->non_union = isset ( $_REQUEST ['non_union'] ) ? $_REQUEST ['non_union'] : '0';
				$focus->prevailing_wage = isset ( $_REQUEST ['prevailing_wage'] ) ? $_REQUEST ['prevailing_wage'] : '0';
				
				$focus->sector_private = isset ( $_REQUEST ['sector_private'] ) ? $_REQUEST ['sector_private'] : '0';
				$focus->sector_public = isset ( $_REQUEST ['sector_public'] ) ? $_REQUEST ['sector_public'] : '0';
			}
		}
	}
    
    function setProviewLink(&$focus){
    	
    	if($_REQUEST['action'] == 'EditView'){
    		return;
    	}
    	
//     	if($focus->proview_url != '')
//     	{
//     		$focus->proview_url = $focus->proview_url;
//     		if (preg_match('/^[^:\/]*:\/\/.*/', $focus->proview_url)) {
//     			$focus->proview_url= $focus->proview_url;
//     		} else {
//     			$focus->proview_url = 'http://' . $focus->proview_url;
//     		}
    		
//     		$focus->proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     	}
//     	else{
//     		//$focus->proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     		$focus->proview_url = '';
//     	}

    	

    	$focus->proview_url = proview_url(array('url'=>$focus->proview_url));
    	
    }
    
    function setModified(&$focus){
    	if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])){
    		if($_REQUEST['form_updated']=='1'){    			
    			$focus->is_modified = 1;    		
    		} 		
    	}
    }
    /**
     * function to save first sorted classification 
     * @param unknown_type $focus
     */
    function setFirstSortedClassification(&$focus){
    	
    	global $db;
    	//get all related classifications with this accounts
    	updateClientsFirstClassification($focus->id);
    	/**
    	 * Added by : Ashutosh
    	 * Date : 31 March 2014
    	 * purpose : to sync the client name with opportunity, Client Opportunity contains first 20
    	 * chars of Client with "/" separator, if there is a change in client name then change the
    	 * client opportunity name.
    	 */   	
    	$stPreviousClientName = self::$arPreviousValues['name'];
    	$stPrevousClientPart =  substr($stPreviousClientName, 0,19);
    	$stNewClientPart = substr($focus->name, 0,19);
    	//check if there is a change in first 20 chars in Client Name
    	if($stPrevousClientPart !=  $stNewClientPart){
    		
    		$GLOBALS['log']->fatal('there is a changes in name', $focus->name, self::$arPreviousValues['name']);
    	//get all client opportunities related to this client
    	$stGetCOpp = 'SELECT opportunities.id
    						, opportunities.name 
    	 FROM `accounts_opportunities`  
    	 LEFT JOIN opportunities on `opportunity_id` = opportunities.id AND opportunities.deleted=0
		 WHERE accounts_opportunities.deleted=0 AND opportunities.parent_opportunity_id IS NOT NULL AND 
    	 accounts_opportunities.account_id = '.$db->quoted($focus->id);
    	$rsResult = $db->query ( $stGetCOpp );
    	
    	//if there are any results
		if ($rsResult) {
			//itrate through all the related COP
			while ( $arRow = $db->fetchByAssoc ( $rsResult ) ) {
				
				// check if there is a change in account name
				if (strstr ( $arRow ['name'], '/'.$stPrevousClientPart)) {
					$newClientOppName = str_replace('/'.$stPrevousClientPart, '/'.$stNewClientPart, $arRow ['name']);
					$GLOBALS['log']->fatal('Found Name ', $newClientOppName);
				} else {
					$newClientOppName = $arRow ['name'].'/'.$stPrevousClientPart;
					$GLOBALS['log']->fatal('Not Found Name ', $newClientOppName);
				}
				// update Opportunity Name
				$db->query ( 'UPDATE opportunities set name=' . $db->quoted ( $newClientOppName ) . ' WHERE id=' . $db->quoted ( $arRow ['id'] ) );
			}
		}
    	}
    	 
    	
    }    
    /**
     * update CRM data to corresponding outlook data
     * @author Mohit KUmar Gupta
     * @date 06-03-2014
     */
    function updateOutlookData(&$focus){
        global $db;
        if (!empty($focus->website) && empty($focus->proview_url)) {
            $query = "UPDATE accounts SET proview_url='".$focus->website."' WHERE id='".$focus->id."'";
            $db->query($query);
        }
    }
    
    /**
     * update county name value with dropdown county selected
     * @author Shashank Verma
     * @date 28-07-2014
     */
    function updateCountyName(&$focus){
    	if(!empty($focus->county_id)){
    		$countyObj = new oss_County();
    		$countData = $countyObj->retrieve($focus->county_id);
    		$focus->county_name = $countData->name;
    	}
    }
 
    /**
     * Delete Quickbooks Id from Accounts
     * @author Shashank Verma
     * @date 15-09-2014
     */
	function deleteQuickbooksId(&$bean, $event, $arguments){
		
		if($bean->quickbooks_id != ''){
			$bean->quickbooks_id = '';
			$bean->save();
		}
	}
}
?>
