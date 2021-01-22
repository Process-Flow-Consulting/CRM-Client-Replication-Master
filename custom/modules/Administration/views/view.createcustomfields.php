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
class AdministrationViewCreatecustomfields extends SugarView {
	/**
	 * default constructor
	 * @author Shashank Verma
	 * @date 04-08-2014
	 */
	private $moduleCount;
	
	function AdministrationViewCreatecustomfields(){
		parent::SugarView();
		
		$accountCustom = $this->generateCustomFields('Accounts');
		
		$contactCustom = $this->generateCustomFields('Contacts');
		
		$leadCustom = $this->generateCustomFields('Leads');
		
		$parOpportunityCustom = $this->generateCustomFields('Opportunities','parent_opportunity');
		
		$cliOpportunityCustom = $this->generateCustomFields('Opportunities','client_opportunity');
		
		$encodedArray['Account'] = $accountCustom;
		$encodedArray['Contact'] = $contactCustom;
		$encodedArray['Lead'] = $leadCustom;
		$encodedArray['parent_opportunity'] = $parOpportunityCustom;
		$encodedArray['client_opportunity'] = $cliOpportunityCustom;
		
		
		$this->moduleCount = json_encode($encodedArray);

	}
	/**
	 * default display function
	 * use to display the edit view
	 * @see SugarView::display()
	 * @author Shashank Verma
	 * @date 04-08-2014
	 */
	function display() {
		global $mod_strings,$app_list_strings,$db;
		
		$process_path = '';
		$fieldsString = 'fields';
		$editField = isset($_REQUEST['editMode'])? $_REQUEST['editMode']: 0;
		$editFieldName = isset($_REQUEST['customEditfield'])? $_REQUEST['customEditfield']: '';
		$editFieldType = isset($_REQUEST['customFieldType'])? $_REQUEST['customFieldType']: 'noType';
		$edit = 0;
		$ADVANCE_SEARCH_VALUE = 0;
		$error_count = 1;
		$parCliOpportunity = '';
		$opportunitySelected = isset($_REQUEST['user_modules'])? $_REQUEST['user_modules'] : '';

		//Module DropDown
		$moduleName = array (
				'Account' => $mod_strings['LBL_MODULE_ACCOUNTS'],
				'Contact' => $mod_strings['LBL_MODULE_CONTACTS'],
				'Lead' => $mod_strings['LBL_MODULE_LEADS'],
				'parent_opportunity' => $mod_strings['LBL_MODULE_OPPORTUNITY_PARENT'],
				'client_opportunity' => $mod_strings['LBL_MODULE_OPPORTUNITY_CLIENT'],
				
		);
		
		//Type Dropdown
		$type = array (
				'varchar' => $mod_strings['LBL_TYPE_TEXT'],
				'enum' => $mod_strings['LBL_TYPE_DROPDOWN'],
				'multienum' => $mod_strings['LBL_TYPE_MULTI_SELECT_DROP_DOWN']
		);
		
		$process_path = 'custom/Extension/modules/';
		$fieldlabel = !empty($_POST['create_label'])? $_POST['create_label']: '';
			
		//Set Module Path for vardef Generator
		if(isset($_REQUEST['user_modules'])){
			switch($_REQUEST['user_modules']){
				case 'Account': $modulePath = 'Accounts';
				break;
				case 'Contact': $modulePath = 'Contacts';
				break;
				case 'Lead': $modulePath = 'Leads';
				break;
				case 'parent_opportunity': $modulePath = 'Opportunities'; $_REQUEST['user_modules'] = 'Opportunity';
				break;
				case 'client_opportunity': $modulePath = 'Opportunities'; $_REQUEST['user_modules'] = 'Opportunity';
				break;
				
			}
		
			$vardefPath = $process_path.$modulePath.'/Ext/Vardefs/';
			$languagePath = $process_path.$modulePath.'/Ext/Language/';
			$vardefFileName = $vardefPath.'_usercustom_fields.php';
			$languageFileName = $languagePath.'_en_us.usercustom_lang.php';
			$dropDownFileName = 'custom/Extension/application/Ext/Language/_en_us.usercustom_options.php';
		}

		//if the form post by click on save button
		if (isset($_POST['button']) && $_POST['button'] == 'Save'){
			
				//Check if Vardef File & Language File Exists
				if(file_exists($vardefFileName) && file_exists($languageFileName)){
					$vardefHeader = file_get_contents($vardefFileName);
					$langHeader = file_get_contents($languageFileName);
					
					//If field in edit mode
					if($editField == 1){
						$finalVardef = $this->fieldEdit($_POST['customEditfield'],$vardefHeader);
						sugar_file_put_contents($vardefFileName,$finalVardef);
						$vardefHeader = file_get_contents($vardefFileName);
						$finalLanguage = $this->fieldEdit($_POST['customEditfield'],$langHeader,'lang');
						sugar_file_put_contents($languageFileName,$finalLanguage);
						$langHeader = file_get_contents($languageFileName);
						$edit = 1;
					}
					
					if($modulePath == 'Opportunities'){
						$parCliOpportunity = $_POST['user_modules'];	
					}
	
					$createField = $this->createCustomVardef($_POST,'file_exist',$modulePath,$edit,$_POST['customEditfield'],$parCliOpportunity);						
					
				}
				//If file doesn't Exist the simply create a file
				else{
					$createField = $this->createCustomVardef($_POST,'file_not_exist',$modulePath,0,'',$parCliOpportunity);
				}
				if(!empty($createField)){
				    //Only Work if Field is Enum or multienum
				    if(array_key_exists('options', $createField)){
                        //Check if type is dropdown
                        $dropHeader = file_get_contents($dropDownFileName);
                        if($edit == 1){
                            $finalDropOpt = $this->fieldEdit($_POST['customEditfield'],$dropHeader,'drop',$modulePath);
                        }
                        else{
                            $finalDropOpt = $this->fieldEdit($createField['name'],$dropHeader,'drop',$modulePath);
                        }
                        sugar_file_put_contents($dropDownFileName,$finalDropOpt);
                        $dropHeader = file_get_contents($dropDownFileName);
                        $createDropdown = $this->createDropdownOption($_POST,$createField['options']);
                        write_array_to_file('app_list_strings["'.$createField['options'].'"]', $createDropdown, $dropDownFileName, 'w', $dropHeader);
                    }
				    write_array_to_file('dictionary["'.$_REQUEST['user_modules'] .'"]["'.$fieldsString.'"]["'.$createField['name'].'"]', $createField, $vardefFileName, 'w', $vardefHeader);
				    write_array_to_file('mod_strings["'.$createField['vname'].'"]', $fieldlabel, $languageFileName, 'w', $langHeader);
					//Rebuild Cache
					$this->repairSugar($modulePath,$createField,$edit);
					
					SugarApplication::redirect("index.php?module=Administration&action=viewcustomfields&editField={$editField}&fieldName={$createField['name']}&tableName={$modulePath}");
				}
			}
			if($editField == 1){
				
				$moduleSelected = $_REQUEST['user_modules'];
				
				if($opportunitySelected == 'parent_opportunity' || $opportunitySelected == 'client_opportunity'){
					$moduleSelected = $opportunitySelected;
				}
				
				$fieldLabel = $_REQUEST['customFieldLabel'];
				
				if(($editFieldType == 'enum' || $editFieldType == 'multienum') && isset($app_list_strings[$editFieldName."_".$modulePath."_DOM"])){
					
					$fieldOptions = $app_list_strings[$editFieldName."_".$modulePath."_DOM"];
				}
				
				if(isset($_REQUEST['advance_search']) && $_REQUEST['advance_search'] == 'true'){
					$ADVANCE_SEARCH_VALUE = '1';
				}
			}
			
		$this->ss->assign('moduleFieldCount',$this->moduleCount);
		$this->ss->assign('ADVANCE_SEARCH_VALUE',$ADVANCE_SEARCH_VALUE);
		$this->ss->assign('MULTISELECT_OPTIONS',$fieldOptions);
		$this->ss->assign('SELECTED_MODULE',$moduleSelected);
		$this->ss->assign('SELECTED_LABEL',$fieldLabel);
		$this->ss->assign('editFieldName',$editFieldName);
		$this->ss->assign('editFieldType',$editFieldType);
		$this->ss->assign('editMode',$editField);
		$this->ss->assign('moduleName', $moduleName);
		$this->ss->assign('type', $type);
		$this->ss->assign('RETURN_MODULE',(isset($_REQUEST['return_module']))?$_REQUEST['return_module']:'Administration');
		$this->ss->assign('RETURN_ACTION',(isset($_REQUEST['return_action']))?$_REQUEST['return_action']:'index');
		$this->ss->display('custom/modules/Administration/tpls/createcustomfields.tpl');
		
	}
	/**
	 * Vardef generator
	 * use to create vardef of the module
	 * @author Shashank Verma
	 * @date 07-08-2014
	 */
	function createCustomVardef($formArray=array(),$fileExist,$modulePath,$edit=0,$customEditfield='',$parCliOpportunity)
	{
		$createField = array();
		$maxFieldCount = 1;
		$opporCount = 1;
		$opporKey = $parCliOpportunity;
		
		if($fileExist == 'file_exist'){
			$moduleObj= BeanFactory::getBean($modulePath);
			$fieldDef = $moduleObj->getFieldDefinitions();
			
			if($edit == 0){
				
				$fieldDef = $this->natkrsort($fieldDef);				
				
				//Find the count & break the loop to get the max count
				foreach ($fieldDef as $fieldName => $fieldValue){
					if(array_key_exists('bluebook', $fieldValue)){
						$maxFieldName = $fieldName;
						break;
					}
				}
	
				//Strip out the count value
				if(!empty($maxFieldName)){
					$maxFieldCount = substr($maxFieldName, '13');
					$maxFieldCount++;
				}
				
				if($modulePath=='Opportunities'){
					foreach ($fieldDef as $fieldName => $fieldValue){
						if(array_key_exists($opporKey, $fieldValue)){
							$opporCount++;
						}
					}
				}
			}
		}
		//Only 8 Custom Fields are allowed
		if($maxFieldCount <= 12 || ($modulePath=='Opportunities' && $maxFieldCount <= 32 && $opporCount<=16)){
			if(!empty($formArray)){
				if($fileExist == 'file_not_exist'){
					$field_name = 'custom_field_1';
				}
				else if($edit == 0 && $fileExist='file_exist'){
					$field_name = 'custom_field_'.$maxFieldCount;
				}
				else{
					$field_name = $customEditfield;
				}
			
				$createVname = 'LBL_'.strtoupper($field_name);
				$createDropdownOption = $field_name.'_'.$modulePath.'_DOM';
	
				$createField = array (
			    	'name' => $field_name,
					'vname' => $createVname,
					'type' => $formArray['create_type'],
					'reportable' => true,
					'importable' => true,
					'advance_search' => isset($formArray['advance_search'])? 'true' : 'false',
					'bluebook' => 'bluebook',
					'required' => false,									
				);
				
				if($formArray['create_type'] == 'enum' || $formArray['create_type'] == 'multienum'){
					$createField['options'] = $createDropdownOption;
				}
				
				if($formArray['create_type'] == 'multienum'){
					$createField['isMultiSelect'] = true;
				}
				
				if($parCliOpportunity == 'parent_opportunity'){
					$createField['parent_opportunity'] = 'parent_opportunity';
				}
				else if($parCliOpportunity == 'client_opportunity'){
					$createField['client_opportunity'] = 'client_opportunity';
				}
			}
		}
		return $createField;
	}
	
