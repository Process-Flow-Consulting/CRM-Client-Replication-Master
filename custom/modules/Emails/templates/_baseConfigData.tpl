{*
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

*}

SUGAR.email2.composeLayout.charsets = {$emailCharsets};
SUGAR.default_inbound_accnt_id = '{$defaultOutID}';
SUGAR.email2.userPrefs = {$userPrefs};
SUGAR.email2.signatures = {$defaultSignature};
{$tinyMCE}
SUGAR.email2.composeLayout.teams = '';
linkBeans = {$linkBeans};
{$lang}


{literal}


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
 
/**
*    Enable Popup for Client and Project opportunities
*/
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