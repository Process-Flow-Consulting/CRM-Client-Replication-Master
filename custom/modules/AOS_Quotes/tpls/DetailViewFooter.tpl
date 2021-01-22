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
<div class="panel panel-default">
<div class="detail view">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<!-- BEGIN: line item Row -->
<tr>
	<td scope="row" align="left" colspan="7"><h4>{$MOD.LBL_LINE_ITEM_INFORMATION}</h4></td>
</tr>	
<tr>	
	<td scope="row" width="20%" valign="top" style="text-align: left;">{$MOD.LBL_TITLE}</td>
	<td scope="row" width="30%" valign="top" style="text-align: left;">{$MOD.LBL_DESCRIPTION}</td>			
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_QUANTITY}</td>
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_PRICE}</td>
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_MARKUP}</td>
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_UNIT_PRICE}</td>	
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_TOTAL}</td>	
</tr>
		{foreach from=$line_items item=line_item}
		<tr>
			<td style="border-bottom:none;"></td>
			<td style="border-bottom:none;"></td>
			<td style="border-bottom:none;"></td>
			<td style="border-bottom:none;"></td>
			<td style="border-bottom:none;"></td>
			<td style="text-align: right; border-bottom:none;">
				{if $line_item->bb_tax_per gt 0 || $line_item->discount_amount gt 0 || $line_item->bb_shipping gt 0}				
				<div id="adv_opt_div_3" style="height: 5px; color: rgb(255, 0, 0); font-weight: bold; font-size: 16px;">*</div>
				{/if}
			</td>
			<td style="border-bottom:none;"></td>
		</tr>		
		<tr>
			<td valign="top" style="text-align: left;">{$line_item->name}</td>
			<td valign="top" style="text-align: left;">{$line_item->description | nl2br}</td>		    
			<td valign="top" style="text-align: right;">{$line_item->quantity} {$line_item->unit_measure_name}</td>
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$line_item->cost_price}</td>
			<td valign="top" style="text-align: right;">{if $line_item->markup_inper==1} {$line_item->list_price|number_format:$CURRENCY_DIGIT:$DECIMAL_POINT:$THOUSAND_SEP}{$PERCENT_SIGN}{else}{sugar_currency_format var=$line_item->list_price}{/if}</td>
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$line_item->unit_price}</td>		     
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$line_item->total}</td>					
		</tr>
		{/foreach}     
		<tr valign="top">			
			<td colspan="6" valign="top" style="text-align: right;">{$MOD.LBL_SUBTOTAL}:</td>
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$bean->subtotal}</td>			
		</tr>
<!-- BEGIN: Inclusions Row -->
<tr>
	<td scope="row" align="left" colspan="7"><h4>{$MOD.LBL_INCLUSION_INFORMATION}</h4></td>
</tr>	
	<tr>	
	<td scope="row" width="20%" valign="top" style="text-align: left;">{$MOD.LBL_TITLE}</td>
	<td colspan="3" scope="row" width="40%" valign="top" style="text-align: left;">{$MOD.LBL_DESCRIPTION}</td>	
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_PRICE}</td>
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_MARKUP}</td>
	<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_TOTAL}</td>	
</tr>
		{foreach from=$inclusions item=inclusion}
		<tr>
			<td valign="top" style="text-align: left;">{$inclusion->name}</td>
			<td colspan="3" valign="top" style="text-align: left;">{$inclusion->description | nl2br}</td>		    
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$inclusion->cost_price}</td>
			<td valign="top" style="text-align: right;">{if $inclusion->markup_inper==1} {$inclusion->list_price|number_format:$CURRENCY_DIGIT:$DECIMAL_POINT:$THOUSAND_SEP}{$PERCENT_SIGN}{else}{sugar_currency_format var=$inclusion->list_price}{/if}</td>
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$inclusion->total}</td>								
		</tr>
		{/foreach}     
		<tr valign="top">			
			<td colspan="6" valign="top" style="text-align: right;">{$MOD.LBL_SUBTOTAL}:</td>
			<td valign="top" style="text-align: right;">{sugar_currency_format var=$bean->subtotal_inclusion}</td>			
		</tr>
