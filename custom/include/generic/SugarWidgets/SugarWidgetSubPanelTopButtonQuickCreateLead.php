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




require_once('include/generic/SugarWidgets/SugarWidgetSubPanelTopButton.php');

class SugarWidgetSubPanelTopButtonQuickCreateLead extends SugarWidgetSubPanelTopButton
{

	function __construct(){
		$this->module = '';
		$this->form_value = 'Create';
	}
	function &_get_form($defines, $additionalFormFields = null)
	{
		global $app_strings;
		global $currentModule;

		// Create the additional form fields with real values if they were not passed in
		if(empty($additionalFormFields) && $this->additional_form_fields)
		{
			foreach($this->additional_form_fields as $key=>$value)
			{
				if(!empty($defines['focus']->$value))
				{
					$additionalFormFields[$key] = $defines['focus']->$value;
				}
				else
				{
					$additionalFormFields[$key] = '';
				}
			}
		}

		if(!empty($this->module))
		{
			$defines['child_module_name'] = $this->module;
		}
		else
		{
			$defines['child_module_name'] = $defines['module'];
		}

		$defines['parent_bean_name'] = get_class( $defines['focus']);
		if($defines['parent_bean_name'] == 'bbProjectLeads'){
			$defines['parent_bean_name'] = 'Lead';
		}
		//if($defines['parent_bean_name'] == '')
		$relationship_name = $this->get_subpanel_relationship_name($defines);

		$form = 'form' . $relationship_name;
		$button = '<form onsubmit="return SUGAR.subpanelUtils.sendAndRetrieve(this.id, \'subpanel_' . $defines['subpanel_definition']->name . '\', \'' . addslashes($app_strings['LBL_LOADING']) . '\');" action="index.php" method="post" name="form" id="form' . $form . "\">\n";

		//module_button is used to override the value of module name
		$button .= "<input type='hidden' name='target_module' value='".$defines['child_module_name']."'>\n";
		$button .= "<input type='hidden' name='".strtolower($defines['parent_bean_name'])."_id' value='".$defines['focus']->id."'>\n";

		if(isset($defines['focus']->name))
		{
			$button .= "<input type='hidden' name='".strtolower($defines['parent_bean_name'])."_name' value='".$defines['focus']->name."'>";
			#26451,add these fields for custom one-to-many relate field.
            if(!empty($defines['child_module_name'])){
            	$button .= "<input type='hidden' name='". $relationship_name ."_name' value='".$defines['focus']->name."'>";
            	$childFocusName = !empty($GLOBALS['beanList'][$defines['child_module_name']]) ? $GLOBALS['beanList'][$defines['child_module_name']] : "";
            	if(!empty($GLOBALS['dictionary'][ $childFocusName ]["fields"][$relationship_name .'_name']['id_name'])){
            		$button .= "<input type='hidden' name='". $GLOBALS['dictionary'][ $childFocusName ]["fields"][$relationship_name .'_name']['id_name'] ."' value='".$defines['focus']->id."'>";
            	}
            }
		}
		if(!empty($defines['view']))
		$button .= '<input type="hidden" name="target_view" value="'. $defines['view'] . '" />';
		$button .= '<input type="hidden" name="to_pdf" value="true" />';
        $button .= '<input type="hidden" name="tpl" value="QuickCreate.tpl" />';
		$button .= '<input type="hidden" name="return_module" value="' . $currentModule . "\" />\n";
		$button .= '<input type="hidden" name="return_action" value="' . $defines['action'] . "\" />\n";
		$button .= '<input type="hidden" name="return_id" value="' . $defines['focus']->id . "\" />\n";
		$button .= '<input type="hidden" name="return_relationship" value="' . $relationship_name . "\" />\n";   
		// TODO: move this out and get $additionalFormFields working properly
		if(empty($additionalFormFields['parent_type']))
		{
			if($defines['focus']->object_name=='Contact') {
				$additionalFormFields['parent_type'] = 'Accounts';
			}
			else {
				$additionalFormFields['parent_type'] = $defines['focus']->module_dir;
			}
		}
		if(empty($additionalFormFields['parent_name']))
		{
			if($defines['focus']->object_name=='Contact') {
				$additionalFormFields['parent_name'] = $defines['focus']->account_name;
				$additionalFormFields['account_name'] = $defines['focus']->account_name;
			}
			else {
				$additionalFormFields['parent_name'] = $defines['focus']->name;
			}
		}
		if(empty($additionalFormFields['parent_id']))
		{
			if($defines['focus']->object_name=='Contact') {
				$additionalFormFields['parent_id'] = $defines['focus']->account_id;
				$additionalFormFields['account_id'] = $defines['focus']->account_id;
			}
			else {
				$additionalFormFields['parent_id'] = $defines['focus']->id;
			}
		}

        if(strtolower($defines['child_module_name']) =='contracts') {
            //set variables to account name, or parent account name
            if(strtolower($defines['parent_bean_name']) == 'account' ){
                //if account is parent bean, then get focus id/focus name
                if(isset($defines['focus']->id))$additionalFormFields['account_id'] = $defines['focus']->id;
                if(isset($defines['focus']->name))$additionalFormFields['account_name'] = $defines['focus']->name;
            }elseif(strtolower($defines['parent_bean_name']) == 'quote' ){
                //if quote is parent bean, then get billing_account_id/billing_account_name
                if(isset($defines['focus']->billing_account_id))$additionalFormFields['account_id'] = $defines['focus']->billing_account_id;
                if(isset($defines['focus']->billing_account_name))$additionalFormFields['account_name'] = $defines['focus']->billing_account_name;
            }else{
                if(isset($defines['focus']->account_id))$additionalFormFields['account_id'] = $defines['focus']->account_id;
                if(isset($defines['focus']->account_name))$additionalFormFields['account_name'] = $defines['focus']->account_name;
            }
        }

		$button .= '<input type="hidden" name="action" value="SubpanelCreates" />' . "\n";
		$button .= '<input type="hidden" name="module" value="Home" />' . "\n";
		$button .= '<input type="hidden" name="target_action" value="QuickCreate" />' . "\n";

		// fill in additional form fields for all but action
		foreach($additionalFormFields as $key => $value)
		{
			if($key != 'action')
			{
				$button .= '<input type="hidden" name="' . $key . '" value=\'' . $value . '\' />' . "\n";
			}
		}

		return $button;
	}
	
	/**
	 * get_subpanel_relationship_name
	 * Get the relationship name based on the subapnel definition
	 * @param mixed $defines The subpanel definition
	 */
	function get_subpanel_relationship_name($defines) {
		 $relationship_name = '';
		 if(!empty($defines)) {
		 	$relationship_name = isset($defines['module']) ? $defines['module'] : '';
	     	$dataSource = $defines['subpanel_definition']->get_data_source_name(true);
         	if (!empty($dataSource)) {
				$relationship_name = $dataSource;
				//Try to set the relationship name to the real relationship, not the link.
				if (!empty($defines['subpanel_definition']->parent_bean->field_defs[$dataSource])
				 && !empty($defines['subpanel_definition']->parent_bean->field_defs[$dataSource]['relationship']))
				{
					$relationship_name = $defines['subpanel_definition']->parent_bean->field_defs[$dataSource]['relationship'];
				}
			}
		 }
		 return $relationship_name;
	}
}
?>
