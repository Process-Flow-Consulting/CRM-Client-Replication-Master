{*

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
*}


<style>
{literal}
.moduleTitle h2
{
    font-size: 18px;
}
.link {
    text-decoration:underline
}
{/literal}
</style>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>

<div class="dashletPanelMenu wizard">
    <div class="bd">
            <div class="screen">
            {$MODULE_TITLE}
            <br>
            {$CONFIRM_SAVE_INSTRUCTION}
            <div class="hr"></div>

<form enctype="multipart/form-data" real_id="importcsvfinal" id="importcsvfinal" name="importcsvfinal" method="POST" action="index.php">

{foreach from=$smarty.request key=k item=v}
    {if $k neq 'current_step' && $k neq 'action'  &&  $k neq 'previous_action' &&  $k neq 'module'}
        {if is_array($v)}
            {foreach from=$v key=k1 item=v1}
                <input type="hidden" name="{$k}[]" value="{$v1}">
            {/foreach}
        {else}
            <input type="hidden" name="{$k}" value="{$v}">
        {/if}
    {/if}
{/foreach}
<input type="hidden" name="module" value="Leads">
<input type="hidden" name="action" value="{$NEXT_ACTION}">
<input type="hidden" name="previous_action" value="{$PREVIOUS_ACTION}">
<input type="hidden" name="current_step" value="{$CURRENT_STEP}"> 
<br>



<table border="0" cellspacing="0" cellpadding="0" width="100%" id="importTable" class="detail view">
<tr>  
    <td align="left" scope="row" width="15%">
        <label for="import_source">{$MOD.LBL_IMPORT_SOURCE}</label>
    </td>
    <td align="left" colspan="2">
        {$smarty.request.import_source}
   </td>
   <td></td>
</tr>
</table>

<br />

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
    <td align="left">
        <input title="{$MOD.LBL_BACK}"  id="goback" class="button" type="submit" name="button" value="  {$MOD.LBL_BACK}  ">&nbsp;
        <input title="{$MOD.LBL_SAVE}"  id="gonext" class="button" type="submit" name="button" value="  {$MOD.LBL_SAVE}  ">
    </td>
</tr>
</table>

<script>
{$JAVASCRIPT}
</script>  
