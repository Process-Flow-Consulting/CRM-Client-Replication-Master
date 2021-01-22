{literal}
<style>
#grid_container a.export {
    background: url("custom/themes/Sugar/images/icon-sprits.png") no-repeat scroll -3px -177px rgba(0, 0, 0, 0);

}
#grid_container a {
    display: block;
    line-height: 26px;
    padding-left: 25px;
    padding-right: 10px;
    text-decoration: none;
	border: 1px solid #CCCCCC;
    border-radius: 3px 3px 3px 3px;
    float: left;
    height: 26px;
    margin-left: 10px;
    background: linear-gradient(to bottom, #FDFDFD 9%, #E6E6E6 85%) repeat scroll 0 0 rgba(0, 0, 0, 0);
}
.paginationChangeButtons button {
    background-color: rgba(0, 0, 0, 0);
    background-image: none;
    border: 0 none;
    filter: none;
    height: 24px;
    padding: 0;
    position: relative;
    color: #000000;
}
</style>
<script type="text/javascript">
function orderByThisField(fieldname , order)
{
	if(order == ''){
		order = 'DESC';
	}
	else if(order == 'ASC')
	{
		order = 'DESC';
	}
	else{
		order = 'ASC';
	}
	document.getElementsByName('ordersequence')[0].value = order;
	document.getElementsByName('ordermethod')[0].value = fieldname;
	document.search_form.submit();
}
Calendar.setup ({
	inputField : "date_from",
	ifFormat : "%m/%d/%Y %H:%M",
	daFormat : "%m/%d/%Y %H:%M",
	button : "date_from_trigger",
	singleClick : true,
	dateStr : "",
	startWeekday: 0,
	step : 1,
	weekNumbers:false
});
Calendar.setup ({
	inputField : "date_to",
	ifFormat : "%m/%d/%Y %H:%M",
	daFormat : "%m/%d/%Y %H:%M",
	button : "date_to_trigger",
	singleClick : true,
	dateStr : "",
	startWeekday: 0,
	step : 1,
	weekNumbers:false
});
function next(limit) 
{
	document.getElementById('limit').value = (limit + 20);
	document.search_form.submit();
}
function previous(limit)
{
	document.getElementById('limit').value = (limit - 20);
	document.search_form.submit();
}
function first() 
{
	document.getElementById('limit').value = 0;
	document.search_form.submit();
}
function last(total , limit) 
{
	document.getElementById('limit').value = (total - limit);
	document.search_form.submit();
}
function showHideDate() {
		var dateOption1 = new Array('=','not_equal','greater_than','less_than');
		var dateOption2 = new Array('between');
		if(dateOption1.indexOf(document.getElementById('activityDateRange').value) != -1) {
			document.getElementById('start_date_field').style.display = "";
			document.getElementById('end_date_field').style.display = "none";
		}
		else if(dateOption2.indexOf(document.getElementById('activityDateRange').value) != -1){
			document.getElementById('start_date_field').style.display = "";
			document.getElementById('end_date_field').style.display = "";
		}
		else {
			document.getElementById('start_date_field').style.display = "none";
			document.getElementById('end_date_field').style.display = "none";
		}
}

$(function() {
	showHideDate();
});
</script>
{/literal}
<div>
<h2>
	Users Activity Report
</h2>
</div>
<div class = "listViewBody">
<form id="search_form" class="search_form" method="post" name="search_form" action="index.php?module=AOR_Reports&action=users_all_activities">
<input type="hidden" name="ordersequence" value=""/>
<input type="hidden" name="ordermethod" value=""/>
<input type="hidden" name="limit" id="limit" value=""/>

