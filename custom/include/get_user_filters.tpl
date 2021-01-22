<div style="height: 300px;  overflow: scroll;">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="list view">
	{foreach from=$result item=res key=k}		
		<tr>
			<td scope="col">{$k|ucwords}(s)</td>
		</tr>
		{foreach from=$res item=filter}
			<tr>
				<td>{$filter}</td>		
			</tr>
		{/foreach}
		{foreachelse}
		<tr>
			<td align="center">No Filter Defined !!!</td>		
		</tr>				
	{/foreach}
</table>
</div>