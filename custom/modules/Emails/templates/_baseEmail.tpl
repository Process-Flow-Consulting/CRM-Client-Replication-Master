{*
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

*}
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Emails/EmailUI.css'}" />
{include file="modules/Emails/templates/_baseJsVars.tpl"}
<script type="text/javascript" src='{sugar_getjspath file='include/javascript/tiny_mce/tiny_mce.js'}'></script>
<script type="text/javascript" src='{sugar_getjspath file='cache/include/javascript/sugar_grp_emails.js'}'></script>
<script type="text/javascript" src='{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}'></script>
<script type="text/javascript" src="include/javascript/jsclass_base.js"></script>
<script type="text/javascript" src="include/javascript/jsclass_async.js"></script>

<script type="text/javascript" language="Javascript">

{include file="modules/Emails/templates/_baseConfigData.tpl"}

    var calFormat = '{$calFormat}';
    var theme = "{$theme}";

    {$quickSearchForAssignedUser}

    SUGAR.email2.detailView.qcmodules = {$qcModules};

    //SUGAR.email2.composeLayout.teamsSettingsFolder = '';

    var isAdmin = {$is_admin};
    var loadingSprite = app_strings.LBL_EMAIL_LOADING + " <img src='include/javascript/yui/build/assets/skins/sam/wait.gif' alt=$mod_strings.LBL_WAIT height='14' align='absmiddle'>";
</script>
<div class="email">
<form id="emailUIForm" name="emailUIForm">
    <input type="hidden" id="module" name="module" value="Emails">
    <input type="hidden" id="action" name="action" value="EmailUIAjax">
    <input type="hidden" id="to_pdf" name="to_pdf" value="true">
    <input type="hidden" id="emailUIAction" name="emailUIAction">
    <input type="hidden" id="mbox" name="mbox">
    <input type="hidden" id="uid" name="uid">
    <input type="hidden" id="ieId" name="ieId">
    <input type="hidden" id="forceRefresh" name="forceRefresh">
    <input type="hidden" id="focusFolder" name="focusFolder">
    <input type="hidden" id="focusFolderOpen" name="focusFolderOpen">
    <input type="hidden" id="sortBy" name="sortBy">
    <input type="hidden" id="reverse" name="reverse">
</form>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td NOWRAP style="padding-bottom: 2px;">
            <button class="button" id="checkEmailButton" onclick="SUGAR.email2.folders.startEmailAccountCheck();"><img src="themes/default/images/icon_email_check.gif" align="absmiddle" border="0"> {$app_strings.LBL_EMAIL_CHECK}</button>
            <button class="button" id="composeButton" onclick="SUGAR.email2.composeLayout.c0_composeNewEmail();"><img src="themes/default/images/icon_email_compose.gif" align="absmiddle" border="0"> {$mod_strings.LNK_NEW_SEND_EMAIL}</button>
            <button class="button" id="settingsButton" onclick="SUGAR.email2.settings.showSettings();"><img src="themes/default/images/icon_email_settings.gif" align="absmiddle" border="0"> {$app_strings.LBL_EMAIL_SETTINGS}</button>
        </td>
        <td NOWRAP align="right" style="padding-bottom: 2px;">
            <a href="index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module=Emails&help_action=index&key={$server_unique_key}" width='13' height='13' alt='{$app_strings.LNK_HELP}' border='0' align='absmiddle' target="_blank"></a>
            &nbsp;
            <a href="index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module=Emails&help_action=index&key={$server_unique_key}" class='utilsLink' target="_blank">{$app_strings.LNK_HELP}</a>
        </td>
    </tr>
</table>


{include file="modules/Emails/templates/overlay.tpl"}

<div id="emailContextMenu"></div>
<div id="folderContextMenu"></div>
<div id="container" class="email" style="position:relative; height:550px; overflow:hidden;"></div>
<div id="innerLayout" class="yui-hidden"></div>
<div id="listViewLayout" class="yui-hidden"></div>
<div id="settingsDialog"></div>

