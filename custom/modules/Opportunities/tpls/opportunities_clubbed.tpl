<div class="moduleTitle" style="float:left; width:100%;">
<h2><a href="index.php?module=Opportunities&action=index">
        <span class="suitepicon suitepicon-module-opportunities" align="absmiddle" title="Opportunities" ></span></a><span class="pointer">&raquo; </span> 
              <a href="index.php?module=Opportunities&action=DetailView&record={$OB_OPP_DATA->id}"> {$OB_OPP_DATA->name} </a> <span class="pointer">&raquo; </span>{$MOD.LBL_PROJECT_OPPORTUNITY_SUMMARY} </h2>
        <span class="utils"></span>
 <div style="float: right;width: 134px;padding: 11px;" >
    {if $B_SHOWPLANS}
 	<b><a id="pln{$OB_OPP_DATA->project_lead_id}" style="text-decoration:none" href="javascript:void(0)"  onclick="javascript:open_urls(event,'index.php?module=Leads&action=projecturl&record={$OB_OPP_DATA->project_lead_id}&to_pdf=true&all=1','Online Plans - {$OB_OPP_DATA->project_lead_name|urlencode}')" >Online Plans</a></b>
 	{/if}
 <div id="urlpln{$OB_OPP_DATA->project_lead_id}"></div>
 </div>
</div>

