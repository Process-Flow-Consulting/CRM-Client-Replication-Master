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
<form id="EditView" name="EditView" method="POST" action="index.php">
	<input type="hidden" value="Leads" name="module"> 
	<input type="hidden" name="action" value="save_new_opportunity"> 
	<input type="hidden" name="primary_lead_id" value="{$record}">
	<input type="hidden" name="classification" value="{$classification}"> 
	<input type="hidden" name="record" value="{$record}">
	<input type="hidden" name="opportunity_id" id="opportunity_id" value="{$opportunity_id}">
	<input type="hidden" name="earlier_date" id="earlier_date" value="{$earlier_date}">
	<input type="hidden" name="earlier_bids_due_timezone" id="earlier_bids_due_timezone" value="{$earlier_bids_due_timezone}">
		
	<div style="height: 20px;"></div>
	<div class="detail view  detail508">
	<h4>&nbsp;</h4>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr height="30">
			<td scope="col" style="font-size: 13px; font-weight: bold;width:195px">{$MOD.LBL_OPPORTUNITY_AMOUNT}:</td>
			<td style="font-size: 13px;">${$p_oppr_amount}</td>
		</tr>
		<tr height="30">
			<td scope="col" style="font-size: 13px; font-weight: bold;">{$MOD.LBL_SALES_STAGE}:</td>
			<td style="font-size: 13px;">{$p_oppr_sales_stage}</td>			
		</tr>
		<tr>
			<td scope="col" colspan="2">&nbsp;</td>
		</tr>
		</table>
	</div>	
	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		{* <tr>
			<td colspan="2" style="font-weight: bold;">{$MOD.LBL_PREV_CONVERTED_BIDDER}</td>
		</tr> 
		
		<tr>
			<td colspan="2">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="list view">
					<tbody>
						<tr style="background-color: #EBEBED; font-weight: bold;">
							<td width="30%">{$MOD.LBL_COMPANY}</td>
							<td>{$MOD.LBL_ROLE}</td>
						</tr>
						{foreach from=$prev_bidders_array item=prev_bidder}
						<tr>
							<td style="vertical-align: top;">{$prev_bidder.account_name}<!-- {if
					$prev_bidder.lead_source eq 'bb'} <img alt="Blue Book Icon"
					src="custom/themes/default/images/blue_book_icon.jpg"> {/if} --></td>
				<td>
					{if	$prev_bidder.classifications|@count gt 1}<a style="text-decoration: none;" id="displayTextC_{$prev_bidder.id}"
					href="javascript:toggleClientOppr('{$prev_bidder.id}');">&nbsp;<strong>+</strong>&nbsp;
				</a>{/if} {if $prev_bidder.classifications|@count eq 0} Unknown {else}
					{$prev_bidder.classifications[0]} {/if}
					<div class="role_div" id="role-div-c_{$prev_bidder.id}" style="display: none; padding-left: 10px;">
						{foreach from=$prev_bidder.classifications item=classification_name name=classificationArr} {if
						$smarty.foreach.classificationArr.index neq 0}
						<li>{$classification_name}</li> {/if} {/foreach}
					</div>
				</td>	
						</tr>
						{/foreach}
					</tbody>
				</table>*}
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
			<input id="SAVE_HEADER" class="button primary" type="submit"
				value="Save" name="button" accesskey="S" title="Save [Alt+S]"
				onclick="{literal}if(checkSelected()){SUGAR.ajaxUI.showLoadingPanel();return  true;;}else{return false;};{/literal}" > 
			<input id="CANCEL_HEADER" class="button" type="button" value="Cancel" name="button"
				onclick="window.location.href='index.php?module=Leads&action={$return_action}{if $return_action neq 'ListView'}&record={$record}{/if}'; return false;"
				accesskey="X" title="Cancel [Alt+X]">
			</td>			
		</tr>						
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>	
		<tr >
		<td  align="right" style="font-size: 13px;" colspan="2">
			<div style="float:left;width:45%;text-align:left;padding:6px 0">
				<img align="absmiddle"  src="{sugar_getimagepath file='green_money.gif'}" />Indicates Bidders who were previosly converted on an opportunity from this Project Lead. 
		    </div>
			
			<div style="float:right;width:45%;">
				
			{$MOD.LBL_CLASSIFICATION_MSG}:  
			<select name="all_classification"
				onchange="javascript:classificationFilter(this.value);">
				<option value="">Select Classification</option>
				{html_options output=$all_classification values=$all_classification selected=$classification}
			</select>
			</div>
			</td>
		</tr>
	</table>
	
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="list view">
		<tbody>
			<tr style="background-color: #EBEBED; font-weight: bold;">
				<td  scope="col" width="5%" style="padding-left:9px;">
				{*$MOD.LBL_CREATE*}
				<input title="{$MOD.LBL_CREATE}" type="checkbox" id="bidder_check_all" name="bidder_check_all" onClick='checkAllBidder();'>
				</td>				
				<td  scope="col" width="5%" align="center">{$MOD.LBL_PRE_BID_TO}</td>				
				<td  scope="col" width="20%">
						<a  class="listViewThLinkS1" href="{$url}&sort=account_name&odr={$order}"  >
					{$MOD.LBL_COMPANY}
					</a>
					{if $smarty.request.sort eq 'account_name' and $smarty.request.odr eq 'ASC' }
	                    {assign var=imageName value='arrow_up.gif'}
    	           {elseif $smarty.request.sort eq 'account_name' and $smarty.request.odr eq 'DESC' }
        	           {assign var=imageName value='arrow_down.gif'}
            	   {else}
                	    {assign var=imageName value='arrow.gif'}
               		{/if}
               		<img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
               </td>
				<td  scope="col" width="10%" align="center">{$MOD.LBL_ADD_TO_CONTACT}</td>
				<td  scope="col" width="20%">{$MOD.LBL_ROLE}</td>
				<td scope="col" width="10%">		
								<a class="listViewThLinkS1" href="{$url}&sort=role&odr={$order}"  >Role</a>
						{if $smarty.request.sort eq 'role' and $smarty.request.odr eq 'ASC' }
			                    {assign var=imageName value='arrow_up.gif'}
			               {elseif $smarty.request.sort eq 'role' and $smarty.request.odr eq 'DESC' }
			                   {assign var=imageName value='arrow_down.gif'}
			               {else}
			                    {assign var=imageName value='arrow.gif'}
			               {/if}
			                <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
				</td>
				<td  scope="col" width="10%">{$MOD.LBL_BID_STATUS}</td>		
				<td  scope="col" width="15%" align="right" style="padding-right:20px;">{$MOD.LBL_ASSIGN}&nbsp;&nbsp;<input type="text"
					autocomplete="off" value="" size=""
					id="assigned_user_name"
					class="sqsEnabled sqsNoAutofill yui-ac-input"
					name="assigned_user_name"> <input type="hidden"
					value="" id="assigned_user_id"
					name="assigned_user_id">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Users",600,400,"&lead_reviewer=false",true,false,{literal} { {/literal} "call_back_function":"set_return_assigned","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"assigned_user_id","user_name":"assigned_user_name" {literal} }} {/literal} ,
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
			{foreach name=bidderIteration from=$bidders_list item=bidders  name=count} {if
			$smarty.foreach.bidderIteration.iteration is odd} {assign
			var='_rowColor' value=oddListRow} {else} {assign var='_rowColor'
			value=evenListRow} {/if}
			<tr class='{$_rowColor}S1'>
				<td style="vertical-align: top;"><input type="checkbox"
					id="bid_{$smarty.foreach.count.index}" name="bid[]" value="{$bidders.id}" onClick=enableAssignee(this.id,this.value);><input type="hidden"
					name="account_id_{$bidders.id}" value="{$bidders.account_id}"> <input
					type="hidden" name="lead_id_{$bidders.id}"
					value="{$bidders.lead_id}"> <input type="hidden"
					name="biddersIds_{$bidders.id}" value="{$bidders.bidder_ids_str}">
					<input type="hidden" name="contact_id_{$bidders.id}" value="{$bidders.contact_id}">
				</td>
				<td style="vertical-align: top;" align="center">{if $bidders.prev_bid_to eq 1}<!-- <img align="absmiddle" src="{sugar_getimagepath file=green_money.gif }" title="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}"  alt="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}" /> -->*{/if}</td>				
				
				<td  style="vertical-align: top;">
				{if $bidders.converted_to_oppr eq '1'}
				<img align="absmiddle" src="{sugar_getimagepath file=green_money.gif }" title="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}"  alt="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}" /> 
				{/if}
				{sugar_proview_url url=$bidders.proview_url}
					<!--  {if $bidders.proview_url neq ""}
					<a href="javascript:void(0)"  onclick="window.open('{$bidders.proview_url|to_url}','','width=600,height=500')"  />
					<img src="custom/themes/default/images/proview_icon.gif" border="0"/>
					</a>
					{/if}-->
					
					{if $bidders.client_visibility eq '1'}
					<a href="index.php?module=Accounts&action=DetailView&record={$bidders.company_id}" target="_blank">{$bidders.company}</a> 
					{else} 
					{if $bidders.proview_url neq ""}
					<a href="{$bidders.proview_url|to_url}" target="_blank">{$bidders.company}</a>
					{else}
					<a href="javascript:void(0)" class="no_proview" >{$bidders.company}</a>
					{/if} 
					{/if}
				</td>
				<td align="center" style="vertical-align: top;"
					id="tr_add_to_client_{$bidders.id}">{if $bidders.client_visibility
					eq '1'} <img alt="Create"
					src="custom/themes/default/images/tick_mark.jpg"> {else} <img
					alt="Create" src="themes/Sugar/images/create-record.png"
					style="cursor: pointer;"
					onclick="addToContact('{$bidders.account_id}','{$bidders.id}');">
					{/if}
				</td>
				<td style="vertical-align: top;">{if
					$bidders.roles|@count gt 1}<a id="displayText_{$bidders.id}"
					href="javascript:toggle('{$bidders.id}');">&nbsp;<strong>+</strong>&nbsp;
				</a>{/if} {if $bidders.roles|@count eq 0} Unknown {else}
					{$bidders.roles[0]} {/if}
					<div class="role_div" id="role-div_{$bidders.id}"
						style="display: none; padding-left: 10px;">
						{foreach from=$bidders.roles item=role name=roleArr} {if
						$smarty.foreach.roleArr.index neq 0}
						<li>{$role}</li> {/if} {/foreach}
					</div>
				</td>
				<td >{$bidders.role}</td>
				<td >{$bidders.bid_status}</td>
				<td id ="td_{$bidders.id}" style="vertical-align: top; background-color:#BDBDBD; padding-right:20px;" align="right">
					<input type="text" 
					autocomplete="off" size="" 
					id="assigned_user_name_{$bidders.id}"
					class="sqsEnabled sqsNoAutofill yui-ac-input"
					value="{$bidders.assigned_user_name}" 
					name="assigned_user_name_{$bidders.id}" disabled="true">
					<input type="hidden"
					value="{$bidders.assigned_user_id}"  id="assigned_user_id_{$bidders.id}"
					name="assigned_user_id_{$bidders.id}">
					<span class="id-ff multiple">
						<button
							onclick='open_popup("Users",600,400,"&lead_reviewer=false",true,false,{literal} { {/literal} "call_back_function":"set_return","form_name":"EditView","field_to_name_array": {literal} { {/literal} "id":"assigned_user_id_{$bidders.id}","user_name":"assigned_user_name_{$bidders.id}" {literal} }} {/literal} ,
