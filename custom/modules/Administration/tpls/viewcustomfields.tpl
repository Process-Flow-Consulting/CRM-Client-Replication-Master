{*
/*********************************************************************************
* The contents of this file are subject to the SugarCRM Master
Subscription * Agreement ("License") which can be viewed at *
http://www.sugarcrm.com/crm/master-subscription-agreement * By
installing or using this file, You have unconditionally agreed to the *
terms and conditions of the License, and You may not use this file
except in * compliance with the License. Under the terms of the license,
You shall not, * among other things: 1) sublicense, resell, rent, lease,
redistribute, assign * or otherwise transfer Your rights to the
Software, and 2) use the Software * for timesharing or service bureau
purposes such as hosting the Software for * commercial gain and/or for
the benefit of a third party. Use of the Software * may be subject to
applicable fees and any use of the Software without first * paying
applicable fees is strictly prohibited. You do not have the right to *
remove SugarCRM copyrights from the source code or user interface. * *
All copies of the Covered Code must include on each user interface
screen: * (i) the "Powered by SugarCRM" logo and * (ii) the SugarCRM
copyright notice * in the same form as they appear in the distribution.
See full license for * requirements. * * Your Warranty, Limitations of
liability and Indemnity are expressly stated * in the License. Please
refer to the License for the specific language * governing these rights
and limitations under the License. Portions created * by SugarCRM are
Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

*}
{literal}
<style>
	.configuratorHeader {
		border-style: solid;
    	border-color: #A9A9A9;
    	width: 96%;
    	border-width: 2px;
    	padding : 1% 1%;
	}
</style>
<script type="text/javascript">
	function showPopup(txt,TitleText,width,height){
     oReturn = function(body, caption, width, theme) {
                                        $(".ui-dialog").find(".open").dialog('destroy').remove();
                                        var bidDialog = $('<div class="open"></div>')
                                        .html(body)
                                        .dialog({
              									modal:true,
                                                autoOpen: false,
                                                title: caption,
                                                width: width,
              									height : height,
              									resizable: false,
              									position: 'fixed',
              									//show: "slide",
              				//hide: "scale",                                                
                                        });
                                        bidDialog.dialog('open');
                                        $('div[role=dialog]').css('position', 'fixed');
                                };
       
oReturn(txt,TitleText, width, '');
                                                
return;
        
}
	function loadCustomFields(editMode,customEditfield,customFieldType,customFieldLabel,advance_search,user_modules){
	 $.ajax({
			    	url : "index.php?module=Administration&action=createcustomfields&to_pdf=1",
			        data : 'editMode='+editMode+'&customEditfield='+customEditfield+'&customFieldType='+customFieldType+'&customFieldLabel='+customFieldLabel+'&advance_search='+advance_search+'&user_modules='+user_modules,
			        error : function(xhr, ajaxOptions, thrownError) {
			    	},
			    	success : function(dataVal) {
			    		
			    		showPopup(dataVal,'Create Configurator','600');
			    		SUGAR.util.evalScript(dataVal);
			    	  	//$("#showCreateCustomFields").html('');
			        	//$("#showCreateCustomFields").html(data);
			        	  
			  		}
			  		
		    	});
	
	
		
 	}
 	
function repairSugar(editField,tableName,fieldName)
{
	window.location.href="index.php?module=Administration&action=repaircustomfield&editField="+editField+"&tableName="+tableName+"&fieldName="+fieldName;
}

</script>
{/literal}

<div class="moduleTitle">
	<h2>{$MOD.LBL_CREATE_CUSTOM_FIELDS}</h2>
</div>

<div style="height: 20px;"></div>
<br/>
{if $fieldExist eq 1}
<span>{$MOD.LBL_FIELD_REPAIR_ERROR}&nbsp;&nbsp;<input type="button" Value="Repair" onclick="repairSugar({$editField},'{$tableName}','{$fieldName}')"></button></span>
{/if}
<div id='configuratorHeader' class='configuratorHeader' sytle="position:fixed">
<input type="button" class="button primary" Value="{$MOD.LNK_CREATE_CUSTOM_FIELDS}" id="openCustomFields" onclick="loadCustomFields(0,'','','','','','');">
<input id="CANCEL_HEADER" class="button" type="button" value="Cancel" name="button" onclick="window.location.href='index.php?module=Administration&action=index'; return false;" accesskey="X" title="Cancel [Alt+X]">
</div>
<div id="showCreateCustomFields" class="showCreateCustomFields"></div>
<div style="height: 10px;"></div>
	<h4>{$MOD.LBL_MODULE_CLIENTS}</h4>
	<table width="98%" cellspacing="0" cellpadding="1" border="0" class="list view" >
		<tbody>									
			<tr style="background-color: #c2c3c4;">
				<th scope="col" width='2%' align="center">
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_SERIAL_NUMBER}
				</th>
				<th scope="col" width='30%' align="center">
					{$MOD.LBL_FIELD_NAME}
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_FIELD_TYPE}	
				</th>
				<th scope="col" width='30%' style="text-align:center;">
					{$MOD.LBL_ADVANCE_SEARCH}	
				</th>
				<th align="left" width='8%'>
				</th>
			</tr>
		{assign var="counter" value=1}
		{foreach from=$accountCustom key=fieldName item=fieldValue}
			<tr class = '{if $counter%2 eq 0}evenListRowS1{else}oddListRowS1{/if}'>
				<td>
				</td>
				<td scope="row"  >
					{$counter}
				</td>
				<td scope="row"  >
					{$fieldValue.label}
				</td>
				<td scope="row"  >
					{if $fieldValue.type eq 'varchar'}{$MOD.LBL_TYPE_TEXT}{elseif $fieldValue.type eq 'multienum'}{$MOD.LBL_TYPE_MULTI_SELECT_DROP_DOWN}{else}{$MOD.LBL_TYPE_DROPDOWN}{/if}
				</td>
				<td align="center">
					<input type="checkbox" disabled {if $fieldValue.advance_search eq 'true'} checked {/if}>
				</td>
				<td scope="row" style="text-align:center;">
					<a onclick="loadCustomFields(1,'{$fieldValue.name}','{$fieldValue.type}','{$fieldValue.label}','{$fieldValue.advance_search}','Account');" href="#">{$MOD.LBL_CUSTOM_EDIT}</a>
				</td>
				
			</tr>
			{assign var="counter" value=$counter+1}
		{/foreach}
		{assign var="counter" value=1}
		</tbody>
	</table>	
<h4>{$MOD.LBL_MODULE_CLIENT_CONTACTS}</h4>
	<table width="98%" cellspacing="0" cellpadding="1" border="0" class="list view" >
		<tbody>		
			<tr style="background-color: #c2c3c4;">
				<th scope="col" width='2%' align="center">
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_SERIAL_NUMBER}
				</th>
				<th scope="col" width='30%' align="center">
					{$MOD.LBL_FIELD_NAME}
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_FIELD_TYPE}	
				</th>
				<th scope="col" width='30%' style="text-align:center;">
					{$MOD.LBL_ADVANCE_SEARCH}	
				</th>
				<th align="left" width='8%'>
				</th>
			</tr>
			{foreach from=$contactCustom key=fieldName item=fieldValue}
			<tr class = '{if $counter%2 eq 0}evenListRowS1{else}oddListRowS1{/if}'>
				<td>
				</td>
				<td scope="row"  >
					{$counter}
				</td>
				<td scope="row"  >
					{$fieldValue.label}
				</td>
				<td scope="row"  >
					{if $fieldValue.type eq 'varchar'}{$MOD.LBL_TYPE_TEXT}{elseif $fieldValue.type eq 'multienum'}{$MOD.LBL_TYPE_MULTI_SELECT_DROP_DOWN}{else}{$MOD.LBL_TYPE_DROPDOWN}{/if}
				</td>
				<td align="center">
					<input type="checkbox" disabled {if $fieldValue.advance_search eq 'true'} checked {/if}>
				</td>
				<td scope="row" style="text-align:center;">
					<a onclick="loadCustomFields(1,'{$fieldValue.name}','{$fieldValue.type}','{$fieldValue.label}','{$fieldValue.advance_search}','Contact');" href="#">{$MOD.LBL_CUSTOM_EDIT}</a>
				</td>
			</tr>
		{assign var="counter" value=$counter+1}
		{/foreach}
		{assign var="counter" value=1}
	</tbody>
</table>		
<h4>{$MOD.LBL_MODULE_PROJECT_LEAD}</h4>
<table width="98%" cellspacing="0" cellpadding="1" border="0" class="list view">
	<tbody>
		<tr style="background-color: #c2c3c4;">									
			<th scope="col" width='2%' align="center">
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_SERIAL_NUMBER}
				</th>
				<th scope="col" width='30%' align="center">
					{$MOD.LBL_FIELD_NAME}
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_FIELD_TYPE}	
				</th>
				<th scope="col" width='30%' style="text-align:center;">
					{$MOD.LBL_ADVANCE_SEARCH}	
				</th>
				<th align="left" width='8%'>
				</th>
		</tr>
	</tbody>
	{foreach from=$leadCustom key=fieldName item=fieldValue}
		<tr class = '{if $counter%2 eq 0}evenListRowS1{else}oddListRowS1{/if}'>
				<td>
				</td>
				<td scope="row"  >
					{$counter}
				</td>
				<td scope="row"  >
					{$fieldValue.label}
				</td>
				<td scope="row"  >
					{if $fieldValue.type eq 'varchar'}{$MOD.LBL_TYPE_TEXT}{elseif $fieldValue.type eq 'multienum'}{$MOD.LBL_TYPE_MULTI_SELECT_DROP_DOWN}{else}{$MOD.LBL_TYPE_DROPDOWN}{/if}
				</td>
				<td align="center">
					<input type="checkbox" disabled {if $fieldValue.advance_search eq 'true'} checked {/if}>
				</td>
			<td scope="row" style="text-align:center;">
				<a onclick="loadCustomFields(1,'{$fieldValue.name}','{$fieldValue.type}','{$fieldValue.label}','{$fieldValue.advance_search}','Lead');" href="#">{$MOD.LBL_CUSTOM_EDIT}</a>
			</td>
		</tr>
		{assign var="counter" value=$counter+1}
	{/foreach}
	{assign var="counter" value=1}
</table>		
<h4>{$MOD.LBL_PARENT_OPPORTUNITY}</h4>
<table width="98%" cellspacing="0" cellpadding="1" border="0" class="list view">
	<tbody>									
		<tr style="background-color: #c2c3c4;">
			<th scope="col" width='2%' align="center">
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_SERIAL_NUMBER}
				</th>
				<th scope="col" width='30%' align="center">
					{$MOD.LBL_FIELD_NAME}
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_FIELD_TYPE}	
				</th>
				<th scope="col" width='30%' style="text-align:center;">
					{$MOD.LBL_ADVANCE_SEARCH}	
				</th>
				<th align="left" width='8%'>
				</th>
		</tr>
	{foreach from=$opportunityCustom key=fieldName item=fieldValue}
		{if array_key_exists('parent_opportunity',$fieldValue)}
		<tr class = '{if $counter%2 eq 0}evenListRowS1{else}oddListRowS1{/if}'>
				<td>
				</td>
				<td scope="row"  >
					{$counter}
				</td>
				<td scope="row"  >
					{$fieldValue.label}
				</td>
				<td scope="row"  >
					{if $fieldValue.type eq 'varchar'}{$MOD.LBL_TYPE_TEXT}{elseif $fieldValue.type eq 'multienum'}{$MOD.LBL_TYPE_MULTI_SELECT_DROP_DOWN}{else}{$MOD.LBL_TYPE_DROPDOWN}{/if}
				</td>
				<td align="center">
					<input type="checkbox" disabled {if $fieldValue.advance_search eq 'true'} checked {/if}>
				</td>
			<td scope="row" style="text-align:center;">
				<a onclick="loadCustomFields(1,'{$fieldValue.name}','{$fieldValue.type}','{$fieldValue.label}','{$fieldValue.advance_search}','parent_opportunity');" href="#">{$MOD.LBL_CUSTOM_EDIT}</a>
			</td>
		</tr>
		{assign var="counter" value=$counter+1}
		{/if}
	{/foreach}
	{assign var="counter" value=1}
	</tbody>
</table>		
<h4>{$MOD.LBL_CLIENT_OPPORTUNITY}</h4>
<table width="98%" cellspacing="0" cellpadding="1" border="0" class="list view">
	<tbody>									
		<tr style="background-color: #c2c3c4;">
			<th scope="col" width='2%' align="center">
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_SERIAL_NUMBER}
				</th>
				<th scope="col" width='30%' align="center">
					{$MOD.LBL_FIELD_NAME}
				</th>
				<th scope="col" width='15%' align="center">
					{$MOD.LBL_FIELD_TYPE}	
				</th>
				<th scope="col" width='30%' style="text-align:center;">
				</th>
				<th align="left" width='8%'>
				</th>
		</tr>
	</tbody>
	{foreach from=$opportunityCustom key=fieldName item=fieldValue}
		{if array_key_exists('client_opportunity',$fieldValue)}
		<tr class = '{if $counter%2 eq 0}evenListRowS1{else}oddListRowS1{/if}'>
			<td>
			</td>
			<td scope="row"  >
				{$counter}
			</td>
			<td scope="row"  >
				{$fieldValue.label}
			</td>
			<td scope="row"  >
				{if $fieldValue.type eq 'varchar'}{$MOD.LBL_TYPE_TEXT}{elseif $fieldValue.type eq 'multienum'}{$MOD.LBL_TYPE_MULTI_SELECT_DROP_DOWN}{else}{$MOD.LBL_TYPE_DROPDOWN}{/if}
			</td>
			<td align="center">
			</td>
			<td scope="row" style="text-align:center;">
				<a onclick="loadCustomFields(1,'{$fieldValue.name}','{$fieldValue.type}','{$fieldValue.label}','{$fieldValue.advance_search}','client_opportunity');" href="#">{$MOD.LBL_CUSTOM_EDIT}</a>
			</td>
		</tr>
		{assign var="counter" value=$counter+1}
		{/if}
	{/foreach}
	{assign var="counter" value=1}
</table>		