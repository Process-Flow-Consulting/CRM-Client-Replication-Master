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




require_once('include/generic/SugarWidgets/SugarWidgetField.php');

class SugarWidgetSubPanelClassificationToggle extends SugarWidgetField
{
	
	/**
	 * @see SugarWidgetField::displayList()
	 */
	function displayList(&$layout_def)
	{
		$stClassifications ='';
		global $current_user;
//Changes made by parveen badoni on 03/07/2014 $arSelectedId defined as array so that it doesnt give any warning when used in implode.
		$arSelectedId = array();
		$arTmpClassification = array();
		$stSingleDivId = 'cls_one_'.$layout_def['fields']['ID'];
		$stMultiDivId = 'all_one_'.$layout_def['fields']['ID'];
		
		
		
		//get classifications of this account
		$obClient = new Account();
		/* $obClient->retrieve($layout_def['fields']['ACCOUNT_ID']);
		 $arClassificationList = $obClient->get_linked_beans('oss_classifation_accounts', 'oss_classification',array('' =>'description','' =>'ASC'));
		 */
		$stSQL = "SELECT oss_classification .id
    					,oss_classification.description
						,oss_classification.name
				 FROM oss_classifion_accounts_c
				 INNER JOIN oss_classification ON oss_classi48bbication_ida  =oss_classification.id AND oss_classification.deleted=0
				WHERE  oss_classid41cccounts_idb = '".$layout_def['fields']['ACCOUNT_ID']."' and oss_classifion_accounts_c.deleted =0
				";
		
		
		if($current_user->is_admin == '1'){
				    $obAdmin = new Administration ();
				    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
				    $obTargetClass = new oss_Classification();
				    $arSelectedClass = $arAdminData->settings['instance_target_classifications'];
				    $arSelectedId = json_decode(base64_decode($arSelectedClass));
				    $stSelectedIds = implode("','",$arSelectedId);
					
				    $classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM config,oss_classification c WHERE  id in ('".$stSelectedIds."')";
				    $classification_filter_result = $GLOBALS['db']->query($classification_filter_query);
				    $classification_filter_count = $GLOBALS['db']->getRowCount($classification_filter_result);
				    				    
		}else{
		    $classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters`		
				 uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE 
				`filter_type`='classification'  AND uf.assigned_user_id = '".$GLOBALS['current_user']->id."' AND uf.`deleted`=0";
			$classification_filter_result = $GLOBALS['db']->query($classification_filter_query);
			$classification_filter_count = $GLOBALS['db']->getRowCount($classification_filter_result);
		}
		
		if($classification_filter_count > 0){
			while($classification_filter_row = $GLOBALS['db']->fetchByAssoc($classification_filter_result)){
				$classification_filter_array[$classification_filter_row['id']] = $classification_filter_row['name'];
			}
				
			$classification_filter = implode("','", $classification_filter_array);
			$stSQL .= " ORDER BY FIELD(oss_classification.name, '$classification_filter') DESC, oss_classification.description ASC  ";
		}else{
			$stSQL .= " ORDER BY oss_classification.description ASC ";  
		}
		
		$rsResult = $GLOBALS['db']->query($stSQL);
		//$arData = $GLOBALS['db']->fetchByAssoc($rsResult);
		
		while($obClassification = $GLOBALS['db']->fetchByAssoc($rsResult)){
			$arTmpClassification[] = $obClassification['description'];
		}
		//echo '<pre>';print_r($arTmpClassification);;
		
		
		if(count($arTmpClassification)  == 1 ){
			$stClassifications = $arTmpClassification[0];
			
		}else	if(count($arTmpClassification) >1 )
		{
		$stClassifications = ' <div id = "'.$stMultiDivId.'" style="display:none"  > 
		<ul style="list-style-type: none;margin:0px;padding:0px;" >
		<li style="list-style-type: none;margin:0px;padding:0px">
			[<a href="javascript:void()" onclick="document.getElementById(\''.$stMultiDivId.'\').style.display=\'none\';document.getElementById(\''.$stSingleDivId.'\').style.display=\'\'" >-</a>]
		</li>
		<li style="list-style-type: none;margin:1px;padding:1px">'
		.implode("</li><li style='list-style-type: none;margin:1px;padding:1px'>", $arTmpClassification).'</li></ul>		
		</div>
					<div  id = "'.$stSingleDivId.'"  > [<a href="javascript:void()" onclick="this.parentNode.style.display=\'none\';document.getElementById(\''.$stMultiDivId.'\').style.display=\'\'" >+</a>]'
							 .$arTmpClassification[0].'</div>
					' ;
		}
		return $stClassifications;
	}
}

?>
