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
<span>
{if {{$vardef.name}} == 'sector_served_advanced'}
<!--<input type="hidden" name="sector_private_advanced" value="0"> -->
<input type="checkbox" id="sector_private" name="sector_private_advanced" value="1" title="{sugar_translate label='LBL_PRIVATE' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.sector_private_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_PRIVATE' module='Accounts'}
&nbsp;&nbsp;
<!--<input type="hidden" name="sector_public_advanced" value="0">--> 
<input type="checkbox" id="sector_public" name="sector_public_advanced" value="1" title="{sugar_translate label='LBL_PUBLIC' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.sector_public_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_PUBLIC' module='Accounts'}

{elseif {{$vardef.name}} == 'labor_affiliation_advanced'} 
<!--<input type="hidden" name="union_c_advanced" value="0"> -->
<input type="checkbox" id="union_c" name="union_c_advanced" value="1" title="{sugar_translate label='LBL_UNION' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.union_c_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_UNION' module='Accounts'}
&nbsp;&nbsp;
<!--<input type="hidden" name="non_union_advanced" value="0"> -->
<input type="checkbox" id="non_union" name="non_union_advanced" value="1" title="{sugar_translate label='LBL_NON_UNION' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.non_union_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_NON_UNION' module='Accounts'}
&nbsp;&nbsp;
<!--<input type="hidden" name="prevailing_wage_advanced" value="0"> -->
<input type="checkbox" id="prevailing_wage" name="prevailing_wage_advanced" value="1" title="{sugar_translate label='LBL_PREVAILING_WAGE' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.prevailing_wage_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_PREVAILING_WAGE' module='Accounts'}

{elseif {{$vardef.name}} == 'certification_advanced'}  
<!--<input type="hidden" name="bim_certified_advanced" value="0"> -->
<input type="checkbox" id="bim_certified" name="bim_certified_advanced" value="1" title="{sugar_translate label='LBL_BIM_CERTIFIED' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.bim_certified_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_BIM_CERTIFIED' module='Accounts'}
&nbsp;&nbsp;
<!--<input type="hidden" name="leed_certified_advanced" value="0"> -->
<input type="checkbox" id="leed_certified" name="leed_certified_advanced" value="1" title="{sugar_translate label='LBL_LEED_CERTIFIED' module='Accounts'}" tabindex="{{$tabindex}}" {if $smarty.request.leed_certified_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_LEED_CERTIFIED' module='Accounts'}

{elseif {{$vardef.name}} == 'labor_affiliation_lead_advanced'} 
<!--<input type="hidden" name="lead_union_c_advanced" value="0"> -->
<input type="checkbox" id="lead_union_c" name="lead_union_c_advanced" value="1" title="{sugar_translate label='LBL_LEAD_UNION_C' module='Opportunities'}" tabindex="{{$tabindex}}" {if $smarty.request.lead_union_c_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_LEAD_UNION_C' module='Opportunities'}
&nbsp;&nbsp;
<!--<input type="hidden" name="lead_non_union_advanced" value="0"> -->
<input type="checkbox" id="lead_non_union" name="lead_non_union_advanced" value="1" title="{sugar_translate label='LBL_LEAD_NON_UNION' module='Opportunities'}" tabindex="{{$tabindex}}" {if $smarty.request.lead_non_union_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_LEAD_NON_UNION' module='Opportunities'}
&nbsp;&nbsp;
<!--<input type="hidden" name="lead_prevailing_wage_advanced" value="0"> -->
<input type="checkbox" id="lead_prevailing_wage" name="lead_prevailing_wage_advanced" value="1" title="{sugar_translate label='LBL_LEAD_PREVAILING_WAGE' module='Opportunities'}" tabindex="{{$tabindex}}" {if $smarty.request.lead_prevailing_wage_advanced ==1}checked="checked" {/if}>&nbsp;{sugar_translate label='LBL_LEAD_PREVAILING_WAGE' module='Opportunities'}

{/if}
</span>