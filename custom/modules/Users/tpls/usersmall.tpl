{php}
	 $this->assign('STATE_DOM',$GLOBALS['app_list_strings']['state_dom']);
{/php}

{if $title eq ""}
{assign var=title value=$smarty.request.title}
{/if}
{if $smarty.request.count eq ''}
{assign var=totalCount value=$smarty.request.$title.first_name|@count}
{else}
{assign var=totalCount value=$smarty.request.count}
{/if}

{section name=itrator loop=$smarty.request.$title.first_name|@count step=1 }
{assign var=index value=$smarty.section.itrator.index}
{assign var=indexCount value=$smarty.section.itrator.index+1}
<div id="{$title}{$indexCount}" style="float:left;width: 70%;padding:1%;background-color:#ffffff ">
    {$APP_LIST}
<table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">
    <tr>
        <td colspan="4" scope="row" style="text-align:center;">
            <b>{if $title eq 'team_manager' }
                {$MOD.LBL_TEAM_MGR}
               {elseif $title eq 'full_pipeline' }
               {$MOD.LBL_FULL_PIPELINE}
               {elseif  $title eq 'lead_reviewer'   }
               {$MOD.LBL_LEAD_REVIEW}
               {elseif  $title eq 'opp_reviewer'  } {$MOD.LBL_OPP_REVIEW} {/if}{$indexCount}</b>
        </td>
    </tr>
    <tr>
    <td colspan="4" class="required"><i>{$MOD.LBL_ALL_FIELDS_REQUIRED}</i></td>
    </tr>
<tr>
    <td class="txtblue">
        {$MOD.LBL_FIRST_NAME}
    </td>
    <td>
        <input type="text" name="{$title}[first_name][]" value="{$smarty.request.$title.first_name[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.first_name.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.first_name.$index}</div>{/if}
    </td>
    <td class="txtblue">
        {$MOD.LBL_LAST_NAME}
    </td>
    <td>
        <input type="text" name="{$title}[last_name][]"  value="{$smarty.request.$title.last_name[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.last_name.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.last_name.$index}</div>{/if}
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_USER_NAME}</td>
    <td>
        <input type="text" name="{$title}[user_name][]"  value="{$smarty.request.$title.user_name[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.user_name.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.user_name.$index}</div>{/if}
    </td>
    <td class="txtblue">{$MOD.LBL_EMAIL}</td>
    <td>
        <input type="text" class="checkUserEmailCustom" name="{$title}[email][]"  value="{$smarty.request.$title.email[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.email.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.email.$index}</div>{/if}
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_PASSWORD}</td>
    <td>
            <input type="password" name="{$title}[password][]"  />
            {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.password.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.password.$index}</div>{/if}
    </td>
    <td class="txtblue">{$MOD.LBL_CONFIRM_PASSWORD}</td>
    <td>
        <input type="password" name="{$title}[con_password][]"  />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.con_password.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.con_password.$index}</div>{/if}
    </td>
</tr>

<!-- new fileds -->
<tr>
    <td class="txtblue">{$MOD.LBL_COMPANY}</td>
    <td>
        <input type="text" name="{$title}[company_name][]"  value="{$smarty.request.$title.company_name[$index]}"   />
    </td>
    <td 
    </td>
    <td>
       
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_ADDRESS}</td>
    <td>
        <input type="text" name="{$title}[address][]"  value="{$smarty.request.$title.address[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.address.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.address.$index}</div>{/if}
    </td>
    <td class="txtblue">{$MOD.LBL_CITY}</td>
    <td>
        <input type="text" name="{$title}[city][]"  value="{$smarty.request.$title.city[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.city.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.city.$index}</div>{/if}
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_STATE}</td>
    <td>
    	{*
        <input type="text" name="{$title}[state][]"  value="{$smarty.request.$title.state[$index]}" />
        
	      *}
	      {php}
	      $this->assign('STATE_DOM',$GLOBALS['app_list_strings']['state_dom']);
	      {/php}
	     <select name="{$title}[state][]"  >
        {html_options options=$STATE_DOM selected=$smarty.request.$title.state[$index] }
        </select>
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.state.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.state.$index}</div>{/if}
    </td>
    <td class="txtblue">{$MOD.LBL_ZIP}</td>
    <td>
        <input type="text" name="{$title}[zip][]"  value="{$smarty.request.$title.zip[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.zip.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.zip.$index}</div>{/if}
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_PHONE}</td>
    <td>
        <input type="text" name="{$title}[phone][]"  value="{$smarty.request.$title.phone[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.phone.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.phone.$index}</div>{/if}
    </td>
    <td class="txtblue">{$MOD.LBL_FAX}</td>
    <td>
        <input type="text" name="{$title}[fax][]"  value="{$smarty.request.$title.fax[$index]}" />
        {if $IS_ERRORS eq 1 and $ERROR_MESSAGES.$title.fax.$index neq ''}<br/><div class="required validation-message">{$ERROR_MESSAGES.$title.fax.$index}</div>{/if}
    </td>
