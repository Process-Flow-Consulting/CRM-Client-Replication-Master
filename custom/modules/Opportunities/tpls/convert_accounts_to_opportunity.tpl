{literal}
<style type="text/css">
.role_div {
	
}
.role_div li {
	list-style: none;
}
</style>
{/literal}
{assign var=url value='index.php?'}
{foreach from=$smarty.get item=values key=params}
{assign var=url value=$url|cat:$params|cat:"="|cat:$values|cat:"&"}
{/foreach}

{assign var=url value=$url|cat:"&odr="|cat:$order}
<div class="moduleTitle">
	<h2>{$MOD.LBL_CREATE_ACCOUNTS_OPPORTUNITY_TITLE}</h2>
</div>
<form id="EditView" name="EditView" method="POST" action="index.php">
	<input type="hidden" value="Opportunities" name="module"> 
	<input type="hidden" name="action" value="save_accounts_opportunity"> 
	<input type="hidden" name="clientIds" id="clientIds" value="{$ST_ASSIGN_IDS}">
	<input type="hidden" name="odr" id="odr" value="{$order}">
	<input type="hidden" name="sort" id="sort" value="{$orderBy}">
	<input type="hidden" name="record" value="">
	<div style="height: 30px;"></div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top">
				<label><input onclick ="showHideDiv(this.value)" type="radio" title="" id="opportunity_type" value="1" name="opportunity_type">
					{$MOD.LBL_CREATE_NEW_OPPORTUNITY}
				</label>&nbsp;&nbsp;
				<label><input onclick ="showHideDiv(this.value)" type="radio" title="" id="opportunity_type" checked="checked" value="2" name="opportunity_type">
					{$MOD.LBL_CREATE_EXISTING_OPPORTUNITY}
				</label>
			</td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>		
	</table>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
			<input id="SAVE_HEADER" class="button primary" type="submit"
				value="Save" name="button" accesskey="S" title="Save [Alt+S]"
				onclick="{literal}if(checkSelected()){SUGAR.ajaxUI.showLoadingPanel();return  true;;}else{return false;};{/literal}"> <input id="CANCEL_HEADER"
				class="button" type="button" value="Cancel" name="button"
				onclick="window.location.href='index.php?module=Accounts&action=ListView'; return false;"
				accesskey="X" title="Cancel [Alt+X]"></td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>		
	</table>
	<div id="new_opportunity_id" style="display:none">
				
	</div>
	<div id="existing_opportunity_id" style="display:block">
	<table width="100%" height="20%" cellpadding="0" cellspacing="0" border="0" class="list view">		
		<tbody>
		    <tr>
				<td colspan="2" scope="col" style="background-color: #EBEBED; font-weight: bold;"></td>									
			</tr>
			<tr>
				<td  scope="col" style="border:0;background-color: #EBEBED; font-weight: bold;text-align:right;" width="30%">{$MOD.LBL_PROJECT_OPPORTUNITY}:</td>									
            	<td  style="text-align:left;" width="70%">
            	<input type="text" autocomplete="off" title="" value="" size="" id="opportunity_name" tabindex="0" class="sqsEnabled sqsNoAutofill" name="opportunity_name">
            	<input type="hidden" value="" id="parent_opportunity_id" name="parent_opportunity_id">
            	<span class="id-ff multiple">
					<button
					 onclick='open_popup("Opportunities",600,400,"&parent_opportunity_only=true",true,false,{literal} { {/literal} "call_back_function":"setReturnOpportunityAssigned","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"parent_opportunity_id","name":"opportunity_name" {literal} }} {/literal} ,
