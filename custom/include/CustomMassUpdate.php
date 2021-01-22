<?php
require_once 'include/MassUpdate.php';
class CustomMassUpdate extends MassUpdate {
	
	/**
	 * Displays the massupdate form
	 */
	function getMassUpdateForm($hideDeleteIfNoFieldsAvailable = false) {
		global $app_strings;
		global $current_user;
		
		if ($this->sugarbean->bean_implements ( 'ACL' ) && (! ACLController::checkAccess ( $this->sugarbean->module_dir, 'edit', true ) || ! ACLController::checkAccess ( $this->sugarbean->module_dir, 'massupdate', true ))) {
			return '';
		}
		
		$lang_delete = translate ( 'LBL_DELETE' );
		$lang_update = translate ( 'LBL_UPDATE' );
		$lang_confirm = translate ( 'NTC_DELETE_CONFIRMATION_MULTIPLE' );
		$lang_sync = translate ( 'LBL_SYNC_CONTACT' );
		$lang_oc_status = translate ( 'LBL_OC_STATUS' );
		$lang_unsync = translate ( 'LBL_UNSYNC' );
		$lang_archive = translate ( 'LBL_ARCHIVE' );
		$lang_optout_primaryemail = $app_strings ['LBL_OPT_OUT_FLAG_PRIMARY'];
		
		$field_count = 0;
		
		$html = "<div id='massupdate_form' style='display:none;'><table width='100%' cellpadding='0' cellspacing='0' border='0' class='formHeader h3Row'><tr><td nowrap><h3><span>" . $app_strings ['LBL_MASS_UPDATE'] . "</h3></td></tr></table>";
		$html .= "<div id='mass_update_div'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='edit view' id='mass_update_table'>";
		
		$even = true;
		
		if ($this->sugarbean->object_name == 'Contact') {
			//$html .= "<tr><td width='15%' scope='row'>$lang_sync</td><td width='35%' class='dataField'><select name='Sync'><option value=''>{$GLOBALS['app_strings']['LBL_NONE']}</option><option value='false'>{$GLOBALS['app_list_strings']['checkbox_dom']['2']}</option><option value='true'>{$GLOBALS['app_list_strings']['checkbox_dom']['1']}</option></select></td>";
			$even = false;
		} else if ($this->sugarbean->object_name == 'Employee') {
			$this->sugarbean->field_defs ['employee_status'] ['type'] = 'enum';
			$this->sugarbean->field_defs ['employee_status'] ['massupdate'] = true;
			$this->sugarbean->field_defs ['employee_status'] ['options'] = 'employee_status_dom';
		} else if ($this->sugarbean->object_name == 'InboundEmail') {
			$this->sugarbean->field_defs ['status'] ['type'] = 'enum';
			$this->sugarbean->field_defs ['status'] ['options'] = 'user_status_dom';
		}
		
		// These fields should never appear on mass update form
		static $banned = array (
				'date_modified' => 1,
				'date_entered' => 1,
				'created_by' => 1,
				'modified_user_id' => 1,
				'deleted' => 1,
				'modified_by_name' => 1 
		);
		
		foreach ( $this->sugarbean->field_defs as $field ) {
			/* if (ACLField::hasAccess ( $field ['name'], $this->sugarbean->module_dir, $GLOBALS ['current_user']->id, false ) < 2) {
				continue;
			} */
			if (! isset ( $banned [$field ['name']] ) && (! isset ( $field ['massupdate'] ) || ! empty ( $field ['massupdate'] ))) {
				$newhtml = '';
				
				if ($even) {
					$newhtml .= "<tr>";
				}
				
				if (isset ( $field ['vname'] )) {
					$displayname = translate ( $field ['vname'] );
				} else {
					$displayname = '';
				}
				
				if (isset ( $field ['type'] ) && $field ['type'] == 'relate' && isset ( $field ['id_name'] ) && $field ['id_name'] == 'assigned_user_id') {
					$field ['type'] = 'assigned_user_name';
				}
				
				if (isset ( $field ['custom_type'] )) {
					$field ['type'] = $field ['custom_type'];
				}
				
				if (isset ( $field ['type'] )) {
					switch ($field ["type"]) {
						case "relate" :
							// bug 14691: avoid laying out an empty cell in the
							// <table>
							$handleRelationship = $this->handleRelationship ( $displayname, $field );
							if ($handleRelationship != '') {
								$even = ! $even;
								$newhtml .= $handleRelationship;
							}
							break;
						case "parent" :
							$even = ! $even;
							$newhtml .= $this->addParent ( $displayname, $field );
							break;
						case "int" :
							if (! empty ( $field ['massupdate'] ) && empty ( $field ['auto_increment'] )) {
								$even = ! $even;
								$newhtml .= $this->addInputType ( $displayname, $field );
							}
							break;
						case "contact_id" :
							$even = ! $even;
							$newhtml .= $this->addContactID ( $displayname, $field ["name"] );
							break;
						case "assigned_user_name" :
							$even = ! $even;
							$newhtml .= $this->addAssignedUserID ( $displayname, $field ["name"] );
							break;
						case "account_id" :
							$even = ! $even;
							$newhtml .= $this->addAccountID ( $displayname, $field ["name"] );
							break;
						case "account_name" :
							$even = ! $even;
							$newhtml .= $this->addAccountID ( $displayname, $field ["id_name"] );
							break;
						case "bool" :
							$even = ! $even;
							
							//change drop down value for archive field for leads
							//@modified by Mohit Kumar Gupta
							//@date 05-02-2014
							if ($this->sugarbean->object_name == 'Lead' && $field['name'] == 'is_archived' && $field['massupdate'] == '1') {
							    $newhtml .= $this->addLeadArchiveBool( translate ( 'LBL_MASS_UPDATE_ARCHIVE' ), $field ["name"] );
							}else{
							    $newhtml .= $this->addBool ( $displayname, $field ["name"] );
							}							
							break;
						case "enum" :
						case "multienum" :
							if (! empty ( $field ['isMultiSelect'] )) {
								$even = ! $even;
								$newhtml .= $this->addStatusMulti ( $displayname, $field ["name"], translate ( $field ["options"] ) );
								break;
							} else if (! empty ( $field ['options'] )) {
								$even = ! $even;
								
								//remove default --none-- for lead
								if ($this->sugarbean->object_name == 'Lead') {
									$newhtml .= $this->addLeadStatus ( $displayname, $field ["name"], translate ( $field ["options"] ) );
								}else{
									$newhtml .= $this->addStatus ( $displayname, $field ["name"], translate ( $field ["options"] ) );
								}

								break;
							} else if (! empty ( $field ['function'] )) {
								$functionValue = $this->getFunctionValue ( $this->sugarbean, $field );
								$even = ! $even;
								$newhtml .= $this->addStatus ( $displayname, $field ["name"], $functionValue );
								break;
							}
							break;
						case "radioenum" :
							$even = ! $even;
							$newhtml .= $this->addRadioenum ( $displayname, $field ["name"], translate ( $field ["options"] ) );
							break;
						case "datetimecombo" :
							$even = ! $even;
							$newhtml .= $this->addDatetime ( $displayname, $field ["name"] );
							break;
						case "datetime" :
						case "date" :
							$even = ! $even;
							$newhtml .= $this->addDate ( $displayname, $field ["name"] );
							break;
						case "team_list" :
							$teamhtml = $this->addTeamList ( translate ( 'LBL_TEAMS' ), $field );
							break;
						default :
							$newhtml .= $this->addDefault ( $displayname, $field, $even );
							break;
							break;
					}
				}
				
				if ($even) {
					$newhtml .= "</tr>";
				}
				
				$field_count ++;
				
				if (! in_array ( $newhtml, array (
						'<tr>',
						'</tr>',
						'<tr></tr>',
						'<tr><td></td></tr>' 
				) )) {
					$html .= $newhtml;
				}
			}
		}
		
		if (isset ( $teamhtml )) {
			if (! $even) {
				$teamhtml .= "</tr>";
			}
			
			if (! in_array ( $teamhtml, array (
					'<tr>',
					'</tr>',
					'<tr></tr>',
					'<tr><td></td></tr>' 
			) )) {
				$html .= $teamhtml;
			}
			
			$field_count ++;
		}
		
		//if ($this->sugarbean->object_name == 'Contact' || $this->sugarbean->object_name == 'Account' || $this->sugarbean->object_name == 'Lead' || $this->sugarbean->object_name == 'Prospect') {
		if ($this->sugarbean->object_name == 'Prospect') {	
			$html .= "<tr><td width='15%'  scope='row' class='dataLabel'>$lang_optout_primaryemail</td><td width='35%' class='dataField'><select name='optout_primary'><option value=''>{$GLOBALS['app_strings']['LBL_NONE']}</option><option value='false'>{$GLOBALS['app_list_strings']['checkbox_dom']['2']}</option><option value='true'>{$GLOBALS['app_list_strings']['checkbox_dom']['1']}</option></select></td></tr>";
		}
		$html .= "</table>";
		
		$html .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td class='buttons'><input onclick='return sListView.send_mass_update(\"selected\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\")' type='submit' id='update_button' name='Update' value='{$lang_update}' class='button'>&nbsp;<input onclick='javascript:toggleMassUpdateForm();' type='button' id='cancel_button' name='Cancel' value='{$GLOBALS['app_strings']['LBL_CANCEL_BUTTON_LABEL']}' class='button'>";
		// TODO: allow ACL access for Delete to be set false always for users
		// if($this->sugarbean->ACLAccess('Delete', true) &&
		// $this->sugarbean->object_name != 'User') {
		// global $app_list_strings;
		// $html .=" <input id='delete_button' type='submit' name='Delete'
		// value='{$lang_delete}' onclick='return confirm(\"{$lang_confirm}\")
		// && sListView.send_mass_update(\"selected\",
		// \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\", 1)' class='button'>";
		// }
		
		// only for My Inbox views - to allow CSRs to have an "Archive" emails
		// feature to get the email "out" of their inbox.
		if ($this->sugarbean->object_name == 'Email' && (isset ( $_REQUEST ['assigned_user_id'] ) && ! empty ( $_REQUEST ['assigned_user_id'] )) && (isset ( $_REQUEST ['type'] ) && ! empty ( $_REQUEST ['type'] ) && $_REQUEST ['type'] == 'inbound')) {
			$html .= <<<eoq
			<input type='button' name='archive' value="{$lang_archive}" class='button' onClick='setArchived();'>
			<input type='hidden' name='ie_assigned_user_id' value="{$current_user->id}">
			<input type='hidden' name='ie_type' value="inbound">
eoq;
		}
		
		$html .= "</td></tr></table></div></div>";
		
		$html .= <<<EOJS
<script>
function toggleMassUpdateForm(){
    document.getElementById('massupdate_form').style.display = 'none';
}
</script>
EOJS;
		
		if ($field_count > 0) {
			return $html;
		} else {
			// If no fields are found, render either a form that still permits
			// Mass Update deletes or just display a message that no fields are
			// available
			$html = "<div id='massupdate_form' style='display:none;'><table width='100%' cellpadding='0' cellspacing='0' border='0' class='formHeader h3Row'><tr><td nowrap><h3><span>" . $app_strings ['LBL_MASS_UPDATE'] . "</h3></td></tr></table>";
			if ($this->sugarbean->ACLAccess ( 'Delete', true ) && ! $hideDeleteIfNoFieldsAvailable) {
				$html .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><input type='submit' name='Delete' value='$lang_delete' onclick=\"return confirm('{$lang_confirm}')\" class='button'></td></tr></table></div>";
			} else {
				$html .= $app_strings ['LBL_NO_MASS_UPDATE_FIELDS_AVAILABLE'] . "</div>";
			}
			return $html;
		}
	}
	
	
	/**
	 * Add Status selection popup window HTML code
	 * @param displayname Name to display in the popup window
	 * @param varname name of the variable
	 * @param options array of options for status
	 */
	function addLeadStatus($displayname, $varname, $options){
		
		global $app_strings, $app_list_strings;
	
		// cn: added "mass_" to the id tag to differentiate from the status id in StoreQuery
		$html = '<td scope="row" width="15%">'.$displayname.'</td><td>';
		if(is_array($options)){
			if(!isset($options['']) && !isset($options['0'])){
				$new_options = array();
				$new_options[''] = '';
				foreach($options as $key=>$value) {
					$new_options[$key] = $value;
				}
				$options = $new_options;
			}
			$options = $this->get_select_options_with_id_separate_key($options, $options, '', true);;
			$html .= '<select id="mass_'.$varname.'" name="'.$varname.'">'.$options.'</select>';
		}else{
			$html .= $options;
		}
		$html .= '</td>';
		return $html;
	}
	