<!-- Hidden Content -->
<div class="yui-hidden">
    <div id="searchTab" style="padding:5px">
        {include file="modules/Emails/templates/advancedSearch.tpl"}
    </div>
    <div id="settings">
        {include file="modules/Emails/templates/emailSettings.tpl"}
    </div>
    
    <div id="footerLinks" class="yui-hidden"></div>
</div>


<div id="editContact" class="yui-hidden"></div>
<div id="editContactTab" class="yui-hidden"></div>
<div id="editMailingList" class="yui-hidden"></div>
<div id="editMailingListTab" class="yui-hidden"></div>


<!-- for detailView quickCreate() calls -->
<div id="quickCreateForEmail"></div>
<div id="quickCreateContent"></div>


<div id="importDialog"></div>
<div id="importDialogContent" ></div>


<div id="relateDialog"  ></div>
<div id="relateDialogContent"  ></div>


<div id="assignmentDialog"  ></div>
<div id="assignmentDialogContent"  ></div>


<div id="emailDetailDialog"  ></div>
<div id="emailDetailDialogContent"  ></div>


<!-- for detailView views -->
<div id="viewDialog"></div>
<div id="viewDialogContent"></div>

<!-- addressBook select -->
{include file="modules/Emails/templates/addressSearchContent.tpl"}

<!-- accounts outbound server dialogue -->
<div id="outboundDialog" class="yui-hidden">
    {include file="modules/Emails/templates/outboundDialog.tpl"}
</div>

<!-- accounts edit dialogue -->
<div id="editAccountDialogue" class="yui-hidden">
    {include file="modules/Emails/templates/editAccountDialogue.tpl"}
</div>

<div id="testOutboundDialog" class="yui-hidden">
    {include file="modules/Emails/templates/outboundDialogTest.tpl"}
</div>

<div id="assignToDiv" class="yui-hidden">
    {include file="modules/Emails/templates/assignTo.tpl"}
</div>


<script type="text/javascript" language="Javascript">
	enableQS(true);
</script>

<!-- //separeate client and project opportunity -- hirak -->
<script type="text/javascript" language="Javascript">
{literal}
SUGAR.email2.detailView.showQuickCreate =  function(ieId, uid, mailbox) {
    var panelId = SE.util.getPanelId();
    var context = document.getElementById("quickCreateSpan" + panelId);

    if (!SE.detailView.cqMenus)
    	SE.detailView.cqMenus = {};

    if (SE.detailView.cqMenus[context])
    	SE.detailView.cqMenus[context].destroy();

    var menu = SE.detailView.cqMenus[context] = new YAHOO.widget.Menu("qcMenuDiv" + panelId, {
		lazyload:true,
		context: ["quickCreateSpan" + panelId, "tr","br", ["beforeShow", "windowResize"]]
    });

    for (var i=0; i < this.qcmodules.length; i++) {
        var module = this.qcmodules[i];

        if(module == 'Opportunities'){
	
        	menu.addItem({
	            text:   app_strings['LBL_EMAIL_QC_PROJECT_OPPORTUNITIES' ],
	            modulename: module,
	            value: module,
	            onclick: { fn: function() {
	        			SE.detailView.quickCreate('ProjectOpportunities', ieId, uid, mailbox);
	        		}
	        	}
	        });

        	menu.addItem({
	            text:   app_strings['LBL_EMAIL_QC_CLIENT_OPPORTUNITIES'],
	            modulename: module,
	            value: module,
	            onclick: { fn: function() {
	        			SE.detailView.quickCreate('ClientOpportunities', ieId, uid, mailbox);
	        		}
	        	}
	        });
        	
        }
        else{
	        menu.addItem({
	            text:   app_strings['LBL_EMAIL_QC_' + module.toUpperCase()],
	            modulename: module,
	            value: module,
	            onclick: { fn: function() {
	        			SE.detailView.quickCreate(this.value, ieId, uid, mailbox);
	        		}
	        	}
	        });
        }
    }

	menu.render(document.body);
	menu.show();
};
/**
 * Makes async call to get QuickCreate form
 * Renders a modal edit view for a given module
 */
