{literal}
<style type="text/css">
.yui-navset .yui-content, .yui-navset .yui-navset-top .yui-content
{
	background: none;
}
.atag_head1{
	font-weight: bold;
	color: #353535;
	border-bottom:1px solid #cccccc;
}
.atag_head2{
	font-weight: bold;
	color: #565656;
	border-bottom:1px solid #cccccc;
}
.atag_table{
	border: 1px solid;
	border-color: #98C6EA !important;	
    border-radius: 6px 6px 6px 6px;
	width:99%;
	margin:1%; 
	background: url(index.php?entryPoint=getImage&themeName=Sugar&imageName=dp-bd.png) repeat;  
}
.atag_table th{
	padding: 5px;
	font-size: 13px;
	vertical-align: middle;
	border-top: none;
	height: 35px;
	background:  repeat scroll 0 0 #EBEBED !important;
}
.atag_table td{
	padding: 5px;
	font-size: 13px;
	vertical-align: top;		
}

.module_sep{
	border-bottom: 1px solid;
	border-color: #98C6EA !important;
}

.span_class{
	border: 1px solid;
	border-color: #EBEBED !important;
	padding: 1%;
	width: 98%;
	border-radius: 6px 6px 6px 6px;
	
}
a:link{
	text-decoration: none;
}
.accordionButton {
	margin: 10px;
	cursor: pointer;
	padding: 10px;
	background:  repeat scroll 0 0 #EBEBED !important;
}
.accordionContent {	
	margin: 10px;
	display: none;
}
.bd{
	height:500px;
	overflow:scroll;
}
</style>
{/literal}
<div id="tab_pd" class="yui-navset">

<!-- TABS -->
<ul class="yui-nav">
	{if $ONLINE_PLANS}
		<li {$OP_TAB_SELECTED}><a href="#tab0"><em>Online Plans</em></a></li>
	{/if}
	{if $PROJECT_OPP_DOCS}
		<li {$PO_TAB_SELECTED}><a href="#tab1"><em>Project Opportunity</em></a></li>
	{/if}
	{if $CLIENT_OPP_DOCS}
		<li {$CO_TAB_SELECTED}><a href="#tab2"><em>Client Opportunity</em></a></li>
	{/if}
</ul>

<!-- BODY -->
<div class="yui-content">
	
	{if $ONLINE_PLANS}
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="100%" style="padding-left:10px;">
				<div style="margin-bottom:10px;">
					<input type="button" name="create_online_plan" id="create_online_plan" value ="Create Online Plan" onClick="$('#online_plan_form_div').slideDown('normal').css('dissplay','block');">
				</div>
				<div id="online_plan_form_div" style="display:none;">
				<form name="OnlinePlan" id="OnlinePlan" action="" method="post"> 
					<table class="edit view edit508" cellpadding="0" cellspacing="1" width="100%" border="0">
						<tr>
							<td id="description_label" scope="col" valign="top" width="12.5%">
								<label for="description">URL Link:</label>
								<span class="required">*</span>
							</td>
							<td colspan="3" valign="top" width="37.5%">
								<textarea name="description" id="description" cols="80" rows="6"></textarea>
								<input name="project_lead_id" id="project_lead_id" value="{$PROJECT_LEAD_ID}" type="hidden">
							</td>
						</tr>
					</table>
					<div class="buttons">
						<input value="Save" id="OnlinePlans_save_button" name="OnlinePlans_save_button" onclick="return saveOnlinePlan(); return false;" class="button" title="Save" type="button">
						<input value="Cancel" id="OnlinePlans_cancel_button" name="OnlinePlans_cancel_button" onclick="$('#online_plan_form_div').slideUp('normal').css('dissplay','none');return false;" class="button" title="Cancel" type="button">
					</div>
				</form>
				</div>
			</td>
		</tr>
		<tr>
			<td width="100%">
				<div id="online_plans_data">
				<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<th width="20%" align="center" class="atag_head1">{$AR_OP_TITLE.TYPE}</th>
						<th width="20%" align="center" class="atag_head1">{$AR_OP_TITLE.SOURCE}</th>
						<th width="20%" align="center" class="atag_head1">{$AR_OP_TITLE.REVIEW}</th>
						<th width="20%" align="center" class="atag_head1">{$AR_OP_TITLE.LINK}</th>
					</tr>		
					{foreach from=$AR_ONLINE_PLAN item=data}
					<tr>
						<td width="20%" align="center"><span > {$data.plan_type}</span></td>
						<td width="20%" align="center"><span > {$data.plan_source}</span></td>
						<td width="20%" align="center"><span >{$timedate->to_display_date_time($data.last_reviewed_date)} </span></td>
						<td width="20%" align="center">
							<span >
								<a href="index.php?module=oss_OnlinePlans&action=openUrl&record={$data.id}" target="_blank">Open</a>
							</span>
						</td>
					</tr>
					{foreachelse}
					<tr><td>{$APP.LBL_NO_DATA}</td></tr>
					{/foreach}
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
	</table>
	{/if}
				
	{if $PROJECT_OPP_DOCS}
	<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td>
				<div id="list_subpanel_project_documents">
				{$PRO_OP_SUBPANEL}
				</div>
			</td>
		</tr>
	</table>
	{/if}
	
	
	{if $CLIENT_OPP_DOCS}
	<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td>
				<div id="accordion">
					{$CLIENT_OP_SUBPANEL}
				</div>
			</td>
		</tr>
	</table>
	{/if}
	
	
	{if $NO_DOCUMENTS}
		{$NO_DOCUMENTS}
	{/if}
</div>
	
</div>
{literal}
<script text="text/javascript">

	function saveOnlinePlan(){

		addForm('OnlinePlan');
		addToValidate('OnlinePlan', 'description', 'text', true, 'URL Link' );

		if(!check_form('OnlinePlan')){
			return false;
		}
		
		var postdata = $('#OnlinePlan').serialize();
		$('#online_plan_form_div').slideUp('normal').css('dissplay','none');
		$('#online_plans_data').html('<div id="ajax_content"><center><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></center></div>');
		var callback = {
    		success:function(o){
				$('#online_plans_data').html(o.responseText);
				$('#description').val('');
    		}
    	}
		var URL = 'index.php?module=oss_OnlinePlans&action=createplan&to_pdf=true';
		YAHOO.util.Connect.asyncRequest ('POST', URL , callback, postdata);
	}
	
</script>
{/literal}