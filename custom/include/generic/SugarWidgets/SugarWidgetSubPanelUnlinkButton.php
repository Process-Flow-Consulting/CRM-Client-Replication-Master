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






class SugarWidgetSubPanelUnlinkButton extends SugarWidgetField
{
	function displayHeaderCell(&$layout_def)
	{
		return '&nbsp;';
	}

	function displayList(&$layout_def)
	{
		
		global $app_strings, $mod_strings;
        global $subpanel_item_count, $db;

		$unique_id = $layout_def['subpanel_id']."_remove_".$subpanel_item_count; //bug 51512
		
		$parent_record_id = $_REQUEST['record'];
		$parent_module = $_REQUEST['module'];

		$action = 'unlinkop';
		$record = $layout_def['fields']['ID'];
		$current_module=$layout_def['module'];

		//Get Sub Opportunity Count of Project Opportunity
		if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])){
			$parent_opp_sql = "SELECT sub_opp_count FROM opportunities WHERE id='".$_REQUEST['record']."' AND deleted=0";
			$parent_opp_query = $db->query($parent_opp_sql);
			$parent_opp_result = $db->fetchByAssoc($parent_opp_query);
			$sub_opp_count = $parent_opp_result['sub_opp_count'];			 			
		}
		
		$return_module = $_REQUEST['module'];
		$return_action = 'SubPanelViewer';
		$subpanel = $layout_def['subpanel_id'];
		$return_id = $_REQUEST['record'];
		if (isset($layout_def['linked_field_set']) && !empty($layout_def['linked_field_set'])) {
			$linked_field= $layout_def['linked_field_set'] ;
		} else {
			$linked_field = $layout_def['linked_field'];
		}
		$refresh_page = 0;
		if(!empty($layout_def['refresh_page'])){
			$refresh_page = 1;
		}
		$return_url = "index.php?module=$return_module&action=$return_action&subpanel=$subpanel&record=$return_id&sugar_body_only=1&inline=1";
		
		$lang = return_module_language('en_us', 'Opportunities');
		
		$icon_remove_text = strtolower($lang['LBL_UNLINK']);
			
		
		$remove_url = $layout_def['start_link_wrapper']
		. "index.php?module=$parent_module"
		. "&action=$action"
		//. "&record=$parent_record_id"
		//. "&linked_field=$linked_field"
		. "&linked_id=$record"
		. "&return_url=" . urlencode(urlencode($return_url))
		. "&refresh_page=1"
		. $layout_def['end_link_wrapper'];
		
		
		
		//based on listview since that lets you select records
		if($sub_opp_count <= 1){
			$remove_confirmation_text = $app_strings['NTC_UNLINK_SINGLE_SUB_OPP'];
			
			$retStr = '<a href="#"'
					. ' class="listViewTdToolsS1"'
					. "id=$unique_id"
					. " onclick=\"return alert(SUGAR.language.get('Opportunities', 'NTC_UNLINK_SINGLE_SUB_OPP'));\""
					. ">$icon_remove_text</a>";
			
		}else{
			$remove_confirmation_text = $app_strings['NTC_REMOVE_CONFIRMATION'];
			$retStr = '<a href="' . $remove_url . '"'
					. ' class="listViewTdToolsS1"'
					. "id=$unique_id"
					. " onclick=\"return confirm('$remove_confirmation_text');\""
					. ">$icon_remove_text</a>";
		}
		if($layout_def['ListView']) {           
        	return $retStr;            
		}else{
			return '';
		}
	}
}
