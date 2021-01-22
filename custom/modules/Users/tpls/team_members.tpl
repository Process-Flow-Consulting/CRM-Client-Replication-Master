<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td class="centerMiddle" colspan="4" >{$MOD.LBL_USER_TM_INFO}</td>
</tr>
<tr>
	<td width="5%">&nbsp;</td>
	<td  width="45%">&nbsp;</td>
	<td  width="5%" >&nbsp;</td>
	<td  width="45%">&nbsp;</td>
</tr>
<tr>
<td class="centerMiddle" scope="row" >
                    {$MOD.LBL_USER_LBL}:</td>
<td class="centerMiddle" >
	<slot>
	<select multiple="true" name="tms[]" id="tms">
	{html_options options=$DOM_TEAMMEMBERS}
	</select>
	</slot>
</td>
<td class="centerMiddle" scope="row" nowrap="nowrap">
                       <slot> 
                       <input type=button value=">>" onclick="javascript:swapSelected('tms','tms_filter')"  /><br/>
                       <input type=button value="<<" onclick="javascript:swapSelected('tms_filter','tms')">
                     </slot>
                    </td>
<td class="centerMiddle" >
    <slot><select multiple="true" name="tms_filter[]" id="tms_filter">
    	{html_options options=$TEAM_MEMBER_OPTOIONS}
	</select>
	</slot></td>
</tr>
</table>