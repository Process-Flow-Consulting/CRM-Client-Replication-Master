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
require_once("modules/Administration/QuickRepairAndRebuild.php");
require_once 'include/utils/file_utils.php';

/**
 * use to create custom fields
 * @author Shashank Verma
 * @date 04-08-2014
*/
class AdministrationViewViewcustomfields extends SugarView {
	/**
	 * default constructor
	 * @author Shashank Verma
	 * @date 04-08-2014
	 */
	function AdministrationViewViewcustomfields(){
		parent::SugarView();
	}
	/**
	 * default display function
	 * use to display the edit view
	 * @see SugarView::display()
	 * @author Shashank Verma
	 * @date 08-08-2014
	 */
	function display(){
		
		global $db;
		$fieldExist = 0;

		//Check if the field is not created on repair rebuild provide an option to do a repair rebuild
		if(isset($_REQUEST['editField']) && $_REQUEST['editField'] == 0){
			if(isset($_REQUEST['fieldName']) && isset($_REQUEST['tableName'])){
				$tableName = strtolower($_REQUEST['tableName']);
				$fieldName = $_REQUEST['fieldName'];
				$columnSql = "SHOW COLUMNS FROM {$tableName} LIKE '{$fieldName}'";
				$result = $db->query($columnSql);
				$exists = ($db->getRowCount($result))?TRUE:FALSE;
				 
				if(!$exists){
					$fieldExist = 1;
				}	
			}
		}
		
		$accountCustom = $this->generateCustomFields('Accounts');
		ksort($accountCustom);
		
		$contactCustom = $this->generateCustomFields('Contacts');
		ksort($contactCustom);
		
		$leadCustom = $this->generateCustomFields('Leads');
		ksort($leadCustom);
		
		$opportunityCustom = $this->generateCustomFields('Opportunities','parent_opportunity');
		$opportunityCustom = $this->natksort($opportunityCustom);
		
		$this->ss->assign('accountCustom',$accountCustom);
		$this->ss->assign('contactCustom',$contactCustom);
		$this->ss->assign('leadCustom',$leadCustom);
		$this->ss->assign('opportunityCustom',$opportunityCustom);
		$this->ss->assign('fieldExist',$fieldExist);
		$this->ss->assign('fieldName',$fieldName);
		$this->ss->assign('tableName',$tableName);
		$this->ss->assign('editField',$_REQUEST['editField']);
		$this->ss->display('custom/modules/Administration/tpls/viewcustomfields.tpl');
		
	}
	
	function generateCustomFields($moduleName,$parCliOpportunity=''){
		
		global $current_language;
		
		$moduleObj= BeanFactory::getBean($moduleName);
		$fieldDef = $moduleObj->getFieldDefinitions();

		foreach ($fieldDef as $fieldName => $fieldValue){
			if (array_key_exists('bluebook', $fieldValue)){
				$customFieldArray[$fieldName]['name'] = $fieldValue['name'];
				$customFieldArray[$fieldName]['type'] = $fieldValue['type'];
				$customFieldArray[$fieldName]['vname'] = $fieldValue['vname'];
				$customFieldArray[$fieldName]['advance_search'] = $fieldValue['advance_search'];
				if(array_key_exists('parent_opportunity', $fieldValue)){
					$customFieldArray[$fieldName]['parent_opportunity'] = $fieldValue['parent_opportunity'];
				}
				else if(array_key_exists('client_opportunity', $fieldValue)){
					$customFieldArray[$fieldName]['client_opportunity'] = $fieldValue['client_opportunity'];
				}
			}
		}
		
		$langLabels = return_module_language($current_language,$moduleName);
		foreach ($customFieldArray as $fieldName => $fieldValue){
			$vname = $fieldValue['vname'];
			if(array_key_exists($vname, $langLabels)){
				$getName = $langLabels[$vname];							
			}
			else{
				$getName = '';
			}
			$customFieldArray[$fieldName]['label'] = $getName;
		}		
		return $customFieldArray;		
	}

	//Sort Array Keys in natural Order keep maintating the keys if they are string too
	function natksort($array)
	{
		$keys = array_keys($array);
		natsort($keys);
	
		foreach ($keys as $k)
		{
			$new_array[$k] = $array[$k];
		}
	
		return $new_array;
	}
}