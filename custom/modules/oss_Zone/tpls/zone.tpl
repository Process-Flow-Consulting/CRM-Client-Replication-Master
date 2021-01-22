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

<div id="main">
    <div id="content">
        <table style="width:99%;height:600px;" align="center"><tr><td align="center">

<form id="UserWizard" name="UserWizard" enctype='multipart/form-data' method="POST" action="index.php?module=oss_Zone&action=Save" onkeypress="return disableReturnSubmission(event);">
<input type='hidden' name='action' value='Save'/>
<input type='hidden' name='record' value='{$RECORD_ID}'/>
<input type='hidden' name='module' value='oss_Zone'/>
<span class='error'>{$error.main}</span>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='modules/Emails/javascript/vars.js'}"></script>

<div class="action_buttons" style="float:left;">
	<input type="submit" id="SAVE_FOOTER" value="Save" name="button" onclick="return checkCustomVAlidation();" class="button primary" accesskey="a" title="Save">  
	<input type="button" id="CANCEL_FOOTER" value="Cancel" name="button" onclick="SUGAR.ajaxUI.loadContent('index.php?action={$RETURN_ACTION}&amp;module=oss_Zone&amp;record={$CANCEL_RECORD_ID}'); return false;" class="button" accesskey="l" title="Cancel [Alt+Shift+l]">  
	<div class="clear"></div>
</div>
<div style="height:10px;"></div>
<div class="dashletPanelMenu wizard">

