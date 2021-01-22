{*
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

*}


{$ROLLOVER}
<script type="text/javascript" src="{sugar_getjspath file='modules/Emails/javascript/vars.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_emails.js'}"></script>
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Users/PasswordRequirementBox.css'}">
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>
<script type='text/javascript' src='{sugar_getjspath file='include/SubPanel/SubPanelTiles.js'}'></script>
<script type='text/javascript'>
var ERR_RULES_NOT_MET = '{$MOD.ERR_RULES_NOT_MET}';
var ERR_ENTER_OLD_PASSWORD = '{$MOD.ERR_ENTER_OLD_PASSWORD}';
var ERR_ENTER_NEW_PASSWORD = '{$MOD.ERR_ENTER_NEW_PASSWORD}';
var ERR_ENTER_CONFIRMATION_PASSWORD = '{$MOD.ERR_ENTER_CONFIRMATION_PASSWORD}';
var ERR_REENTER_PASSWORDS = '{$MOD.ERR_REENTER_PASSWORDS}';
</script>
<script type='text/javascript' src='{sugar_getjspath file='modules/Users/User.js'}'></script>
<script type='text/javascript' src='{sugar_getjspath file='modules/Users/UserEditView.js'}'></script>
<script type='text/javascript' src='{sugar_getjspath file='modules/Users/PasswordRequirementBox.js'}'></script>
{$ERROR_STRING}
<!-- This is here for the external API forms -->
<form name="DetailView" id="DetailView" method="POST" action="index.php">
	<input type="hidden" name="record" id="record" value="{$ID}">
	<input type="hidden" name="module" value="Users">
	<input type="hidden" name="return_module" value="Users">
	<input type="hidden" name="return_id" value="{$RETURN_ID}">
	<input type="hidden" name="return_action" value="EditView">
</form>

<form name="EditView" enctype="multipart/form-data" id="EditView" method="POST" action="index.php">
	<input type="hidden" name="display_tabs_def">
	<input type="hidden" name="hide_tabs_def">
	<input type="hidden" name="remove_tabs_def">
	<input type="hidden" name="module" value="Users">
	<input type="hidden" name="record" id="record" value="{$ID}">
	<input type="hidden" name="action">
	<input type="hidden" name="page" value="EditView">
	<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
	<input type="hidden" name="return_id" value="{$RETURN_ID}">
	<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
	<input type="hidden" name="password_change" id="password_change" value="false">
    <input type="hidden" name="required_password" id="required_password" value='{$REQUIRED_PASSWORD}' >
	<input type="hidden" name="old_user_name" value="{$USER_NAME}">
	<input type="hidden" name="type" value="{$REDIRECT_EMAILS_TYPE}">
	<input type="hidden" id="is_group" name="is_group" value='{$IS_GROUP}' {$IS_GROUP_DISABLED}>
	<input type="hidden" id='portal_only' name='portal_only' value='{$IS_PORTALONLY}' {$IS_PORTAL_ONLY_DISABLED}>
	<input type="hidden" name="is_admin" id="is_admin" value='{$IS_FOCUS_ADMIN}' {$IS_ADMIN_DISABLED} >
	<input type="hidden" name="is_current_admin" id="is_current_admin" value='{$IS_ADMIN}' >
	<input type="hidden" name="edit_self" id="edit_self" value='{$EDIT_SELF}' >
	<input type="hidden" name="required_email_address" id="required_email_address" value='{$REQUIRED_EMAIL_ADDRESS}' >
    <input type="hidden" name="isDuplicate" id="isDuplicate" value="{$isDuplicate}">
	<div id="popup_window"></div>

<script type="text/javascript">
var EditView_tabs = new YAHOO.widget.TabView("EditView_tabs");

{literal}
//Override so we do not force submit, just simulate the 'save button' click
SUGAR.EmailAddressWidget.prototype.forceSubmit = function() { document.getElementById('Save').click();}

EditView_tabs.on('contentReady', function(e){
{/literal}
{if $ID}
{literal}
    var eapmTabIndex = 4;
    {/literal}{if !$SHOW_THEMES}{literal}eapmTabIndex = 3;{/literal}{/if}{literal}
    EditView_tabs.getTab(eapmTabIndex).set('dataSrc','index.php?sugar_body_only=1&module=Users&subpanel=eapm&action=SubPanelViewer&inline=1&record={/literal}{$ID}{literal}&layout_def_key=UserEAPM&inline=1&ajaxSubpanel=true');
    EditView_tabs.getTab(eapmTabIndex).set('cacheData',true);
    EditView_tabs.getTab(eapmTabIndex).on('dataLoadedChange',function(){
        //reinit action menus
        $("ul.clickMenu").each(function(index, node){
            $(node).sugarActionMenu();
        });
    });

    if ( document.location.hash == '#tab5' ) {
        EditView_tabs.selectTab(eapmTabIndex);
    }
{/literal}
{/if}

{if $scroll_to_cal}
    {literal}
        //we are coming from the tour welcome page, so we need to simulate a click on the 4th tab
        // and scroll to the calendar_options div after the tabs have rendered
        document.getElementById('tab4').click();
        document.getElementById('calendar_options').scrollIntoView();
    {/literal}
{/if}

});
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer">
    <tr>
        <td>
            {sugar_action_menu id="userEditActions" class="clickMenu fancymenu" buttons=$ACTION_BUTTON_HEADER flat=true}
        </td>
        <td align="right" nowrap>
            <span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span> {$APP.NTC_REQUIRED}
        </td>
    </tr>
