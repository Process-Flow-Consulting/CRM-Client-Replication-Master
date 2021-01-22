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
	.span_error{
		color:red;
	}
</style>

<script type="text/javascript">
	var check_error = 0;
	var field_error = 0;
	
	
	$(document).ready(function() {
	
		showDisableAddValue(); 
		setHiddenModule();
		addToValidate('createcustomfields','create_label','varchar',true,'Label');
	});
	
	//Enable Disable Add & Text Value
	function showDisableAddValue()
	{
		var getSelectedValPP = $('#create_type :selected').val();
		
		if(getSelectedValPP == 'enum' || getSelectedValPP == 'multienum'){
			$('#add_value').show();
			$('#remove_value').show();
		}
		else{
			$('#add_value').hide();
			$('#create_add_value').val('');
			$('#remove_value').hide();
			$('#dropdown_values').html("<option value=''></option>");			
		}
	}
	
	//Add Options to Multiselect on click of add button
	function addValues()
	{
		var inputValue = $.trim($('#create_add_value').val());
		
		if(validateValue(inputValue)){
			$("#dropdown_values > option").each(function(){
				 if(inputValue == this.text){
				 	check_error = 1;
				 	return false;	
				 }
				 else{
				 	check_error = 0;
				 }
			});
		
			if (inputValue != '' && check_error == 0){
				$('#dropdown_values').append("<option value='"+inputValue+"'>"+inputValue+"</option>");
				$('#error_span').html("");	
				$('#create_add_value').val("");
			}
			else{
				if(check_error = 1){
					$('#error_span').html(SUGAR.language.get('Administration','LBL_VALUE_EXISTS'));
				}
			}
		}
		else{
			$('#error_span').html(SUGAR.language.get('Administration','LBL_ALPHANUMERIC_ERROR'));
		}
	}
	
	//Remove Selected Values from Multiselect
	function removeValues()
	{
		var $select = $('#dropdown_values');
    	$('option:selected',$select).remove();
	}
	
	function setHiddenModule()
	{
		var getUserModule = $('#module_name :selected').val();
		$('#user_modules').val(getUserModule);
		if(getUserModule == 'client_opportunity'){
			$('#remove_value_advance_search').hide();
		}
		else{
			$('#remove_value_advance_search').show();
		}
		
		if($('#editMode').val() == 0){
			moduleFieldCount = '{/literal}{$moduleFieldCount}{literal}';
			
			obj = jQuery.parseJSON(moduleFieldCount);
			if(obj[getUserModule] == 12){
				$('#field_error').text(SUGAR.language.get('Administration','LBL_MAX_8_VALUE_ERROR'));
				field_error = 1;
			}
			else{
				$('#field_error').text('');
				field_error = 0;
			}
		}
	}
	
	function validateValue(input)
	{
		var alphaReg = /^[A-Za-z0-9 _.-]+$/;
		var valid = alphaReg.test(input);
		if(!valid) {
        	return false;
    	} else {
    		return true;
    	}
	}
	
	function customValidation()
	{
		var counter = 0;
		if(check_form('createcustomfields')){
			if(!validateValue($('#create_label').val())){
				$('#field_error_lang').html(SUGAR.language.get('Administration','LBL_ALPHANUMERIC_ERROR'));
				return false;
			}
			else{
				$('#field_error_lang').html('');
			}
			if($('#create_type :selected').val() == 'enum' || $('#create_type :selected').val() == 'multienum'){
				if($("#dropdown_values option:not(:empty)").length > 0){
					$("#dropdown_values > option").each(function(){
						$(this).attr('selected', 'selected');
					});
				}
				else{
					$('#error_span').text(SUGAR.language.get('Administration','LBL_NO_VALUE_ERROR'));
					return false;
				}
			}
			if(field_error == 1){
				return false;	
			}
			return true;
		}		
		return false;
	}
	