"single",true);'						
					 value="Select" class="button firstChild" title="Select" tabindex="0" id="btn_opportunity_name" name="btn_opportunity_name" type="button">
					<img src="themes/default/images/id-ff-select.png">
					</button>
					<button value="Clear Selection" onclick="SUGAR.clearRelateField(this.form, 'opportunity_name', 'parent_opportunity_id');" class="button lastChild" title="Clear Selection" tabindex="0" id="btn_clr_opportunity_name" name="btn_clr_opportunity_name" type="button">
					<img src="themes/default/images/id-ff-clear.png">
					</button>
				</span>
            	</td>	
			</tr>
			<tr>
				<td colspan="2" scope="col" style="background-color: #EBEBED; font-weight: bold;"></td>									
			</tr>
			</tbody>		
	</table>
	</div>	
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="list view">
		<tbody>
			<tr style="background-color: #EBEBED; font-weight: bold;">
				<td  scope="col" width="19%">				
					<a  class="listViewThLinkS1" href="javascript:void(0)" onclick="customSort();"  >
					{$MOD.LBL_ACCOUNTS_COMPANY}
					</a>
					{if $smarty.request.sort eq 'name' and $smarty.request.odr eq 'ASC' }
	                    {assign var=imageName value='arrow_up.gif'}
    	           {elseif $smarty.request.sort eq 'name' and $smarty.request.odr eq 'DESC' }
        	           {assign var=imageName value='arrow_down.gif'}
            	   {else}
                	    {assign var=imageName value='arrow.gif'}
               		{/if}
               		<img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
               </td>
				<td  scope="col" width="10%" align="center">{$MOD.LBL_ACCOUNTS_CLASSFICATION}<br/>
					<select title="" id="opportunity_classification_all" name="opportunity_classification_all" onchange="return setClassificationId(this.value)">
					{$customClassificationsListAll}
					</select>
				</td>
				<td  scope="col" width="25%">{$MOD.LBL_ACCOUNTS_CONTACT}</td>
				<td  scope="col" width="8%" align="right" >{$MOD.LBL_OPPORTUNITY_ASSIGNED_USER}<br/><input type="text"
					autocomplete="off" value="" size=""
					id="assigned_user_nameClient"
					class="sqsEnabled sqsNoAutofill"
					name="assigned_user_nameClient"> <input type="hidden"
					value="" id="assigned_user_idClient"
					name="assigned_user_idClient">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Users",600,400,"&lead_reviewer=false",true,false,{literal} { {/literal} "call_back_function":"set_return_assigned","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"assigned_user_idClient","user_name":"assigned_user_nameClient" {literal} }} {/literal} ,
"single",true);'
							value="Select" class="button firstChild" accesskey="T"
							title="Select [Alt+T]" id="btn_assigned_user_nameClient"
							name="btn_assigned_user_nameClient" type="button">
							<img src="themes/default/images/id-ff-select.png">
						</button><button value="Clear"
							onclick="document.getElementById('assigned_user_nameClient').value = ''; document.getElementById('assigned_user_idClient').value = '';"
							class="button lastChild" accesskey="C" title="Clear [Alt+C]"
							id="btn_clr_assigned_user_nameClient" name="btn_clr_assigned_user_nameClient"
							type="button">
							<img src="themes/default/images/id-ff-clear.png">
						</button>
					</span>
				</td>							
			</tr>
			{foreach name=accountIteration from=$account_list item=eachAccount} {if
			$smarty.foreach.accountIteration.iteration is odd} {assign
			var='_rowColor' value=oddListRow} {else} {assign var='_rowColor'
			value=evenListRow} {/if}
			<tr class='{$_rowColor}S1'>
				<td  style="vertical-align: top;">
					{if $eachAccount.proview_url neq ""  }
						{sugar_proview_url url=$eachAccount.proview_url}<a href="index.php?module=Accounts&action=DetailView&record={$eachAccount.id}" target="_blank">{$eachAccount.name}</a>
					{else}
						<a href="index.php?module=Accounts&action=DetailView&record={$eachAccount.id}"  target="_blank" class="no_proview" >{$eachAccount.name}</a>
					{/if}
				</td>
				<td style="vertical-align: top;">
					<select title="" id="opportunity_classification_{$eachAccount.id}" name="opportunity_classification_{$eachAccount.id}">
					{$eachAccount.customClassificationsList}
					</select>					
				</td>
				{*
				<td style="vertical-align: top;">
					{if $eachAccount.customClassifications|@count gt 1}<a id="displayText_{$eachAccount.id}"
					href="javascript:toggle('{$eachAccount.id}');">&nbsp;<strong>+</strong>&nbsp;</a>
					{/if} 
					{if $eachAccount.customClassifications[0] eq ''} Unknown 
					{else}
					{$eachAccount.customClassifications[0]} 
					{/if}
					<div class="role_div" id="role-div_{$eachAccount.id}" style="display: none; padding-left: 10px;">
						{foreach from=$eachAccount.customClassifications item=role name=roleArr} 
							{if $smarty.foreach.roleArr.index neq 0} <li>{$role}</li> {/if} 
						{/foreach}
					</div>
				</td>
				*}
				<td id ="td_{$eachAccount.id}" style="vertical-align: top; padding-right:20px;" align="left">
					<input type="text" 
					autocomplete="off" value="{$eachAccount.contact_name}" size="" 
					id="contact_name_{$eachAccount.id}"
					class="sqsEnabled sqsNoAutofill yui-ac-input"
					name="contact_name_{$eachAccount.id}" >
					<input type="hidden"
					value="{$eachAccount.contact_id}" id="contact_id_{$eachAccount.id}"
					name="contact_id_{$eachAccount.id}">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Contacts",600,400,"&account_id={$eachAccount.id}",true,false,{literal} { {/literal} "call_back_function":"set_return","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"contact_id_{$eachAccount.id}","name":"contact_name_{$eachAccount.id}" {literal} }} {/literal} ,
