
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
 *a
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
{literal}
<style>
select[name^=region]{
	width:75%;
}
input[type=text]{
	width:75%;
	border:1px solid black;
}
button{
	background-color:transparent;
	border:0px none;
}
</style>
{/literal}
<hr />

	<div class="sectitle">{sugar_translate label='LBL_SEARCH_RESULTS' module=''}</div>
	<form action="" method="post">
	<input type="hidden" name="module" value="Accounts" />
	<input type="hidden" name="action" value="wirelessmasterlookup" />
	<input type="hidden" name="page" value="{$SEARCH_RESPONSE->page}" />
	
	<input type="hidden" name="search_by" value="{$smarty.post.search_by}" />
	<input type="hidden" name="company" value="{$smarty.post.company}" />
	<input type="hidden" name="region_company" value="{$smarty.post.region_company}" />
	<input type="hidden" name="class_cat_id" value="{$smarty.post.class_cat_id}" />
	<input type="hidden" name="classification" value="{$smarty.post.classification}" />
	<input type="hidden" name="phone" value="{$smarty.post.phone}" />
	<input type="hidden" name="region" value="{$smarty.post.region}" />
	<input type="hidden" name="search_bluebook" value="1" />
	
	<table class="sec">
	{if $SEARCH_RESPONSE->SearchResult|@count neq 0}	
	    <tr>
	        <td colspan="3">
	            <input type="submit" value="Import Client" name="import_clients" />
	        </td>
	        <td align="right">
	        <form action="" method="post" >
	<input type="hidden" name="module" value="Accounts" />
	<input type="hidden" name="action" value="wirelessmasterlookup" />
	
	<input type="hidden" name="search_by" value="{$smarty.post.search_by}" />
	<input type="hidden" name="company" value="{$smarty.post.company}" />
	<input type="hidden" name="region_company" value="{$smarty.post.region_company}" />
	<input type="hidden" name="class_cat_id" value="{$smarty.post.class_cat_id}" />
	<input type="hidden" name="classification" value="{$smarty.post.classification}" />
	<input type="hidden" name="phone" value="{$smarty.post.phone}" />
	<input type="hidden" name="region" value="{$smarty.post.region}" />
	<input type="hidden" name="search_bluebook" value="1" />
	{if $SEARCH_RESPONSE->page > 1}<small>
	        <button name="page" value="{$SEARCH_RESPONSE->page-1}" > 
	        <img src="themes/Sugar/images/previous.png" alt="Previous" align="absmiddle" border="0">
	         </button>
	 {else}
	   <img src="themes/Sugar/images/previous_off.png" alt="Previous" align="absmiddle" border="0">
    {/if}
	{if $SEARCH_RESPONSE->nextpage eq 1}<small>
	        <button name="page" value="{$SEARCH_RESPONSE->page+1}" > 
	            <img src="themes/Sugar/images/next.png" alt="Next" align="absmiddle" border="0"> 
	            </button>
	        {else}
	        <img src="themes/Sugar/images/next_off.png" alt="Next" align="absmiddle" border="0">
    {/if}
    
	
	</form>
	        </td>
	   </tr>
	{/if}
	<tr>    <td scope='col' width='1%' nowrap="nowrap"> </td>
		    <td scope='col' width='30%' nowrap="nowrap">
		    Client Name
		    </td>
			<td scope='col' width='20%' nowrap="nowrap">		
				Phone
			</td>
			<td scope='col' width='29%' nowrap="nowrap">		
				Classification
			</td>
		
	</tr>	
	{foreach from=$SEARCH_RESPONSE->SearchResult item="rowData" name="recordlist"}
	
	<tr>
	    <td width="5" class="{if $smarty.foreach.recordlist.index % 2 == 0}odd{else}even{/if}" >
	        <input type="checkbox" name="import[]" value='{$rowData->client_bb_id}' />
	    </td>
	    <td  class="{if $smarty.foreach.recordlist.index % 2 == 0}odd{else}even{/if}">{$rowData->name}</td>
	     <td  class="{if $smarty.foreach.recordlist.index % 2 == 0}odd{else}even{/if}">{$rowData->phone}</td>
	     <td  class="{if $smarty.foreach.recordlist.index % 2 == 0}odd{else}even{/if}">{$rowData->className}</td>
	</tr>
	{foreachelse}
	<tr><td colspan="4"><i>No Result.</i></td></tr>
	{/foreach}
	
	{if $SEARCH_RESPONSE->SearchResult|@count neq 0}	
	    <tr><td colspan="4"><input type="submit" value="Import Client" name="import_clients" /></td></tr>
	{/if}
    </table>
    </form>
	
	<div class="nav_sec" align="right">
	<form action="" method="post" >
	<input type="hidden" name="module" value="Accounts" />
	<input type="hidden" name="action" value="wirelessmasterlookup" />
	
	<input type="hidden" name="search_by" value="{$smarty.post.search_by}" />
	<input type="hidden" name="company" value="{$smarty.post.company}" />
	<input type="hidden" name="region_company" value="{$smarty.post.region_company}" />
	<input type="hidden" name="class_cat_id" value="{$smarty.post.class_cat_id}" />
	<input type="hidden" name="classification" value="{$smarty.post.classification}" />
	<input type="hidden" name="phone" value="{$smarty.post.phone}" />
	<input type="hidden" name="region" value="{$smarty.post.region}" />
	<input type="hidden" name="search_bluebook" value="1" />
	{if $SEARCH_RESPONSE->page > 1}<small>
	        <button name="page" value="{$SEARCH_RESPONSE->page-1}" > 
	        <img src="themes/Sugar/images/previous.png" alt="Previous" align="absmiddle" border="0">
	         </button>
	 {else}
	   <img src="themes/Sugar/images/previous_off.png" alt="Previous" align="absmiddle" border="0">
    {/if}
	{if $SEARCH_RESPONSE->nextpage eq 1}<small>
	        <button name="page" value="{$SEARCH_RESPONSE->page+1}" > 
	            <img src="themes/Sugar/images/next.png" alt="Next" align="absmiddle" border="0"> 
	            </button>
	        {else}
	        <img src="themes/Sugar/images/next_off.png" alt="Next" align="absmiddle" border="0">
    {/if}
    
	
	</form>
	</div>