	/**
	 * Create HTML to display select options in a dropdown list.  To be used inside
	 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values are the display strings.
	 * param $label_list - the array of strings to that contains the option list
	 * param $key_list - the array of strings to that contains the values list
	 * param $selected - the string which contains the default value
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
	 * All Rights Reserved.
	 * Contributor(s): ______________________________________..
	 */
	function get_select_options_with_id_separate_key ($label_list, $key_list, $selected_key) {
		global $app_strings;
		$select_options = "";
	
		if (empty($key_list)) $key_list = array();
		//create the type dropdown domain and set the selected value if $opp value already exists
		foreach ($key_list as $option_key=>$option_value) {
	
			$selected_string = '';
			// the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
			// The bug was only happening with one of the users in the drop down.  It was being replaced by none.
			if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (is_array($selected_key) &&  in_array($option_key, $selected_key)))
			{
				$selected_string = 'selected ';
			}
	
			$html_value = $option_key;
	
			$select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
		}
	
		return $select_options;
	}
		
	/**
	 * Add Status selection popup window HTML code for archive of lead
	 * @param displayname Name to display in the popup window
	 * @param varname name of the variable
	 */
	function addLeadArchiveBool($displayname, $varname){
	    global $app_strings, $app_list_strings;
	    return $this->addStatus($displayname, $varname, $app_list_strings['lead_archive_checkbox_dom']);
	}
	
	/**
	 * Add AssignedUser popup window HTML code
	 * @Modified By Mohit Kumar Gupta
	 * @date 28-03-2014
	 * @param displayname Name to display in the popup window
	 * @param varname name of the variable
	 * @return HTML 
	 */
	function addAssignedUserID($displayname, $varname){
	    global $app_strings;
	
	    $json = getJSONobj();
	
	    $popup_request_data = array(
	            'call_back_function' => 'set_return',
	            'form_name' => 'MassUpdate',
	            'field_to_name_array' => array(
	                    'id' => 'assigned_user_id',
	                    'user_name' => 'assigned_user_name',
	            ),
	    );
	    $encoded_popup_request_data = $json->encode($popup_request_data);
	    $qsUser = array(
	            'form' => 'MassUpdate',
	            'method' => 'get_user_array', // special method
	            'field_list' => array('user_name', 'id'),
	            'populate_list' => array('assigned_user_name', 'assigned_user_id'),
	            'conditions' => array(array('name'=>'user_name','op'=>'like_custom','end'=>'%','value'=>'')),
	            'limit' => '30','no_match_text' => $app_strings['ERR_SQS_NO_MATCH']);
	
	    $qsUser['populate_list'] = array('mass_assigned_user_name', 'mass_assigned_user_id');
	    $img = SugarThemeRegistry::current()->getImageURL("id-ff-select.png");
	    
	    //Modify to add sqsNoAutofill for auto complete off on type-ahead of mass update
	    $html = <<<EOQ
		<td width="15%" scope="row">$displayname</td>
		<td ><input class="sqsEnabled sqsNoAutofill" autocomplete="off" id="mass_assigned_user_name" name='assigned_user_name' type="text" value=""><input id='mass_assigned_user_id' name='assigned_user_id' type="hidden" value="" />
		<span class="id-ff multiple"><button id="mass_assigned_user_name_btn" title="{$app_strings['LBL_SELECT_BUTTON_TITLE']}" type="button" class="button" value='{$app_strings['LBL_SELECT_BUTTON_LABEL']}' name=btn1
				onclick='open_popup("Users", 600, 400, "", true, false, $encoded_popup_request_data);' /><img src="$img"></button></span>
		</td>
EOQ;
	    $html .= '<script type="text/javascript" language="javascript">if(typeof sqs_objects == \'undefined\'){var sqs_objects = new Array;}sqs_objects[\'MassUpdate_assigned_user_name\'] = ' .
	    $json->encode($qsUser) . '; registerSingleSmartInputListener(document.getElementById(\'mass_assigned_user_name\'));
				addToValidateBinaryDependency(\'MassUpdate\', \'assigned_user_name\', \'alpha\', false, \'' . $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_ASSIGNED_TO'] . '\',\'assigned_user_id\');
				</script>';
	
	    return $html;
	}
	
}
?>