<script type='text/javascript' src="{sugar_getjspath file='include/javascript/overlibmws.js'}"></script>
<script type='text/javascript' src="{sugar_getjspath file='custom/modules/Opportunities/oppComposeEmail.js'}"></script>
<!--<script type='text/javascript' src="{sugar_getjspath file='custom/modules/Opportunities/oppPDF.js'}"></script>-->
<!-- <div id='overDiv' style='position:absolute; visibility:hidden; z-index:1000;'></div>-->
<div class="listViewBody">


	<form name="search_form" id="search_form" class="search_form"
		method="post" action="">
		<div id="Opportunitiesbasic_searchSearchForm" style=""
			class="edit view search basic">
			<table border="0" cellpadding="0" cellspacing="0" width="40%">
				<tr>
					<td scope="row" width="1%" nowrap="nowrap">{$MOD.LBL_OPPORTUNITY_NAME}</td>
					<td width="1%" nowrap="nowrap"><input type="text"
						name="search_string" value="{$search_string}" /></td>
					<td scope="row" width="1%" nowrap="nowrap">
						{$APP.LBL_CURRENT_USER_FILTER}</td>
					<td width="1%" nowrap="nowrap"><input type="checkbox"
						name="my_items" value="{$smarty.request.my_items.value}"
						{if $cur_user_only eq 1} checked="checked" {/if} /></td>
					<td class="sumbitButtons" width="1%" nowrap="nowrap"><input
						tabindex="2" title="Search [Alt+Q]" accesskey="Q"
						onclick="SUGAR.savedViews.setChooser()" class="button"
						type="submit" name="button" value="Search" id="search_form_submit">
						<input tabindex="2" title="Clear [Alt+C]" accesskey="C"
						onclick="SUGAR.searchForm.clear_form(this.form); return false;"
						class="button" type="button" name="clear" id="search_form_clear"
						value="Clear">
						<input tabindex="2" title="Add client to Opportunity[Alt+A]" accesskey="A"
						onclick="createClientOpportunity('{$OB_OPP_DATA->id}');"
						class="button" type="button" name="add_client" id="add_client"
						value="{$MOD.LBL_ADD_CLIENT_TO_OPPORTUNITY}">
						<input tabindex="2" title="{$MOD.LNK_PULL_UPDATE_OPPORTUNITY}[Alt+U]" accesskey="U"
						onclick="udateProjectOpportunity('{$OB_OPP_DATA->id}');"
						class="button" type="button" name="updateProjectOpp" id="updateProjectOpp"
						value="{$MOD.LNK_PULL_UPDATE_OPPORTUNITY}"></td>
				</tr>
			</table>
		</div>
	</form>

	<form id="MassUpdate" onsubmit="return validate_delete_form('MassUpdate');" name="MassUpdate" method="post" action="index.php">
		<input type="hidden" name="module" value="Opportunities" />
		<input type="hidden" name="sub_op_count" value="{$SUB_OP_COUNT}" /> 
		<input type="hidden" name="action" value="DetailView" /> 
		<input type="hidden" name="opportunity_name" value="{$OB_OPP_DATA->name}" /> 
		<input type="hidden" name="status" value="" />
		<input type="hidden" name="record" value="{$OB_OPP_DATA->id}" />
		<textarea name="uid" style="display: none"></textarea>	

		<table cellpadding="0" cellspacing="0" width="100%" border="0"
			class="list view">
			<tbody>
				<tr class="pagination">
					<td colspan="15">
						<table border='0' cellpadding='0' cellspacing='0' width='100%' class='paginationTable'>
							<tr>
								<td nowrap="nowrap" width='2%' class='paginationActionButtons'>
									{*$actionsLink*}	
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height="20" style="background-color: #332e2e80;">
					<th scope="col" nowrap="nowrap" width="10%" class="selectCol" colspan="2">						
						<input type="checkbox" class="checkbox" name="massall"	id="massall" value=""	onclick="sListView.check_all(document.MassUpdate, 'mass[]', this.checked);">
						<input type="button" onclick="verifySelectedProposals(this)" value="Verify Proposals"/>
					</th>
					<th scope="col" width="25%" nowrap="nowrap">
						<div style="white-space: nowrap;" width="100%" align="left">
							<a href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&sort=client_name&record={$OB_OPP_DATA->id}&odr={$order}"
								class="listViewThLinkS1"> {$MOD.LBL_LIST_CLIENT} </a>&nbsp;&nbsp;
							{if $smarty.request.sort eq 'client_name' and $smarty.request.odr
							eq 'ASC' } {assign var=imageName value='arrow_up.gif'} {elseif
							$smarty.request.sort eq 'client_name' and $smarty.request.odr eq
							'DESC' } {assign var=imageName value='arrow_down.gif'} {else}
							{assign var=imageName value='arrow.gif'} {/if} <img border="0"
								src="{sugar_getimagepath file=$imageName}" width="8" height="10"
								align="absmiddle" alt="Sort">

						</div>
					</th>
					<th scope="col" width="25%" nowrap="nowrap">
						<div style="white-space: nowrap;" width="100%" align="left">
							<a href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&sort=opportunity_classification&record={$OB_OPP_DATA->id}&odr={$order}"
								class="listViewThLinkS1"> {$MOD.LBL_OPPORTUNITY_CLASSFICATION} </a>&nbsp;&nbsp;<img border="0"
								src="{sugar_getimagepath file=$imageName}" width="8" height="10"
								align="absmiddle" alt="Sort">

						</div>
					</th>
					<th scope="col" width="10%" nowrap="nowrap">
						<div style="white-space: nowrap;" width="100%" align="left">
							<a
								href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&sort=client_bid_status&record={$OB_OPP_DATA->id}&odr={$order}"
								class="listViewThLinkS1"> {$MOD.LBL_CLIENT_BID_STATUS} </a>&nbsp;&nbsp;
							{if $smarty.request.sort eq 'client_bid_status' and $smarty.request.odr
							eq 'ASC' } {assign var=imageName value='arrow_up.gif'} {elseif
							$smarty.request.sort eq 'client_bid_status' and $smarty.request.odr eq
							'DESC' } {assign var=imageName value='arrow_down.gif'} {else}
							{assign var=imageName value='arrow.gif'} {/if} <img border="0"
								src="{sugar_getimagepath file=$imageName}" width="8" height="10"
								align="absmiddle" alt="Sort">

						</div>
					</th>
					<th scope="col" width="10%" nowrap="nowrap">
						<div style="white-space: nowrap;" width="100%" align="left">
							<a
								href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&sort=sales_stage&record={$OB_OPP_DATA->id}&odr={$order}"
								class="listViewThLinkS1"> {$MOD.LBL_LIST_SALES_STAGE} </a>&nbsp;&nbsp;
							{if $smarty.request.sort eq 'sales_stage' and $smarty.request.odr
							eq 'ASC' } {assign var=imageName value='arrow_up.gif'} {elseif
							$smarty.request.sort eq 'sales_stage' and $smarty.request.odr eq
							'DESC' } {assign var=imageName value='arrow_down.gif'} {else}
							{assign var=imageName value='arrow.gif'} {/if} <img border="0"
								src="{sugar_getimagepath file=$imageName}" width="8" height="10"
								align="absmiddle" alt="Sort">

						</div>
					</th>
					<th scope="col" nowrap="nowrap" width="10%">{$MOD.LBL_PROPOSAL}</th>
					<th scope="col" nowrap="nowrap" width="15%">{$MOD.LBL_PROPOSAL_DELIVERY}</th>
					<th scope="col" nowrap="nowrap" width="10%" >{$MOD.LBL_PROPOSAL_VERIFIED}</th>
					<th scope="col" nowrap="nowrap" width="15%">
						<div style="white-space: nowrap;" width="100%" align="left">
						<a href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&sort=assigned_user_name&record={$OB_OPP_DATA->id}&odr={$order}"
								class="listViewThLinkS1"> {$MOD.LBL_LIST_ASSIGNED_TO_NAME} </a>&nbsp;&nbsp;
							{if $smarty.request.sort eq 'assigned_user_name' and $smarty.request.odr
							eq 'ASC' } {assign var=imageName value='arrow_up.gif'} {elseif
							$smarty.request.sort eq 'assigned_user_name' and $smarty.request.odr eq
							'DESC' } {assign var=imageName value='arrow_down.gif'} {else}
							{assign var=imageName value='arrow.gif'} {/if} <img border="0"
								src="{sugar_getimagepath file=$imageName}" width="8" height="10"
								align="absmiddle" alt="Sort">
						</div>
					</th>
					<th nowrap="nowrap" width='2%' class='paginationActionButtons'>
						{$clientOppActionsLink}	
					</th>
					
				</tr>

