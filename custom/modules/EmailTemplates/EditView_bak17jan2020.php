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

/*********************************************************************************

 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Campaigns/utils.php');

//if campaign_id is passed then we assume this is being invoked from the campaign module and in a popup.
$has_campaign=true;
$inboundEmail=true;  
if (!isset($_REQUEST['campaign_id']) || empty($_REQUEST['campaign_id'])) {
	$has_campaign=false;
}
if (!isset($_REQUEST['inboundEmail']) || empty($_REQUEST['inboundEmail'])) {
    $inboundEmail=false;
}
$focus = new EmailTemplate();

if(isset($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}

$old_id = '';
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
    $old_id = $focus->id; // for attachments down below
    $focus->id = "";
}



//setting default flag value so due date and time not required
if(!isset($focus->id)) $focus->date_due_flag = 1;

//needed when creating a new case with default values passed in
if(isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) {
    $focus->contact_name = $_REQUEST['contact_name'];
}
if(isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
    $focus->contact_id = $_REQUEST['contact_id'];
}
if(isset($_REQUEST['parent_name']) && is_null($focus->parent_name)) {
    $focus->parent_name = $_REQUEST['parent_name'];
}
if(isset($_REQUEST['parent_id']) && is_null($focus->parent_id)) {
    $focus->parent_id = $_REQUEST['parent_id'];
}
if(isset($_REQUEST['parent_type'])) {
    $focus->parent_type = $_REQUEST['parent_type'];
}
elseif(!isset($focus->parent_type)) {
    $focus->parent_type = $app_list_strings['record_type_default_key'];
}
if(isset($_REQUEST['filename']) && $_REQUEST['isDuplicate'] != 'true') {
        $focus->filename = $_REQUEST['filename'];
}

if($has_campaign || $inboundEmail) {
    insert_popup_header($theme);
}


$params = array();

if(empty($focus->id)){
	$params[] = $GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL'];
}else{
	$params[] = "<a href='index.php?module={$focus->module_dir}&action=DetailView&record={$focus->id}'>{$focus->name}</a>";
	$params[] = $GLOBALS['app_strings']['LBL_EDIT_BUTTON_LABEL'];
}

echo getClassicModuleTitle($focus->module_dir, $params, true);

$GLOBALS['log']->info("EmailTemplate detail view");

//change file path from main file to custom file
//@modified by Mohit Kumar Gupta 28-03-2014
if($has_campaign || $inboundEmail) {
	$xtpl=new XTemplate ('modules/EmailTemplates/EditView.html');
} else {
	$xtpl=new XTemplate ('custom/modules/EmailTemplates/EditViewMain.html');
} // else
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("LBL_ACCOUNT",$app_list_strings['moduleList']['Accounts']);
$xtpl->parse("main.variable_option");

$returnAction = 'index';
if(isset($_REQUEST['return_module'])) $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
if(isset($_REQUEST['return_action'])){
	$xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
	$returnAction = $_REQUEST['return_action'];
}
if(isset($_REQUEST['return_id'])) $xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
// handle Create $module then Cancel
if(empty($_REQUEST['return_id'])) {
    $xtpl->assign("RETURN_ACTION", 'index');
}

if ($has_campaign || $inboundEmail ) {
    $cancel_script="window.close();";
}else {
    $cancel_script="this.form.action.value='{$returnAction}'; this.form.module.value='{$_REQUEST['return_module']}';
    this.form.record.value=";
    if(empty($_REQUEST['return_id'])) {
        $cancel_script="this.form.action.value='index'; this.form.module.value='{$_REQUEST['return_module']}';this.form.name.value='';this.form.description.value=''"; 
    } else {
        $cancel_script.="'{$_REQUEST['return_id']}'";
    }
}

//Setup assigned user name
$popup_request_data = array(
	'call_back_function' => 'set_return',
	'form_name' => 'EditView',
	'field_to_name_array' => array(
		'id' => 'assigned_user_id',
		'user_name' => 'assigned_user_name',
		),
	);
$json = getJSONobj();
$xtpl->assign('encoded_assigned_users_popup_request_data', $json->encode($popup_request_data));
if(!empty($focus->assigned_user_name))
    $xtpl->assign("ASSIGNED_USER_NAME", $focus->assigned_user_name);

$xtpl->assign("assign_user_select", SugarThemeRegistry::current()->getImage('id-ff-select'));
$xtpl->assign("assign_user_clear", SugarThemeRegistry::current()->getImage('id-ff-clear'));
//Assign qsd script
require_once('include/QuickSearchDefaults.php');
$qsd = new QuickSearchDefaults();
$sqs_objects = array( 'EditView_assigned_user_name' => $qsd->getQSUser());
$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . $json->encode($sqs_objects) . '; enableQS();</script>';

$xtpl->assign("CANCEL_SCRIPT", $cancel_script);
$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
$xtpl->assign("JAVASCRIPT", get_set_focus_js() . $quicksearch_js);

if(!is_file($GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $GLOBALS['current_language'] . '.js')) {
    require_once('include/language/jsLanguage.php');
    jsLanguage::createAppStringsCache($GLOBALS['current_language']);
}
$jsLang = '<script type="text/javascript" src="' . $GLOBALS['sugar_config']['cache_dir'] . 'jsLanguage/' . $GLOBALS['current_language'] . '.js?s=' . $GLOBALS['sugar_version'] . '&c=' . $GLOBALS['sugar_config']['js_custom_version'] . '&j=' . $GLOBALS['sugar_config']['js_lang_version'] . '"></script>';
$xtpl->assign("JSLANG", $jsLang);

$xtpl->assign("ID", $focus->id);
if(isset($focus->name)) $xtpl->assign("NAME", $focus->name); else $xtpl->assign("NAME", "");
if(isset($focus->description)) $xtpl->assign("DESCRIPTION", $focus->description); else $xtpl->assign("DESCRIPTION", "");
if(isset($focus->subject)) $xtpl->assign("SUBJECT", $focus->subject); else $xtpl->assign("SUBJECT", "");
if( $focus->published == 'on')
{
$xtpl->assign("PUBLISHED","CHECKED");
}
//if text only is set to true, then make sure input is checked and value set to 1
if(isset($focus->text_only) && $focus->text_only){
    $xtpl->assign("TEXTONLY_CHECKED","CHECKED");
    $xtpl->assign("TEXTONLY_VALUE","1");
}else{//set value to 0
    $xtpl->assign("TEXTONLY_VALUE","0");
}


//Assign the Teamset field
require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
$teamSetField = new SugarFieldTeamset('Teamset');
$code = $teamSetField->getClassicView($focus->field_defs);
$xtpl->assign("TEAM", $code);   

include_once 'custom/modules/EmailTemplates/CustomEmailTemplate.php';
$custom_focus = new CustomEmailTemplate();


$xtpl->assign("FIELD_DEFS_JS", $custom_focus->generateFieldDefsJS());
$xtpl->assign("LBL_CONTACT",$app_list_strings['moduleList']['Contacts']);

global $current_user;
if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])) { 
    $record = '';
    if(!empty($_REQUEST['record'])) {
        $record =   $_REQUEST['record'];
    }
    $xtpl->assign("ADMIN_EDIT","<a href='index.php?action=index&module=DynamicLayout&from_action=" . $_REQUEST['action']
	."&from_module=".$_REQUEST['module'] ."&record=".$record. "'>".SugarThemeRegistry::current()->getImage("EditLayout","border='0' alt='Edit Layout' align='bottom'")."</a>");    
}
if(isset($focus->parent_type) && $focus->parent_type != "") {
    $change_parent_button = "<input title='".$app_strings['LBL_SELECT_BUTTON_TITLE']."' accessKey='".$app_strings['LBL_SELECT_BUTTON_KEY']."'
tabindex='3' type='button' class='button' value='".$app_strings['LBL_SELECT_BUTTON_LABEL']."' name='button' LANGUAGE=javascript onclick='return
window.open(\"index.php?module=\"+ document.EditView.parent_type.value +
\"&action=Popup&html=Popup_picker&form=TasksEditView\",\"test\",\"width=600,height=400,resizable=1,scrollbars=1\");'>";
    $xtpl->assign("CHANGE_PARENT_BUTTON", $change_parent_button);
}
if($focus->parent_type == "Account") {
	$xtpl->assign("DEFAULT_SEARCH","&query=true&account_id=$focus->parent_id&account_name=".urlencode($focus->parent_name));
}

$xtpl->assign("DESCRIPTION", $focus->description);
$xtpl->assign("TYPE_OPTIONS", get_select_options_with_id($app_list_strings['record_type_display'], $focus->parent_type));
//$xtpl->assign("DEFAULT_MODULE","Accounts");

if(isset($focus->body)) $xtpl->assign("BODY", $focus->body); else $xtpl->assign("BODY", "");
if(isset($focus->body_html)) $xtpl->assign("BODY_HTML", $focus->body_html); else $xtpl->assign("BODY_HTML", "");


if(true) {
    if ( !isTouchScreen() ) {
        require_once("include/SugarTinyMCE.php");
        $tiny = new SugarTinyMCE();
        $tiny->defaultConfig['cleanup_on_startup']=true;
        $tiny->defaultConfig['height']=600;
        $tinyHtml = $tiny->getInstance();
        $xtpl->assign("tiny", $tinyHtml);
	}
	///////////////////////////////////////
	////	MACRO VARS
	$xtpl->assign("INSERT_VARIABLE_ONCLICK", "insert_variable(document.EditView.variable_text.value)");
	if(!$inboundEmail){
		$xtpl->parse("main.NoInbound.variable_button");
	}
	///////////////////////////////////////
	////	CAMPAIGNS
	if($has_campaign || $inboundEmail) {
		$xtpl->assign("INPOPUPWINDOW",'true');	
		$xtpl->assign("INSERT_URL_ONCLICK", "insert_variable_html_link(document.EditView.tracker_url.value)");
		if($has_campaign){
		  $campaign_urls=get_campaign_urls($_REQUEST['campaign_id']);
		}
		if(!empty($campaign_urls)) {
			$xtpl->assign("DEFAULT_URL_TEXT",key($campaign_urls)); 
	  	}
	    if($has_campaign){
		  $xtpl->assign("TRACKER_KEY_OPTIONS", get_select_options_with_id($campaign_urls, null));
		  $xtpl->parse("main.NoInbound.tracker_url");
	    }
	}
	
	// The insert variable drodown should be conditionally displayed.
	// If it's campaign then hide the Account. 
	if($has_campaign) {
	    $dropdown="<option value='Contacts'>
						".$mod_strings['LBL_CONTACT_AND_OTHERS']."
			       </option>";
	     $xtpl->assign("DROPDOWN",$dropdown);
	     $xtpl->assign("DEFAULT_MODULE",'Contacts');
         //$xtpl->assign("CAMPAIGN_POPUP_JS", '<script type="text/javascript" src="include/javascript/sugar_3.js"></script>');                  	 
	} else {
	     $dropdown="<option value='Accounts'>
						".$mod_strings['LBL_ACCOUNT']."
		  	       </option>
			       <option value='Contacts'>
						".$mod_strings['LBL_CONTACT_AND_OTHERS']."
			       </option>
			       <option value='Users'>
						".$mod_strings['LBL_USERS']."
			       </option>
	     		  <option value='AOS_Quotes'>
						".$mod_strings['LBL_AOS_QUOTES']."
			      </option>
			      <option value='Opportunities'>
						".$mod_strings['LBL_OPPORTUNITIES']."
			      </option>
	     		  <option value='Instance'>
					".$mod_strings['LBL_INSTANCE']."
			      </option>";
		$xtpl->assign("DROPDOWN",$dropdown);      
		$xtpl->assign("DEFAULT_MODULE",'Accounts');
	}
	////	END CAMPAIGNS
	///////////////////////////////////////

	///////////////////////////////////////
	////    ATTACHMENTS
	$attachments = '';
	if(!empty($focus->id)) {
	    $etid = $focus->id;
	} elseif(!empty($old_id)) {
	    $xtpl->assign('OLD_ID', $old_id);
	    $etid = $old_id;
	}
	if(!empty($etid)) {
	    $note = new Note();
	    $where = "notes.parent_id='{$etid}' AND notes.filename IS NOT NULL";
	    $notes_list = $note->get_full_list("", $where,true);
	
	    if(!isset($notes_list)) {
	        $notes_list = array();
	    }
	    for($i = 0;$i < count($notes_list);$i++) {
	        $the_note = $notes_list[$i];
	        if( empty($the_note->filename)) {
	            continue;
	        }
	        $secureLink = 'index.php?entryPoint=download&id='.$the_note->id.'&type=Notes';
	        $attachments .= '<input type="checkbox" name="remove_attachment[]" value="'.$the_note->id.'"> '.$app_strings['LNK_REMOVE'].'&nbsp;&nbsp;';
	        $attachments .= '<a href="'.$secureLink.'" target="_blank">'. $the_note->filename .'</a><br>';
	    }
	}
	$attJs  = '<script type="text/javascript">';
	$attJs .= 'var file_path = "'.$sugar_config['site_url'].'/'.$sugar_config['upload_dir'].'";';
	$attJs .= 'var lnk_remove = "'.$app_strings['LNK_REMOVE'].'";';
	$attJs .= '</script>';
	$xtpl->assign('ATTACHMENTS', $attachments);
	$xtpl->assign('ATTACHMENTS_JAVASCRIPT', $attJs);

	////    END ATTACHMENTS
	///////////////////////////////////////
	
	// done and parse
	$xtpl->parse("main.textarea");
}

//Add Custom Fields
require_once('modules/DynamicFields/templates/Files/EditView.php');
if(!$inboundEmail){
    $xtpl->parse("main.NoInbound");
    $xtpl->parse("main.NoInbound1");
    $xtpl->parse("main.NoInbound2");
    $xtpl->parse("main.NoInbound3");
    $xtpl->parse("main.NoInbound4");
    $xtpl->parse("main.NoInbound5");
}
$xtpl->parse("main");

$xtpl->out("main");

$javascript = new javascript();
$javascript->setFormName('EditView');
$javascript->setSugarBean($focus);
$javascript->addAllFields('');
echo $javascript->getScript();
?>