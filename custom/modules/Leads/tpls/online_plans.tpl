<div
	style="overflow-x: hidden; overflow-y: auto; height: 200px; padding-right: 20px">	
		
			<table class="list view" border="0" style="width:520px" class="view list" cellspacing="0" cellpadding="0" align="center">

				<tr height="20" class="oddListRowS1" style="background-color:#929798;">
					<th scope="col" valign="top" width="20%">{$AR_TITLE.type}</th>
					<th scope="col" valign="top"  width="20%">{$AR_TITLE.source}</th>
					<th scope="col" valign="top"  width="20%">{$AR_TITLE.review}</th>
					<th scope="col" valign="top" width="20%">{$AR_TITLE.link}</th>
				</tr>
				
				{foreach from=$AR_DATA item=data}

				<tr height="20" class="oddListRowS1">
					<td scope="row" valign="top" width="20%"><span > {$data.plan_type}</span></td>
					<td scope="row" valign="top"  width="20%"><span > {$data.plan_source}</span></td>
					<td scope="row" valign="top"  width="20%"><span >{$timedate->to_display_date_time($data.last_reviewed_date)} </span></td>
					<td scope="row" valign="top" width="20%">
						<span >
							<a href="index.php?module=oss_OnlinePlans&action=openUrl&record={$data.id}" target="_blank">Open</a>
						</span>
					</td>
				</tr>
				{foreachelse}
				<tr height="20" class="oddListRowS1"><td>{$APP.LBL_NO_DATA}</td></tr>
				{/foreach}
				<tr style="background-color:#929798;">
					<th colspan=4 ></th>
				</tr>
			</table>

		</div>
		


