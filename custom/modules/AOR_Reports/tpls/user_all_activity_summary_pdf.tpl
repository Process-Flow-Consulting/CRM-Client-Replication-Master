{* Summary Report For PDF *}
<font size="12">{$MOD.LBL_SUMMARY_PDF}</font>
<br/>
<br/>
{assign var="meetingColSpan" value=0}
{assign var="callsColSpan" value=0}
{assign var="emailsColSpan" value=0}
{assign var="tasksColSpan" value=0}
{assign var="notesColSpan" value=0}
<table border=1>
	<tr >
		<thead>
			<th align="center" bgcolor="{$options.header.fill}">
				<font color="{$options.header.textColor}" size="9"><b>User Name</b></font>
			</th>
			{foreach from=$colSpanMod key=modKey item=modValue}
				{if $modKey eq 'Meeting'}
					{assign var="meetingColSpan" value=$modValue}		
					<th colspan="{$meetingColSpan}" align="center" bgcolor="{$options.header.fill}">
						<font color="{$options.header.textColor}" size="9"><b>Meetings</b></font>
					</th>				
				{/if}
				{if $modKey eq 'Call'}	
					{assign var="callsColSpan" value=$modValue}	
					<th colspan="{$callsColSpan}" align="center" bgcolor="{$options.header.fill}">
						<font color="{$options.header.textColor}" size="9"><b>Calls</b></font>
					</th>			
				{/if}
				{if $modKey eq 'Email'}		
					{assign var="emailsColSpan" value=$modValue}
					<th colspan="{$emailsColSpan}" align="center" bgcolor="{$options.header.fill}">
						<font color="{$options.header.textColor}" size="9"><b>Emails</b></font>
					</th>				
				{/if}
				{if $modKey eq 'Task'}	
					{assign var="tasksColSpan" value=$modValue}	
					<th colspan="{$tasksColSpan}" align="center" bgcolor="{$options.header.fill}">
						<font color="{$options.header.textColor}" size="9"><b>Tasks</b></font>
					</th >				
				{/if}
				{if $modKey eq 'Notes'}		
					{assign var="notesColSpan" value=$modValue}
					<th colspan="{$notesColSpan}"align="center" bgcolor="{$options.header.fill}">
						<font color="{$options.header.textColor}" size="9"><b>Notes</b></font>
					</th>			
				{/if}	
			{/foreach}
		</thead>
	</tr>
		<tbody>
		{assign var="outerCounter" value=0}
		{assign var="headerCounter" value=0}
		{php}
			$totalArray = array();
		{/php}
		{assign var="rowCounter" value=1}
		{foreach from=$summaryFinalPdf key=summaryFinalKey item=summaryFinalValue}
			{if $headerCounter eq 0}
				<tr>
					<td> &nbsp;</td>	
					{foreach from=$summaryFinalValue key=moduleName item=moduleValue}
							{foreach from=$moduleValue key=statusCol item=statusVal}
								{if $statusCol neq 'user_name'}						
									<td align="center">
										<font size="9"><b>{$statusCol}</b></font>
									</td>	
								{/if}				
							{/foreach}										
					{/foreach}
				</tr>	
			{/if}
			{assign var="headerCounter" value=$headerCounter+1}
			<tr {if $rowCounter%2 eq 0} bgcolor="{$options.evencolor}" {/if}>
			<td>{$summaryFinalKey}</td>
			{foreach from=$summaryFinalValue key=moduleName item=moduleValue}
				{foreach from=$moduleValue key=statusCol item=statusVal}
					{if $statusCol neq 'user_name'}						
							<td align="center">
								<font size="9">{$statusVal}</font>
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
												
			{/foreach}
			</tr>
			{assign var="rowCounter" value=$rowCounter+1}
		{/foreach}
		{php} $this->assign('totalCounts',$totalCounts); {/php}
			<tr>				
				<td>Total</td>
				{foreach from=$totalCounts key=moduleName item=moduleVal}
					{foreach from=$moduleVal item=statusKey value=statusValue}		
						<td align="center">
							<font size="9.5"><b>{$statusKey}</b></font>
						</td>
					{/foreach}
				{/foreach}
			</tr>
	</tbody>
</table>