<div id="Searchbasic_searchSearchForm" class="edit view search basic" style="">

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td width="8.3333333333333%" nowrap="nowrap" scope="row">
					<label for="Users">Users</label>
				</td>
				<td width="25%" nowrap="nowrap">
					<select multiple="true" style="width: 150px;height: 96px;" size="6" id="assigned_user_id_search" name="assigned_user_id_search[]">
						<option value=""></option>
						{html_options options=$userArray selected=$selectedAssignedUserId}					
					</select>
				</td>
				<td width="8.3333333333333%" nowrap="nowrap" scope="row">
					<label for="Activity Type"> Activity Type </label>
				</td>
				<td width="25%" nowrap="nowrap">
					<select multiple="true" style="width: 150px; height: 96px;" size="6" id="activity_type_search" name="activity_type_search[]">
						<option value=""></option>
						{foreach from=$activityTypeOptions key=moduleName item=moduleVal}
							{if in_array($moduleName,$selectedActivitySearch)}
								<option value="{$moduleName}" label="{$moduleName}" selected="selected">{$moduleVal}</option>
							{ else }
								<option value="{$moduleName}" label="{$moduleName}" >{$moduleVal}</option>
							{/if}	
						{/foreach}  
					</select> 
				</td>
				<td width="25%" nowrap="nowrap">
					<input type="checkbox" name="hide_activity" value="1" {if $hide_activity eq 1} checked="checked" {/if} >Hide Activity Panel
				</td>
				<td width="25%" nowrap="nowrap">
					<input type="checkbox" name="hide_summary" value="1" {if $hide_summary eq 1} checked="checked" {/if} >Hide Summary Panel
				</td>				
			</tr>			
			<tr>
				<td width="8.3333333333333%" nowrap="nowrap" scope="row">
					<label for="Date">Date</label>
				</td>
				<td nowrap="nowrap" scope="row">
					<select name="activityDateRange" id="activityDateRange" onchange="showHideDate();"style="margin-top: 13px;">
						<option value="" ></option>
						{foreach from = $activityDateRange key=k item=value}
							{if $selectedDateSearch eq $k}
								<option value="{$k}" selected="selected">{$value}</option>
							{else}	
								<option value="{$k}">{$value}</option>
							{/if}
						{/foreach}	
					</select>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap" scope="row">
				</td>
				<td nowrap="nowrap" scope="row">
					<span id="start_date_field" style="display:none;">
           		 		<input type="text" name="date_from" value="{$dateFrom}" size="13" id="date_from">
						<img src="index.php?entryPoint=getImage&amp;themeName=Sugar&amp;imageName=jscalendar.gif" id="date_from_trigger" style="vertical-align:bottom;padding-left:3px;padding-right:3px;">
						&nbsp;&nbsp;
					</span>
					<span id="end_date_field" style="display:none">
						<input type="text"  value="{$dateTo}" name="date_to" size="13" id="date_to" />
						<img src="index.php?entryPoint=getImage&amp;themeName=Sugar&amp;imageName=jscalendar.gif" id="date_to_trigger" style="vertical-align:bottom;padding-left:3px;padding-right:3px;">
					</span>
				
			</tr>
			<tr>
				<td colspan="5">
					<input id="search_form_submit" class="button" type="submit" value="Search" name="button" title="Search" tabindex="2">
            		<input id="search_form_clear" class="button" type="button" value="Clear" name="clear" onclick="SUGAR.searchForm.clear_form(this.form); return false;" title="Clear" tabindex="2">
            		<input type="submit" name="pdf" value="{$MOD.LBL_DOWNLOAD_PDF}"/>
            	</td>
			</tr>
		</tbody>
	</table>	
</div>
</form>
{if $hide_summary neq 1}	
<div class="moduleTitle">
	<h2> Summary Panel </h2>
</div>
{* Summary Report *}
<table class="list view summaryHeadr" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr height="20">
			<th width="13%" style = "color:black;" scope="col">
				User Name
			</th>
			{foreach from=$selectedActivitySearch key=Activity_key item=Activity_val}
				<th width="{$selectedActivityWidth}%" style = "color:black;" scope="col">
					{$Activity_val}
				</th>
			{/foreach}			
		</tr>
		{assign var="outerCounter" value=0}
		{assign var="headerCounter" value=0}
		{php}
			$totalArray = array();
		{/php}
		{foreach from=$summaryFinal key=summaryFinalKey item=summaryFinalValue}
			{if $headerCounter eq 0}
				<tr class="oddListRowS1">
					<td> &nbsp;</td>	
					{foreach from=$summaryFinalValue key=moduleName item=moduleValue}
						<td  class="statusHeadersGroups" width="">
						<table width="100%">
							{assign var="statusColumnCount" value=$moduleValue|@count}
							{assign var="statusColumnCount" value=$statusColumnCount-1}
							{assign var="columnWidth" value=$selectedActivityWidth/$statusColumnCount|string_format:"%d"}							
							{foreach from=$moduleValue key=statusCol item=statusVal}
								{if $statusCol neq 'user_name'}						
									<td style="max-width : {$columnWidth}%" width="{$columnWidth}%" class="statusHeaders" nowrap="nowrap">
										<b>{$statusCol}</b>
									</td>	
								{/if}				
							{/foreach}	
						</table>	
						</td>								
					{/foreach}
				</tr>	
			{/if}
			{assign var="headerCounter" value=$headerCounter+1}
			<tr>
			<td>{$summaryFinalKey}</td>
			{foreach from=$summaryFinalValue key=moduleName item=moduleValue}
				<td class="statusHeadersGroups">
				<table id="my-fixed-width-table">
				{foreach from=$moduleValue key=statusCol item=statusVal}
					{if $statusCol neq 'user_name'}						
							<td nowrap="nowrap">
								{$statusVal}
								{php}
									$modName = $this->_tpl_vars['moduleName'];
									$statCol = $this->_tpl_vars['statusCol'];
									$statVal = $this->_tpl_vars['statusVal'];
								    $totalCounts[$modName][$statCol] = isset($totalCounts[$modName][$statCol]) ?$totalCounts[$modName][$statCol]:0;
									$totalCounts[$modName][$statCol] += $statVal; 
								{/php}
							</td>	
					{/if}				
				{/foreach}	
				</table>	
				</td>								
			{/foreach}
			</td>
			</tr>
			
		{/foreach}
		{php} $this->assign('totalCounts',$totalCounts); {/php}
		
			<tr>
				<td><b>Total<b></td>
				{foreach from=$totalCounts key=moduleName item=moduleVal}
				<td class="statusHeadersGroups">
				<table id="my-fixed-width-table">
					{foreach from=$moduleVal item=statusKey value=statusValue}		
						<td  nowrap="nowrap">
							<b>{$statusKey}</b>
						</td>
					{/foreach}
					</table>
				</td>
				{/foreach}
			</tr>
		</td></tr>
	</tbody>