SUGAR.email2.detailView.quickCreate = function(module, ieId, uid, mailbox) {

	var omodule = '';
	
	if(module == 'ProjectOpportunities' || module == 'ClientOpportunities'){
		omodule = module;
		module = 'Opportunities';	
	}
		
    var get = "&qc_module=" + module + "&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;

    if(ieId == null || ieId == "null" || mailbox == 'sugar::Emails') {
        get += "&sugarEmail=true";
    }

    if(omodule == 'ClientOpportunities'){
        get += "&target_view=QuickCreateSub";
    }
    
    AjaxObject.startRequest(callbackQuickCreate, urlStandard + '&emailUIAction=getQuickCreateForm' + get);
};


/**
 *  Enable the quick search for the compose relate field or search tab
 */
SUGAR.email2.composeLayout.enableQuickSearchRelate =  function(idx,overides){

     if(typeof overides != 'undefined')
     {
         var newModuleID = overides['moduleSelectField']; //data_parent_type_search
         var newModule = document.getElementById(newModuleID).value;
         var formName = overides['formName'];
         var fieldName = overides['fieldName'];
         var fieldId = overides['fieldId'];
         var fullName = formName + "_" + fieldName;
         var postBlurFunction = null;
     }
     else
     {
         var newModule = document.getElementById('data_parent_type'+idx).value;
         var formName = 'emailCompose'+idx;
         var fieldName = 'data_parent_name'+idx;
         var fieldId = 'data_parent_id'+idx;
         var fullName = formName + "_" + fieldName;
         var postBlurFunction = "SE.composeLayout.qsAddAddress";
     }

     var omodule = '';
     if(newModule == 'ProjectOpportunities' || newModule == 'ClientOpportunities'){
         omodule = newModule;
         newModule = 'Opportunities';
     }
     
     if(typeof sqs_objects == 'undefined')
         window['sqs_objects'] = new Array;

     window['sqs_objects'][fullName] = {
         form:formName,
			method:"query",
			modules:[newModule],
			group:"or",
         field_list:["name","id", "email1"],populate_list:[fieldName,fieldId],required_list:[fieldId],
         conditions:[{name:"name",op:"like_custom",end:"%",value:""}],
			post_onblur_function: postBlurFunction,
         order:"name","limit":"30","no_match_text":"No Match"};

     window['sqs_objects'][fullName]['parent_opportunity_only'] = '0';
     //QSFieldsArray[fullName].sqs.parent_opportunity_only = '0';
     if(omodule == 'ProjectOpportunities'){
    	 window['sqs_objects'][fullName]['parent_opportunity_only'] = '1';
    	// QSFieldsArray[fullName].sqs.parent_opportunity_only = '1';
     }

     if(typeof QSProcessedFieldsArray != 'undefined')
     	QSProcessedFieldsArray[fullName] = false;
     if (typeof(QSFieldsArray) != 'undefined' && typeof(QSFieldsArray[fullName]) != 'undefined') {
     	QSFieldsArray[fullName].destroy();
     	delete QSFieldsArray[fullName];
     }
     if (Dom.get(fullName + "_results")) {
     	Dom.get(fullName + "_results").parentNode.removeChild(Dom.get(fullName + "_results"));
     }

     enableQS(false);
 };

 SUGAR.email2.composeLayout.callopenpopupForEmail2 = function(idx,options) {

     var formName = 'emailCompose' + idx;

     if(typeof(options) != 'undefined' && typeof(options.form_name) != 'undefined')
         formName = options.form_name;

		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		if (!SE.composeLayout.isParentTypeValid(idx)) {
			return;
		} // if

		var module = parentTypeValue;
		if( parentTypeValue == "ProjectOpportunities" || parentTypeValue == "ClientOpportunities" ){
			var module = "Opportunities";
		}
		
		var params = '';
		if(parentTypeValue == "ProjectOpportunities" ){
			params = "&tree=ProductsProd&parent_opportunity_only=true";
		}else{
			params = "&tree=ProductsProd";
		}
		
		open_popup(module,600,400,params,true,false,{
			call_back_function:"SE.composeLayout.popupAddEmail",
			form_name:formName,
			field_to_name_array:{
				id:'data_parent_id' + idx,
				name:'data_parent_name' + idx,
				email1:'email1'}
		});
};
{/literal}
</script>
</div>