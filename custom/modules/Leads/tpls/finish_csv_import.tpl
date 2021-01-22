{*

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/




*}
{$MODULE_TITLE}
<span>
{if $noSuccess}
	<p>{$MOD.LBL_FAILURE}</p>
{else}
	<p>{$MOD.LBL_SUCCESS}</p>
{/if}
{if $createdCount > 0}

		{* FOR PROJECT LEADS WE ARE DIPLAYING HOW MANY OF WHAT ARE BEING CREATED IN THIS IMPORT *}

		{if $PL_IMPORT_STATUS|@count gt 0}
			{foreach from=$PL_IMPORT_STATUS item=NEW_COUNT key=ST_MODULE}
				{if $ST_MODULE AND $ST_MODULE neq 'Client Contacts'}
				<b>{$NEW_COUNT}</b>&nbsp; {$MOD.LBL_SUCCESSFULLY_IMPORTED|replace:records:$ST_MODULE}<br />
				{/if}
			{/foreach}
		{else}
			All records already exist in the system.
		{/if}	
{else}
	All records already exist in the system.
{/if}
<!-- {if $updatedCount > 0} 
{* FOR PROJECT LEADS WE ARE DIPLAYING HOW MANY OF WHAT ARE BEING CREATED IN THIS IMPORT *}	
		<b>{$updatedCount}</b>&nbsp;{$MOD.LBL_UPDATE_SUCCESSFULLY}<br />
{/if} -->
<br>
{if $errorCount > 0}
<b>{$errorCount}</b>&nbsp;{$MOD.LBL_RECORDS_SKIPPED_DUE_TO_ERROR}<br />
<a href="{$errorFile}" target='_blank'>{$MOD.LNK_ERROR_LIST}</a><br />
<a href ="{$errorrecordsFile}" target='_blank'>{$MOD.LNK_RECORDS_SKIPPED_DUE_TO_ERROR}</a><br />
{/if}
<!--{if $dupeCount > 0}
<b>{$dupeCount}</b>&nbsp;{$MOD.LBL_DUPLICATES}<br />
<a href ="{$dupeFile}" target='_blank'>{$MOD.LNK_DUPLICATE_LIST}</a><br />
{/if} -->
<br>

<form name="importlast" id="importlast" method="POST" action="index.php">
<input type="hidden" name="module" value="Import">
<input type="hidden" name="action" value="Undo">
<input type="hidden" name="import_module" value="Leads">

<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td align="left" style="padding-bottom: 2px;">
{if !$noSuccess|@count lt 1}
    <input title="{$MOD.LBL_UNDO_LAST_IMPORT}" accessKey="" class="button"
        type="submit" name="undo" id="undo" value="  {$MOD.LBL_UNDO_LAST_IMPORT}  ">
{/if}
    <input title="{$MOD.LBL_IMPORT_MORE}" accessKey="" class="button" type="submit"
            name="importmore" id="importmore" value="  {$MOD.LBL_IMPORT_MORE}  ">
        <input title="{$MOD.LBL_FINISHED}{$MODULENAME}" accessKey="" class="button" type="submit" 
            name="finished" id="finished" value="  {$MOD.LBL_IMPORT_COMPLETE}  ">
    </td>
</tr>
</table>
</form>
{$JAVASCRIPT}
