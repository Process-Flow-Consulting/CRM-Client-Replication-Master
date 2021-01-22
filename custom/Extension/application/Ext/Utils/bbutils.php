<?php
/*****************************************************************************
If this file are subject to the SugarCRM Master Subscription
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
* by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

/**
 * To get the html for structure dropdown
* Added By : Ashutosh
* Date : 1 Oct 2013
* @return array
*/
function getBluebookStructureDom(){

    global $app_list_strings;

    $arStructureDom = array_merge($app_list_strings['structure_residential'],  $app_list_strings['structure_non_building'],$app_list_strings['structure_non_residential']);

    return $arStructureDom;
}
/**
 * To get All the assigned zones 
 * for report 
 * Date : 29 Oct 2013
 * 
 */
function get_all_zones(){
    
    
    $arDomZones = array();
    $obZone = new oss_Zone();
    $arAllZones = $obZone->get_full_list(' oss_zone.name ASC');
//Changes made by parveen badoni on 07/07/2014 to provide valid argument to foreach loop
    if(!empty($arAllZones)) {
    foreach($arAllZones as $obFetchedZone)
    {
    	//$arDomZones[$obFetchedZone->id]=$obFetchedZone->name;
    	$arDomZones[$obFetchedZone->name]=$obFetchedZone->name;
    }	
}
    return $arDomZones;
}
/**
 * set mapping of role to classification
 * @author Mohit Kumar Gupta
 * @date 20-Nov-2013
 * @modified By Mohit Kumar Gupta 17-11-2015
 * update roles and classifications array for bidscope integration BSI-787
 * @param array $data
 * @return void
 */
function setRolesClassifications($data=array()) {
	if(empty($data)) {
		$roleClassificationArr = array(
	        'Sub Contractor' => '',
	        'Supplier' => '',
	        'Miscellaneous' => '',
	        'sub-contractor' => '',
			'Owner' => 'dc8726db-8d12-11e5-8a84-94de80c5f548',
		    'Owner’s Agent' => 'dc8726db-8d12-11e5-8a84-94de80c5f548',
		    'Owner&#39;s Agent' => 'dc8726db-8d12-11e5-8a84-94de80c5f548',
		    'Owner&#039;s Agent' => 'dc8726db-8d12-11e5-8a84-94de80c5f548',
		    'Owner-Reported' => 'd9729023-b463-6a45-0cd3-5034b472584e',
		    'Developer' => 'dc872df7-8d12-11e5-8a84-94de80c5f548',
		    'Landlord' => 'e803b017-41d8-9328-613d-5034b4075da8',
		    'Joint Venture' => 'dc872f6c-8d12-11e5-8a84-94de80c5f548',
		    'Consulting Architect' => '905abcf0-e45b-db83-75c1-5034b459f47c',
			'Architect' => '905abcf0-e45b-db83-75c1-5034b459f47c',
		    'Interior Designer' => '668a4c20-0993-ced7-fec0-5034b4f9e122',
		    'Landscape Architect' => '9f90069d-3b2c-bd4f-82f2-5034b40e3736',
		    'Consultant' => 'dc8730ef-8d12-11e5-8a84-94de80c5f548',
		    'Construction Manager' => '532d1105-b1e5-4752-dbaa-5034b4223ba8',
			'Engineer' => '3de8100a-faae-b29b-7c45-5034b41392b5',
		    'Consulting Engineer' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Civil)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Electrical)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Environmental)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Mechanical)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Plumbing)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
	        'Engineer (Structural)' => '3de8100a-faae-b29b-7c45-5034b41392b5',
		    'Surveyor' => '8b39beba-e449-084b-9f79-5034b47c0347',
			'General Contractor' => '8cd2f5ac-359d-9b2f-d5e2-5034b429aea1',						
			'General Contractor (BB-Bid)' => '8cd2f5ac-359d-9b2f-d5e2-5034b429aea1',			
		);
	} 
	$GLOBALS['db']->query('DELETE FROM config where category = "instance" AND name="role_classifications" ');
	if(count($roleClassificationArr)>0){
		$roleClassificationStr = base64_encode(json_encode($roleClassificationArr));
		$obAdmin=new Administration();
		//reset the role classification
		$obAdmin->saveSetting('instance','role_classifications',$roleClassificationStr);
	}	
}
/**
 * get mapping of role to classification
 * @author Mohit Kumar Gupta
 * @date 20-Nov-2013
 * @return array
 */
