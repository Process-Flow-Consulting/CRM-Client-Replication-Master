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
<div class="moduleTitle">
<h2>{$MOD.LBL_SEARCH_MERGE_CLIENT_ACTION_MENU}</h2>
</div>
<fieldset style="-webkit-border-radius: 6px;border: 1px solid #CCC;">
    <legend style="padding-left:10%;width:30%;">{$MOD.LBL_CLIENT_SEARCH_MERGE}</legend>
    <div class="listViewBody">
        <form name="search_form" id="search_form" class="search_form" method="post" action="index.php?module=Accounts&action=clientmerge" style="margin-bottom:0px;">
           <input type="hidden" value="{$smarty.request.record}" name="record" />
           <input type="hidden" name="selected_ids" value="{$AR_CLIENT_TO_MERGE_IDS}" />
           <input type="hidden" value="{$primary_client}}" name="primary_client" />
            <div id="Accountsbasic_searchSearchForm" style="" class="edit view search basic">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                        <tr>
                            <td scope="row" nowrap="nowrap" width="1%">
                                <label for="name_basic">{$MOD.LBL_NAME}</label>
                            </td>
                            <td nowrap="nowrap" width="1%">
                                <input type="text" name="name_basic" id="name_basic" size="30" maxlength="255" value="{$smarty.request.name_basic}" title="" accesskey="9">
                            </td>
                            <td class="sumbitButtons">
                                <input tabindex="2" title="Search" class="button" type="submit" name="button" value="Search" id="search_form_submit">
                                <input tabindex="2" title="Clear" onclick="SUGAR.searchForm.clear_form(this.form); return false;" class="button" type="button" name="clear" id="search_form_clear" value="Clear">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
         </form>
        <form action="" method="post" id="select_merge" name="select_merge" > 
       		<input type="hidden" value="{$primary_client}" name="record" />
            <input type="hidden" name="name_basic" value="{$smarty.request.name_basic}" />
            <input type="hidden" name="selected_ids" value="{$AR_CLIENT_TO_MERGE_IDS}" />
            <input type="hidden" value="{$smarty.request.primary_client}" name="primary_client" />
            <input type="hidden" value="{$ORDER}" name="order" id="order" />
            <input type="hidden" value="{$ORDER_BY}" name="order_by" id="order_by" />
            <table cellpadding="0" cellspacing="0" width="100%" border="0" class="list view">
                <tbody>
                    <tr class="pagination" role="presentation">
                        <td colspan="6" align="left">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                    <tr>
                                        <td align="left">{literal}                                            
                                            <input title="Select all" type="checkbox" class="checkbox massall" name="massall" id="massall_top" value="" onclick="sListView.check_all(document.MassUpdate, 'mass[]', this.checked);">
                                            <input type="button" id="select_to_merge" name="select_to_merge" value="{/literal}{$MOD.LBL_MERGE_MARK_SELECT}" onclick="select_for_merge()" /></td>
                                        <td nowrap="" colspan="20" align="right">                                        
							                    {if $pageData.urls.startPage}
							                            <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start}' class='button' {if $prerow}onclick='return sListView.save_checks(0, "{$moduleString}");'{else} onClick='getURLdata("{$pageData.urls.startPage}")' {/if}>
							                                    <img src='{sugar_getimagepath file='start.png'}' alt='{$navStrings.start}' align='absmiddle' border='0'>
							                            </button>
							                    {else}
							                            <button type='button' id='listViewStartButton' name='listViewStartButton' title='{$navStrings.start}' class='button' disabled='disabled'>
							                                    <img src='{sugar_getimagepath file='start_off.png'}' alt='{$navStrings.start}' align='absmiddle' border='0'>
							                            </button>
							                    {/if}
							                    {if $pageData.urls.prevPage}
							                            <button type='button' id='listViewPrevButton' name='listViewPrevButton' title='{$navStrings.previous}' class='button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.prev}, "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.prevPage}")'{/if}>
							                                    <img src='{sugar_getimagepath file='previous.png'}' alt='{$navStrings.previous}' align='absmiddle' border='0'>							
							                            </button>
							                    {else}
							                            <button type='button' id='listViewPrevButton' name='listViewPrevButton' class='button' title='{$navStrings.previous}' disabled='disabled'>
							                                    <img src='{sugar_getimagepath file='previous_off.png'}' alt='{$navStrings.previous}' align='absmiddle' border='0'>
							                            </button>
							                    {/if}
							                            <span class='pageNumbers'>({if $pageData.offsets.lastOffsetOnPage == 0}0{else}{$pageData.offsets.current+1}{/if} - {$pageData.offsets.lastOffsetOnPage} {$navStrings.of} {if $pageData.offsets.totalCounted}{$pageData.offsets.total}{else}{$pageData.offsets.total}{if $pageData.offsets.lastOffsetOnPage != $pageData.offsets.total}+{/if}{/if})</span>
							                    {if $pageData.urls.nextPage}
							                            <button type='button' id='listViewNextButton' name='listViewNextButton' title='{$navStrings.next}' class='button' {if $prerow}onclick='return sListView.save_checks({$pageData.offsets.next}, "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.nextPage}")'{/if}>
							                                    <img src='{sugar_getimagepath file='next.png'}' alt='{$navStrings.next}' align='absmiddle' border='0'>
							                            </button>
							                    {else}
							                            <button type='button' id='listViewNextButton' name='listViewNextButton' class='button' title='{$navStrings.next}' disabled='disabled'>
							                                    <img src='{sugar_getimagepath file='next_off.png'}' alt='{$navStrings.next}' align='absmiddle' border='0'>
							                            </button>
							                    {/if}
							                    {if $pageData.urls.endPage  && $pageData.offsets.total != $pageData.offsets.lastOffsetOnPage}
							                            <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end}' class='button' {if $prerow}onclick='return sListView.save_checks("end", "{$moduleString}")' {else} onClick='getURLdata("{$pageData.urls.endPage}")'{/if}>
							                                    <img src='{sugar_getimagepath file='end.png'}' alt='{$navStrings.end}' align='absmiddle' border='0'>							
							                            </button>
							                    {elseif !$pageData.offsets.totalCounted || $pageData.offsets.total == $pageData.offsets.lastOffsetOnPage}
							                            <button type='button' id='listViewEndButton' name='listViewEndButton' title='{$navStrings.end}' class='button' disabled='disabled'>
							                                    <img src='{sugar_getimagepath file='end_off.png'}' alt='{$navStrings.end}' align='absmiddle'>
							                            </button>
							                    {/if}
							            </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr height="20" style="background-color: #e6e6e6;">
                        <th scope="col" width="5%">
                        	<span sugar="slot0" style="white-space:normal;"></span>
                        </th>
                        <th scope="col" width="25%">	
                        	<span sugar="slot1" style="white-space:normal;">
	                            <a class="listViewThLinkS1" href="javascript:sort('name','{$ORDER_NEXT}')">
									{$MOD.LBL_NAME} 
									
									{if $ORDER_BY eq 'name' and $ORDER eq 'ASC'}
										{assign var='img_name' value='arrow_up'}
									{elseif $ORDER_BY eq 'name' and $ORDER eq 'DESC'}
										{assign var='img_name' value='arrow_down'}	
									{else}
									{assign var='img_name' value='arrow'}
									{/if}
									{sugar_image name=$img_name height="8" width="10"  border="0" align="absmiddle" alt="Sort"}	
								</a>
							</span>
                        </th>
                        <th scope="col" width="15%">	
                        	<span sugar="slot2" style="white-space:normal;">
                        		{$MOD.LBL_PHONE_OFFICE}
                        	</span>
                        </th>
                        <th scope="col" width="15%">	
	                        <span sugar="slot3" style="white-space:normal;">
								<a class="listViewThLinkS1" href="javascript:sort('billing_address_city','{$ORDER_NEXT}')" >
									{$MOD.LBL_BILLING_ADDRESS_CITY}
									
									{if $ORDER_BY eq 'billing_address_city' and $ORDER eq 'ASC'}
										{assign var='img_name' value='arrow_up'}
									{elseif $ORDER_BY eq 'billing_address_city' and $ORDER eq 'DESC'}
										{assign var='img_name' value='arrow_down'}	
									{else}
									{assign var='img_name' value='arrow'}
									{/if}
									{sugar_image name=$img_name height="8" width="10"  border="0" align="absmiddle" alt="Sort"}	
									
								</a>
							</span>
                        </th>
                        <th scope="col" width="15%">	
                        	<span sugar="slot2" style="white-space:normal;">
                        		{$MOD.LBL_BILLING_ADDRESS_STATE}
                        	</span>
                        </th>
                        <th scope="col" width="25%">	
                        	<span sugar="slot2" style="white-space:normal;">
								{$MOD.LBL_DATE_ENTERED|replace:':':''}
							</span>
                        </th>
                    </tr>
                    
                    {foreach from=$AR_CLIENT_LIST item=DATA name='clients'} 
                    {if $smarty.foreach.clients.index%2 eq 0}
                     {assign var=stClass value='oddListRowS1'} 
                     {else} 
                     {assign var=stClass value='evenListRowS1'} 
                     {/if}
                    <tr height="20" class="{$stClass}">
                        <td width="5%">
                            <input type="checkbox" class="mass" name="mass[]" value="{$DATA->id}" />
                        </td>
                        <td width="25%"> {$DATA->proview_url} <a href="index.php?module=Accounts&action=DetailView&record={$DATA->id}"><b>{$DATA->name}</b></a> 
                        </td>
                        <td width="10%">{sugar_phone_number_format value=$DATA->phone_office}</td>
                        <td width="15%">{$DATA->billing_address_city}</td>
                        <td>{assign var=bil_state value=$DATA->billing_address_state}{$APP_STATE_DOMS.$bil_state}</td>
                        <td>{$DATA->date_entered}</td>
                    </tr>{foreachelse}
                    <tr height="20" class="oddListRowS1">
                        <td colspan="5"> <em>{$APP.LBL_NO_DATA}</em>

                        </td>
                    </tr>{/foreach}</tbody>
            </table>
    </div>
    </form>