"single",true);'
							value="Select" class="button firstChild" accesskey="T"
							title="Select [Alt+T]" id="btn_assigned_user_name_{$bidders.id}"
							name="btn_assigned_user_name" type="button" disabled="true">
							<img src="themes/default/images/id-ff-select.png">
						</button><button value="Clear"
							onclick="document.getElementById('assigned_user_name_{$bidders.id}').value = ''; document.getElementById('assigned_user_id_{$bidders.id}').value = '';"
							class="button lastChild" accesskey="C" title="Clear [Alt+C]"
							id="btn_clr_assigned_user_name_{$bidders.id}" name="btn_clr_assigned_user_name"
							type="button" disabled="true">
							<img src="themes/default/images/id-ff-clear.png">
						</button>
					</span>
				</td>								
			</tr>
			{foreachelse}
			<tr>
				<td colspan="7" align="center"><strong>No Bidders were found for
						this Project Lead.</strong></td>
			</tr>
			{/foreach} 
			</tbody>
	</table>	
</form>

<script type="text/javascript">
var opportunity_id = "{$opportunity_id}";
{literal}
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

function toggleClientOppr(bid) {        
    var ele = document.getElementById("role-div-c_"+bid);
    var text = document.getElementById("displayTextC_"+bid);
    if(ele.style.display == "block") {
            ele.style.display = "none";
            text.innerHTML = "&nbsp;<strong>+</strong>&nbsp;";
    }
    else {
            ele.style.display = "block";
            text.innerHTML = "&nbsp;<strong>-</strong>&nbsp;";
    }
}

