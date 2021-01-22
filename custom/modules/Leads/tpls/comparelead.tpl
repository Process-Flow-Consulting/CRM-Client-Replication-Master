{literal}
<style>
/*
td[scope="col"]  {
	white-space: none;
	word-wrap: break-word;
}
*/
.edit tr td table{
	border-top: 1px solid #000000;
	border-bottom: 1px solid #000000;
}
.yui-dialog .container-close{
	background:url("custom/themes/default/images/close.png") no-repeat;
	height: 20px;
}
</style>
{/literal}
{assign var=allowedChars value=60}
<input type="hidden" name="pdld" id="pdld" value="{$POTENTIAL_DUPLICATE_LEAD_DATA->id}">
<table border="1" width="100%" cellspacing="1" cellpadding="2" class="edit view" align="center">
<tr>
	<td scope="col" width="10%" >Name: </td>
	<td scope="col" width="35%" align="left">{$ORIGINAL_LEAD_DATA->name|wordwrap:$allowedChars:"<br />\n":true}</td>
	<td scope="col" width="10%" >Name: </td>
	<td scope="col" width="35%" align="left">{$POTENTIAL_DUPLICATE_LEAD_DATA->name|wordwrap:$allowedChars:"<br />\n":true}</td>
</tr>
<tr>
	<td scope="col" width="10%" >Street Address: </td>
	<td scope="col" width="35%" align="left">{$ORIGINAL_LEAD_DATA->address|wordwrap:$allowedChars:"<br />\n":true}</td>
	<td scope="col" width="10%" >Street Address: </td>
	<td scope="col" width="35%" align="left">{$POTENTIAL_DUPLICATE_LEAD_DATA->address|wordwrap:$allowedChars:"<br />\n":true}</td>
</tr>
<tr>
	<td scope="col" width="10%" >Type: </td>
	<td scope="col" width="35%" align="left">{$ORIGINAL_LEAD_DATA->type|wordwrap:$allowedChars:"<br />\n":true}</td>
	<td scope="col" width="10%" >Type: </td>
	<td scope="col" width="35%" align="left">{$POTENTIAL_DUPLICATE_LEAD_DATA->type|wordwrap:$allowedChars:"<br />\n":true}</td>
</tr>
<tr>
	<td scope="col" width="10%" >Structure: </td>
	<td scope="col" width="35%" align="left">{$ORIGINAL_LEAD_DATA->structure|wordwrap:$allowedChars:"<br />\n":true}</td>
	<td scope="col" width="10%" >Structure: </td>
	<td scope="col" width="35%" align="left">{$POTENTIAL_DUPLICATE_LEAD_DATA->structure|wordwrap:$allowedChars:"<br />\n":true}</td>
</tr>
<tr>
	<td scope="col" width="50%"  colspan="2">Scope: </td>
	<td scope="col" width="50%"  colspan="2">Scope: </td>
</tr>
<tr>
	<td width="100%" colspan="4">
		<table width="100%">
		<tr>
			<td width="50%" colspan="2"><div id='tdiv' style="width:100%; height:200px;overflow-y:scroll;">{$ORIGINAL_LEAD_DATA->scope_of_work|wordwrap:$allowedChars:"<br />\n":true}</div></td>
			<td width="50%" colspan="2"><div id='tdiv' style="width: 100%; height:200px;overflow-y:scroll;">{$POTENTIAL_DUPLICATE_LEAD_DATA->scope_of_work|wordwrap:$allowedChars:"<br />\n":true}</div></td>
		</tr>
		</table>
	</td>
</tr>
<!--<tr>
	<td width="100%" colspan="4" align="right">
		<input type="button" name="link_to_project" id="link_to_project" value="Link to Project" class="button selected">
		<input type="button" name="cancel" id="cancel" value="Cancel" class="button">
	</td>-->
</table>