</fieldset>
<br clear="all"/>
<fieldset style="-webkit-border-radius: 6px;border: 1px solid #CCC;">
    <legend style="padding-left:10%;width:30%;">{$MOD.LBL_MERGE_CLIENTS}</legend>
    <div class="">
        <form action="" method="post" name="merge_clients" id="merge_clients">
        <input type="hidden" value="clientmerge" name="action">
        <input type="hidden" value="Accounts" name="module">
        <input type="hidden" value="false" name="massupdate">
        <input type="hidden" value="Accounts" name="merge_module">  
        <input type="hidden" value="false" name="delete">  
        <input type="hidden" value="Accounts" name="return_module">  
        <input type="hidden" value="DetailView" name="return_action">  
            
        <input type="hidden" value="" name="remove_merge_client" />        
         <input type="hidden" value="{$primary_client}" name="record" />
         <input type="hidden" value="{$primary_client}" name="return_id" />
         
         <input type="hidden" name="selected_ids" value="{$AR_CLIENT_TO_MERGE_IDS}" />
         <input type="button" class="primary button" value="{$MOD.LBL_MERGE_CLIENTS}" onclick="post_merge_records(this);return false;" />
         <input type="submit" class="button" name="clear_merge" value="Clear" />
            <table id="merge_client_tbl" cellpadding="0" cellspacing="0" width="100%" border="0" class="list view">
                <tbody>
                    <tr class="pagination" role="presentation">
                        <td colspan="6" align="left"></td>
                    </tr>
                    <tr height="20" style="background-color: #e6e6e6;">
                        <th scope="col" width="2%">	
                        <span sugar="slot0" style="white-space:normal;">
                        </span>
                        </th>
                        <th scope="col" width="22%">	
                        	<span sugar="slot1" style="white-space:normal;">                        		
                        			{$MOD.LBL_NAME} 								
							</span>
                        </th>
                        <th scope="col" width="15%">	
                        	<span sugar="slot2" style="white-space:normal;">
                        		{$MOD.LBL_PHONE_OFFICE}
                        	</span>
                        </th>
                        <th scope="col" width="15%">	
                        	<span sugar="slot3" style="white-space:normal;">                        		
                        			{$MOD.LBL_BILLING_ADDRESS_CITY}                        			
                        	</span>
                        </th>
                        <th scope="col" width="10%">	
                        	<span sugar="slot2" style="white-space:normal;">
                        		{$MOD.LBL_BILLING_ADDRESS_STATE}
                        	</span>
                        </th>                        
                        <th scope="col" width="15%">	
                        	<span sugar="slot2" style="white-space:normal;">
                        		{$MOD.LBL_DATE_ENTERED|replace:':':''}
                        	</span>
                        </th>
                        <th scope="col" width="10%"></th>
                        <th scope="col" width="5%"></th>
                    </tr>
                    {foreach from=$AR_CLIENT_TO_MERGE_LIST.list	 item=DATA name='clients'} 
                    {if $smarty.foreach.clients.index%2 eq 0} 
                    	{assign var=stClass value='oddListRowS1'} 
                    {else} 
                    	{assign var=stClass value='evenListRowS1'} 
                    {/if}
                    <tr height="20" class="{$stClass}">
                        <td scope="row"> {if $DATA->id neq $primary_client}<input id='merge_{$DATA->id}' type='hidden' name="mass[]" value="{$DATA->id}" />{/if}</td>
                        <td scope="row">{$DATA->proview_url}<a href="index.php?module=Accounts&action=DetailView&record={$DATA->id}"><b>{$DATA->name}</b></a> 
                        </td>
                        <td scope="row">{sugar_phone_number_format value=$DATA->phone_office}</td>
                        <td scope="row" >{$DATA->billing_address_city}</td>
                        <td scope="row">{$DATA->billing_address_state}{assign var=bil_state_merge value=$DATA->billing_address_state}{$APP_STATE_DOMS.$bil_state_merge}</td>
                        <td scope="row">{$DATA->date_entered}</td>
                        <td scope="row">
                              {if $DATA->id eq $primary_client}
                              {sugar_image name=yes-icon width=15 height=15 alt="Primary Client" align="absmiddle" }
                              {else}
                        	   <a  href="javascript:mark_primary('{$DATA->id}')"  >{$MOD.LBL_MERGE_MARK_PRIMARY}</a>
                        	   {/if}
                        </td>
                        <td scope="row">
                        	<a href="javascript:remove_from_merge('{$DATA->id}')" >
                        		{sugar_image name=no-icon width=10 height=10 alt="Remove from Merge" }
                        	</a>
                        </td>
                    </tr>{foreachelse}
                    <tr height="20" class="oddListRowS1">
                        <td colspan="5"> 
                        	<em>{$APP.LBL_NO_DATA}</em>
                        </td>                        
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            <input type="button" class="primary button" value="{$MOD.LBL_MERGE_CLIENTS}"  onclick="post_merge_records(this);return false;" />
			<input type="submit" class="button" name="clear_merge" value="Clear" />
    </div>
    </form>