{*
	@author modified : Basudeba Rath.
	@date : 18/Oct/2012.
	@description : Disable the checkbox if the proposal delivery method: is manual.	
*}
				{foreach name='list' from=$AR_CHILDS item=data }
				<tr height="10"
					class="{if $smarty.foreach.list.index%2 eq 0}oddListRowS1{else}evenListRowS1{/if}">
					<td class="nowrap"> <input type="checkbox" class="checkbox"
						name="mass[]" value="{$data->fetched_row.qid}" /> 
						<input type="hidden" name="mass_opp[{$data->fetched_row.qid}]" value="{$data->id}" />
                    </td>
					<td class="nowrap">
						<a id="edit-{$data->id}" 
							class="quickEdit" data-list="true" data-module="Opportunities" 
							data-record="{$data->id}" 
							href="index.php?module=Opportunities&offset=3&stamp=1334234198087146800&return_module=Opportunities&&action=EditView&record={$data->id}" title="Edit">
							<span class="suitepicon suitepicon-action-edit"></span>
						</a>
					</td>
					<td class="nowrap">
						<div class="accName">
							{if $data->fetched_row.proview_url neq ''}
							{assign var=pLink value=$data->fetched_row.proview_url|to_url}
							{assign var=attrs value="onclick=\"window.open('"|cat:$pLink|cat:"','','width=600,height=500')\" "}
							{sugar_getlink url="javascript:void(0)" title="" attr=$attrs img_name="proview_icon.gif" img_attr='border="0" align="absmiddle"'}
							{/if}
							<a href="index.php?module=Opportunities&action=DetailView&viewDetail=1&record={$data->id}">
							{$data->fetched_row.client_name}
							</a>
						</div>
					</td>
					<td scope="row" align="left" valign="top" class="">
						{$data->fetched_row.classification_name}
                    </td>
					<td scope="row" align="left" valign="top" class="">
						{$data->client_bid_status}
                    </td>
					<td scope="row" align="left" valign="top" class="">
						{$data->sales_stage}
                    </td>
					<!-- <td class="nowrap"> </td> -->
					<td scope="row" align="left" valign="top" class="">
                        {if $data->fetched_row.prop_total neq ''}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">{$data->fetched_row.prop_total|number_format:2}</a>
						{else}
							<a href="index.php?module=AOS_Quotes&opportunity_id={$data->id}&opportunity={$data->name|urlencode}&action=EditView">Create</a>
						{/if}
                    </td>
					<td scope="row" align="left" valign="top" class="">                                            
						{if $data->fetched_row.dto neq '' &&  $data->fetched_row.dto > $data->fetched_row.adtd}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">Opened {if $data->fetched_row.pdm eq 'M'} - Manual {/if} <br> {$timedate->to_display_date_time($data->fetched_row.dto,true,false)} {$data->fetched_row.dtz} </a>
						{elseif $data->fetched_row.dtr neq '' &&  $data->fetched_row.dtr > $data->fetched_row.adtd}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">Received {if $data->fetched_row.pdm eq 'M'} - Manual {/if}<br>{$timedate->to_display_date_time($data->fetched_row.dtr,true,false)} {$data->fetched_row.dtz} </a>
						{elseif $data->fetched_row.dts neq '' &&  $data->fetched_row.dts > $data->fetched_row.adtd}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">Sent {if $data->fetched_row.pdm eq 'M'} - Manual {/if}<br>{$timedate->to_display_date_time($data->fetched_row.dts,true,false)} {$data->fetched_row.dtz} </a>
						{elseif $data->fetched_row.dtd neq ''}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">Scheduled {if $data->fetched_row.pdm eq 'M'} - Manual {/if}<br>{$timedate->to_display_date_time($data->fetched_row.dtd,true,false)} {$data->fetched_row.dtz} </a>
						{elseif $data->fetched_row.qid neq ''}
							<a href="index.php?module=AOS_Quotes&action=DetailView&record={$data->fetched_row.qid}&parent_module=Opportunities&parent_id={$data->id}">Schedule</a>
						{else}
							<a href="index.php?module=AOS_Quotes&opportunity_id={$data->id}&opportunity={$data->name|urlencode}&action=EditView">Schedule</a>
						{/if}
                    </td>
					<td scope="row" align="center" valign="top" class="">           
                    {if $data->fetched_row.pdm eq 'M'}
					 <img src="custom/themes/default/images/manuel.png" height="20" width="60" alt="yes" border="0">
                   	{elseif $data->fetched_row.proposal_verified eq '1'}
                   		<img src="custom/themes/default/images/yes-icon.png" alt="yes" border="0">
                   		<input type="hidden" name="{$data->fetched_row.qid}_status" id="{$data->fetched_row.qid}_status" value="v">
                   	{elseif ($data->fetched_row.proposal_verified eq '2') && ($data->fetched_row.verify_email_sent eq '1')}
	                   	<a href="javascript:void(0);" onClick="verifyProposal('{$data->fetched_row.qid}','{$data->id}')" id="{$data->id}" value="{$data->id}">
	                   	<img src="custom/themes/default/images/pending-icon.png" alt="pending" border="0">
	                   	</a>
	                   	
	                   	<input type="hidden" name="{$data->fetched_row.qid}_status" id="{$data->fetched_row.qid}_status" value="p">
					{else}
						{if ($data->fetched_row.qid neq '') && ($data->fetched_row.pdm neq 'M')}
							<a href="javascript:void(0);" onClick="verifyProposal('{$data->fetched_row.qid}','{$data->id}')">
						{/if}
                   		<img src="custom/themes/default/images/no-icon.png" alt="no" border="0">
                   		{if ($data->fetched_row.qid neq '') && ($data->fetched_row.pdm neq 'M')}
							</a>
						{/if}
						{if ($data->fetched_row.qid neq '')}
							<input type="hidden" name="{$data->fetched_row.qid}_status" id="{$data->fetched_row.qid}_status" value="u">
						{/if}
                   	{/if}
                   </td>
					<td scope="row" align="left" valign="top" class="">
                   	{$data->assigned_user_name}
					</td>
					<td  {sugar_email_checkbox  module_id=$data->contact_id  attr='class="noEmail" title="Missing Primary Email."'}>
						<input {sugar_email_checkbox  module_id=$data->contact_id } type="checkbox" value="{$data->id}" oppval="{$data->id}" name="mass_email[]" />
						
                    </td> 
				</tr>
				{foreachelse}
				<tr class="">
					<td colspan="9">{$APP.LBL_NO_DATA}</td>
				</tr>
				{/foreach}
				<tr class="oddListRowS1">
					<td colspan="11"></td>
				</tr>
			</tbody>
		</table>
		<!-- <div style="width: 100px; position: absolute; visibility: hidden; z-index: 1000; left: 525px; top: 0px; background-image: none;"
			id="overDiv"></div>-->
	</form>
	<form name="create_client_opportunity" id="create_client_opportunity" method="post" action="index.php">
		<input type="hidden" name="action" id="action" value="createclient">
		<input type="hidden" name="module" id="module" value="Opportunities">
		<input type="hidden" name="record" id="record" value="{$OB_OPP_DATA->id}">
		<input type="hidden" name="client_data" id="client_data">
		<input type="hidden" name="select_entire_list" id="select_entire_list">
	</form>