function classificationFilter(className){
    document.EditView.action.value = 'convert_to_opportunity';    
    document.EditView.classification.value = className;
    document.EditView.opportunity_id.value = opportunity_id;
    document.EditView.submit();
}

function addToContact(client,bidderId){    
    var callback = {
    success:function(o){       
    if(o.responseText == 'Done'){
    	ajaxStatus.hideStatus();
        document.getElementById('tr_add_to_client_'+bidderId).innerHTML = '<img alt="Create" src="custom/themes/default/images/tick_mark.jpg">';
        }    
    }
    }
    ajaxStatus.showStatus('Loading...');
    var connectionObject = YAHOO.util.Connect.asyncRequest ("GET", "index.php?module=Leads&action=add_to_contact&to_pdf=true&client="+client, callback);
}

//addToValidate('EditView', 'name', 'varchar', true,'Name' );
//addToValidate('EditView', 'amount', 'currency', true,'Amount' );
//addToValidate('EditView', 'sales_stage', 'varchar', true,'Sales Stage' );


function checkSelected(){
	
	var bid = document.getElementsByName('bid[]');
	var bidChecked = 0;
	var ajaxSubmit = false;
	//var bidCheckedFill = 0;
	for (i=0;i<bid.length;i++){
		if(bid[i].checked == true){
			bidChecked += 1;
			//if(document.getElementById('amount_'+bid[i].value).value != '' && document.getElementById('sales_stage_'+bid[i].value).value != ''){
			//	bidCheckedFill += 1;				
			//}						
		}
	}
	
	//return false;
	if(bidChecked == 0){
		alert('Select atleast one record !!');
		ajaxSubmit = false;	
	/*}else if(bidCheckedFill == 0){
		alert("{/literal}{$MOD.LBL_AMOUNT_SALES_STAGE_WARNING}{literal}");
		ajaxSubmit = false;*/
	}else{		
		ajaxSubmit = check_form('EditView');
	}
	
	if(ajaxSubmit){
		initiateOppCoversion();
	}
}