<!-- BEGIN: Exclusions Row -->
<tr>
	<td scope="row" align="left" colspan="7"><h4>{$MOD.LBL_EXCLUSION_INFORMATION}</h4></td>
</tr>	
	<tr>	
	<td scope="row" width="20%" valign="top" style="text-align: left;">{$MOD.LBL_TITLE}</td>
	<td colspan="6" scope="row" width="40%" valign="top" style="text-align: left;">{$MOD.LBL_DESCRIPTION}</td>		
</tr>
		{foreach from=$exclusions item=exclusion}
		<tr>
			<td valign="top" style="text-align: left;">{$exclusion->name}</td>
			<td colspan="6" valign="top" style="text-align: left;">{$exclusion->description | nl2br}</td>										
		</tr>     
		{/foreach}		
    
	{* BEGIN: grand_total *}
	<tr>
		<td scope="row" colspan='7' valign="top" style="text-align: left;">{$MOD.LBL_LIST_GRAND_TOTAL}</td>
	</tr>
	<tr>
			<td colspan="6" NOWRAP style="text-align: right;">{$MOD.LBL_TOTAL}</td>
			<td NOWRAP style="text-align: right;">{sugar_currency_format var=$bean->grand_subtotal}</td>
	</tr>	
        <tr>
			{if $SALES_TAX_FLAG	eq 'total_item'}
				<td scope="row">{$MOD.LBL_TAXRATE}</td>
				<td >{$TAX_RATE_NAME}</td>
				<td colspan="4" NOWRAP style="text-align: right;">{$MOD.LBL_TAX}<span  id="tax_rate_label">{$TAX_RATE_PER}</span></td>
            {else}
				<td colspan="6" NOWRAP style="text-align: right;">{$MOD.LBL_TAX}</td>
            {/if}	
			
			<td NOWRAP style="text-align: right;">{sugar_currency_format var=$bean->tax}</td>
		</tr>
		<tr>			
			<td colspan="6" NOWRAP style="text-align: right;">{$MOD.LBL_SHIPPING}</td>
			<td NOWRAP style="text-align: right;">{sugar_currency_format var=$bean->shipping}</td>
		</tr>		
		<tr>		
			<td colspan="6" NOWRAP style="text-align: right;">{$MOD.LBL_LIST_GRAND_TOTAL}</td>
			<td NOWRAP style="text-align: right;">{sugar_currency_format var=$bean->total}</td>
	</tr>
	{* END: grand_total *}
	<tr>
		<td scope="row" align="left" colspan="7"><h4>{$MOD.LBL_ALTERNATES_INFORMATION}</h4></td>
	</tr>
	<tr>
		<td scope="row" width="20%" valign="top" style="text-align: left;">{$MOD.LBL_TITLE}</td>
		<td scope="row" width="30%" valign="top" style="text-align: left;">{$MOD.LBL_DESCRIPTION}</td>			
		<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_QUANTITY} </td>
		<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_PRICE}</td>
		<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_MARKUP}</td>
		<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_UNIT_PRICE}</td>	
		<td scope="row" width="10%" valign="top" style="text-align: right;">{$MOD.LBL_TOTAL}</td>	
	</tr>
			{foreach from=$alternates item=alternates}
	<tr>
		<td style="border-bottom:none;"></td>
		<td style="border-bottom:none;"></td>
		<td style="border-bottom:none;"></td>
		<td style="border-bottom:none;"></td>
		<td style="border-bottom:none;"></td>
		<td style="text-align: right; border-bottom:none;">
			{if $alternates->bb_tax_per gt 0 || $alternates->discount_amount gt 0 || $alternates->bb_shipping gt 0}				
			<div id="adv_opt_div_3" style="height: 5px; color: rgb(255, 0, 0); font-weight: bold; font-size: 16px;">*</div>
			{/if}
		</td>
		<td style="border-bottom:none;"></td>
	</tr>		
	<tr>
		<td valign="top" style="text-align: left;">{$alternates->name}</td>
		<td valign="top" style="text-align: left;">{$alternates->description | nl2br}</td>		    
		<td valign="top" style="text-align: right;">{$alternates->quantity} {$alternates->unit_measure_name}</td>
		<td valign="top" style="text-align: right;">{sugar_currency_format var=$alternates->cost_price}</td>
		<td valign="top" style="text-align: right;">{if $alternates->markup_inper==1} {$alternates->list_price|number_format:$CURRENCY_DIGIT:$DECIMAL_POINT:$THOUSAND_SEP}{$PERCENT_SIGN}{else}{sugar_currency_format var=$alternates->list_price}{/if}</td>
		<td valign="top" style="text-align: right;">{sugar_currency_format var=$alternates->unit_price}</td>		     
		<td valign="top" style="text-align: right;">{sugar_currency_format var=$alternates->total}</td>					
	</tr>
		{/foreach} 	   
