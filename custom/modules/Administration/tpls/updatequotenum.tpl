{*
/*********************************************************************************
* The contents of this file are subject to the SugarCRM Master
Subscription * Agreement ("License") which can be viewed at *
http://www.sugarcrm.com/crm/master-subscription-agreement * By
installing or using this file, You have unconditionally agreed to the *
terms and conditions of the License, and You may not use this file
except in * compliance with the License. Under the terms of the license,
You shall not, * among other things: 1) sublicense, resell, rent, lease,
redistribute, assign * or otherwise transfer Your rights to the
Software, and 2) use the Software * for timesharing or service bureau
purposes such as hosting the Software for * commercial gain and/or for
the benefit of a third party. Use of the Software * may be subject to
applicable fees and any use of the Software without first * paying
applicable fees is strictly prohibited. You do not have the right to *
remove SugarCRM copyrights from the source code or user interface. * *
All copies of the Covered Code must include on each user interface
screen: * (i) the "Powered by SugarCRM" logo and * (ii) the SugarCRM
copyright notice * in the same form as they appear in the distribution.
See full license for * requirements. * * Your Warranty, Limitations of
liability and Indemnity are expressly stated * in the License. Please
refer to the License for the specific language * governing these rights
and limitations under the License. Portions created * by SugarCRM are
Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

*}
<div class="moduleTitle">
	<h2>{$MOD.LBL_MODIFY_PROPOSAL_NUMBER}</h2>
</div>
<form id="Updatequotenum" name="Updatequotenum" method="POST" action="index.php">
	<input type='hidden' name='action' value='updatequotenum' /> 
	<input type='hidden' name='module' value='Administration' /> 
	<input type='hidden' name='return_module' value="{$RETURN_MODULE}" />
	<input type='hidden' name='return_action' value="{$RETURN_ACTION}" />
	<div style="height: 30px;"></div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
			<input id="SAVE_HEADER" class="button primary" type="submit"
				value="Save" name="button" accesskey="S" title="Save [Alt+S]"
				onclick="return customValidation();"> <input id="CANCEL_HEADER"
				class="button" type="button" value="Cancel" name="button"
				onclick="window.location.href='index.php?module={$RETURN_MODULE}&action={$RETURN_ACTION}'; return false;"
				accesskey="X" title="Cancel [Alt+X]"></td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>	
			
	</table>
	<div style="height: 30px;">{$MOD.LBL_NEW_PROPOSAL_NUMBER_NOTICE}</div>
	<table width="100%" cellspacing="0" cellpadding="1" border="1" class="yui3-skin-sam edit view">
		<tbody>						
			<tr style="height:50px;">
				<td  scope="col" width='20%' style="font-weight: bold;">				
					<label for="name">{$MOD.LBL_NEW_PROPOSAL_NUMBER}:</label>
					<span class="required">*</span>	
            	</td>
				<td  scope="col" width='20%'>				
					<input type='text' maxlength="9" name='custom_quote_num' id='custom_quote_num' value='{$OLDQUOTENUM}'>	
            	</td>	
            	<td  scope="col" width='20%'>				
					&nbsp;	
            	</td>
            	<td  scope="col" width='20%'>				
					&nbsp;	
            	</td									
			</tr>
						
		</tbody>
	</table>	
</form>
{literal}
<script type='text/javascript'>
	//apply custom validation on save of auto increment value of proposal
	//@auhtor Mohit Kumar Gupta
	//@date 05-03-2014
	function customValidation(){
		var newQuoteNum = jQuery('#custom_quote_num').val();
		var oldQuoteNum = {/literal}{$OLDQUOTENUM}{literal};
		var quoteNumMsg = '{/literal}{$MOD.LBL_NEW_PROPOSAL_NUMBER}{literal}';
		addToValidate('Updatequotenum', 'custom_quote_num', 'int', true, quoteNumMsg);
		if(check_form('Updatequotenum')){
			if(parseInt(newQuoteNum) > parseInt(oldQuoteNum)){
				SUGAR.ajaxUI.showLoadingPanel();
				return true;
			} else {
				alert('{/literal}{$MOD.LBL_ALERT_PROPOSAL_NUMBER}{literal}');
				return false;				
			}
		} else{
			return false;
		}		
	}
	//loading image should be loaded on the page load
	//@auhtor Mohit Kumar Gupta
	//@date 05-03-2014
	jQuery(document).ready(function(){
		SUGAR.ajaxUI.showLoadingPanel();
		SUGAR.ajaxUI.hideLoadingPanel();
	});
</script>
{/literal}