</script>
{/literal}
<form id="createcustomfields" name="createcustomfields" method="POST" action="index.php">
	<input type='hidden' id='editMode' name='editMode' value={$editMode} />
	<input type='hidden' name='customEditfield' value={$editFieldName} />
	<input type='hidden' id='user_modules' name='user_modules' value={if empty($SELECTED_MODULE)} Account {else} {$SELECTED_MODULE} {/if}/>
	<input type='hidden' name='module' value='Administration' /> 
	<input type='hidden' name='return_module' value="{$RETURN_MODULE}" />
	<input type='hidden' name='return_action' value="{$RETURN_ACTION}" />
	<input type='hidden' name='action' value="createcustomfields" />
	<div style="height: 10px;"></div>
	
	<table width="100%" cellspacing="0" cellpadding="1" border="0" class="yui3-skin-sam edit view">
		<tbody>									
			<tr>
				<th align="left" colspan="8">
					<h4>{$MOD.LBL_CREATE_EDIT_CUSTOM_FIELDS}</h4>
				</th>
			</tr>		
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_CREATE_EDIT_CUSTOM_FIELDS_MODULE}>{$MOD.LBL_CREATE_EDIT_CUSTOM_FIELDS_MODULE}</label>
            	</td>
				<td  scope="col" width='78%'>
				 	<select  name='module_name' id="module_name" {if $editMode eq 1} disabled {/if} onchange='setHiddenModule();'>
		         		{html_options options=$moduleName  selected=$SELECTED_MODULE}
		         	</select>						
		         	<div id='field_error' class='span_error'></div>
            	</td>            		            	
			</tr>
			<tr>
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_CREATE_LABEL}>{$MOD.LBL_CREATE_LABEL}</label>
            	</td>
				<td  scope="col" width='78%'>
					<input type="text" name="create_label" id="create_label" value="{$SELECTED_LABEL}" maxlength="50" />
					<div id='field_error_lang' class='span_error'></div>
            	</td>            		            	
			</tr>
			<tr>
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_CREATE_TYPE}>{$MOD.LBL_CREATE_TYPE}</label>
            	</td>
				<td  scope="col" width='78%'>
					<select  name='create_type' id="create_type" onchange="showDisableAddValue()">
		         		{html_options options=$type  selected=$editFieldType}
		         	</select>	
            	</td>            		            	
			</tr>
			<tr id="add_value">
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_CREATE_ADD_VALUE}>{$MOD.LBL_CREATE_ADD_VALUE}</label>
            	</td>
				<td  width='20%'>
					<input type="text" name="create_add_value" id="create_add_value" value="" maxlength="50" />
					&nbsp;&nbsp;
					<input type="button" value="Add" onclick="addValues();"/>
					&nbsp;&nbsp;
					<div id='error_span' class='span_error'></div>
            	</td>            		            	
			</tr>
			<tr id="remove_value">
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_CREATE_REMOVE_VALUE}>{$MOD.LBL_CREATE_REMOVE_VALUE}</label>
            	</td>
				<td  scope="col" width='78%'>
					<select multiple="true" style="width: 150px;" size="6" id="dropdown_values" name="dropdown_values[]">
						{foreach from=$MULTISELECT_OPTIONS key=optionValue item=optionText}
						<option value="{$optionValue}" selected >{$optionText}</option>
						{/foreach}
					</select>
					&nbsp;&nbsp;
					<input type="button" value="Remove" onclick="removeValues();"/>
            	</td>            		            	
			</tr>
			{if $SELECTED_MODULE neq 'client_opportunity'}
			<tr id="remove_value_advance_search">
				<td  scope="col" width='20%' style="background-color: #EBEBED; font-weight: bold;">				
					<label for={$MOD.LBL_ADD_TO_ADVANCE_SEARCH}>{$MOD.LBL_ADD_TO_ADVANCE_SEARCH}</label>
            	</td>
				<td  scope="col" width='78%'>
					<input type="checkbox" value="1" name="advance_search" id="advance_search" {if $ADVANCE_SEARCH_VALUE eq 1} checked {/if} />
            	</td>            		            	
			</tr>
			{/if}
		</tbody>
	</table>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
			<input id="SAVE_HEADER" class="button primary" type="submit"
				value="Save" name="button" accesskey="S" title="Save [Alt+S]"
				onclick="return customValidation();"> 
			<td align="right">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>	
	</table>
</form>