function getRolesClassifications() {
	//get saved RolesClassifications
	$obAdmin = new Administration ();
	$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );		
	$arSelectedClass = $arAdminData->settings['instance_role_classifications'];
	$arSelectedId = json_decode(base64_decode($arSelectedClass));
	$arSavedRolesClassifications = array();
	if ($arSelectedClass != null) {
		$arSavedRolesClassifications = (array) $arSelectedId;
	}
	return $arSavedRolesClassifications;	
}
/**
 * get mapping of target classifications
 * @author Mohit Kumar Gupta
 * @date 20-Nov-2013
 * @return array
 */
function getTargetClassifications() {
	$obAdmin = new Administration ();
    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
    $arSelectedClass = $arAdminData->settings['instance_target_classifications'];
    $arSelectedId = json_decode(base64_decode($arSelectedClass));
    $obTargetClass = new oss_Classification();
    $arSavedTargetClass = $obTargetClass->get_full_list(' description ASC',' oss_classification.id IN ("'.implode('","',$arSelectedId).'")');
	return $arSavedTargetClass;
}
/**
 * use for getting classifications corresponding to account id
 * @author Mohit Kumar Gupta
 * @date 20-Nov-2013
 * @param string $accountId
 * @return array
 */
function getAccountClassifications($accountId='') {
	global $db;
	$data = array();
	if(!empty($accountId)){
		$stGetAccountsClassifications = ' SELECT group_concat(oc.id) as classifications_id ';
		$stGetAccountsClassifications .= ' FROM oss_classifion_accounts_c  oca
			LEFT JOIN oss_classification oc ON oc.id=oss_classi48bbication_ida AND oc.deleted=0
			WHERE  oca.oss_classid41cccounts_idb = '.$db->quoted($accountId).'  AND oca.deleted = 0
			GROUP BY oca.oss_classid41cccounts_idb  ';
        $rsResult = $db->query($stGetAccountsClassifications);
        $arAccountClsfication = $db->fetchByAssoc($rsResult);
        $data = explode(",", $arAccountClsfication['classifications_id']);
    }
    return $data;
}
/**
 * use for find contact if it is default client contact for that client
 * or this client have only single client contact
 * 
 * @author Mohit Kumar Gupta
 *         @date 16-Dec-2013
 * @param string $accountId            
 * @return array
 */
function getDefaultFirstAccountContact($accountId = '')
{
    global $db;
    $arResultData['contact_id'] = '';
    $arResultData['contact_name'] = '';
    if (!empty($accountId)) {
        $selectContactsSql = "select
				count(ac_con.account_id) total_contact
				,GROUP_CONCAT(case default_contact when 1 then contacts.id else '' end  SEPARATOR '') def_con_id
				,GROUP_CONCAT(case default_contact when 1 then LTRIM(RTRIM(CONCAT(IFNULL(contacts.salutation,''),' ',IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,'')))) else '' end SEPARATOR '') def_con_name
				,contacts.id contact_id
				,LTRIM(RTRIM(CONCAT(IFNULL(contacts.salutation,''),' ',IFNULL(contacts.first_name,''),' ',IFNULL(contacts.last_name,'')))) concat_name
				FROM accounts_contacts ac_con
				JOIN contacts on ac_con.contact_id = contacts.id AND contacts.deleted=0
				WHERE ac_con.deleted=0 AND ( ac_con.account_id = '" . $accountId . "') AND contacts.visibility='1'";
        $ContactsResult = $db->query($selectContactsSql);
        $row = $db->fetchByAssoc($ContactsResult);

		//if total contact linked to this account is one then we have to take it either it is default or not
		if ($row['total_contact'] == 1) {
			$arResultData['contact_id'] = $row['contact_id'];
			$arResultData['contact_name'] = $row['concat_name'];
		} elseif ($row['total_contact'] > 1 && !empty($row['def_con_id']) && !empty($row['def_con_name'])) {
			//if total contact linked to this account is more then one then we have to take
			//only default contact out of them and having non null values
			$arResultData['contact_id'] = $row['def_con_id'];
			$arResultData['contact_name'] = $row['def_con_name'];
		}
	}
	return $arResultData;
}

