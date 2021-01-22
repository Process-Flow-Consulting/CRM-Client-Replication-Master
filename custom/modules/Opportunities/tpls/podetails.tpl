<div class="detail view detail508">
	<table width="100%" cellspacing="0" style="border:none;">
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_NAME}:</td>
			<td width="37.5%">{$opportunity->name}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_DATE_CLOSED}:</td>
			<td width="37.5%">{$oss_timedate->convertDBDateForDisplay($opportunity->date_closed,$opportunity->bid_due_timezone,true)} {$opportunity->bid_due_timezone}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_ADDRESS}:</td>
			<td width="37.5%">{$opportunity->lead_address}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_SOURCE}:</td>
			<td width="37.5%">{$opportunity->lead_source}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_STATE}:</td>
			<td width="37.5%">{$opportunity->lead_state}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_RECEIVED}:</td>
			<td width="37.5%">{$opportunity->lead_received}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_COUNTY}:</td>
			<td width="37.5%">{$opportunity->lead_county}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_STRUCTURE}:</td>
			<td width="37.5%">{$opportunity->lead_structure}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_CITY}:</td>
			<td width="37.5%">{$opportunity->lead_city}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_TYPE}:</td>
			<td width="37.5%">{$opportunity->lead_type}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_ZIP_CODE}:</td>
			<td width="37.5%">{$opportunity->lead_zip_code}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_OWNER}:</td>
			<td width="37.5%">{$opportunity->lead_owner}</td>
		</tr>	
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_PROJECT_STATUS}:</td>
			<td width="37.5%">{$opportunity->lead_project_status}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LABOR_TYPE}:</td>
			<td width="37.5%"><input type="checkbox" name="lead_union_c" id="lead_union_c" value="1" {if $opportunity->lead_union_c==1}checked="checked"{/if} disabled >&nbsp;{$MOD.LBL_LEAD_UNION_C}&nbsp;&nbsp;<input type="checkbox" name="lead_non_union" id="lead_non_union" value="1" {if $opportunity->lead_non_union==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_LEAD_NON_UNION}&nbsp;&nbsp;<input type="checkbox" name="lead_prevailing_wage" id="lead_prevailing_wage" value="1"  {if $opportunity->lead_prevailing_wage==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_LEAD_PREVAILING_WAGE}
</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_START_DATE}:</td>
			<td width="37.5%">{$opportunity->lead_start_date}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_SQUARE_FOOTAGE}:</td>
			<td width="37.5%">{$opportunity->lead_square_footage}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_END_DATE}:</td>
			<td width="37.5%">{$opportunity->lead_end_date}</td>
			<td scope="col" width="12.5%">&nbsp;</td>
			<td width="37.5%">&nbsp;</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_CONTACT_NO}:</td>
			<td width="37.5%">{$opportunity->lead_contact_no}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_STORIES_BELOW_GRADE}:</td>
			<td width="37.5%">{$opportunity->lead_stories_below_grade}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_NUMBER_OF_BUILDINGS}:</td>
			<td width="37.5%">{$opportunity->lead_number_of_buildings}</td>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_STORIES_ABOVE_GRADE}:</td>
			<td width="37.5%">{$opportunity->lead_stories_above_grade}</td>
		</tr>
		<tr>
			<td scope="col" width="12.5%">{$MOD.LBL_LEAD_VALUATION}:</td>
			<td width="37.5%">{if $opportunity->valuation neq "0"}{$opportunity->valuation|number_format:2}{/if}</td>
			<td scope="col" width="12.5%">&nbsp;</td>
			<td width="37.5%">&nbsp;</td>
		</tr>	
		<tr>
			<td colspan="4"><div style="width:100%; height:250px; overflow: scroll; ">{$opportunity->lead_scope_of_work}</div></td>		
		</tr>
		
	</table>
</div>