</table>
</div>
</div>

<script type="text/javascript">
var proposal_verfied = "{$fields.proposal_verified.value}";
var proposal_id = "{$fields.id.value}";
var verify_proposal = "{$verify_proposal}";
//var delivery_method_email = "{$fields.delivery_method_email.value}";
//var delivery_method_fax = "{$fields.delivery_method_fax.value}";
//var delivery_method_both = "{$fields.delivery_method_both.value}";
//hirak : date: 11-10-2012
var proposal_delivery_method = "{$fields.proposal_delivery_method.value}";
var lbl_delivery_method_wmsg = "You have not selected a Delivery Method to send this proposal.";
var lbl_no_line_item_wmsg = "You have not created or uploaded a quote for this proposal.";
var line_items_exists = "{$line_items_exists}";
{literal}

var mySimpleDialog ='';
function getSimpleDialog(){
    if (typeof(mySimpleDialog) != 'undefined' && mySimpleDialog != ''){
        mySimpleDialog.destroy(); 
    }
        mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", { 
        width: "40em", 
        effect:{
            effect: YAHOO.widget.ContainerEffect.FADE,
            duration: 0.25
        }, 
        fixedcenter: true,
        modal: true,
        visible: false,
        draggable: false
    });
        
    mySimpleDialog.setHeader("Warning!");
    mySimpleDialog.cfg.setProperty("icon", YAHOO.widget.SimpleDialog.ICON_WARN);
    return mySimpleDialog;
}

var handleYes = function(){
    this.hide();
    return false;
}

function verifyEmail(){
    var show_message = false;
    var message = 'Verification Message cannot be sent for the following reason.<br><br>';
    //hirak : date: 11-10-2012
    //if(delivery_method_email == 0 && delivery_method_fax == 0 && delivery_method_both == 0){
    if(proposal_delivery_method != 'E' && proposal_delivery_method != 'F' && proposal_delivery_method != 'EF'){
        message += lbl_delivery_method_wmsg+'<br>';
        show_message=true;        
    }    
    if (line_items_exists == 0){
        message += lbl_no_line_item_wmsg;
        show_message=true;
    }
    
    if(show_message){
        mySimpleDialog = getSimpleDialog();
        mySimpleDialog.setBody(message);
        var myButtons = [
        { text: "OK", handler: handleYes }        
        ];
        mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
        mySimpleDialog.render(document.body);    
        mySimpleDialog.show();
    }else{    
        sendVerifyEmail();
    }
    
}

YAHOO.util.Event.onAvailable('show_verify_message',function(Y){
	if(verify_proposal!=1){
		document.getElementById('show_verify_message').style.display='';
	}
	
});