/**
 * Function to get the Target Classification Array
 * @added By : Ashutosh
 * @date : 15 Jan 2013
 */
function getTargetClassDom(){   
    //Get Role Classifications
    $rolesClassificationsArr = getRolesClassifications();
    $countRolesClassifications = count($rolesClassificationsArr);
    //if there are no role class then add default 
    if ($countRolesClassifications == 0) {
        setRolesClassifications();
        $rolesClassificationsArr = getRolesClassifications();
        $countRolesClassifications = count($rolesClassificationsArr);
    }     	
    //get all the target class defined in instance
    $arSavedTargetClass = getTargetClassifications();
    $arSavedTargetClassifications = array();
    //Changes made by parveen badoni on 07/07/2014 to provide valid argument to foreach loop
    if(!empty($arSavedTargetClass)) {
        foreach($arSavedTargetClass as $obSavedClass){        
            $arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->id ;
        }
    }
    $countSavedTargetClassifications = count($arSavedTargetClassifications);
        	
    //get saved roles and targetClassifications start
    $roleTargetClassificationArr = array_unique(array_merge(array_values($rolesClassificationsArr), array_values($arSavedTargetClassifications)));
    
    $obTargetClass = new oss_Classification();
    //no need to add team sql 
    $obTargetClass->disable_row_level_security = true;   
    //get all the class list      
    $arSavedTargetClass = $obTargetClass->get_full_list(' description ASC',' oss_classification.id IN ("'.implode('","',$roleTargetClassificationArr).'")');
    
    $arSavedTargetClassifications = array(''=>'None');
    //set array of target classes
    foreach($arSavedTargetClass as $obSavedClass){              	
        $arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->description ;
    }
    
    return $arSavedTargetClassifications;   	
	
}
/**
 * set visibility 1 to client and client contact for the bidder
 * @param string $bidderId
 * @param string $accountId
 * @param string $contactId
 * @author Mohit Kumar Gupta
 * @date 28-01-2014
 */
function setBidderVisibility($accountId='', $contactId='', $bidderId=''){
    global $db;
    
    //if bidder id is set and account or contact id not set 
	if (!empty($bidderId) && (empty($accountId) || empty($contactId))) {
		$bidderQuery = "SELECT account_id,contact_id FROM oss_leadclientdetail WHERE id='".$bidderId."'";
		$bidderResult = $db->query($bidderQuery);
        $bidderData = $db->fetchByAssoc($bidderResult);
        if (empty($accountId) && !empty($bidderData['account_id'])) {
        	$accountId = $bidderData['account_id'];
        }
        if (empty($contactId) && !empty($bidderData['contact_id'])) {
            $contactId = $bidderData['contact_id'];
        }
	}
	//update accounts visibility to 1 if account id is set
	if (!empty($accountId)) {
		$accountUpdateQuery = "UPDATE accounts SET visibility='1' WHERE id='".$accountId."'";
		$db->query($accountUpdateQuery);
	}
	//update contact visibility to 1 if contact id is set
	if (!empty($contactId)) {
	    $contactUpdateQuery = "UPDATE contacts SET visibility='1' WHERE id='".$contactId."'";
	    $db->query($contactUpdateQuery);
	}
}

/**
 * get product catalog create/update access
 * @author Mohit Kumar Gupta
 * @date 17-04-2014
 * @return boolean
 */
