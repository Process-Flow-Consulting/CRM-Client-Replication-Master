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
            {$CONFIRM_CSV_INSTRUCTION}
            <div class="hr"></div>

<form enctype="multipart/form-data" real_id="importcsvconfirm" id="importcsvconfirm" name="importcsvconfirm" method="POST" action="index.php">
<input type="hidden" name="module" value="Leads">

<input type="hidden" name="action" value="{$NEXT_ACTION}">
<input type="hidden" name="previous_action" value="{$PREVIOUS_ACTION}">
<input type="hidden" name="current_step" value="{$CURRENT_STEP}"> 

<input type="hidden" name="lead_file_name" value="{$LEAD_FILE_NAME}">
<input type="hidden" name="bidder_file_name" value="{$BIDDER_FILE_NAME}">

<input type="hidden" name="lead_has_header" value="{$LEAD_HAS_HEADER}">
<input type="hidden" name="bidder_has_header" value="{$BIDDER_HAS_HEADER}">

<input type="hidden" name="lead_column_count" value ="{$LEAD_COLUMN_COUNT}">
<input type="hidden" name="bidder_column_count" value ="{$BIDDER_COLUMN_COUNT}">

<input type="hidden" name="custom_delimiter" value="{$CUSTOM_DELIMITER}">
<input type="hidden" name="custom_enclosure" value="{$CUSTOM_ENCLOSURE}">

<input type="hidden" name="import_source" value="{$smarty.request.import_source}">


{if $LEAD_FILE_NAME neq '' }
<h2> Leads </h2>
<br>
<div id="confirm_table" class="confirmTable">
{include file='custom/modules/Leads/tpls/lead_confirm_table.tpl'}
</div>

{/if}

{if $BIDDER_FILE_NAME neq '' }
<h2> Bidders </h2>
<br>
<div id="confirm_table" class="confirmTable">
{include file='custom/modules/Leads/tpls/bidder_confirm_table.tpl'}
</div>

{/if}


    <table width="100%" cellpadding="2" cellspacing="0" border="0">
        <tr>
            <td align="left">
                <input title="{$MOD.LBL_BACK}"  id="goback" class="button" type="submit" name="button" value="  {$MOD.LBL_BACK}  ">&nbsp;
                <input title="{$MOD.LBL_NEXT}"  class="button" type="submit" name="button" value="  {$MOD.LBL_NEXT}  " id="gonext">
            </td>
        </tr>
    </table>
</form>

</div>
   </div>
</div>

<script>
{$JAVASCRIPT}
</script>  
