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

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan='100'><h2><!-- <a href="index.php?module=AOS_Quotes&action=DetailView&record={$record}">{$PROPOSAL_NAME}</a> <span class="pointer">»</span> -->Copy Proposal</h2></td></tr>
<tr><td><i class="info"></i></td></tr>
<td colspan='100'>
<tr><td colspan='100'>
<form name="copyProposal" method="POST"  method="POST" action="index.php">
    <input type="hidden" name="module" value="AOS_Quotes">
    <input type="hidden" name="action" value="copyProposal">
    <input type="hidden" name="handleSave" value="0">
    <input type="hidden" name="record" value="{$record}">
    <input type="hidden" name="return_module" value="{$RETURN_MODULE}">
    <input type="hidden" name="return_action" value="{$RETURN_ACTION}">
    <input type="hidden" name="return_id" value="{$RETURN_ID}">
    <br>
    <p>By Selecting Clients below, you will be overwriting any existing proposals with the proposal you have created for <strong>{$CLIENT_NAME}</strong> on <strong>{$OPPORTUNITY_NAME}</strong>, including any attached documents. You may still make individual changes to those client proposals moving forward. Here are the details on the Proposal created for <strong>{$CLIENT_NAME}</strong> on <strong>{$OPPORTUNITY_NAME}</strong>.</p>
    <br>
    <div class='add_table' style='margin-bottom:5px'>
		<table class="edit view small" style='margin-bottom:0px;' border="0" cellspacing="5" cellpadding="2" width="100%">
			<tr>
				<td width='20%'><strong>Proposal Subject:</strong></td>
				<td>{$PROPOSAL_SUBJECT}</td>
			</tr>
			<tr>
				<td width='20%'><strong>Delivery Date / Time:</strong></td>
				<td>{$PROPOSAL_DELIVERY_TIME}</td>
			</tr>
			<tr>
				<td width='20%'><strong>Delivery Time Zone:</strong></td>
				<td>{$PROPOSAL_DELIVERY_TIMEZONE}</td>
			</tr>
			<tr>
				<td width='20%'><strong>Delivery Method:</strong></td>
				<td>{$PROPOSAL_DELIVERY_METHOD}</td>
			</tr>
		</table>
	</div>
	<br>
	<br>
	<table border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button primary" onclick="{literal}if(checkSelected()){SUGAR.ajaxUI.showLoadingPanel();this.form.submit();return true;}else{return false;};{/literal}" type="button" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}">
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="button" onclick="location.href='index.php?module=AOS_Quotes&action=DetailView&record={$record}'; return false;" type="submit" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  ">
			</td>
		</tr>
	</table>
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="list view">
        <tr height="20">
            <td class="selectCol" align="center" scope="col">
               <!-- <input type="checkbox" class="checkbox massall" name="massall" id="massall_top" value="1" onClick="sListView.check_all(document.copyProposal, 'mass[]', this.checked);">  -->
            </td>
			<td width="15%" scope="col">
			    <div align="left" width="100%" style="white-space: normal;">Proposal Status</div>
			</td>
			<td width="30%" scope="col">
			    <div align="left" width="100%" style="white-space: normal;">{$MOD.LBL_LIST_ACCOUNT_NAME}</div>
			</td>
			<td width="20%" scope="col">
			    <div align="left" width="100%" style="white-space: normal;">{$MOD.LBL_BILLING_CONTACT_NAME}</div>
			</td>
			<td width="30%" scope="col">
			    <div align="left" width="100%" style="white-space: normal;">{$MOD.LBL_DELIVERY_METHOD}</div>
			</th>
        </tr>
        {foreach from=$clientOpps item=clientOpp  name=count}
        {if $smarty.foreach.count.iteration is odd}
            {assign var='_rowColor' value=oddListRow} 
        {else} 
            {assign var='_rowColor' value=evenListRow} 
        {/if}
        <tr class='{$_rowColor}S1'>
            <td class="" align="center" valign="top" scope="row">
                    <input type="checkbox"  class="checkbox" name="mass[]" value="{$clientOpp.id}_{$clientOpp.qid}">
            </td>
            <td class="" align="left" valign="top" scope="row">
                {if $clientOpp.pdm eq 'M'}
				    <img src="custom/themes/default/images/manuel.png" height="20" width="60" alt="yes" border="0">
                {elseif $clientOpp.proposal_verified eq '1'}
                    <img src="custom/themes/default/images/yes-icon.png" alt="yes" border="0">
                {elseif ($clientOpp.proposal_verified eq '2') && ($clientOpp.verify_email_sent eq '1')}
	                 <img src="custom/themes/default/images/pending-icon.png" alt="pending" border="0">
	            {elseif ($clientOpp.proposal_verified eq '2') && ($clientOpp.verify_email_sent eq '0')}
	                 <img src="custom/themes/default/images/no-icon.png" alt="pending" border="0">
	            {else}
					No Proposal
               {/if}
            </td>
            <td class="" align="left" valign="top" scope="row">
                <div class="accName">
					{sugar_proview_url url=$clientOpp.proview_url}
				    <a href="index.php?module=Opportunities&action=DetailView&viewDetail=1&record={$clientOpp.id}">
					    {$clientOpp.client_name}
				    </a>
				</div>
            </td>
            <td class="" align="left" valign="top" scope="row">
                {$clientOpp.contact_name}
            </td>
            <td class="" align="left" valign="top" scope="row">
                <label>
                    <input id="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}" type="radio" title="" value="E" name="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}">Email
                </label>
                <label>
                    <input id="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}" type="radio" title="" value="F" name="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}">Fax
                </label>
                <label>
                    <input id="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}" type="radio" title="" value="EF" name="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}">Email & Fax
                </label>
                <label>
                    <input id="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}" type="radio" title="" value="M" name="proposal_delivery_method_{$clientOpp.id}_{$clientOpp.qid}">Manual
                </label>
                <br>
                <span class="error" id="error_{$clientOpp.id}_{$clientOpp.qid}"></span>
                <input type="hidden" name="contact_email_{$clientOpp.id}_{$clientOpp.qid}"  id="contact_email_{$clientOpp.id}_{$clientOpp.qid}" value="{$clientOpp.contact_email}">
                <input type="hidden" name="contact_fax_{$clientOpp.id}_{$clientOpp.qid}"  id="contact_fax_{$clientOpp.id}_{$clientOpp.qid}" value="{$clientOpp.contact_fax}">
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan='100'>
            <div class="list view listViewEmpty">
                    <p class="msg"> No Data </p>
            </div>
            </td>
        </tr>
       {/foreach}
    </table>