</table>
</div>
<div class="moduleTitle"></div>
{/if}
{if $hide_activity neq 1}	
<table class="list view" width="100%" border="0" cellspacing="0" cellpadding="0">
   	<!-- Top buttons-->
   	<tbody>
		<tr>
			{if $limit}
				{assign var="range" value=$limit+20}
				{if $range >= $totalRecords}
					{assign var="range" value=$totalRecords}
				{/if}
			{else}
				{assign var="range" value=20}
				{if $range >= $totalRecords}
					{assign var="range" value=$totalRecords}
				{/if}
			{/if}
			<td><h2>Activity Panel</h2></td><td></td><td></td><td></td><td></td><td></td>
			<td colspan="2" align="right" width="1%" nowrap="nowrap" class="paginationChangeButtons">
				<button {if $limit eq 0}disabled="disabled"{else} onclick="first();"{/if} class="button" title="Start" name="listViewStartButton" type="button">
				<img align="absmiddle" border="0" alt="Start" src="themes/SuiteP/images/start_off.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<button {if $limit eq 0}disabled="disabled"{else}onclick="previous({$limit});"{/if} title="Previous" class="button" name="listViewPrevButton" id="listViewPrevButton" type="button">
				<img align="absmiddle" border="0" alt="Previous" src="themes/SuiteP/images/previous_off.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<span class="pageNumbers" style = "color: #201d1d;">({if $limit}{$limit+1}{else}1{/if} - {$range} of {$totalRecords})</span>
				<button  class="button" title="Next" name="listViewNextButton" type="button" {if $range == $totalRecords}disabled="disabled"{else} onclick="next({if $limit}{$limit}{else}0{/if})"{/if}>
				<img align="absmiddle" border="0" alt="Next" src="themes/SuiteP/images/next.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<button {if $range == $totalRecords}disabled="disabled"{else} onclick="last({$totalRecords},{$lastLimit});"{/if} class="button" title="End" name="listViewEndButton" id="listViewEndButton" type="button">
				<img align="absmiddle" border="0" alt="End" src="themes/SuiteP/images/end.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
			</td>
		</tr>
		<tr height="20" >
			<th width="28%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'name'}
				{assign var=imageName value='arrow_up.gif'}
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'name'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('name' , {if $ordermethod eq 'name'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Subject</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
			<th width="7%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'activity'}
				{assign var=imageName value='arrow_up.gif' }
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'activity'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('activity' , {if $ordermethod eq 'activity'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Activity</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
			<th width="15%" scope="col">
				<b style="text-decoration: none;color:#444444;">Related To</b>
			</th>
			<th width="7%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'status'}
				{assign var=imageName value='arrow_up.gif' }
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'status'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('status' , {if $ordermethod eq 'status'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Status</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
			<th width="13%" scope="col">
				<b style="text-decoration: none;color:#444444;">Date</b>
			</th>
			<th width="13%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'date_modified'}
				{assign var=imageName value='arrow_up.gif' }
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'date_modified'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('date_modified' , {if $ordermethod eq 'date_modified'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Date Modified</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
			<th width="13%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'date_entered'}
				{assign var=imageName value='arrow_up.gif' }
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'date_entered'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('date_entered' , {if $ordermethod eq 'date_entered'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Date Created</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
			<th width="4%" scope="col">
			{if $ordersequence eq 'ASC' && $ordermethod eq 'assigned_user_id'}
				{assign var=imageName value='arrow_up.gif' }
			{elseif $ordersequence eq 'DESC' && $ordermethod eq 'assigned_user_id'}
				{assign var=imageName value='arrow_down.gif'}
			{else}
                {assign var=imageName value='arrow.gif'}
            {/if}
				<a href="javascript:void(0)" onclick="orderByThisField('assigned_user_id' , {if $ordermethod eq 'assigned_user_id'} '{$ordersequence}' {else} '' {/if})" style="text-decoration: none;color:#444444;"><b>Assigned User</b>&nbsp;<img align="absmiddle" border="0" alt="Sort" src="{sugar_getimagepath file=$imageName}"></a>
			</th>
		</tr>
		{foreach from=$allActivities key=allActivitiesKey item=allActivitiesValue}
		<tr class='oddListRowS1'>
			<td><a href="index.php?module={$allActivitiesValue.Activity}&action=DetailView&record={$allActivitiesKey}" target="_blank">{$allActivitiesValue.name}</a></td>
			<td>{$allActivitiesValue.Activity}</td>
			<td><a href="index.php?module={$allActivitiesValue.parent_type}&action=DetailView&record={$allActivitiesValue.parent_id}" target="_blank">{$allActivitiesValue.relatedTo}</a></td>
			<td>{$allActivitiesValue.status} &nbsp;</td>
			<td>{if !empty($allActivitiesValue.Date)}{$timeDate->to_display_date_time($allActivitiesValue.Date)}{else}&nbsp;{/if}</td>
			<td>{$timeDate->to_display_date_time($allActivitiesValue.date_modified)}</td>
			<td>{$timeDate->to_display_date_time($allActivitiesValue.date_entered)}</td>
			<td><a href="index.php?module=users&action=DetailView&record={$allActivitiesValue.assigned_user_id}" target="_blank">{$allActivitiesValue.user_name}</a></td>
			
		</tr>
		{/foreach}	
	</tbody>
</table>
</div>
<div id="grid_container" class="clearfix">
   	<!-- Top buttons-->
   	<table width="100%">
		<tr>
			{if $limit}
				{assign var="range" value=$limit+20}
				{if $range >= $totalRecords}
					{assign var="range" value=$totalRecords}
				{/if}
			{else}
				{assign var="range" value=20}
				{if $range >= $totalRecords}
					{assign var="range" value=$totalRecords}
				{/if}
			{/if}
			<td align="right" width="1%" nowrap="nowrap" class="paginationChangeButtons">
				<button {if $limit eq 0}disabled="disabled"{else} onclick="first();"{/if} class="button" title="Start" name="listViewStartButton" type="button">
				<img align="absmiddle" border="0" alt="Start" src="themes/SuiteP/images/start_off.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<button {if $limit eq 0}disabled="disabled"{else}onclick="previous({$limit});"{/if} title="Previous" class="button" name="listViewPrevButton" id="listViewPrevButton" type="button">
				<img align="absmiddle" border="0" alt="Previous" src="themes/SuiteP/images/previous_off.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<span class="pageNumbers" style = "color: #201d1d;">({if $limit}{$limit+1}{else}1{/if} - {$range} of {$totalRecords})</span>
				<button  class="button" title="Next" name="listViewNextButton" type="button" {if $range == $totalRecords}disabled="disabled"{else} onclick="next({if $limit}{$limit}{else}0{/if})"{/if}>
				<img align="absmiddle" border="0" alt="Next" src="themes/SuiteP/images/next.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
				<button {if $range == $totalRecords}disabled="disabled"{else} onclick="last({$totalRecords},{$lastLimit});"{/if} class="button" title="End" name="listViewEndButton" id="listViewEndButton" type="button">
				<img align="absmiddle" border="0" alt="End" src="themes/SuiteP/images/end.png?v=Gn8xzET_ENY2H2DePnjJTQ">
				</button>
			</td>
		</tr>
	</table>
{/if}
</div>
{literal}
<style>
table.summaryHeadr td{
padding:0 !important;
text-align:center;
}
table.summaryHeadr td.statusHeaders{
color:#000000;
background-color:#FFF !important;
padding:2px;
}
table.summaryHeadr td.statusHeadersGroups{
border:1px solid #EBEBED;
}
table.summaryHeadr th{

text-align:center;
}

#my-fixed-width-table {
	table-layout: fixed;
    width: 100%;
}

#my-fixed-width-table td{
	width: 10px;
    text-align: center;
}

table
<style>
{/literal}