</div>
{literal}
<style type="text/css">
.bbIcon {
	float: left;
	width: 10px;
	background-color: #000077;
	height: 10px;
}

.bbBorderContainer {
	float: left;
}

.accName {
	float: left;
	padding: 0px 3px
}
.to_field{
border:0 !important;
outline-style:none;
}

.vT {
display: inline-block;
color: #222;
margin: 2px 5px;
max-width: 325px;
max-height: 17px;
overflow: hidden;
text-overflow: ellipsis;
direction: ltr;
cursor: pointer;
}
.vR {
font-size: 13px;
display: inline-block;
padding: 3px;
vertical-align: top;
padding-top: 2px;
}
.vN {
background-color: #f5f5f5;
border: 1px solid #d9d9d9;
cursor: default;
display: block;
height: 20px;
white-space: nowrap;
-webkit-border-radius: 3px;
border-radius: 3px;
}
.vM {
display: inline-block;
width: 14px;
height: 20px;
opacity: .6;
vertical-align: top;
cursor: pointer;
}
.noEmail{
background-color:grey;
cursor:pointer;
}
.optOut{
background-color:#E1E139;
cursor:pointer;
}
.invalidEmail{
background-color:#D34141;
cursor:pointer;
}
</style>
<script type="text/javascript">

function open_urls(event,URL,titleName){	
titleName = decodeURIComponent(titleName).replace(/\+/g, ' ');
target = event.target?event.target:event.srcElement;
plid = target.id;
cont = document.getElementById('url'+plid);
			
if(false && cont.innerHTML != '')
{
    showPopupOpp(cont.innerHTML,titleName,'50%','massUpdate');
    SUGAR.util.evalScript(cont.innerHTML);

}else{
ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
var sUrl = URL;
var callback = {
        success: function(o) {                    
            document.getElementById('url'+plid).innerHTML = o.responseText;                        
            document.getElementById('url'+plid).style.display='none';
			showPopupOpp(o.responseText,titleName ,'50%','massUpdate');
			ajaxStatus.hideStatus();
			SUGAR.util.evalScript(o.responseText);
        },
        failure: function(o) {
        
        }
    }

    var transaction = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback, null);
}
}


