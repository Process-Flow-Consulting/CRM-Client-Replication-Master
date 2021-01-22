<div class="detail view detail508" style="border: 1px solid #98C6EA;border-top-color: rgb(152, 198, 234);-webkit-box-shadow: #ccc 0px 0px 10px;">
	<table width="100%" cellspacing="0" style="border:none;">
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_PROJECT_TITLE}:</td>
			<td width="37.5%">{$lead->project_title}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LOCATION}:</td>
			<td width="37.5%">{if $lead->address neq ""}{$lead->address},{/if} {if $lead->city neq ""}{$lead->city}, {/if}{$lead->state} {$lead->zip_code}{if $lead->county neq ""}<br>({$lead->county}){/if}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_BIDS_DUE}:</td>
			<td width="37.5%">{$oss_timedate->convertDBDateForDisplay($lead->bids_due,$lead->bid_due_timezone,true)} {$lead->bid_due_timezone}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_RECEIVED}:</td>
			<td width="37.5%">{$lead->received}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_PRE_BID_MEETING}:</td>
			<td width="37.5%">{$lead->pre_bid_meeting}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_TYPE_STRUCTURE}:</td>
			<td width="37.5%">{$lead->type} / {$lead->structure}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_PROJECT_STATUS}:</td>
			<td width="37.5%">{$lead->project_status}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_OWNER}:</td>
			<td width="37.5%">{$lead->owner}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_START_DATE}:</td>
			<td width="37.5%">{$lead->start_date}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_END_DATE}:</td>
			<td width="37.5%">{$lead->end_date}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_CONTACT_NO}:</td>
			<td width="37.5%">{$lead->contact_no}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_VALUATION}:</td>
			<td width="37.5%">{if $lead->valuation neq "0"}{$lead->valuation|number_format:2}{/if}</td>
		</tr>	
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_SOURCE}:</td>
			<td width="37.5%">{$lead->lead_source}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LABOR_TYPE}:</td>
			<td width="37.5%"><input type="checkbox" name="union_c" id="union_c" value="1" {if $lead->union_c==1}checked="checked"{/if} disabled >&nbsp;{$MOD.LBL_UNION}&nbsp;&nbsp;<input type="checkbox" name="non_union" id="non_union" value="1" {if $lead->non_union==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_NON_UNION}&nbsp;&nbsp;<input type="checkbox" name="prevailing_wage" id="prevailing_wage" value="1"  {if $lead->prevailing_wage==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_PREVAILING_WAGE}
</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_SQUARE_FOOTAGE}:</td>
			<td width="37.5%">{$lead->square_footage}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_STORIES_BELOW_GRADE}:</td>
			<td width="37.5%">{$lead->stories_below_grade}</td>
		</tr>	
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_NUMBER_OF_BUILDINGS}:</td>
			<td width="37.5%">{$lead->number_of_buildings}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_STORIES_ABOVE_GRADE}:</td>
			<td width="37.5%">{$lead->stories_above_grade}</td>
		</tr>
		<tr>
			<td colspan="4"><div style="width:100%; height:250px; overflow: scroll; ">{$lead->scope_of_work}</div></td>		
		</tr>
		
	</table>
</div>
{literal}
<style>
.detail508 tr td[scope="col"] {
color: #888;
background-color:#eee;
border-color:#ccc;
text-align: right;
white-space: nowrap;
}

.detail tr td {
color: #222;
border-color:#ccc;
background-color:#fff;
border-bottom-style: solid;
border-bottom-width: 1px;

}
</style>
{/literal}