function getProductCatalogUpdateAccess(){
    $obAdmin = new Administration ();
    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
    //access allowed
    $flag = true;
    //retrieve product template permission flag value
    if (isset($arAdminData->settings['instance_product_template_permission_flag']) && $arAdminData->settings['instance_product_template_permission_flag'] == '1') {
        //access not allowed
        $flag = false;
    }
    return $flag;
}

/**
 * get unit of measure drop down
 * @author Mohit Kumar Gupta
 * @date 22-05-2014
 * @return array
 */
function getSavedUnitOfMeasure(){
    $obAdmin = new Administration ();
    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
    //default unit of measure selection
    $unitOfMeasureSettingFlag = 'project_pipeline';
    //retrieve product template permission flag value
    if (isset($arAdminData->settings['instance_unit_of_measure_type_setting']) && !empty($arAdminData->settings['instance_unit_of_measure_type_setting'])) {
        //access not allowed
        $unitOfMeasureSettingFlag = $arAdminData->settings['instance_unit_of_measure_type_setting'];
    }
    $obUnitOfMeasure = new oss_UnitOfMeasure();
    //no need to add team sql
    $obUnitOfMeasure->disable_row_level_security = true;
    //get all the class list
    $where = " unit_of_measure_main_type = '".$unitOfMeasureSettingFlag."'";
    $arSavedUnitOfMeasure = $obUnitOfMeasure->get_full_list(' name ASC', $where);
    $arSavedUnitOfMeasureArray = array(''=>'');
    //set array of target classes
    foreach($arSavedUnitOfMeasure as $obSavedUnitOfMeasure){
        $arSavedUnitOfMeasureArray[$obSavedUnitOfMeasure->id] = $obSavedUnitOfMeasure->name ;
    }
    
    return $arSavedUnitOfMeasureArray;
}
/**
 * get unit of measure setiing type
 * @author Mohit Kumar Gupta
 * @date 27-05-2014
 * @return string
 */
function getUnitOfMeasureSettingType(){
    $obAdmin = new Administration ();
    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
    //default unit of measure selection
    $unitOfMeasureSettingFlag = 'project_pipeline';
    //retrieve product template permission flag value
    if (isset($arAdminData->settings['instance_unit_of_measure_type_setting']) && !empty($arAdminData->settings['instance_unit_of_measure_type_setting'])) {
        //access not allowed
        $unitOfMeasureSettingFlag = $arAdminData->settings['instance_unit_of_measure_type_setting'];
    }
    return $unitOfMeasureSettingFlag;
}


/**
 * Get INstance Sales Tax Settings
 */
function salesTaxSettings(){  
    $obAdmin = new Administration ();
    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
    $salesTaxFlag = 'per_item';
    if (isset($arAdminData->settings['instance_sales_tax_setting'])) {
        $salesTaxFlag = $arAdminData->settings['instance_sales_tax_setting'];
    }
    
    return $salesTaxFlag;
}

/**
 * Function to replace the non-printable chars
 * @author Ashutosh
 * @date 19 June 2014
 * @param string $stString - String to clean
 * @param string $stReplace - char to be replaced
 * @return string
 */
function cleanSpecialChars($stString,$stReplace=''){

    $stString=preg_replace('/[\x00-\x1F\x80-\xFF]/', $stReplace, $stString);

    return $stString;

}

/**
 * Function to append custom fields in edit,detail & search view
 * @author Shashank Verma
 * @date 5 Aug 2014
 */