</table>

<div id="EditView_tabs" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a id="tab1" href="#tab1"><em>{$MOD.LBL_USER_INFORMATION}</em></a></li>
        <li {if $CHANGE_PWD == 0}style='display:none'{/if}><a id="tab2" href="#tab2"><em>{$MOD.LBL_CHANGE_PASSWORD_TITLE}</em></a></li>
        {if $SHOW_THEMES}
        	<li><a id="tab3" href="#tab3" style='display:{$HIDE_FOR_GROUP_AND_PORTAL};'><em>{$MOD.LBL_THEME}</em></a></li>
        {/if}
        <li><a id="tab4" href="#tab4" style='display:{$HIDE_FOR_GROUP_AND_PORTAL};'><em>{$MOD.LBL_ADVANCED}</em></a></li>
        {if $ID}
        <li><a id="tab5" href="#tab5" style='display:{$HIDE_FOR_GROUP_AND_PORTAL};'><em>{$MOD.LBL_EAPM_SUBPANEL_TITLE}</em></a></li>
        {/if}
		<li><a id="tab6" href="#tab6" style='display:{$HIDE_FOR_GROUP_AND_PORTAL};'><em>{$MOD.LBL_LAYOUT_OPTIONS}</em></a></li>
		{if $IS_ADMIN and $ID neq $CUR_ID}
          <li><a id="tab7" href="#tab7"><em>{$MOD.LBL_USER_FILTERS_TITLE}</em></a></li>
         {/if}
    </ul>
    <div class="yui-content user-tab-content">
        <div>
<!-- BEGIN METADATA GENERATED CONTENT -->
{literal}
<script type="text/javascript">
function setStatusLoading(stContainerId)
{
	 ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
}
function hideStatusLoading(stContainerId)
{
	 ajaxStatus.hideStatus();
}
/*
YUI().use('node',function(Y){
	
	Y.all("input[id^=SAVE_]").each(
	function(elm){
		elm.on('mouseover',function(){
			
			var allSel = new Array('state_apply','county_filters','zip_filters','pl_type_filter','classification_filters','tms_filter','tms' );
			for(var i=0;i< allSel.length;i++){
				if(Y.one('#'+allSel[i]))
				Y.all('#'+allSel[i]+' option').set('selected','true');
			}
		});

	});

});
*/

$(document).ready(function (){

$('#SAVE_FOOTER, #SAVE_HEADER').each(function(idx,elm){

$(elm).bind('mouseover',function(){

			var allSel = new Array('state_apply','county_filters','zip_filters','pl_type_filter','classification_filters','tms_filter','tms' );
			for(var i=0;i< allSel.length;i++){
				if($('#'+allSel[i]))
				$('#'+allSel[i]+' option').attr('selected',true);
			}
		});

})
});

YUI().use('event','node', function (E) {
    //user_filter_container
	E.on('load',load_filter_content)
	
});
function load_filter_content(){
	
	YUI().use('node',function(Y){
		try{
		
		Y.one('a[href=#tab7]').on('click',function(E){
		
		setFilters = '{/literal}{$ST_GEO_FILTER_LOCATION}{literal}';	
		if(setFilters == ''){
			btnCfg = {Ok:function() {
							window.location.href= 'index.php?module=Users&action=bbwizard&geofilters=1';
        				      $( this ).dialog( "close" );   			
        			},
        			Cancel: function(){
        			$( this ).dialog( "close" ) 
        				}
        			};
			showPopup(SUGAR.language.get('Users','LBL_USER_FILTER_NOTE')+' <br/> <br/> '+SUGAR.language.get('Users','LBL_NO_GEO_FILTER_DEFINED'),'Error!!','50%',btnCfg);
			return; 
		}		
		Y.one('#eapm_area').set('className','');
		YAHOO.util.Dom.get('user_filter_container').innerHTML ='';
		
			
			
			setStatusLoading('loading')
			
			var callbackOutboundTest = { 	
					cache:false,
						
					success	: function(o) {
						Y.one('#user_filter_container').set('className','');
						SUGAR.util.evalScript(o.responseText);
						YAHOO.util.Dom.get('user_filter_container').innerHTML = o.responseText;
							
						hideStatusLoading('eapm_area');
						Y.one('#eapm_area').set('className','yui-hidden');
						}	
			};
   		    YAHOO.util.Connect.asyncRequest("GET", "index.php?module=Users&action=handle_requests&showFiltersOnly=1&to_pdf=true&getFullDetails=1&record={/literal}{$ID}{literal}", callbackOutboundTest,'');
				})
		
		}catch(e){
			//console.log(e)
		}
		});
				
}

function showPopup(txt,TitleText,width,btnCfg){             
              TitleText=	decodeURIComponent(TitleText).replace(/\+/g, ' ');              
              oReturn = function(body, caption, width, theme) {
                                        $(".ui-dialog").find(".open").dialog("close");
                                        var bidDialog = $('<div class="open"></div>')
                                        .html(body)
                                        .dialog({
              									
                                                autoOpen: false,
                                                title: caption,
                                                width: width, 
                                               /* show: "slide",
                                                hide: "scale",*/            									
              									buttons : btnCfg
              									                                               
                                        });
                                        bidDialog.dialog('open');

                                };       
			oReturn(txt,TitleText, width, '');                                                
return;        
}
</script>

{/literal}