function showPopup(txt,TitleText){
overlib(
txt
,STICKY
,10
,WIDTH
,600
, CENTER
,CAPTION
,'<div style="float:left">'+TitleText+'</div>'
, CLOSETEXT
, '<div style=\'float: right\'><img border=0 style=\'margin-left:2px; margin-right: 2px;\' src=themes/Sugar/images/close.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=896855794></div>'
,CLOSETITLE
, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP')
, CLOSECLICK
,FGCLASS
, 'olFgClass'
, CGCLASS
, 'olCgClass'
, BGCLASS
, 'olBgClass'
, TEXTFONTCLASS
, 'olFontClass'
, CAPTIONFONTCLASS
, 'olCapFontClass');
}

function createClientOpportunity(record){
	location.href = "index.php?module=Opportunities&action=EditView&parent_id="+record;
}


function udateProjectOpportunity(projectOppId){
	var current_user_id = '{/literal}{$current_user_id}{literal}';
	var project_lead_id = '{/literal}{$project_lead_id}{literal}';
	initiatePulling(projectOppId, current_user_id, project_lead_id);
}

function initiatePulling(projectOppId, current_user_id, project_lead_id){
	if(projectOppId != '' && current_user_id != '' && project_lead_id != '') {
		jQuery.ajax({
	        type: "POST",
	        url: "cmdscripts/PullBBHOppCommand.php?to_pdf=true&process=getUpdateOpportunities",
	        data: {userId : current_user_id, projectNumber : project_lead_id, projectOppId : projectOppId},
	        dataType: "json",
	       	cache: false,
	       	async: false,
	       	beforeSend:function(){
	       		ajaxStatus.showStatus("Please wait while populating opportunities...");
	       	}, 
	        complete: function (o) {
	        	var res = o.responseText;
				var res = trim(res);			
				if(res=='success'){		
					//unlink status file and redirect to detail view					
					unlinkStatusFile(projectOppId);										
				}else if(res=='start'){
					ajaxStatus.showStatus('Please wait while populating opportunities...');
					setInterval(function(){checkProcessStatus(projectOppId)},5000);							
					return false;
				}else if(res=='running'){
					ajaxStatus.showStatus('Please wait while populating opportunities...');
					setInterval(function(){checkProcessStatus(projectOppId)},5000);							
					return false;    
				}else{
					ajaxStatus.showStatus(res);
					return false;    
				}		
	        }
    	});
	}	  		
}