"single",true);'
							value="Select" class="button firstChild" accesskey="T"
							title="Select [Alt+T]" id="btn_contact_name_{$eachAccount.id}"
							name="btn_contact_name" type="button">
							<img src="themes/default/images/id-ff-select.png">
						</button>
						<button value="Clear"
							onclick="document.getElementById('contact_name_{$eachAccount.id}').value = ''; document.getElementById('contact_id_{$eachAccount.id}').value = '';"
							class="button lastChild" accesskey="C" title="Clear [Alt+C]"
							id="btn_clr_contact_name_{$eachAccount.id}" name="btn_clr_contact_name"
							type="button">
							<img src="themes/default/images/id-ff-clear.png">
						</button>
					</span>
				</td>
				<td id ="td_{$eachAccount.id}" style="vertical-align: top; padding-right:20px;" align="left">
					<input type="text" 
					autocomplete="off" value="{$eachAccount.assigned_user_name}" size="" 
					id="assigned_user_name_{$eachAccount.id}"
					class="sqsEnabled sqsNoAutofill yui-ac-input"
					name="assigned_user_name_{$eachAccount.id}">
					<input type="hidden"
					value="{$eachAccount.assigned_user_id}" id="assigned_user_id_{$eachAccount.id}"
					name="assigned_user_id_{$eachAccount.id}">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Users",600,400,"&lead_reviewer=false",true,false,{literal} { {/literal} "call_back_function":"set_return","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"assigned_user_id_{$eachAccount.id}","user_name":"assigned_user_name_{$eachAccount.id}" {literal} }} {/literal} ,
"single",true);'
							value="Select" class="button firstChild" accesskey="T"
							title="Select [Alt+T]" id="btn_assigned_user_name_{$eachAccount.id}"
							name="btn_assigned_user_name_{$eachAccount.id}" type="button">
							<img src="themes/default/images/id-ff-select.png">
						</button><button value="Clear"
							onclick="document.getElementById('assigned_user_name_{$eachAccount.id}').value = ''; document.getElementById('assigned_user_id_{$eachAccount.id}').value = '';"
							class="button lastChild" accesskey="C" title="Clear [Alt+C]"
							id="btn_clr_assigned_user_name_{$eachAccount.id}" name="btn_clr_assigned_user_name_{$eachAccount.id}"
							type="button">
							<img src="themes/default/images/id-ff-clear.png">
						</button>
					</span>
				</td>
			</tr>
			{foreachelse}
			<tr>
				<td colspan="7" align="center"><strong>No Clients were found.</strong></td>
			</tr>
			{/foreach} 
			</tbody>
	</table>	
</form>
{literal}
<script type="text/javascript">
/*
function toggle(bid) {        
    var ele = document.getElementById("role-div_"+bid);
    var text = document.getElementById("displayText_"+bid);
    if(ele.style.display == "block") {
            ele.style.display = "none";
            text.innerHTML = "&nbsp;<strong>+</strong>&nbsp;";
    }
    else {
            ele.style.display = "block";
            text.innerHTML = "&nbsp;<strong>-</strong>&nbsp;";
    }
}
*/
function checkSelected(){
	countClientRecord = '{/literal}{$countClientResult}{literal}';
	var oppType = jQuery('input[name=opportunity_type]:checked').val();
	var clientIdstring = jQuery("#clientIds").val();
	var clienIdArr = clientIdstring.split(",");
	jQuery.each(clienIdArr, function(key, value){
		addToValidate('EditView', 'contact_name_'+value, 'varchar', true,'Contact' );
		addToValidate('EditView', 'assigned_user_name_'+value, 'varchar', true,'Assign User' );	
	});
	if(countClientRecord>0){
		if(oppType==2) {
			addToValidate('EditView', 'opportunity_name', 'varchar', true,'Project Opportunity' );
			removeFromValidate("EditView", "name");
			removeFromValidate("EditView", "amount");
			removeFromValidate("EditView", "sales_stage");
			removeFromValidate("EditView", "bid_due_timezone");
			removeFromValidate("EditView", "date_closed_date");
			removeFromValidate("EditView", "date_closed_hours");
			removeFromValidate("EditView", "date_closed_minutes");
			removeFromValidate("EditView", "date_closed_meridiem");
			removeFromValidate("EditView", "team_id");
			removeFromValidate("EditView", "team_set_id");
			removeFromValidate("EditView", "team_count");
			removeFromValidate("EditView", "team_name");
		} else if(oppType==1) {
			removeFromValidate("EditView", "opportunity_name");
		}
		return check_form('EditView');
	} else{
	 	alert("Please select clients first.");
	}
}

