{literal}
<style type="text/css">
.role_div {
	
}
.role_div li {
	list-style: none;
}
</style>
{/literal}
<div>
<form id="MassUpdateOpp" name="MassUpdateOpp" method="POST" action="index.php">
	<input type="hidden" value="Opportunities" name="module"> 
	<input type="hidden" value="clientoppmassupdate" name="action"> 
	<textarea name="uid" id="uid" style="display: none">{$OPPIDSTR}</textarea>
	<input type="hidden" value="{$PARENTID}" name="parentId" id="parentId">
				
	<div id="LBL_PANEL_ASSIGNMENT">
		<table class="yui3-skin-sam edit view dcQuickEdit edit508" border="0" cellspacing="1" cellpadding="0" width="100%">
			<tbody>	
				<tr>
				<td id="classification_label" width="12.5%" valign="top" scope="col">
					<label for="opportunity_classification">{$MOD.LBL_ACCOUNTS_CLASSFICATION}</label>
				</td>
				<td width="37.5%" valign="top">
					<select title="" id="opportunity_classification" name="opportunity_classification">
					{$customClassificationsListAll}
					</select>
				</td>
				<td id="client_bid_status_label" width="12.5%" valign="top" scope="col">
						<label for="client_bid_status">{$MOD.LBL_CLIENT_BID_STATUS}</label>
				</td>
				<td width="37.5%" valign="top">
					<select title="" id="client_bid_status" name="client_bid_status">
					{$clientBidStatus}
					</select>
				</td>				
				</tr>			
				<tr>
				<td id="salse_stage_label" width="12.5%" valign="top" scope="col">
					<label for="salse_stage_label">{$MOD.LBL_LIST_SALES_STAGE}</label>
				</td>
				<td width="37.5%" valign="top">
					<select title="" id="salse_stage" name="salse_stage">
					{$salesStage}
					</select>
				</td>
				<td id="assigned_user_name_label" width="12.5%" valign="top" scope="col">
						<label for="assigned_user_name">{$MOD.LBL_LIST_ASSIGNED_TO_NAME}</label>
				</td>
				<td width="37.5%" valign="top">
					<input type="text"
					autocomplete="off" value="" size=""
					id="assigned_user_name"
					class="sqsEnabled sqsNoAutofill"
					name="assigned_user_name"> 
			    	<input type="hidden"
					value="" id="assigned_user_id"
					name="assigned_user_id">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Users",600,400,"&lead_reviewer=false",true,false,{literal} { {/literal} "call_back_function":"set_return_assigned","form_name":"MassUpdateOpp","field_to_name_array": {literal} { {/literal} "id":"assigned_user_id","user_name":"assigned_user_name" {literal} }} {/literal} ,
"single",true);'
							value="Select" class="button firstChild" accesskey="T"
							title="Select [Alt+T]" id="btn_assigned_user_name"
							name="btn_assigned_user_name" type="button">
							<img src="themes/default/images/id-ff-select.png">
						</button><button value="Clear"
							onclick="document.getElementById('assigned_user_name').value = ''; document.getElementById('assigned_user_id').value = '';"
							class="button lastChild" accesskey="C" title="Clear [Alt+C]"
							id="btn_clr_assigned_user_name" name="btn_clr_assigned_user_name"
							type="button">
							<img src="themes/default/images/id-ff-clear.png">
						</button>
					</span>
				</td>				
				</tr>
			</tbody>
	</table>
	</div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
			<input id="SAVE_HEADER" class="button primary" type="submit"
				value="Update" name="button" accesskey="S" title="Update [Alt+S]" onclick="return checkCustom();"> 
			<input id="CANCEL_HEADER"
				class="button" type="button" value="Cancel" name="button"
				onclick="$('.ui-dialog').find('.massUpdate').dialog('close');"
				accesskey="X" title="Cancel [Alt+X]">
			</td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>		
	</table>	
</form>
</div>
{literal}
<script type="text/javascript">
function checkCustom(){
	uid = '{/literal}{$OPPIDSTR}{literal}';
	uidCount = uid.split(",").length;
	if(confirm('Are you sure you want to update the '+uidCount+' selected record(s)?')){
		return true;
	} else {
		return false;
	}	
}
SUGAR.util.doWhen(
"typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['MassUpdateOpp_assigned_user_name']) != 'undefined'",
enableQS
);
if (typeof sqs_objects == 'undefined') {
	var sqs_objects = new Array;
}
function set_return_assigned(popup_reply_data){
	//if callback function called from popup then name_to_value_array have object value
	//else if callback function called from SQS it is have undefined value
	if(typeof popup_reply_data.name_to_value_array != 'undefined'){
		set_return(popup_reply_data);
	}	
}
//use for assigning sqs object to  assigned user name 
sqs_objects['MassUpdateOpp_assigned_user_name'] = {
    "form": "MassUpdateOpp",
    "method": "get_user_array",
    "field_list": ["user_name", "id"],
    "populate_list": ["assigned_user_name", "assigned_user_id"],
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
    "post_onblur_function":"set_return_assigned"
};
</script>
{/literal}