function appendFieldsOnViews(&$editView,&$detailView,&$searchDefs,&$listDefs,&$searchFields,$moduleName,$viewName,$parentClientOpp='')
{
    $customFieldArray = readModuleFields($moduleName,$viewName,$parentClientOpp);

    if(!empty($customFieldArray) && !empty($editView)){

        $editView['panels']['lbl_custom_information'] = $customFieldArray;
    }

    if(!empty($customFieldArray) && !empty($detailView)){

        $detailView['panels']['lbl_custom_information'] = $customFieldArray;
    }

    if(!empty($customFieldArray) || !empty($searchDefs) ||  !empty($searchFields)){

        foreach ($customFieldArray as $fieldName=>$fieldValue){

            $searchDefs['layout']['advanced_search'][] = $fieldValue;
            $searchFieldsCustom[$fieldName] = array('query_type' => 'default');
        }

        if(!empty($searchFieldsCustom)){
            $searchFields =  array_merge($searchFields,$searchFieldsCustom);
        }

    }

    //Modified by mohit kumar gupta on 16th June 2017
    //refference to BSI-883
    if(!empty($customFieldArray) && !empty($listDefs)){
        foreach ($customFieldArray as $fieldName=>$fieldValue){
            $listDefs[strtoupper($fieldValue['name'])] = array(
                'width' => '10%',
                'label' => $fieldValue['label'],
                'default' => false
            );
        }
    }

}
/**
 * Function to return array of Custom Fields where key bluebook exist
 * @author Shashank Verma
 * @date 5 Aug 2014
 * @param array  $moduleName - Name of the module
 * @param array $viewName - whether detail_view,edit_view,seach_view
 * @return string
 */
function readModuleFields($moduleName,$viewName,$parentClientOpp)
{
	$fieldDef = array();
	$customFieldArray = array();

	$moduleObj= BeanFactory::getBean($moduleName);
	
	if($moduleName == 'Opportunities'){
		
		$fieldOpportunity = 1;
	}
	else{
		
		$fieldOpportunity = 0;
	}
		
	//Create a Custom Fields Array where 'Bluebook' key exist
	if(isset($moduleObj) && !empty($moduleObj)){
		$fieldDef = $moduleObj->getFieldDefinitions();
		$counter = 0;
		$subCount = 0;
		
		ksort($fieldDef);
// 		echo "<pre>";
// 		print_r($fieldDef);
// 		die;
		if(($viewName == 'EditView' || $viewName == 'DetailView')){
			foreach ($fieldDef as $fieldName => $fieldValue){
				if ((array_key_exists('bluebook', $fieldValue) && $fieldOpportunity == 0) || (array_key_exists('bluebook', $fieldValue) && $fieldOpportunity == 1 && array_key_exists($parentClientOpp, $fieldValue))) {
					if($subCount < 2){
						$customFieldArray[$counter][$subCount]['name'] = $fieldValue['name'];
						$customFieldArray[$counter][$subCount]['label'] = $fieldValue['vname'];
						$subCount++;
						//Only Allow 2 value array in a Counter array
						if($subCount == 2){
							$subCount = 0;
							$counter++;
						}
					}	
				}
			}	
		}
		//If View is Search then only return custom Array
		else if ($viewName == 'SearchDefs'){
			foreach ($fieldDef as $fieldName => $fieldValue){
				if (array_key_exists('bluebook', $fieldValue)){
					if($fieldValue['advance_search'] == 'true'){
						$customFieldArray[$fieldName]['name'] = $fieldValue['name'];
						$customFieldArray[$fieldName]['label'] = $fieldValue['vname'];
					}
				}
			}
		}
                //If View is list view then only return custom Array
                //Modified by mohit kumar gupta on 16th June 2017
                //refference to BSI-883
                else if ($viewName == 'ListDefs'){
			foreach ($fieldDef as $fieldName => $fieldValue){
				if (array_key_exists('bluebook', $fieldValue)){
                                    //special case for opportunity module
                                    if($moduleName == 'Opportunities'){ 
                                        //include custom fields only for parent opportunity
                                        if(array_key_exists('parent_opportunity', $fieldValue)){
                                            $customFieldArray[$fieldName]['name'] = $fieldValue['name'];
                                            $customFieldArray[$fieldName]['label'] = $fieldValue['vname'];
                                        }
                                    } else {
                                        $customFieldArray[$fieldName]['name'] = $fieldValue['name'];
                                        $customFieldArray[$fieldName]['label'] = $fieldValue['vname'];
                                    }                                    
				}
			}
		}
	}
	return $customFieldArray;		
}
/**
 * set the field mapping of QuickBooks module with Project Pipeline modules
 * @author Mohit Kumar Gupta
 * @date 20-08-2014
 * @param string $mappingName
 * @param array $fieldMappingData
 * @return void
 */