</fieldset>{literal}
<script>
    $('#massall_top').on('click', function() {
        val = ($(this).attr('checked')) ? true : false;
        $('.mass').attr('checked', val);
    })
    function getURLdata(url){
    $('#select_merge').attr('action',url);
    $('#select_merge').submit();
    }
    function mark_primary(id){    
    	$('input[name=record]').val($('#merge_'+id).val());
    	$('#merge_clients').submit();
    }
    function remove_from_merge(id){  
        btnCfg = {Ok:function() {
        				$('input[name=remove_merge_client]').val(id);
    					$('#merge_clients').submit();         			
        			},
        		   'Cancel' : function(){
        		   	$( this ).dialog( "close" )
        		   }	        			
        		 };
    	showPopup(SUGAR.language.get('Accounts','LBL_CONFIRM_DELETE_MERGE'),'Warning!!','40%',btnCfg)  
    	
    }
    function sort(order_by,order){
        $('#order_by').val(order_by);
        $('#order').val(order);
    	$('#select_merge').submit();
    }
    function post_merge_records(objFrm){
         
        if($('#merge_client_tbl tr').length <4){
        	btnCfg = {Ok:function() {
        				      $( this ).dialog( "close" )   			
        			}};
        	showPopup(SUGAR.language.get('Accounts','LBL_ERROR_TWO_CLIENTS'),'Warning!!','40%',btnCfg);
        	return false;
        	
        } 
        if($('#merge_clients input[name=record]').val() == ''){
        	btnCfg = {Ok:function() {
        				    	 $( this ).dialog( "close" )		
        			}};
        	showPopup(SUGAR.language.get('Accounts','LBL_ERROR_PRIMARY_CLIENT'),'Warning!!','40%',btnCfg);
        	return false;
        	
        }   
    
    	document.forms.merge_clients.module.value='MergeRecords';
    	document.forms.merge_clients.action.value='Step3';
    	document.forms.merge_clients.massupdate.value='true';
    	document.forms.merge_clients.submit();
    	
    	
    	
    }
 
    function select_for_merge(){
    	if($('.mass:checked').length ==0){
    	 showPopup(SUGAR.language.get('Accounts','LBL_SELECT_ONE_RECORD'),'Error!!','40%',{Ok:function() { $( this ).dialog( "close" )}})
    	 return false;
    	}else{
    		$('#select_to_merge').prop('type','submit');
    		$('#select_to_merge').click()
    		return true; 
    	}
    	
    }
function showPopup(txt,TitleText,width,btnCfg){             
              TitleText=	decodeURIComponent(TitleText).replace(/\+/g, ' ');              
              oReturn = function(body, caption, width, theme) {
                                        $(".ui-dialog").find(".open").dialog("close");
                                        var bidDialog = $('<div class="open"></div>')
                                        .html(body)
                                        .dialog({
              									
                                                autoOpen: false,
                                                title: caption,
                                                width: width, 
                                               /* show: "slide",
                                                hide: "scale",*/            									
              									buttons : btnCfg
              									                                               
                                        });
                                        bidDialog.dialog('open');

                                };       
			oReturn(txt,TitleText, width, '');                                                
return;        
}
function closeDialog(){

}
</script>
<style>
fieldset{
width:96%;
padding:2%;
}
legend{
font-weight:bold;
font-size:15px;
}
</style>
{/literal}