function proposalVerify(){
	if(proposal_verfied != 1){
		alert('You cannot E-Mail this proposal as pdf Until the proposal has been verified. Please click the "Verify Proposal" button to send yourself a proof via e-mail and fax. If you are satisfied, set "Proposal Verified?" to Yes and you may E-Mail this proposal as pdf.');
		return false;
	}else{
		document.forms['EmailPDF'].email_action.value='EmailLayout';
		return true;
	}			
}



function sendVerifyEmail(){
	
	YUI().use("io-base","node",function(Y){	
		var uri="index.php?module=AOS_Quotes&action=send_verify_email&to_pdf=true&proposal_id="+proposal_id;
		var cfg = {
			    method: 'POST',
			    data: 'user=yahoo',
			    headers: {
			        'Content-Type': 'application/json',
			    },
			    on: {
			        start: function(){
						Y.one("#btn_proposal_verified").set('disabled',true);
		    			ajaxStatus.showStatus("<img align='absmiddle' border='0' src='themes/default/images/sqsWait.gif' /> &nbsp;&nbsp;Delivering proposal for verification..");
		    		},
			        complete:function(id,o){
			        	statusMsgs = ''	;		        	
        				var res = JSON.parse(o.responseText);        				
	        				/*if(res=='Sent'){
	        					ajaxStatus.showStatus("Verification email sent.");
	            			}else{
	            				ajaxStatus.showStatus("Verification email not sent.");
	                		}*/
                			
								statusMsgs = res.messages.join('<br clear="all"/>');
                    			        				
                			ajaxStatus.showStatus(statusMsgs);
				if(res.status &&  document.getElementById('show_verify_message') != null && typeof(document.getElementById('show_verify_message') ) != 'undefined')
				document.getElementById('show_verify_message').innerHTML=SUGAR.language.get('Quotes','LBL_SHOW_VERIFICATION_SENT_MESSAGE');
				        },
			        end: function(){
			        	Y.one("#btn_proposal_verified").set('disabled',false); 					
		    		}
			    }		    
			    
			};    
		
		var request = Y.io(uri,cfg);		
	});	
}


function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}


var handleYes4 = function(){
	this.hide();
};

if(readCookie('proposal') == 'unverified' ){
	
	document.cookie = escape('proposal')+"="+escape('unverified')+'-1'+"; path=/";
	
	mySimpleDialog = getSimpleDialog(); 
    mySimpleDialog.setBody('Your changes have been saved, however the proposal cannot be sent until it has been verified. Please click the "Return to Client Opportunity" button above.<br><br> Locate the red X next to the proposal you have just saved and click the X to send yourself a proof (when the proof has been sent the X button will change to a "pending" icon).<br><br> Once you are satisfied with the proof you may verify the proposal for delivery by clicking on the “pending” icon (when the proposal has been successfully scheduled then the “pending” icon will change into a check mark) and your proposal is ready to be sent.');
    var myButtons = [
    { text: "OK", handler: handleYes4 }		    
    ];
    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
    mySimpleDialog.render(document.body);    
    mySimpleDialog.show();
	
}

function set_selected_doc(pop_up_reply){
	

	mySimpleDialog = getSimpleDialog();
    mySimpleDialog.setBody(SUGAR.language.get('Quotes','MSG_MODIFY_VERIFY_WARNING'));
    var myButtons = [
    { text: "OK", handler: handleYes }        
    ];
    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
    mySimpleDialog.render(document.body);    
    mySimpleDialog.show();
    line_items_exists = 1;
    
    set_return_and_save_background(pop_up_reply);
	//now a line item exists
		line_items_exists =1;
		setNotVerfiedMessage();
	}

function setNotVerfiedMessage(){

//display quote note
msgContainer = document.getElementById('show_verify_message');
msgContainer.style.display='';

msgContainer.innerHTML = SUGAR.language.get('Quotes','LBL_SHOW_VERIFY_MESSAGE');
//mark verified no
YUI().use('node',function(Y){
Y.all('#proposal_verified[value=2]').set('checked',true)
});


}
</script>
{/literal}