function setQBInstanceSettings ($mappingName=null, $fieldMappingData = array()){
    if (!empty($mappingName) && !empty($fieldMappingData)) {
    	$fieldMappingData = base64_encode(json_encode($fieldMappingData));
        $obAdmin=new Administration();
        //set the field mapping of QuickBooks module with Project Pipeline modules
        $obAdmin->saveSetting('instance',$mappingName,$fieldMappingData);
        return true;
    } else {
    	return false;
    }
    
}
/**
 * get the field mapping of QuickBooks module with Project Pipeline modules
 * @author Mohit Kumar Gupta
 * @date 20-08-2014
 * @param string $mappingName
 * @return array
 */
function getQBInstanceSettings ($mappingName=null){
    global $fieldMapping;
    $arSavedFieldMapping = array();
    //if the var is field mapp
    if ($mappingName == 'quickbooks_field_mapping') {

        $stMapStoredType = getQBInstanceSettings('quickbooks_mapping_type');

        $arSavedFieldMappingType = trim($stMapStoredType != '')?$stMapStoredType:'default';

        if ($arSavedFieldMappingType == "custom") {

            // get all fields mapp
            $arClientData = getQBInstanceSettings('quickbooks_customer_mapping');
            $arFinalMapping['customer'] = isset($arClientData['customer']) ?$arClientData['customer']:$fieldMapping['customer'];

            // get Client Opportunity Mapp
            $arClientOppData = getQBInstanceSettings('quickbooks_job_mapping');
            $arFinalMapping['job'] = isset($arClientOppData['job']) ?$arClientOppData['job']:$fieldMapping['job'];

            // get inventory Mapp
            $arProductCatalogData = getQBInstanceSettings('quickbooks_inventory_mapping');
            $arFinalMapping['inventory'] = isset($arProductCatalogData['inventory']) ?$arProductCatalogData['inventory']:$fieldMapping['inventory'];

            // get estimate Mapp
            $arEstimateData = getQBInstanceSettings('quickbooks_estimate_mapping');
            $arFinalMapping['estimate'] = isset($arEstimateData['estimate']) ?$arEstimateData['estimate']:$fieldMapping['estimate'];

            // get invoice Mapp
            $arInvoiceData = getQBInstanceSettings('quickbooks_invoice_mapping');
            $arFinalMapping['invoice'] = isset($arInvoiceData['invoice']) ?$arInvoiceData['invoice']:$fieldMapping['invoice'];

            $arSavedFieldMapping = $arFinalMapping;

        } else
        if ($arSavedFieldMappingType == "default") {
            global $fieldMapping;
            foreach ($fieldMapping as $key=>$arValue){
                foreach ($arValue as $stKey => $stValue){
                    if(strstr($stKey, 'do_not_map')){
                        unset($fieldMapping[$key][$stKey]);
                    }
                }
                 
            }
            $arSavedFieldMapping = $fieldMapping;
        }
    } else
    if (! empty($mappingName)) {
        // get the field mapping of QuickBooks module with Project Pipeline
        // modules
        $obAdmin = new Administration();
        $arAdminData = $obAdmin->retrieveSettings('instance', true);
        $mappingName = 'instance_' . $mappingName;
        $fieldMappingData = $arAdminData->settings[$mappingName];
        if ($fieldMappingData != null) {
            $arSavedFieldMapping = json_decode(
                    base64_decode($fieldMappingData), true);
        }
    }
    return $arSavedFieldMapping;
}
/**
 * get the dropdown of accounts from Quickbook Account Module
 * @author Shashank Verma
 * @date 02-09-2014
 * @return array
 */
function getQBAccount()
{
	global $db;
	$query = "SELECT id, fullname, account_type, account_number FROM oss_quickbooksaccount where deleted = 0";
	$result = $db->query($query, false);
	$accountDetails = array(''=>'-none-');
	
	while(($row = $db->fetchByAssoc($result)) != null){
		$accountDetails[$row['id']] = $row['account_number'].' - '.$row['fullname'].' - '.$row['account_type'];
	}
	return $accountDetails;
}
/**
 * show/hide the quickbook account fields 
 * @author Shashank Verma
 * @date 03-09-2014
 * @return array
 */