function showHideDiv(val) {
	if(val==1) {
		SUGAR.ajaxUI.showLoadingPanel(); 
		jQuery.ajax({
	        type: "POST",
	        url: "index.php?module=Opportunities&action=getquickeditopportunity&to_pdf=1&target_view=EditView",
	        data: {create_accounts_opportunity: 1},
	       	cache: false,
	       	async: true,
	        complete: function (resonseData) {
    			objData = jQuery.parseJSON( resonseData.responseText ); 
    			document.getElementById("new_opportunity_id").innerHTML = objData.html;
    			SUGAR.util.evalScript(objData.html); 
	            SUGAR.ajaxUI.hideLoadingPanel();
	        }
    	});
    	jQuery("#parent_opportunity_id").val("");
    	jQuery("#opportunity_name").val("");
		document.getElementById("new_opportunity_id").style.display = "block";
		document.getElementById("existing_opportunity_id").style.display = "none";
	} else if(val==2) {		
		document.getElementById("new_opportunity_id").style.display = "none";
		document.getElementById("existing_opportunity_id").style.display = "block";
	}
}

function customSort() {
	var frm=$('#EditView');
	$('#EditView input[name=action]').val('accounts_opportunity');
	$('#EditView input[name=module]').val('Opportunities');
	frm.submit();			
}

function setReturnOpportunityAssigned(popup_reply_data){
	//if callback function called from popup then name_to_value_array have object value
	//else if callback function called from SQS it is have undefined value
	if(typeof popup_reply_data.name_to_value_array != 'undefined'){
		set_return(popup_reply_data);
		var parentOpportunityId = popup_reply_data.name_to_value_array.parent_opportunity_id;
	} else {
		var parentOpportunityId = document.getElementById('parent_opportunity_id').value;
	}
	var geoFilter = "{/literal}{$geoFilter}{literal}";
	if(parentOpportunityId != '' && geoFilter == 'project_location') {
		jQuery.ajax({
	        type: "POST",
	        url: "index.php?module=Opportunities&action=assigneduser&to_pdf=true&for_convert_client_project_opp=true",
	        data: {parentOpportunityId: parentOpportunityId},
	        dataType: "json",
	       	cache: false,
	       	async:false,
	        success: function (json) {
	        	if(json !="balnk"){
		            var assigned_user_name = json.name;
					var assigned_user_id = json.id;
		
					var assignee_name = YAHOO.util.Selector.query('input[id^=assigned_user_name_]');
					for (var i = 0; i < assignee_name.length; i++){
						assignee_name[i].value = assigned_user_name;
					}
				
					var assignee_id = YAHOO.util.Selector.query('input[id^=assigned_user_id_]');
					for (var i = 0; i < assignee_id.length; i++){
						assignee_id[i].value = assigned_user_id;
					}
	        	}		
	        }
        });		
	}	
}

