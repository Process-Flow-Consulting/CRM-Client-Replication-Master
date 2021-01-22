<div id='bidder-list' >
{literal}
<style type="text/css">
.role_div {	
}
.role_div li {
	list-style: none;
}
</style>
{/literal}
{assign var=url value='index.php?'}
{foreach from=$smarty.get item=values key=params}
{assign var=url value=$url|cat:$params|cat:"="|cat:$values|cat:"&"}

{/foreach}

{assign var=url value=$url|cat:"&odr="|cat:$order}

<div  style="/*overflow-x:hidden;overflow-y:auto;height:400px*/">
<div class="listViewBody" style="padding-top:5px;padding-right:5px">
    <div class="search_form">    
        <div id="Leadsbasic_searchSearchForm" style="" class="edit view search basic">
        		{if $filter_apply eq true}
	            <strong>{$MOD.LBL_FILTER_MSG}</strong>	            
	            {else}
	            <strong>&nbsp;</strong>
	            {/if}
	            
	            <div style="display:none;text-align:center;foat:left;position:absolute;padding:2px;margin-top:-12px" id='loader_image'>	
	            <img align="absmiddle" src='{sugar_getimagepath file=sqsWait.gif }' /> Loading ...
               </div>
        </div>    
    </div>
      <table border="0" width="100%" class="view list" cellspacing="0" cellpadding="0"  align="center">      	
		{include file=custom/modules/Leads/tpls/bidders_data_paginate.tpl }				
		<tr style="background-color:#929798;color:white;">
		<td scope="col" width="1%">New<br>Bidders</td>
		<td scope="col" width="1%"></td>
		<td scope="col" width="24%">
				<a class="listViewThLinkS1" href="javascript:void(0)" onclick="getURLdata('{$url}&sort=account_name&odr={$order}')" > Client </a>
			   {if $smarty.request.sort eq 'account_name' and $smarty.request.odr eq 'ASC' }
                    {assign var=imageName value='arrow_up.gif'}
               {elseif $smarty.request.sort eq 'account_name' and $smarty.request.odr eq 'DESC' }
                   {assign var=imageName value='arrow_down.gif'}
               {else}
                    {assign var=imageName value='arrow.gif'}
               {/if}
                <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
		</td>
		<td scope="col" width="17%">Client Contact</td>
		<td scope="col" width="13%">Phone</td>
		<td scope="col" width="10%">City / State </td>
		<td scope="col" width="24%">
		Classification 
		{*
				<a  class="listViewThLinkS1" href="javascript:void(0)" onclick="getURLdata('{$url}&sort=classifications&odr={$order}')"  >Classification</a>
				{if $smarty.request.sort eq 'classifications' and $smarty.request.odr eq 'ASC' }
                    {assign var=imageName value='arrow_up.gif'}
               {elseif $smarty.request.sort eq 'classifications' and $smarty.request.odr eq 'DESC' }
                   {assign var=imageName value='arrow_down.gif'}
               {else}
                    {assign var=imageName value='arrow.gif'}
               {/if}
               <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
               *}
		</td>
		<td scope="col" width="10%">
		
			<a class="listViewThLinkS1" href="javascript:void(0)" onclick="getURLdata('{$url}&sort=role&odr={$order}')"  >Role</a>
			{if $smarty.request.sort eq 'role' and $smarty.request.odr eq 'ASC' }
                    {assign var=imageName value='arrow_up.gif'}
               {elseif $smarty.request.sort eq 'role' and $smarty.request.odr eq 'DESC' }
                   {assign var=imageName value='arrow_down.gif'}
               {else}
                    {assign var=imageName value='arrow.gif'}
               {/if}
                <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
		</td>
		<td scope="col" width="10%">{$MOD.LBL_BID_STATUS}</td>
		</tr>
		
		{foreach from=$ALL_BIDDERS.list item=bidder name=bidder}
		<tr class="{if $smarty.foreach.bidder.index%2 eq 0}evenListRowS1{else}oddListRowS1{/if}">			
			
			<td  scope="row">
			{assign var=temp value=$bidder->id} 
			{if $NEW_BIDDER.$temp neq '1'}	
				<span style="color:#00CC00; font-family: Helvetica, sans-serif;"><i>New!!!</i></span>
			{/if}
			</td>
			
			<td  scope="row" style="pointer-events: none;cursor: default;">{assign var=temp value=$bidder->id} 
			{$FAV.$temp neq '1'}</td>
			
			<td  scope="row">
				{if $bidder->converted_to_oppr eq '1'}
				<img align="absmiddle" src="{sugar_getimagepath file=green_money.gif }" title="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}"  alt="{$APP.LBL_PREVIOUS_BID_TO_IMG_ALT_TEXT}" /> 
				{/if}			
				
				{$bidder->account_proview_url}				
				{if $bidder->account_visibility eq '0'}
					{if $bidder->proview_url neq '' }					
						<a target="_blank" href="{$bidder->proview_url|to_url}">
					{else}
						{* no proview no link *}
						<a  class="no_proview" href="javascript:void(0)">
					{/if }					
				{else}
					<a target="_blank" href="index.php?module=Accounts&action=DetailView&record={$bidder->account_id}">
				{/if}
				{$bidder->account_name}
				</a>
			</td>
			<td   scope="row"  >
				{if $bidder->contact_visibility eq '0'}
					{if $bidder->proview_url neq '' }					
						<a target="_blank" href="{$bidder->proview_url|to_url}">
					{else}
						{* no proview no link *}
						<a class="no_proview" href="javascript:void(0)">
					{/if }	
				{else}
					<a target="_blank" href="index.php?module=Contacts&action=DetailView&record={$bidder->contact_id}">
				{/if}
				{$bidder->contact_name}
				</a>
			</td>
			<td scope="row">
				{assign var=phone_number value=$bidder->contact_phone_no}
				{sugar_phone_number_format_edit value=$phone_number }
			</td>
			<td scope="row">{$bidder->city_state}</td>
			<td scope="row">				
				{assign var="classification_arr" value=","|explode:$bidder->classifications}
				{if $classification_arr|@count gt 1}
				<a id="displayText_{$bidder->id}"
					href="javascript:toggle_role_div('{$bidder->id}',this);">&nbsp;<strong>+</strong>&nbsp;
				</a>{/if}{$classification_arr[0]}
				<div class="role_div" id="role_div_{$bidder->id}" style="display:none;padding-left:10px;">
				<ul style="padding:0px;margin:0px">	
				{foreach from=$classification_arr item=csf name=csfArr}
					{if $smarty.foreach.csfArr.index neq 0}
					<li>{$csf}</li>
					{/if}
				{/foreach}
				</ul>
				</div>				
			</td>
			<td scope="row">
			{$bidder->role}
			</td>
			<td scope="row">
			{$bidder->bid_status}
			</td>
		</tr>
		{foreachelse}
		<tr>
			<td scope="row">
			No Data
			</td>
		</tr>
		{/foreach}
		{include file=custom/modules/Leads/tpls/bidders_data_paginate_bottom.tpl }
		
		</table>
            