function set_return_assigned(popup_reply_data){

	set_return(popup_reply_data);

	//alert(popup_reply_data.name_to_value_array);
		
	var assigned_user_name = document.getElementById('assigned_user_name').value;
	var assigned_user_id = document.getElementById('assigned_user_id').value;

	var assignee_name = YAHOO.util.Selector.query('input[id^=assigned_user_name_]');
	for (var i = 0; i < assignee_name.length; i++){
		assignee_name[i].value = assigned_user_name;
	}

	var assignee_id = YAHOO.util.Selector.query('input[id^=assigned_user_id_]');
	for (var i = 0; i < assignee_id.length; i++){
		assignee_id[i].value = assigned_user_id;
	}
}
function enableAssignee(id,value){

	if(document.getElementById(id).checked == true){
		document.getElementById('assigned_user_name_'+value).disabled = false;
		document.getElementById('btn_assigned_user_name_'+value).disabled = false;
		document.getElementById('btn_clr_assigned_user_name_'+value).disabled = false;
		document.getElementById('td_'+value).style.backgroundColor = "";

		//validate assigned user can not be blank
		addToValidate('EditView', 'assigned_user_id_'+value, 'varchar', true, 'Assigned User Name' );
		addToValidate('EditView', 'assigned_user_name_'+value, 'varchar', true, 'Assigned User Name' );
		
	}else{
		document.getElementById('assigned_user_name_'+value).disabled = true;
		document.getElementById('btn_assigned_user_name_'+value).disabled = true;
		document.getElementById('btn_clr_assigned_user_name_'+value).disabled = true;
		document.getElementById('td_'+value).style.backgroundColor = "#BDBDBD";

		//validate assigned user can be blank
		removeFromValidate('EditView', 'assigned_user_id_'+value);
		removeFromValidate('EditView', 'assigned_user_name_'+value);
	}
}

