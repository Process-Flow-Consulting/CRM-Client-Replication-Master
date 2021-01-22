<?php
require_once 'include/ListView/ListViewSmarty.php';

class CustomListViewSmarty extends ListViewSmarty{
	
	public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * Display Mass Update
	 * @see ListViewDisplay::getMassUpdate()
	 */
	function getMassUpdate(){
		require_once 'custom/include/CustomMassUpdate.php';
		return new CustomMassUpdate();
	}
	
	/**
	 * Display the actions link
	 *
	 * @param  string $id link id attribute, defaults to 'actions_link'
	 * @return string HTML source
	 */
	protected function buildActionsLink( $id = 'actions_link',  $location = 'top')
	{
	    global $app_strings;
		$closeText = SugarThemeRegistry::current()->getImage('close_inline', 'border=0', null, null, ".gif", $app_strings['LBL_CLOSEINLINE']);
		$moreDetailImage = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
		$menuItems = array();
		$menuItems[] = $this->buildBulkActionButton($location);
		// delete
		if ( ACLController::checkAccess($this->seed->module_dir,'delete',true) && $this->delete )
			$menuItems[] = $this->buildDeleteLink($location);
		// compose email
        if ( $this->email )
			$menuItems[] = $this->buildComposeEmailLink($this->data['pageData']['offsets']['total'], $location);
		// mass update
		$mass = $this->getMassUpdate();
		$mass->setSugarBean($this->seed);
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
            $menuItems[] = $this->buildMassUpdateLink($location);
		// merge
		if ( $this->mailMerge )
		    $menuItems[] = $this->buildMergeLink(null, $location);
		if ( $this->mergeduplicates )
		    $menuItems[] = $this->buildMergeDuplicatesLink($location);
		// add to target list
		if ( $this->targetList && ACLController::checkAccess('ProspectLists','edit',true) )
		    $menuItems[] = $this->buildTargetList($location);
		// export
		if ( ACLController::checkAccess($this->seed->module_dir,'export',true) && $this->export )
			$menuItems[] = $this->buildExportLink($location);

		// assign user
		$mass = $this->getMassUpdate();
		$mass->setSugarBean($this->seed);
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
			$menuItems[] = $this->buildAssignUserLink($location);
		/**
		 * Added by : Ashutosh
		 * Date : 3 Jan 2013
		 * Purpose : to display dedupe option 
		 */ 
		/*
		 if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
			$menuItems[] = $this->builddedupePrjectLink($location);
		*/
		
		/*
		 * Added by : Ashutosh
		 * Date : 17 Jan 2013
		 * Purpose : to display Link Project Leads option  
		 */
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
			$menuItems[] = $this->buildLinkPrjectLink($location);
		/*
		* Added by : Mohit Kumar Gupta
		* Date : 04-02-2014
		* Purpose : to display archive Project Leads option
		*/
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
		    $menuItems[] = $this->buildLinkArchiveLink($location);

		foreach ( $this->actionsMenuExtraItems as $item )
		    $menuItems[] = $item;

		$link = array(
            'class' => 'clickMenu selectActions fancymenu',
            'id' => 'selectActions',
            'name' => 'selectActions',
            'buttons' => $menuItems,
            'flat' => false,
        );
        return $link;
		
	}
	
	/**
	 * Builds the massupdate link
	 *
	 * @return string HTML
	 */
	protected function buildMassUpdateLink($loc = 'top')
	{
		global $app_strings;

        $onClick = "document.getElementById('massupdate_form').style.display = ''; var yLoc = YAHOO.util.Dom.getY('massupdate_form'); scroll(0,yLoc);";
		return "<a href='javascript:void(0)' id=\"massupdate_listview_". $loc ."\" onclick=\"$onClick\">{$app_strings['LBL_MASS_UPDATE']}</a>";

	}
	
	/**
	 * Builds the assign user link
	 *
	 * @return string HTML
	 */
	protected function buildAssignUserLink($loc = 'top')
	{
		global $mod_strings;
		$onClick = "document.getElementById('assignuser_form').style.display = '';document.getElementById('massupdate_form').style.display = 'none'; var yLoc = YAHOO.util.Dom.getY('assignuser_form'); scroll(0,yLoc);";
		return "<a href='javascript:void(0)' id=\"assigne_listview_". $loc ."\"  onclick=\"$onClick\">{$mod_strings['LBL_ASSIGN_USER']}</a>";
	}
	
