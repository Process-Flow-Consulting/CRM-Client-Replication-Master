{literal}
<style type="text/css">
.yui-navset .yui-content, .yui-navset .yui-navset-top .yui-content
{
	background: none;
}
.atag_head1{
	font-weight: bold;
	color: #353535;
	border-bottom:1px solid #cccccc;
}
.atag_head2{
	font-weight: bold;
	color: #565656;
	1px solid #cccccc;	
	border-bottom:1px solid #cccccc;
}
.atag_table{
	border: 1px solid;
	border-color: #98C6EA !important;	
    border-radius: 6px 6px 6px 6px;    
    
	width:99%;
	margin:1%; 
	background: url(index.php?entryPoint=getImage&themeName=Sugar&imageName=dp-bd.png) repeat;  
}
.atag_table th{
	padding: 5px;
	font-size: 13px;
	vertical-align: middle;
	border-top: none;
	height: 35px;
	background:  repeat scroll 0 0 #EBEBED !important;
	text-align: left;
}
.atag_table td{
	padding: 5px;
	font-size: 13px;
	vertical-align: top;		
}

.module_sep{
	border-bottom: 1px solid;
	border-color: #98C6EA !important;
}

.span_class{
	border: 1px solid;
	border-color: #EBEBED !important;
	padding: 1%;
	width: 98%;
	border-radius: 6px 6px 6px 6px;
	
}
a:link{
	text-decoration: none;
}
</style>
{/literal}
{if $smarty.request.print eq true}
<div style="width:100%; max-height:500px;">
{else}
<div style="width:100%; max-height:500px; overflow: scroll;">
{/if}
		{if $smarty.request.print neq true}
		<div id="bottomLinks" style="margin: 0px;">		
		<a onclick="void window.open('index.php?module=Opportunities&action=ataglance&print=true&opportunity_id={$smarty.request.opportunity_id}','printwin','menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" href="javascript:void(0)" >
		<img alt="" src="themes/Sugar/images/print.png?v=xN4_lOXhmQLmINX7IufcjQ">Print</a>
		</div>
		{/if}
	{foreach from=$opportunities item=opportunity}
	{if $opportunity.parent_opportunity_id eq ""} 		
      <tr><td colspan="6">
      <table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%"> 	
	<tr>
		<td colspan="3" class="atag_head1"> Project : </td>		
		<td width="10%" align="center" class="atag_head1">Project Lead Details:</td>
		<td width="10%" align="center" class="atag_head1">Online Plans:</td>
		<td width="24%" align="center" class="atag_head1">Assigned User:</td>		
	</tr>
       	
	{else}
	<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%">	
		
	<tr>
		<td width="20%" class="atag_head1">Client:</td>
		<td width="20%" class="atag_head1">Client Contact:</td>
		<td width="10%" class="atag_head1">Client Bid Status:</td>
		<td width="10%" class="atag_head1">Sales Stage:</td>
		<td width="10%" class="atag_head1">Phone:</td>
		<td width="5%" align="center" class="atag_head1">Project Lead Details:</td>
		<td width="5%" align="center" class="atag_head1">Online Plans:</td>
		<td width="15%" align="center" class="atag_head1">Assigned User:</td>		
	</tr>
	{/if}
	<tr>
		<td><a href="index.php?module=Opportunities&action=DetailView&record={$opportunity.id}">{if $opportunity.parent_opportunity_id eq ""}{$opportunity.name}{else}{$opportunity.account_name}{/if}</a></td>
		<td>{$opportunity.contact_name}</td>
		{if $opportunity.parent_opportunity_id neq ""}<td>{$opportunity.client_bid_status}</td>
		<td>{$opportunity.sales_stage}</td>{/if}
		<td>{sugar_phone_number_format_edit value=$opportunity.phone_work}</td>
		<td align="center"><a href="javascript:void(0);" onclick="javascript:showPLDetailModal('{$opportunity.project_lead_id}', '{$smarty.request.opportunity_id}');" >View</a></td>
		<td align="center">
		{if $opportunity.online_link_count gt 0}
		<div id="urlpln{$opportunity.id}" style="position: absolute; z-index: 1030; background-image: none; visibility: visible;"></div>
        <a id="pln{$opportunity.id}" href="javascript:void(0)"  onclick="javascript:open_urls(event,'index.php?module=Leads&action=projecturl&record={$opportunity.project_lead_id}&to_pdf=true&all=1','Online Plans - {$opportunity.project_lead_name|urlencode}')" onmouseout="return nd();">View</a>
        {else}
        &nbsp;
        {/if}
		</td>
		<td align="center">{$opportunity.assigned_user_name}</td>
	</tr>
	{if $opportunity.proposals|@count neq '' || $opportunity.activities|@count neq ''}
	<tr>
		<td colspan="8">
			<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%" style="background:none;background-color: #FFF;">
				<tr height="30px">
					<th width="12%" class="atag_head2">Actions:</th>
					<th width="15%" class="atag_head2">Status:</th>
					<th width="35%" colspan="3" class="atag_head2">Subject:</th>
					<th class="atag_head2" width="25%">Date:</th>
					<th class="atag_head2" width="18%">Assigned User:</th>				
				</tr>
				<!-- Display Proposal Data -->
				{foreach from=$opportunity.proposals item=proposal}				
				<tr>
					<td>Proposal</td>
					<td>{if $proposal->verify_email_sent eq 0}Unverified
						{elseif $proposal->verify_email_sent eq 1 && $proposal->proposal_verified eq 2}Pending					
						{elseif $proposal->verify_email_sent eq 1 && $proposal->proposal_verified eq 1}Verified
						{else}None Set{/if}						
						</td>
					<td colspan="3">{$proposal->name}</td>
					<td>
						{if $proposal->date_time_opened neq ''}
							Opened: {$proposal->date_time_opened}
						{elseif $proposal->date_time_received neq ''}
							Received: {$proposal->date_time_received}</a>
						{elseif $proposal->date_time_sent neq ''}
							Sent: {$proposal->date_time_sent}
						{elseif $proposal->date_time_delivery neq ''}
							Scheduled: {$oss_timedate->convertDBDateForDisplay($proposal->date_time_delivery,$proposal->delivery_timezone,true)}
						{else}
							None Set
						{/if}
					</td>
					<td>
					   {$proposal->assigned_user_name}
					</td>				
				</tr>
				{/foreach}				
				<!-- Display Actvities Data -->
				{foreach from=$opportunity.activities key=opp_key item=activity}			
				{if $opportunity.proposals|@count eq 0}
					{if $opp_key gt 0}
						<tr>
							<td colspan="7" style="border-bottom: 1px solid #98C6EA"></td>
						</tr>
					{/if}
				{else}
						<tr>
							<td colspan="7" style="border-bottom: 1px solid #98C6EA"></td>
						</tr>
				{/if}
								
				<tr>
					<td>{$activity.action}</td>
					<td>{$activity.status}</td>
					<td colspan="3">{$activity.subject|nl2br}</td>
					<td>
					{if $activity.action eq 'Meeting' || $activity.action eq 'Call' || $activity.action eq 'Task'}
					Start: 
					{elseif $activity.action eq 'Note'}
					Modified: 
					{elseif $activity.action eq 'Email'}
					Sent: 
					{/if}
					{$oss_timedate->to_display_date_time($activity.sort_date)}
					</td>
					<td>
					{$activity.assigned_user_name}
					</td>
				</tr>
				
					{if $activity.description neq ''}					
						<tr>					
							<td height="25px" colspan="7" ><div class="span_class">{$activity.description|nl2br}</div></td>					
						</tr>
					{/if}
				
				{/foreach}								
			</table>
		</td>		
	</tr>
	{/if}	
	{if $opportunity.parent_opportunity_id eq ""} 		
      </table></td></tr>
      
    {/if}	
	{/foreach}	
</table>

</div>