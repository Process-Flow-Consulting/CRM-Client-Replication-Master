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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

*}

<div>
    {if $ID}
    <div id="eapm_area" style='display:{$HIDE_FOR_GROUP_AND_PORTAL};'>
        <div style="text-align:center; width: 100%">{sugar_image name="loading"}</div>
    </div>
    
    {/if}
     <div id="user_filter_container" class="yui-hidden"></div>
</div>

<script type="text/javascript">

var mail_smtpport = '{$MAIL_SMTPPORT}';
var mail_smtpssl = '{$MAIL_SMTPSSL}';
{literal}
EmailMan = {};

function Admin_check(){
	if (('{/literal}{$IS_FOCUS_ADMIN}{literal}') && document.getElementById('is_admin').value=='0'){
		r=confirm('{/literal}{$MOD.LBL_CONFIRM_REGULAR_USER}{literal}');
		return r;
		}
	else
		return true;
}

$(document).ready(function() {
    $('#calendar_publish_key').keypress(function(){
        $('#cal_pub_key_span').html( $(this).val());
        $('#ical_pub_key_span').html( $(this).val());
    });
    $('#calendar_publish_key').change(function(){
        $('#cal_pub_key_span').html( $(this).val());
        $('#ical_pub_key_span').html( $(this).val());
    });
});
{/literal}
</script>
{$JAVASCRIPT}
{literal}
<script type="text/javascript" language="Javascript">
{/literal}
{$getNameJs}
{$getNumberJs}
currencies = {$currencySymbolJSON};
themeGroupList = {$themeGroupListJSON};

onUserEditView();


</script>

</form>

<div id="testOutboundDialog" class="yui-hidden">
    <div id="testOutbound">
        <form>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit view">
			<tr>
				<td scope="row">
					{$APP.LBL_EMAIL_SETTINGS_FROM_TO_EMAIL_ADDR}
					<span class="required">
						{$APP.LBL_REQUIRED_SYMBOL}
					</span>
				</td>
				<td >
					<input type="text" id="outboundtest_from_address" name="outboundtest_from_address" size="35" maxlength="64" value="{$TEST_EMAIL_ADDRESS}">
				</td>
			</tr>
			<tr>
				<td scope="row" colspan="2">
					<input type="button" class="button" value="   {$APP.LBL_EMAIL_SEND}   " onclick="javascript:sendTestEmail();">&nbsp;
					<input type="button" class="button" value="   {$APP.LBL_CANCEL_BUTTON_LABEL}   " onclick="javascript:EmailMan.testOutboundDialog.hide();">&nbsp;
				</td>
			</tr>
		</table>
		</form>
	</div>
</div>
{literal}
<style>
    .actionsContainer.footer td {
        height:120px;
        vertical-align: top;
    }
</style>
{/literal}
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer footer">
    <tr>
        <td>
        {sugar_action_menu id="userEditActions" class="clickMenu fancymenu" buttons=$ACTION_BUTTON_FOOTER flat=true}
        </td>
        <td align="right" nowrap>
            <span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span> {$APP.NTC_REQUIRED}
        </td>
    </tr>
</table>