	function displayEnd() {
		$str = '';
		if($this->show_mass_update_form) {
			if($this->showMassupdateFields){
				$str .= $this->mass->getMassUpdateForm(true);
			}
			
			$str .= $this->assignUserForm();
			
			
			$str .= $this->mass->endMassUpdateForm();
		}
		
		
		return $str;
	}
	
	
	
	/**
	 * Displays the assignn user form
	 */
	function assignUserForm()
	{
		global $app_strings;
		global $current_user;
		global $mod_strings;
	
		if($this->seed->bean_implements('ACL') && ( !ACLController::checkAccess($this->seed->module_dir, 'edit', true) || !ACLController::checkAccess($this->seed->module_dir, 'massupdate', true) ) ){
			return '';
		}
	
		$lang_delete = translate('LBL_DELETE');
		$lang_update = translate('LBL_UPDATE');
		$lang_confirm= translate('NTC_DELETE_CONFIRMATION_MULTIPLE');
		$lang_sync = translate('LBL_SYNC_CONTACT');
		$lang_oc_status = translate('LBL_OC_STATUS');
		$lang_unsync = translate('LBL_UNSYNC');
		$lang_archive = translate('LBL_ARCHIVE');
		$lang_optout_primaryemail = $app_strings['LBL_OPT_OUT_FLAG_PRIMARY'];
		$displayname = $mod_strings['LBL_ASSIGNED_TO_ID'];
	
		$html = "<div id='assignuser_form' style='display:none;'><table width='100%' cellpadding='0' cellspacing='0' border='0' class='formHeader h3Row'><tr><td nowrap><h3><span>" . $mod_strings['LBL_ASSIGN_USER']."</h3></td></tr></table>";
		$html .= "<div id='assign_user_div'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='edit view' id=assign_user_table'>";
	
		$newhtml = '';
		$newhtml .= "<tr>";
		$newhtml .= $this->addAssignedUserID($displayname,  'assigned_user_name');
		$newhtml .="</tr>";
		$html .= $newhtml;
		
		$html .="</table>";
	
		$html .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td class='buttons'><input onclick='return sListView.send_mass_update(\"selected\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\")' type='submit' id='update_button' name='Update' value='{$lang_update}' class='button'>&nbsp;<input onclick='javascript:toggleAssignUserForm();' type='button' id='cancel_button' name='Cancel' value='{$GLOBALS['app_strings']['LBL_CANCEL_BUTTON_LABEL']}' class='button'>";
	
		$html .= "</td></tr></table></div></div>";
	
		$html .= <<<EOJS
<script>
function toggleAssignUserForm(){
	document.getElementById('a_assigned_user_id').value = '';
	document.getElementById('a_assigned_user_name').value = '';
    document.getElementById('assignuser_form').style.display = 'none';
}
</script>
EOJS;
		return $html;
		
	}
	