</td></tr>
</table>
{literal}
<script type="text/javascript">
function checkSelected(){
	var form = document.copyProposal;
	var count= 0;
	var error= 0;
	for (i = 0; i < form.elements.length; i++) {
		if(form.elements[i].type == 'checkbox'){
		    if(form.elements[i].checked == true){
		        var elementId =  form.elements[i].value;
		        var radio_group = "proposal_delivery_method_"+elementId;
		        var checkedRadio = getCheckedRadio(radio_group);
		        if(checkedRadio != '' && typeof checkedRadio != 'undefined') {			        
		            document.getElementById('error_'+elementId).innerHTML = '';
		            var contact_email = document.getElementById('contact_email_'+elementId).value;
		            var contact_fax = document.getElementById('contact_fax_'+elementId).value;
		            if( (contact_email == '' || typeof contact_email == 'undefined' ) && (contact_fax == '' || typeof contact_fax == 'undefined' )  && (checkedRadio == 'E' || checkedRadio == 'F' || checkedRadio == 'EF' )){
		            	document.getElementById('error_'+elementId).innerHTML ='Only Manual Delivery is available. This client contact currently does not have an e-mail address or fax number.';
		            	error++;
			        }
		            else if( (contact_email == '' || typeof contact_email == 'undefined' )  && (checkedRadio == 'E' || checkedRadio == 'EF' ) ){
		            	document.getElementById('error_'+elementId).innerHTML ='Only Fax and Manual Delivery are available. This client contact currently does not have an e-mail address.';
		            	error++;
		            }else if( (contact_fax == '' || typeof contact_fax == 'undefined' )  && (checkedRadio == 'F' || checkedRadio == 'EF' ) ){
		            	document.getElementById('error_'+elementId).innerHTML ='Only Email and Manual Delivery are available. This client contact currently does not have a fax number.';
		            	error++;
		            }
			    }else{
		            document.getElementById('error_'+elementId).innerHTML ='You must select a delivery method.';
		            error++;
		        }
		        count++;
		     }
		}
	}
	if(count == 0){
	    alert('Please select at least one Client Opportunity to Copy Proposal.');
	}
	if(error > 0){
		return false;
	}
	if(count > 0){
		form.handleSave.value = '1';
		return true;
	}
	return false;
}

function getCheckedRadio(radio_group) {
    var radios = document.getElementsByName(radio_group);
    for (var i = 0; i < radios.length; i++) {       
        if (radios[i].checked) {
            return radios[i].value;
        }
    }
    return false;
}
$(document).ready(function() {
	SUGAR.ajaxUI.showLoadingPanel();
	SUGAR.ajaxUI.hideLoadingPanel();
});
</script>
{/literal}