function checkProcessStatus(projectOppId){
	var current_user_id = '{/literal}{$current_user_id}{literal}';
	var project_lead_id = '{/literal}{$project_lead_id}{literal}';
	jQuery.ajax({
	        type: "POST",
	        url: "cmdscripts/opp_import_process_status.php?to_pdf=true",
	        data: {projectOppId : projectOppId},
	        dataType: "json",
	       	cache: false,
	       	async: false,	       
	        complete: function (o) {
	        	var res = o.responseText;       					
				var res_str = res.split('_');
				var import_text_arr = res_str[1].split('|');
				var inserted_opp = trim(import_text_arr[0]);
				var updated_opp = trim(import_text_arr[1]);        				      					
				if(inserted_opp == '0' && updated_opp == '0'){
					var msg_text = 'Data streaming is in progress...';        					
				}else{
					var msg_text = 'Opportunity Imported: '+inserted_opp+' Opportunity Updated: '+updated_opp;
				}         				
				if(trim(res_str[0])=='running'){       						
					ajaxStatus.showStatus(msg_text);
					return true;
				}else if(trim(res_str[0])=='finished'){							
					ajaxStatus.showStatus(inserted_opp+' Opportunity Imported and '+ updated_opp +' Opportunity Updated Successfully.');
					setInterval(function(){ 
						//unlink status file and redirect to detail view					
						unlinkStatusFile(projectOppId);   							
					},5000);
					
					return false;
				}		
	        }
    });		
}

function unlinkStatusFile(projectOppId) {
	jQuery.ajax({
	        type: "POST",
	        url: "cmdscripts/unlink_file.php?to_pdf=true",
	        data: {projectOppId : projectOppId},
	        dataType: "json",
	       	cache: false,
	       	async: false,	       
	        complete: function (o) {
	        	var res = o.responseText;       					
				if(res == 'success'){
					window.location.href="index.php?module=Opportunities&action=DetailView&ClubbedView=1&record="+projectOppId+"&n=" + new Date().getTime();
				}		
	        }
    });	
}
//<!---------start------------------->
//proposal verification
/*
 * @author:Basudeba Rath.
 * @Modified Date:17/Oct/2012. 
 */
var submitted = false;
var handleCancel = function(){
 	this.hide();	
}	

var handleYesQCVerify = function(){

	if(!submitted){
	submitted = true;
	
	}else{
		//do not submit again
		return false;
	}
	if( (typeof(this.proposal_id) != 'undefined') && (this.proposal_id != '')
			|| (typeof(this.selected_status) != 'undefined') && (this.selected_status != '') ){
		// modified By Basudeba, Date: 17/Oct/2012.
		var redirect_url = 'index.php?module=Opportunities&action=verifyproposal&uid='+this.proposal_id+'&status='+this.selected_status+'&record='+this.opportunity_id+'&rec_id='+this.opportunity_child_id;
		window.location.href = redirect_url;
		return false;
	}
	
	document.MassUpdate.submit();
	return false;
}

var un_verify_message = SUGAR.language.get('Opportunities', 'LBL_UN_VERIFY_MESSAGE');
var pending_verify_message = SUGAR.language.get('Opportunities', 'LBL_PENDING_VERIFY_MESSAGE');
var group_verify_messgage = SUGAR.language.get('Opportunities', 'LBL_GROUP_VERIFY_MESSAGE');