</div>
 </div>
 {literal}
<script type="text/javascript" >
function toggle_role_div(bid, linkPlusObj) {
	/*//document.getElementById('urlt'+record)list.innerHTML = '';
	//$('#urlt'+record).html('');	
    var ele = document.getElementById("role_div_"+bid);
    var text = document.getElementById("displayText_"+bid);
    if(ele.style.display == "block") {
           // ele.style.display = "none";
            text.innerHTML = "&nbsp;<strong>+</strong>&nbsp;";

    */
	$("a[id^=displayText_]").each(function(idx,elm){
		
    	if($(elm).attr('id') == 'displayText_'+bid)
         {
			if($(elm).html() == '&nbsp;<strong>-</strong>')
			{
				$(elm).html('&nbsp;<strong>+</strong>')
				
			}else{
				$(elm).html('&nbsp;<strong>-</strong>')

			}       

         }    	
}
				
			);
	
	$("div.role_div").each(function(idx,elm){
		
    	if($(elm).attr('id') == 'role_div_'+bid)
         {
			if($(elm).css('display') == 'none')
			{
				$(elm).css('display','block')
				
			}else{
				$(elm).css('display','none');

			}       

         }    	
});

    /*
    else{
          //ele.style.display = "block";
          text.innerHTML = "&nbsp;<strong>-</strong>&nbsp;";
          $("div.role_div").each(function(idx,elm){
            	if($(elm).attr('id') == 'role_div_'++record)
            	{$(elm).css('display','block')
           })
    }*/
}

function getURLdata(url){

	document.getElementById('loader_image').style.display = '';
	
	$('div.open #Leadsbasic_searchSearchForm').html(SUGAR.language.get('app_strings', 'LBL_LOADING'))
	ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
            var callback = {
                       cache:false,
                       success: function(o) {
                       res = o.responseText;
                       //document.getElementById('overDiv').innerHTML = res;
                      //cont = YAHOO.util.Selector.query("#overDiv div.olFontClass");
                       //cont[0].innerHTML =res;
                       $('div.open').html(res);
                       
                       ajaxStatus.hideStatus();
                      // mySimpleDialog2.setBody(o.responseText);
                       SUGAR.util.evalScript(o.responseText);
                   }
            }

          //  document.getElementById('overDiv').innerHTML = 'Loading data...';

            YAHOO.util.Connect.asyncRequest('GET', url, callback);

          }

 $("a.no_proview").each(function(){ $(this).attr("title", "No proview available."); });
</script>
{/literal}
</div>