function quickbookAccountFields($viewDetails,$beanVal)
{
	if($beanVal->quickbook_type == 'Inventory' || $beanVal->quickbook_type == 'Inventory-Assembly'){
		$allowAll = 1;
		$setAsset = array('name' => 'quickbook_assets_account',	'label' => 'LBL_QUICKBOOK_ASSESTS_ACCOUNT');	
		$setCOGS = array('name' => 'quickbook_cogs_account','label' => 'LBL_QUICKBOOK_COGS_ACCOUNT');
	}
	else{
		$allowAll = 0;
		$setExpense = array('name' => 'quickbook_expense_account','label' => 'LBL_QUICKBOOK_EXPENSE_ACCOUNT');
	}
	if($beanVal->id && ($beanVal->quickbook_type != ''))
	{
		$viewDetails['panels']['default'][] = array(
				array(
						'name' => 'quickbook_income_account',
						'label' => 'LBL_QUICKBOOK_INCOME_ACCOUNT',
				),
				$setExpense
		);
		if($allowAll == 1){
			$viewDetails['panels']['default'][] = array(
				$setCOGS,
				$setAsset
			);
		}
	}
}

/**
 * function to check if do not sync defined in Project Pipeline to 
 * Quickbooks
 * @author Ashutosh
 * @param string $stBean - module name
 * @param detailView $obDetailView - DetailView class Object
 */
function addPushToQuickBooksButton($obFocus,&$obDetailView){
    
    $isRemove = false;
    $isAdd = false;        
    
    // Get Configuration saved from admin
    $obAdmin = BeanFactory::getBean('Administration');
    $obAdmin->disable_row_level_security = true;
    $arAdminData = $obAdmin->retrieveSettings('instance', true);
    $arSyncConfig = json_decode(base64_decode($arAdminData->settings['instance_quickbooks_sync_configuration']), true);
   
    $stBean = $obFocus->getObjectName();
    if (trim($arSyncConfig) !== '') {        
        // get sync configuration var
        switch ($stBean) {
            case 'Account':
                $stMapIndex = 'pp_qb_client_sync';                
                break;
            case 'Opportunity':
                $stMapIndex = 'pp_qb_client_opp_sync';                
                break;
            case 'ProductTemplate':
                $stMapIndex = 'pp_qb_catalog_sync';                
                break;
            case 'Quote':
                $stMapIndex = 'pp_qb_proposal_sync';                
                break;
        }
        // if not sync is defined do not display the Push to QB button
        if ($arSyncConfig[$stMapIndex] == 'notsync') {
            $isRemove = true;
        } else {
            //add the Push to QB button 
            $isAdd = true;
        }
        
        $bFound = false;
        //itrate through the button and remove the Push to QB one
        foreach ($obDetailView->defs['templateMeta']['form']['buttons'] as $stKey => $arVal) {
            if (isset($arVal['customCode']) && is_array($arVal)) {
                if ( stristr($arVal['customCode'], 'pushToQuickBook') !== false) {
                    $bFound = true;
                    //check do we need to remove this button
                    if($isRemove || $isAdd){
                        $bFound = false;
                        unset($obDetailView->defs['templateMeta']['form']['buttons'][$stKey]);
                    }
                    //found an entry
                    
                }
            }
        }
        //check if we need to add this button and we didn't found it in defs
        if($isAdd && !$bFound){
            //add button to the defs
            $obDetailView->defs['templateMeta']['form']['buttons'][]= array('customCode' => '<input type="button" class="button" name="pushToQuickBook"  id="pushToQuickBook" value="{$APP.LBL_PUSH_TOQUICKBOOK}"  onclick="return PushToQuickbook(\'{$fields.id.value}\',\''.$stBean.'\',\''.$arSyncConfig['sync_record_priority'].'\');" >',);
        }
    }
}
?>