$(document).ready(function() {
 $("a.no_proview").each(function(indexVal,elm){$(elm).tipTip({maxWidth: "auto",edgeOffset: 10,content: "No proview available.",defaultPosition: "bottom"})});
 SUGAR.ajaxUI.showLoadingPanel();
 SUGAR.ajaxUI.hideLoadingPanel();
});

/**
* checked or unchecked all checkboxes for bidders selection
* @author Mohit Kumar Gupta
* @date 05/11/2014
*/
function checkAllBidder () {
	if($('#bidder_check_all').prop('checked')){
		$('input[id^=bid_]').each(
			function(idx,elm){
				$('#'+elm.id).prop('checked',true);
				enableAssignee(elm.id,elm.value);
			}
		);
	} else {
		$('input[id^=bid_]').each(
			function(idx,elm){
				$('#'+elm.id).prop('checked',false);
				enableAssignee(elm.id,elm.value);
			}
		);
	}
}
/**
* initialize lead conversion request
* @author Mohit Kumar Gupta
* @date 06/11/2014
*/
function initiateOppCoversion() {
	var current_user_id = '{/literal}{$current_user_id}{literal}';
	$.ajax({
		url : 'cmdscripts/convertOppCommand.php',
		type : 'post',
		async : true,
		cache : false,
		data : 'process=convertOpp&type=update&userId='+current_user_id+'&jsonString='+encodeURIComponent(JSON.stringify($('#EditView').serializeArray())),
		beforeSend : function(){
			SUGAR.ajaxUI.showLoadingPanel();
			ajaxStatus.showStatus("Please wait while converting leads...");
		},		
		success : function (response){
			var res = response.split('_');	
			if(res[0]=='start'){
				ajaxStatus.showStatus('Please wait while converting leads...');
				setInterval(function(){checkOppProcessStatus(res[1])},6000);							
				return false;
			}else if(res[0]=='running'){
				ajaxStatus.showStatus('Please wait while converting leads...');
				setInterval(function(){checkOppProcessStatus(res[1])},6000);							
				return false;    
			}else{
				ajaxStatus.showStatus(res[0]);
				return false;    
			} 
		}		
	});
}
/**
* handle lead conversion status
* @author Mohit Kumar Gupta
* @date 06/11/2014
*/
function checkOppProcessStatus(timestamp){
	$.ajax({
		url : 'cmdscripts/opp_process_status.php',
		type : 'post',
		async : true,
		cache : false,
		data : 'currentTime='+timestamp,
		success : function (response){
			var res_str = response.split('_');
			var insertedOpp = res_str[1];
			var parentOpportunityId = $('#opportunity_id').val();        				      					
			if(insertedOpp == '0'){
				var msg_text = 'Data streaming is in progress...';        					
			}else{
				var msg_text = 'Opportunity converted: '+insertedOpp;
			}         				
			if(trim(res_str[0])=='running'){       						
				ajaxStatus.showStatus(msg_text);
				return true;
			}else if(trim(res_str[0])=='finished'){							
				ajaxStatus.showStatus('All Opportunities created Successfully.');
				var interval = setInterval(function(){
					window.location.href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&record="+parentOpportunityId;
					clearInterval(interval);	
				},5000);
				
				return false;
			} 
		}		
	});				
}
</script>
{/literal}