function set_return_assigned(popup_reply_data){
	//if callback function called from popup then name_to_value_array have object value
	//else if callback function called from SQS it is have undefined value
	if(typeof popup_reply_data.name_to_value_array != 'undefined'){
		set_return(popup_reply_data);
	}	
	var assigned_user_name = document.getElementById('assigned_user_nameClient').value;
	var assigned_user_id = document.getElementById('assigned_user_idClient').value;

	var assignee_name = YAHOO.util.Selector.query('input[id^=assigned_user_name_]');
	for (var i = 0; i < assignee_name.length; i++){
		assignee_name[i].value = assigned_user_name;
	}

	var assignee_id = YAHOO.util.Selector.query('input[id^=assigned_user_id_]');
	for (var i = 0; i < assignee_id.length; i++){
		assignee_id[i].value = assigned_user_id;
	}
}
function setClassificationId(classficationId) {
	var opportunity_classification = YAHOO.util.Selector.query('select[id^=opportunity_classification_]');
	for (var i = 0; i < opportunity_classification.length; i++){
		opportunity_classification[i].value = classficationId;
	}
}
$(document).ready(function() {
 $("a.no_proview").each(function(indexVal,elm){$(elm).tipTip({maxWidth: "auto",edgeOffset: 10,content: "No proview available.",defaultPosition: "bottom"})});
 SUGAR.ajaxUI.showLoadingPanel();
 SUGAR.ajaxUI.hideLoadingPanel();
});
SUGAR.util.doWhen(
"typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['EditView_opportunity_name']) != 'undefined'",
enableQS
);
SUGAR.util.doWhen(
"typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['EditView_assigned_user_nameClient']) != 'undefined'",
enableQS
);
if (typeof sqs_objects == 'undefined') {
	var sqs_objects = new Array;
}
//use for assigning sqs object to to project opportunity 
//and then assigned assigned user to other assigned user name also as per user filter rules
sqs_objects['EditView_opportunity_name'] = {
	"form": "EditView",
	"method": "query",
	"modules": ["Opportunities"],
	"group": "and",
	"field_list": ["name", "id"],
	"populate_list": ["opportunity_name", "parent_opportunity_id"],
	"required_list": ["parent_id"],
	"conditions": [{
	"name": "name",
	"op": "like_custom",
	"end": "%",
	"value": ""
	}],
	"order": "name",
	"limit": "30",
	"no_match_text": "No Match",
	"parent_opportunity_only": "1",
	"post_onblur_function":"setReturnOpportunityAssigned"
};

//use for assigning sqs object to top assigned user name 
//and then assigned results to other assigned user name also
sqs_objects['EditView_assigned_user_nameClient'] = {
    "form": "EditView",
    "method": "get_user_array",
    "field_list": ["user_name", "id"],
    "populate_list": ["assigned_user_nameClient", "assigned_user_idClient"],
    "required_list": ["assigned_user_idClient"],
    "conditions": [{
        "name": "user_name",
        "op": "like_custom",
        "end": "%",
        "value": ""
    }],
    "limit": "30",
    "no_match_text": "No Match",
    "lead_reviewer": "false",
    "post_onblur_function":"set_return_assigned"
};

//use for assigning sqs object to each of the client contact 
//and also enable condition for find contact for coressponding client only 
$('input[name^=contact_name_]').each(
	function(idx,elm){
		var contactName = elm.id;
		var contactId = contactName.replace("contact_name_","contact_id_");
		var clientIdArray = contactName.split("_");
		var clientId = clientIdArray[2];
		sqs_objects['EditView_'+contactName]={
			"form":"EditView",
			"method":"get_default_contact_array",
			"modules":["Contacts"],
			"field_list":["salutation","first_name","last_name","id"],
			"populate_list":[contactName,contactId,contactId,contactId],
			"required_list":["contact_id"],
			"group":"or",
			"conditions":[
				{"name":"first_name",
				"op":"contains",
				"end":"%",
				"value":""
				},
				{"name":"last_name",
				"op":"contains",
				"end":"%",
				"value":""
				},
				{"name":"salutation",
				"op":"contains",
				"end":"%",
				"value":""
				}
			],
			"order":"last_name",
			"limit":"30",
			"no_match_text":"No Match",
			"account_contact_id" : clientId
		}
	}
);
//use for assigning sqs object to each of the assigned user name 
$('input[name^=assigned_user_name_]').each(
	function(idx,elm){
		var assignedUserName = elm.id;
		var assignedUserId = assignedUserName.replace("assigned_user_name_","assigned_user_id_");
		var clientIdArray = assignedUserName.split("_");
		var clientId = clientIdArray[2];
		sqs_objects['EditView_'+assignedUserName]={
			"form": "EditView",
		    "method": "get_user_array",
		    "field_list": ["user_name", "id"],
		    "populate_list": [assignedUserName, assignedUserId],
		    "required_list": ["assigned_user_id"],
		    "conditions": [{
		        "name": "user_name",
		        "op": "like_custom",
		        "end": "%",
		        "value": ""
		    }],
		    "limit": "30",
		    "no_match_text": "No Match",
		    "lead_reviewer": "false",
		}
	}
);
</script>
{/literal}