var single_un_verify_message = SUGAR.language.get('Opportunities', 'LBL_SINGLE_UN_VERIFY_MESSAGE');
var single_pending_verify_message = SUGAR.language.get('Opportunities', 'LBL_SINGLE_PENDING_VERIFY_MESSAGE');

var mySimpleDialog ='';

function getSimpleDialog(){
	
	if (typeof(mySimpleDialog) != 'undefined' && mySimpleDialog != ''){
		mySimpleDialog.destroy(); 
	}
	mySimpleDialog = new YAHOO.widget.SimpleDialog('dlg', { 
	    width: '40em', 
	    effect:{
	        effect: YAHOO.widget.ContainerEffect.FADE,
	        duration: 0.25
	    }, 
	    fixedcenter: true,
	    modal: true,
	    visible: false,
	    draggable: false
	});
    
	mySimpleDialog.setHeader('Warning!');
	mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
	return mySimpleDialog;

}

function verifySelectedProposals(){
	var checkedItems = new Array();
	var checkedCount = 0;
	var theForm = document.MassUpdate;
	inputs_array = theForm.elements;
	
	for(var wp = 0 ; wp < inputs_array.length; wp++) {
		if(inputs_array[wp].name == "mass[]") {
			if(inputs_array[wp].checked == true){

				var checkedItem  = inputs_array[wp].value;
				var checked_status = document.getElementById(checkedItem+'_status').value;
				
				if(checkedCount == 0){
					var selected_status = checked_status;
				}
				
				if(selected_status != checked_status){

					mySimpleDialog = getSimpleDialog(); 
					mySimpleDialog.setBody(group_verify_messgage);
					
				    var myButtons = [
				    { text: 'OK', handler: handleCancel }    
				    ];
				    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
				    mySimpleDialog.render(document.body);    
				    mySimpleDialog.show();
					return false;
					
				}else{
					checkedItems[checkedCount] = checkedItem;
					checkedCount++;
				}
			}
		}
	}
	
	if( (checkedCount > 0) 
			&&  ( (typeof(selected_status) != 'undefined') ||  (selected_status != ''))){

		if( selected_status == 'u'){

			theForm.uid.value = checkedItems.join(',');
			theForm.status.value = selected_status;
			theForm.action.value = 'verifyproposal';
			
			mySimpleDialog = getSimpleDialog(); 
			mySimpleDialog.setBody(un_verify_message);
		    var myButtons = [
		    { text: 'OK', handler: handleYesQCVerify },
		    { text: 'Cancel', handler: handleCancel }	    
		    ];
		    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
			return false;
			
		} else if(selected_status == 'p'){

			theForm.uid.value = checkedItems.join(',');
			theForm.status.value = selected_status;
			theForm.action.value = 'verifyproposal';
				
			mySimpleDialog = getSimpleDialog(); 
			mySimpleDialog.setBody(pending_verify_message);
		    var myButtons = [
		    { text: 'OK', handler: handleYesQCVerify },
		    { text: 'Cancel', handler: handleCancel }	    
		    ];
		    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
			return false;
			
		}else{
			return false;
		}
	} else if( checkedCount < 1 ){
		alert("Please select at least one record.");
		return false;
	}
}
function verifyProposal(proposal_id,opportunity_child_id){
	
	var selected_status = document.getElementById(proposal_id+'_status').value;

	if( selected_status == 'u'){
		
		mySimpleDialog = getSimpleDialog(); 
		mySimpleDialog.setBody(single_un_verify_message);
		
		mySimpleDialog.selected_status = selected_status;
		mySimpleDialog.proposal_id = proposal_id;
		mySimpleDialog.opportunity_child_id = opportunity_child_id;
		mySimpleDialog.opportunity_id = document.MassUpdate.record.value;
		
	    var myButtons = [
	    { text: 'OK', handler: handleYesQCVerify },
	    { text: 'Cancel', handler: handleCancel }	    
	    ];
	    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
	    mySimpleDialog.render(document.body);    
	    mySimpleDialog.show();
	    
		return false;
		
	} else if(selected_status == 'p'){
		
		mySimpleDialog  = getSimpleDialog(); 
		mySimpleDialog.setBody(single_pending_verify_message);
		
		mySimpleDialog.selected_status = selected_status;
		mySimpleDialog.proposal_id = proposal_id;
		mySimpleDialog.opportunity_child_id = opportunity_child_id;
		mySimpleDialog.opportunity_id = document.MassUpdate.record.value;
		
	    var myButtons = [
	    { text: 'OK', handler: handleYesQCVerify },
	    { text: 'Cancel', handler: handleCancel }	    
	    ];
	    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
	    mySimpleDialog.render(document.body);    
	    mySimpleDialog.show();
		return false;
		
	}else{		
		return false;
	}
}
function openCustomMassUpdateForm(parentId){
   var uid= '';
   var opid = '';   
   //validate if atleast one item is checked
   if($('input[name^=mass_email]:checked').length ==0){
	   alert(SUGAR.language.get('app_strings','LBL_LISTVIEW_NO_SELECTED'))
	   return false;
   }   
   //validate not should be more than 20
   if($('input[name^=mass_email]:checked').length >20){
	   alert(SUGAR.language.get('Opportunities','LBL_MAX_RECORD'))
	   return false;
   }   
   $('input[name^=mass_email]:checked').each(function(i,e){
   		uid += $(e).val()+',';
   		opid+=$(e).attr('oppval')+',';
   });
   //remove mass update form
   $('#MassUpdateOpp').remove();	
   //var myTabs = new YAHOO.widget.TabView("tab_pd");
   var callback = {
   		success:function(o){
   			SUGAR.util.evalScript(o.responseText);
			showPopupOpp(o.responseText,'Mass Update ','90%','massUpdate');
		}
	};
	var URL = 'index.php?module=Opportunities&action=clientoppmassupdateform&to_pdf=true&parentId='+parentId+'&uid='+uid;
	YAHOO.util.Connect.asyncRequest ('GET', URL , callback);
	return false;   
}
/**
* common pop up for opening all popups
* @author Mohit Kumar Gupta
* @date 25-02-2014
*/
function showPopupOpp(txt,TitleText,width,classId,height){
    TitleText=	decodeURIComponent(TitleText).replace(/\+/g, ' ');
                        
    oReturn = function(body, caption, width, theme) {
        $(".ui-dialog").find("."+classId).dialog("close");
		var myPos = { my: "center top", at: "center top+70", of: window };
        var bidDialog = $("<div class="+classId+"></div>")
        .html(body)
        .dialog({
				model:true,
                autoOpen: false,
                title: caption,
                width: width,
				height : height,
				resizable : false,
				position: myPos,
        });
        bidDialog.dialog('open');       
    };       
    oReturn(txt,TitleText, width, '');
    return;
}
{/literal}
{if $smarty.request.verification_error eq '1'}
{literal}
msgErrorVerification =  getSimpleDialog(); 