</tr>
<!-- new fileds -->

</table>
</div>
{sectionelse}
<div id="{$title}{$smarty.request.count}" style="float:left;width: 98%;padding:1%;background-color:#ffffff ">
    
<table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">
    <tr>
        <td colspan="4" scope="row" style="text-align:center;">
            <b>{if $title eq 'team_manager' }
                {$MOD.LBL_TEAM_MGR}
               {elseif $title eq 'full_pipeline' }
               {$MOD.LBL_FULL_PIPELINE}
               {elseif  $title eq 'lead_reviewer'   }
               {$MOD.LBL_LEAD_REVIEW}
               {elseif  $title eq 'opp_reviewer'  } 
				{$MOD.LBL_OPP_REVIEW}
               {elseif  $title eq 'admin'  }
                {$MOD.LBL_ADMIN}
               {/if}{$smarty.request.count}</b>
        </td>
    </tr>
    <tr>
    <td colspan="4" class="required"><i>{$MOD.LBL_ALL_FIELDS_REQUIRED}</i></td>
    </tr>
<tr>
    <td class="txtblue">
        {$MOD.LBL_FIRST_NAME}
    </td>
    <td>
        <input type="text" name="{$title}[first_name][]" />
    </td>
    <td class="txtblue">
        {$MOD.LBL_LAST_NAME}
    </td>
    <td>
        <input type="text" name="{$title}[last_name][]"  />
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_USER_NAME}</td>
    <td>
        <input type="text" name="{$title}[user_name][]"  />
    </td>
    <td class="txtblue">{$MOD.LBL_EMAIL}</td>
    <td>
        <input type="text" class="checkUserEmailCustom" name="{$title}[email][]"  />
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_PASSWORD}</td>
    <td>
            <input type="password" name="{$title}[password][]"  />
    </td>
    <td class="txtblue">{$MOD.LBL_CONFIRM_PASSWORD}</td>
    <td>
        <input type="password" name="{$title}[con_password][]"  />
    </td>
</tr>

<!-- new fileds -->
<tr>
    <td class="txtblue">{$MOD.LBL_COMPANY}</td>
    <td>
        <input type="text" name="{$title}[company_name][]"  value="{$COMPANY_NAME}"  />
    </td>
    <td 
    </td>
    <td>
       
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_ADDRESS}</td>
    <td>
        <input type="text" name="{$title}[address][]" value="{$COMPANY_ADDRESS}" />
    </td>
    <td class="txtblue">{$MOD.LBL_CITY}</td>
    <td>
        <input type="text" name="{$title}[city][]" value="{$COMPANY_CITY}" /> 
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_STATE}</td>
    <td>
         
	     <select name="{$title}[state][]" >
        {html_options options=$STATE_DOM selected=$COMPANY_STATE}
        </select>
    {*     <input type="text" name="{$title}[state][]" value="{$COMPANY_STATE}" /> 
*}     
    </td>
    <td class="txtblue">{$MOD.LBL_ZIP}</td>
    <td>
        <input type="text" name="{$title}[zip][]" value="{$COMPANY_ZIP}" />
    </td>
</tr>
<tr>
    <td class="txtblue">{$MOD.LBL_PHONE}</td>
    <td>
        <input type="text" name="{$title}[phone][]"  value="{sugar_phone_number_format_edit value=$COMPANY_PHONE}"  />
    </td>
    <td class="txtblue">{$MOD.LBL_FAX}</td>
    <td>
        <input type="text" name="{$title}[fax][]"  value="{sugar_phone_number_format_edit value=$COMPANY_FAX}" />
    </td>
</tr>
<!-- new fileds -->

</table>
</div>
{/section}
<style>
{literal}
.edit tr td {
    font-weight: normal;
    /* vertical-align: top; */
}
.error:empty {
   background: inherit;
}
{/literal}
</style>