<div class="bd">				
<div id="finish" class="screen">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
     <tr>
        <td>
            <div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <!--Row for Zone Name Start-->
                	<tr>
                    	<td width="10%">&nbsp;</td>
                    	<td  width="40%">&nbsp;</td>
                    	<td  width="5%" >&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    </tr>
                    <tr id="zone_name_text">
                        <td class="centerMiddle"scope="row" nowrap="nowrap">
                    <slot>
                    <label>{$MOD.LBL_ZONE_NAME}:</label>
                    </slot>
                    </td>
                    <td class="centerMiddle" colspan="3">
                    <slot>
                        <input type="text" accesskey="7" title="" value="{$ZONE_NAME_VALUE}" maxlength="255" size="30" id="name" name="name" onblur="return checkZoneName();">
                        <br/><div id="zone_msg" style="display:none;">
                    </slot>                    
                    </tr>    
                <!--Row for Zone Name End-->
                
                <!--Row for City Start-->   
                	<tr>
                        <td class="centerMiddle" colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                    	<td width="10%">&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    	<td  width="5%" >&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    </tr>   
                	<tr  id="city_row_filter">
                    <td class="centerMiddle" scope="row" nowrap=" Citynowrap">
                    <slot>
                    	<label><input type="radio" name="geo_filter_for"  {if $GEO_FILTER_FOR eq 'city'} checked  {/if} value="city" />            
                    	{$MOD.LBL_CITY_NAME}:
                    	</label>						
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <input id="user_city_val" type="text" name="city_name"  {if $GEO_FILTER_FOR neq 'city'} disabled  {/if} />
                    </slot>
                    </td>
                	<td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot> 
                       <input type=button value=">>" onclick="javascript:swapSelected('user_city_val','city_name')" {if $GEO_FILTER_FOR neq 'city'} disabled  {/if}   /><br/>
                       <input type=button value="<<" onclick="javascript:swapSelected('city_name','user_city_val')" {if $GEO_FILTER_FOR neq 'city'} disabled  {/if}  />
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <select id="city_name" name="city_name[]" multiple="true" {if $GEO_FILTER_FOR neq 'city'} disabled  {/if}  >
						{html_options options=$CITY_OPTOIONS}
                    </select>
                    </td>
                    </tr>                
                <!--Row for City End-->
                
                <!--Row for State Start-->                                                                  	             	                
                    <tr>
                        <td class="centerMiddle" colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                    	<td width="10%">&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    	<td  width="5%" >&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    </tr>
                    <tr id="state_row_filter">
                        <td class="centerMiddle"scope="row" nowrap="nowrap">
                    <slot>
                    <label><input type="radio" name="geo_filter_for"  {if $GEO_FILTER_FOR eq 'state'} checked  {/if} value="state" />            
                    {$MOD.LBL_STATE_NAME}:
                    </label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                    <select  multiple="true"  tabindex='14' id="state_filters" name='state_filters'  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if}>
    					{html_options options=$DOM_STATE }
                    </select>
                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot>
                        <input type=button value=">>"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} onclick="javascript:swapSelected('state_filters','state_apply');/*manageCourties()*/"  /><br/>
                        <input type=button value="<<"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} onclick="javascript:swapSelected('state_apply','state_filters');/*manageCourties()*/">
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <select  multiple="true" id="state_apply" name="state_apply[]"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} >
							{html_options options=$STATE_OPTOIONS }
                        </select>
                    </slot>
                    </td>
                    </tr>
                <!--Row for State End-->    
                    
                 <!--Row for County Start-->    
                    <tr>
                        <td class="centerMiddle"colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                    	<td width="10%">&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    	<td  width="5%" >&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    </tr>
                    <tr  id="county_row_filter">                        
                        <td class="centerMiddle"scope="row" nowrap="nowrap">
                    <slot>
                       <label> 
                       <input type="radio" name="geo_filter_for" value="county" {if $GEO_FILTER_FOR eq 'county'} checked  {/if} />       
                       {$MOD.LBL_COUNTY_NAME}:
                       </label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                    	<div style="padding:5px 0">
                    	{php}
                    	
                    	$this->assign('ALL_STATE',$GLOBALS['app_list_strings']['state_dom']);
                    	{/php}
                       <select id="state_county" name="state_county" onchange="getCounty(this.value,'');" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} >
                           {html_options options=$ALL_STATE}
                       </select>
                       </div>
                       <div id="county_div">
                       <select class="county" id="county" multiple="true" name="state" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if}>
                        
                       </select>
                       </div>
                       <input type="hidden" id="symbol" value="">
                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                       <slot> 
                       <input id="count_swap_lft" type=button value=">>" onclick="javascript:swapSelected('county','county_filters')" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} /> <br/>
                       <input id="count_swap_rgt" type=button value="<<" onclick="javascript:swapSelected('county_filters','county')" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} />
                     </slot>
                    </td>
                    <td class="centerMiddle">
                    <select id="county_filters" multiple="true" name="county_filters[]"  multiple="true" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} >
						{html_options options=$COUNTY_OPTOIONS}
                    </select>
                    </td>
                    </tr>
                 <!--Row for County End-->
                 
                 <!--Row for Zip Code Start-->  
                    <tr>
                        <td class="centerMiddle"colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr  id="zip_row_filter">
                        <td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot>
						<label>	
						<input type="radio" name="geo_filter_for" value="zip" {if $GEO_FILTER_FOR eq 'zip'} checked  {/if}  /> 
						{$MOD.LBL_ZIP_CODE_NAME}:
						</label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <input id="user_zip_val" type="text" name="zip_filter"  {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if} />
                    </slot>
                    </td>
                	 <td class="centerMiddle" scope="row" nowrap="nowrap">
                        <slot> 
                       <input type=button value=">>" onclick="javascript:swapSelected('user_zip_val','zip_filters')" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}   /><br/>
                       <input type=button value="<<" onclick="javascript:swapSelected('zip_filters','user_zip_val')" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}  />
                     </slot>
                    </td>
                    <td class="centerMiddle">
                    <select id="zip_filters" name="zip_filters[]" multiple="true" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}  >
							{html_options options=$ZIP_OPTOIONS}
                    </select>
                    </td>
                    </tr>
                 <!--Row for Zip Code End-->                 
              </table>
           </div>   
         </td>
      </tr>                        
    </table>    
</div>
</div>

</div>
<div style="height:10px;"></div>
<div class="action_buttons" style="float:left;">
	<input type="submit" id="SAVE_FOOTER" value="Save" name="button" onclick="return checkCustomVAlidation();" class="button primary" accesskey="a" title="Save">  
	<input type="button" id="CANCEL_FOOTER" value="Cancel" name="button" onclick="SUGAR.ajaxUI.loadContent('index.php?action={$RETURN_ACTION}&amp;module=oss_Zone&amp;record={$CANCEL_RECORD_ID}'); return false;" class="button" accesskey="l" title="Cancel [Alt+Shift+l]">  
	<div class="clear"></div>
</div>
{literal}
<style>
select{
width:200px;
}
select[multiple]{
width:96%;height:150px
}
div.user_groups div.edit.view td.centerMiddle {
    text-align: center;
    vertical-align: middle;
    width: 4px;
} 
select[disabled], input[disabled]{
	background-color:#E8E8E8; 
}
.yui-ac-content{
	width:auto
}
.yui-ac-content li{
text-align:left
}
.error:empty{
	background-color:initial;
}
</style>
{/literal}
</form>