msgErrorVerification.setBody(SUGAR.language.get('Opportunities', 'LBL_VERIFICATION_ERROR_MSG'));
var myButtons = [
{ text: 'OK', handler: function (){this.hide()} }  
];
msgErrorVerification.cfg.queueProperty('buttons', myButtons);  
msgErrorVerification.render(document.body);    
msgErrorVerification.show();
{/literal}
	{/if}
{literal}


function pdfLinkValidation() {
    arAllIds=new Array();
    arCheckedIds =new Array();
    arAllIds=document.getElementsByName("mass_email[]");
    var j=0;
    for(i=0;i<arAllIds.length;i++) {
        if (arAllIds[i].checked) {
            arCheckedIds[j] = arAllIds[i].value;
            j=j+1;
        }
    }
    
    if (arCheckedIds.length >= 1) {
    /*
        var form = document.createElement("form");
        form.setAttribute("method", 'post');
        form.setAttribute("action", 'index.php?to_pdf=1&module=Opportunities&action=opp_pdf&sugarpdf=jobinfosheet');
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "records");
        hiddenField.setAttribute("value", arCheckedIds);
        form.appendChild(hiddenField);
        document.body.appendChild(form);
        form.submit();
    */
    
    	$form = $("<form method='post' action='index.php?to_pdf=1&module=Opportunities&action=opp_pdf&sugarpdf=jobinfosheet'></form>");
		$form.append('<input type="hidden" name="records" value="'+arCheckedIds+'" />');
		$('body').append($form);
        $form.submit();
    }
    else {
        alert(SUGAR.language.get('app_strings','LBL_LISTVIEW_NO_SELECTED'))
        return false;
    }
}
</script>
{/literal}