<div class="sectitle">
    {sugar_translate label='LNK_SEARCH_BLUEBOOK_MENU' module='Accounts'} 
</div>

<form action="" method="post">
<input type="hidden" name="module" value="Accounts" />
<input type="hidden" name="action" value="wirelessmasterlookup" />

 <br/><small> Search By :</small> <br/>  {html_options  selected=$smarty.post.search_by name="search_by" options=$SEARCH_BY} 
            
            <input class="button" type='submit' value='Go' name='set_search_type'/>
  
{if $smarty.post.search_by eq 'company_name'}
    
    <br/> <small>Company Name :</small> <br/>  <input  size="20" maxlength="50" type="text" name="company" value="{$smarty.post.company}" />
    {if $AR_MESSAGES.company neq ''}<br/><span class='required'>{$AR_MESSAGES.company}</span>{/if}
    <br/> <small>Region : </small> <br/> {html_options name="region_company" options=$REGION_DOM selected=$smarty.post.region_company}
    {if $AR_MESSAGES.region_company neq ''}<br/><span class='required'>{$AR_MESSAGES.region_company}</span>{/if}
    
{elseif $smarty.post.search_by eq 'classification'}
    
    <br/> <small> {$MOD.LBL_CLASSIFICATION} :</small> <br/>  <input type="text" name="classification" value="{$smarty.post.classification}" />
    <input class="button" type="submit" name="searchclass" value="Search" />    
    <br/><small>{html_radios name='class_cat_id' options=$CLASS_LIST   selected=$smarty.post.class_cat_id separator='</small><br /><small>'}</small>
    
     {if $AR_MESSAGES.classification neq ''}<br/><span class='required'>{$AR_MESSAGES.classification}</span>{/if}
    <br/><small>Region :</small>  <br/> {html_options name="region" options=$REGION_DOM  selected=$smarty.post.region}
     {if $AR_MESSAGES.region neq ''}<br/><span class='required'>{$AR_MESSAGES.region}</span>{/if}
{elseif $smarty.post.search_by eq 'phone'}    
    <br/> <small>Phone / Fax :</small>       <input size="20" maxlength="50" type="text" name="phone" value="{$smarty.post.phone}" />
     {if $AR_MESSAGES.phone neq ''}<br/><span class='required'>{$AR_MESSAGES.phone}</span>{/if}
{/if}
{if $smarty.post.search_by neq ''}
<br/>
<input class="button" type="submit" name="search_bluebook" value="{sugar_translate label='LNK_SEARCH_BLUEBOOK_MENU' module='Accounts'} " />
{/if}
</form>
	
