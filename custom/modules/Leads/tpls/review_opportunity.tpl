{literal}
<style>
.opp-label{	
	font-weight: bold;
}
.opp-td{
	font-size: 13px;
}
</style>
{/literal}
<form id="EditView" name="EditView" method="POST" action="index.php">
<input type="hidden" value="Leads" name="module"> 
<input type="hidden" name="action" value="review_opportunity">
<input type="hidden" name="record" value="{$record}">
<input type="hidden" name="return_action" value="{$return_action}">
<div style="height: 20px;"></div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="font-size: 13px; font-weight: bold;">{$MOD.LBL_PREVIOUS_OPPORTUNITY_MSG}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>		
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				{foreach from=$opp_list item=opportunity}
				<tr height="25">
					<td width="30px"><input type="radio" name="opp_radio" value="{$opportunity->id}"></td>
					<td class="opp-td"><span class="opp-label">{$MOD.LBL_NAME}:</span> {$opportunity->name} <span class="opp-label">&nbsp;&nbsp;$ {$MOD.LBL_AMOUNT}:</span> ${$opportunity->amount} <span class="opp-label">&nbsp;&nbsp;{$MOD.LBL_SALES_STAGE}:</span> {$opportunity->sales_stage}</td>
				</tr>
				{/foreach}
				<tr height="25">
					<td width="30px"><input type="radio" name="opp_radio" value=""></td>
					<td class="opp-td"><span class="opp-label">{$MOD.LBL_CREATE_NEW_OPPORTUNITY}</span></td>
				</tr>
			</table>			
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<input id="SAVE_HEADER" class="button primary" type="submit" value="Save" name="save" accesskey="S" title="Save [Alt+S]"
				onclick=""> 
			<input id="CANCEL_HEADER" class="button" type="button" value="Cancel" name="cancel"	onclick="window.location.href='index.php?module=Leads&action={$return_action}{if $return_action neq 'ListView'}&record={$record}{/if}'; return false;"
				accesskey="X" title="Cancel [Alt+X]"></td>
	</tr>
</table>
</form>