	function createDropdownOption($formArray=array(),$optionName='')
	{
		$dropOptions = array();
		if(!empty($formArray['dropdown_values'])){
			//Copy key same as value in dropdown
			foreach ($formArray['dropdown_values'] as $dropKey=>$dropValue){		
				$dropOptions[$dropValue] = $dropValue;
			}  
		}
		else{
			$dropOptions[' '] = ' ';
		}
		return $dropOptions;
	}
	
	/**
	 * use to repair sugar cache files
	 * @author Shashank Verma
	 * @date 07-08-2014
	 */
	function repairSugar($modulePath,$fieldVardef,$edit=0)
	{	
		global $db;
		
		$rac = new RepairAndClear();
		
  		$rac->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), true,false);
  		
  		/* Check if The new column exist in the database or not 
  		 * if Not then add the column */
  		if($edit == 0){
	  		$tableName = strtolower($modulePath);
	  		$fieldName = $fieldVardef['name'];
	  		$columnSql = "SHOW COLUMNS FROM {$tableName} LIKE '{$fieldName}'";
	  		$result = $db->query($columnSql);
	  		$exists = ($db->getRowCount($result))?TRUE:FALSE;
	  		
	  		if(!$exists){
	  			$db->addColumn($tableName,$fieldVardef);
	  		}
  		}
 	}
	
	/**
	 * use to edit the fields that were created before 
	 * @author Shashank Verma
	 * @date 20-08-2014
	 */
	function fieldEdit($fieldEditName,$vardefHeader,$typeMode='vardef',$modulePath='')
	{
		if($typeMode == 'lang'){
			
			$fieldEditName = 'LBL_'.strtoupper($fieldEditName);
		}
		else if ($typeMode == 'drop'){
		
			$fieldEditName = $fieldEditName.'_'.$modulePath.'_DOM';
		}
		
		$GLOBALS['log']->fatal('FieldEditName',$fieldEditName);
		
		$firstTwolines = explode("\n", $vardefHeader);
		$phpTag = (isset($firstTwolines[0]) && trim($firstTwolines[0]) == '')?'<?php':$firstTwolines[0];
		$createdPart = $firstTwolines[1];
		$fieldsContent = implode("\n", array_slice($firstTwolines, 2));
		
		$fieldVardef = explode(';', trim($fieldsContent));
		//Filter Blank Array key & value
		$fieldVardef = array_filter($fieldVardef);
		
		$GLOBALS['log']->fatal('FieldVardef',$fieldVardef);
 		
		foreach ($fieldVardef as $fieldKey => $fieldValue){
			//Find If the field exist in array then unset that key
			if(strpos($fieldValue, $fieldEditName)){				
				unset($fieldVardef[$fieldKey]);
			}
		}
		$remainingVardef = implode(';',$fieldVardef);
		
		$finalContent = $phpTag."\n".$createdPart."\n";
		
		if(!empty($remainingVardef)){
			$finalContent .= $remainingVardef.';';
		}
		
		return $finalContent;
	}
	
	//Sort Array Keys in Reverse Order keep maintating the natural order in keys if they are string too
	function natkrsort($array)
	{
		$keys = array_keys($array);
		natsort($keys);
	
		foreach ($keys as $k)
		{
			$new_array[$k] = $array[$k];
		}
	
		$new_array = array_reverse($new_array, true);
	
		return $new_array;
	}

	function generateCustomFields($moduleName,$parCliOpportunity='')
	{
		$fieldCounter = 0;
		
		$moduleObj= BeanFactory::getBean($moduleName);
		$fieldDef = $moduleObj->getFieldDefinitions();
		
		if($moduleName=='Opportunities'){
			$moduleOppor = 1;	
		}
		else{
			$moduleOppor = 0;
		}
	
		foreach ($fieldDef as $fieldName => $fieldValue){
			if (($moduleOppor == 0 && array_key_exists('bluebook', $fieldValue)) || ($moduleOppor == 1 && array_key_exists('bluebook', $fieldValue) && array_key_exists($parCliOpportunity, $fieldValue))){
				$fieldCounter++; 
			}
		}
		return $fieldCounter;
	}
}