	function addAssignedUserID($displayname, $varname){
		global $app_strings;
	
		$json = getJSONobj();
	
		$popup_request_data = array(
				'call_back_function' => 'set_return',
				'form_name' => 'MassUpdate',
				'field_to_name_array' => array(
						'id' => 'a_assigned_user_id',
						'user_name' => 'a_assigned_user_name',
				),
		);
		$encoded_popup_request_data = $json->encode($popup_request_data);
		$qsUser = array(
				'form' => 'MassUpdate',
				'method' => 'get_user_array', // special method
				'field_list' => array('user_name', 'id'),
				'populate_list' => array('a_assigned_user_name', 'a_assigned_user_id'),
				'conditions' => array(array('name'=>'user_name','op'=>'like_custom','end'=>'%','value'=>'')),
				'limit' => '30','no_match_text' => $app_strings['ERR_SQS_NO_MATCH']);
	
		$qsUser['populate_list'] = array('a_mass_assigned_user_name', 'a_mass_assigned_user_id');
		$img = SugarThemeRegistry::current()->getImageURL("id-ff-select.png");
		$html = <<<EOQ
		<td width="15%" scope="row">$displayname</td>
		<td ><input class="sqsEnabled sqsNoAutofill" autocomplete="off" id="a_mass_assigned_user_name" name='a_assigned_user_name' type="text" value=""><input id='a_mass_assigned_user_id' name='a_assigned_user_id' type="hidden" value="" />
		<span class="id-ff multiple"><button id="a_mass_assigned_user_name_btn" title="{$app_strings['LBL_SELECT_BUTTON_TITLE']}" type="button" class="button" value='{$app_strings['LBL_SELECT_BUTTON_LABEL']}' name=btn1
				onclick='open_popup("Users", 600, 400, "", true, false, $encoded_popup_request_data);' /><img src="$img"></button></span>
		</td>
EOQ;
		$html .= '<script type="text/javascript" language="javascript">if(typeof sqs_objects == \'undefined\'){var sqs_objects = new Array;}sqs_objects[\'MassUpdate_a_assigned_user_name\'] = ' .
		$json->encode($qsUser) . '; registerSingleSmartInputListener(document.getElementById(\'mass_assigned_user_name\'));
		addToValidateBinaryDependency(\'MassUpdate\', \'a_assigned_user_name\', \'alpha\', false, \'' . $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_ASSIGNED_TO'] . '\',\'a_assigned_user_id\');
		</script>';
	
		return $html;
	}
	
	/**
	 * Modified By : Ashutosh
	 * Date : 3 Jan 2013
	 * Purpose : to add Deduping Option on list view
	 */
	function builddedupePrjectLink(){
		global $mod_strings;
		$onClick ="if(document.MassUpdate.select_entire_list.value ==1){alert('Please select from current screen.');return false;} var frm=$('#MassUpdate');$('#MassUpdate input[name=action]').val('mass_deduping');frm.submit();";

		return "<a href='javascript:void(0)' id=\"assigne_listview_\"  onclick=\"$onClick\">{$mod_strings['LBL_SUGGEST_DEDUPE']}</a>";
		
	}
	
	/**
	 * Modified By : Ashutosh
	 * Date : 17 Jan 2013
	 * Purpose : to add Deduping Option on list view
	 */
	function buildLinkPrjectLink(){
		global $mod_strings;
		$onClick ="if(document.MassUpdate.select_entire_list.value ==1){alert('Please select from current screen.');return false;} var frm=$('#MassUpdate');$('#MassUpdate input[name=action]').val('link_projects');frm.submit();";
	
		return "<a href='javascript:void(0)' id=\"assigne_listview_\"  onclick=\"$onClick\">{$mod_strings['LBL_LINK_CUSTOM_DEDUPE']}</a>";
	
	}
	
	/**
	 * Modified By : Mohit Kumar Gupta
	 * Date : 04 Feb 2014
	 * Purpose : to add Archive Option on list view
	 */
	function buildLinkArchiveLink($loc = 'top'){
	    global $mod_strings;
	    $onClick ="
    		var frm=$('#MassUpdate');   
                 	
           	//create is_archived hidden element	            
            var input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', 'is_archived');
	        input.setAttribute('id', 'is_archived');
            input.setAttribute('value', '1');
    
            //append is_archived element to mass update form element
            //so that mass upadate action automatically update its value to selected records
            document.MassUpdate.appendChild(input);
            if (document.MassUpdate.select_entire_list &&
				document.MassUpdate.select_entire_list.value == 1)
				mode = 'entire';
			else
				mode = 'selected';
            if(sListView.send_mass_update(mode, SUGAR.language.languages['app_strings']['LBL_LISTVIEW_NO_SELECTED'])){
               frm.submit();
	           ajaxStatus.showStatus(SUGAR.language.languages['app_strings']['ARCHIVE_UPDATE_STATUS_MESSAGE']);
            } else {
	           document.MassUpdate.removeChild(input);
	        }
		";
	    
	    return "<a href='javascript:void(0)' id=\"assigne_listview_". $loc ."\"  onclick=\"$onClick\">{$mod_strings['LBL_ARCHIVE']}</a>";	   
	}
}