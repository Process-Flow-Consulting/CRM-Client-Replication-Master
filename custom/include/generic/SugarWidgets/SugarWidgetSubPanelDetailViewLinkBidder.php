<?php
//require_once 'include/generic/SugarWidgets/SugarWidgetSubPanelDetailViewLink.php';
require_once('include/generic/SugarWidgets/SugarWidgetField.php');

class SugarWidgetSubPanelDetailViewLinkBidder extends SugarWidgetField{
	
		
	function displayList(&$layout_def)
	{
		
		global $focus;
	
		$module = '';
		$record = '';
		
		if(isset($layout_def['varname']))
		{
			$key = strtoupper($layout_def['varname']);
		}
		else
		{
			$key = $this->_get_column_alias($layout_def);
			$key = strtoupper($key);
		}
		if (empty($layout_def['fields'][$key])) {
			return "";
		} else {
			$value = $layout_def['fields'][$key];
		}
	
	
		if(empty($layout_def['target_record_key']))
		{
			$record = $layout_def['fields']['ID'];
		}
		else
		{
			$record_key = strtoupper($layout_def['target_record_key']);
			$record = $layout_def['fields'][$record_key];
		}
	
		if(!empty($layout_def['target_module_key'])) {
			if (!empty($layout_def['fields'][strtoupper($layout_def['target_module_key'])])) {
				$module=$layout_def['fields'][strtoupper($layout_def['target_module_key'])];
			}
		}
	
		if (empty($module)) {
			if(empty($layout_def['target_module']))
			{
				$module = $layout_def['module'];
			}
			else
			{
				$module = $layout_def['target_module'];
			}
		}
	
		//links to email module now need additional information.
		//this is to resolve the information about the target of the emails. necessitated by feature that allow
		//only on email record for the whole campaign.
		$parent='';
		if (!empty($layout_def['parent_info'])) {
			if (!empty($focus)){
				$parent="&parent_id=".$focus->id;
				$parent.="&parent_module=".$focus->module_dir;
			}
		} else {
			if(!empty($layout_def['parent_id'])) {
				if (isset($layout_def['fields'][strtoupper($layout_def['parent_id'])])) {
					$parent.="&parent_id=".$layout_def['fields'][strtoupper($layout_def['parent_id'])];
				}
			}
			if(!empty($layout_def['parent_module'])) {
				if (isset($layout_def['fields'][strtoupper($layout_def['parent_module'])])) {
					$parent.="&parent_module=".$layout_def['fields'][strtoupper($layout_def['parent_module'])];
				}
			}
		}
		
		/*echo '<pre>';
		print_r($layout_def);
		echo '</pre>';*/
	
		$action = 'DetailView';
		$value = $layout_def['fields'][$key];
		
		if($layout_def['module']!='Accounts'){
			$proview_url = $layout_def['fields']['ACCOUNT_PROVIEW_URL'];
		}else{
			$proview_url = $layout_def['fields']['PROVIEW_URL'];
			//$proview_url = proview_url(array('url' => $layout_def['fields']['PROVIEW_URL']));
		}	
		
		//$proview_icon = proview_url(array('url' => $proview_url));
		$proview_icon = $proview_url;		
		
		$obAccount = new Account();
		$obAccount->retrieve($layout_def['fields']['ACCOUNT_ID']);
		
		if (preg_match('/^[^:\/]*:\/\/.*/', $obAccount->proview_url)) {
			$obAccount->proview_url= $obAccount->proview_url;
		} else {
			$obAccount->proview_url = 'http://' . $obAccount->proview_url;
		}
		
		
		//if field is contact in bidders
		if( $layout_def['name'] == 'lcd_contact'  ){
			$link = ajaxLink("index.php?module=$module&action=$action&record={$record}{$parent}");				
			if($obAccount->visibility == '0'){			
			   $stHrefVal = (trim($obAccount->proview_url) == '' || trim($obAccount->proview_url) ==  'http://' )?'"javascript:void(0)"  class="no_proview" ':"'{$obAccount->proview_url}' target='_blank'";
			   $new_link = '<a  href=' .$stHrefVal . ' >'.$value.'</a>';		
			}else{				
				$new_link = '<a href="' . $link . '" >'.$value.'</a>';
			}
				
			return $new_link;
		}
		
		global $current_user,$app_strings;
		if(  !empty($record) &&
				($layout_def['DetailView'] && !$layout_def['owner_module']
						||  $layout_def['DetailView'] && !ACLController::moduleSupportsACL($layout_def['owner_module'])
						|| ACLController::checkAccess($layout_def['owner_module'], 'view', $layout_def['owner_id'] == $current_user->id)))
		{
			$link = ajaxLink("index.php?module=$module&action=$action&record={$record}{$parent}");
			$new_link = '<a href="' . $link . '" >'."$value</a>";
			if(isset($layout_def['fields']['CONVERTED_TO_OPPR']) && $layout_def['fields']['CONVERTED_TO_OPPR']==1 && $layout_def['module']=='oss_LeadClientDetail'){
				
				if($obAccount->visibility == '0'){					
					$new_link = '<img align="absmiddle" src="custom/themes/default/images/green_money.gif" title="'.$app_strings['LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT'].'"  alt="'.$app_strings['LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT'].'" />'.$proview_icon.'&nbsp;<a href="' . $obAccount->proview_url . '"  target="_blank" >'."$value</a>";
				}else{					
					$new_link = '<img align="absmiddle" src="custom/themes/default/images/green_money.gif" title="'.$app_strings['LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT'].'"  alt="'.$app_strings['LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT'].'" />'.$proview_icon.'&nbsp;<a href="' . $link . '" >'."$value</a>";
				}				
				
			}else{
				if($obAccount->visibility == '0'){
					$stHrefVal = (trim($obAccount->proview_url) == '' || trim($obAccount->proview_url) ==  'http://' )?'"javascript:void(0)"  class="no_proview" ':"'{$obAccount->proview_url}' target='_blank'";
					$new_link = $proview_icon.'&nbsp;<a  href=' . $stHrefVal . ' >'.$value.'</a>';
				}else{
				 $new_link = $proview_icon.'&nbsp;<a href="' . $link . '" >'.$value.'</a>';
				}
				
			}
			return $new_link;
	
		}else{
			return $value;
		}
	
	}
}
?>