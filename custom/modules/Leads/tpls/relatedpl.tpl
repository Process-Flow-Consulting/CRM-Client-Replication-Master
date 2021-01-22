<div style="width:970px; max-height:500px; overflow: scroll;">
<h3>Related Project Leads</h3>
<table class="list view" cellspacing="0" cellpadding="0" align="center" width="100%" style="border: 1px solid;border-radius: 6px;-webkit-border-radius: 6px;border-color:#98c6ea !important;">
	<tr style="background-color:#c2c3c4;">
		<th width="40%">{$MOD.LBL_PROJECT_TITLE}</th>
		<th width="15%">Source</th>
		<th width="15%">Bid Due</th>
		<th width="15%">{$MOD.LBL_PRE_BID_MEETING}</th>
		<th width="15%">{$MOD.LBL_DATE_MODIFIED_RPL}</th>
	</tr>
	{foreach from=$leads item=lead}
	<tr>
		<td><a href="javascript:void(0);" onclick="showPLDetailModal('{$lead.id}');">{$lead.project_title}</a></td>
		<td>
		{assign var="ls" value=$lead.lead_source}
		{$lead_source_dom.$ls}
		</td>
		<td>{$oss_timedate->to_display_date_time($lead.bids_due,true,false)}</td>
		<td>{$oss_timedate->to_display_date_time($lead.pre_bid_meeting)}</td>		
		<td> 
		{if $lead.audit_cnt gt 0}
		<a onclick="open_popup('Audit', '600', '400', '&record={$lead.id}&module_name=Leads', true, false,{literal} { 'call_back_function':'set_return','form_name':'EditView','field_to_name_array':[] }{/literal} ); return false;" href="">{$oss_timedate->to_display_date_time($lead.date_modified)}</a>
		{else}
		{$oss_timedate->to_display_date_time($lead.date_modified)}
		{/if}
		</td>		
	</tr>
	{foreachelse}
	<tr>
		<td colspan="5" align="center">No Record Found.</td>
	</tr>	
	{/foreach}
</table>
<div style="height: 50px;"></div>
{if $from_oppr eq 'opportunity'}
<table class="list view" border="0" cellspacing="0" cellpadding="0" align="center" width="100%" style="border: 1px solid;border-radius: 6px;-webkit-border-radius: 6px;border-color:#98c6ea !important;">
	<tr style="background-color:#c2c3c4;">
		<th>Project Opportunity</th>
		<th width="15%">{$MOD.LBL_DATE_MODIFIED_RPL}</th>		
	</tr>	
	<tr>
		<td><a href="javascript:void(0);" onclick="showPODetailModal('{$opportunity->id}');">{$opportunity->name}</a></td>
		<td><a onclick="open_popup('Audit', '600', '400', '&record={$opportunity->id}&module_name=Opportunities', true, false,{literal} { 'call_back_function':'set_return','form_name':'EditView','field_to_name_array':[] }{/literal} ); return false;" href="">{$opportunity->date_modified}</a></td>		
	</tr>	
</table>
{/if}
</div>