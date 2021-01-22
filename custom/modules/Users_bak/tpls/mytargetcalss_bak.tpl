{*
/*********************************************************************************
* The contents of this file are subject to the SugarCRM Master
Subscription * Agreement ("License") which can be viewed at *
http://www.sugarcrm.com/crm/master-subscription-agreement * By
installing or using this file, You have unconditionally agreed to the *
terms and conditions of the License, and You may not use this file
except in * compliance with the License. Under the terms of the license,
You shall not, * among other things: 1) sublicense, resell, rent, lease,
redistribute, assign * or otherwise transfer Your rights to the
Software, and 2) use the Software * for timesharing or service bureau
purposes such as hosting the Software for * commercial gain and/or for
the benefit of a third party. Use of the Software * may be subject to
applicable fees and any use of the Software without first * paying
applicable fees is strictly prohibited. You do not have the right to *
remove SugarCRM copyrights from the source code or user interface. * *
All copies of the Covered Code must include on each user interface
screen: * (i) the "Powered by SugarCRM" logo and * (ii) the SugarCRM
copyright notice * in the same form as they appear in the distribution.
See full license for * requirements. * * Your Warranty, Limitations of
liability and Indemnity are expressly stated * in the License. Please
refer to the License for the specific language * governing these rights
and limitations under the License. Portions created * by SugarCRM are
Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

*}{*
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html{$langHeader}>
<head>
<link rel="SHORTCUT ICON" href="{$FAVICON_URL}">
<meta http-equiv="Content-Type"
	content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.LBL_WIZARD_TITLE}</title> {literal}
<script type='text/javascript'>
function disableReturnSubmission(e) {
   var key = window.event ? window.event.keyCode : e.which;
   return (key != 13);
}
</script>
{/literal} 
</head>
<body class="yui-skin-sam">
{literal}
<script type='text/javascript'>
function disableReturnSubmission(e) {
   var key = window.event ? window.event.keyCode : e.which;
   return (key != 13);
}
</script>{/literal}
*}  {$SUGAR_JS}{$SUGAR_CSS}{$CSS}
	<div id="main">
		<div id="content">
			<table style="width: auto;" align="center">
				<tr>
					<td align="center">

						<form id="UserWizard" name="UserWizard"	 method="POST" action="index.php" onsubmit="return saveClassification()">
							<input type='hidden' name='action' value='mytargetclass' /> 
							<input type='hidden' name='module' value='Users' /> 
							<input type='hidden' name='return_module' value="{$RETURN_MODULE}" />
							<input type='hidden' name='return_action' value="{$RETURN_ACTION}" />
							<input type='hidden' name='transfer_action' value="{$TRANSFER_ACTION}" />
													
							<span class='error'>{$error.main}</span>
										
							
							<div class="dashletPanelMenu wizard">

								<div class="bd">


									<div id="target_class" class="screen">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>
													<div class="edit view">
														<table width="100%" border="0" cellspacing="0"
															cellpadding="0">
															<tr>
																<th align="left" scope="row" colspan="4"><h2>
																		<img align="left"
																			src="{sugar_getimagepath file='projectpipeline_bb_wizard.png'}"
																			border=0 width="320" height="54" />
																	</h2></th>
															</tr>
															<tr>
																<td scope="row" colspan="4">
																	<p>
																		<slot>{$MOD.LBL_CONST_REL_MGMT}</slot>
																	</p>
																</td>
															</tr>
															<tr>
																<td scope="row" colspan="4">
																	<div class="userWizWelcome">
																		<p>{$MOD.LBL_MY_CLS_TXT}</p>
																	</div>
																</td>
															</tr>
															<tr>
																<td  colspan="4" scope="row">
																{if $MSG_SAVED neq ''}
																		<div id="dialog-modal" title="Success!" >{$MSG_SAVED}</div>
																<script type="text/javascript">{literal}
																$(document).ready(function() {
																	$(function() {$( "#dialog-modal" ).dialog({//height: 120
																	show: { effect: "highlight", duration: 1000}
																	,hide: {effect: "explode", duration: 1000}
																	,resizable:false,modal: true
																	,buttons: {Ok: function() {window.location.href='index.php?module=Administration'; },Cancel: function() {$( this ).dialog( "close" ); }      }    });
  });																
																//showModel({/literal}'{$MSG_SAVED}'{literal},'Warning!');
																});
																{/literal}</script>
																{/if}
																<table width="100%">
																<tr>
																<th width="5%" valign="top" align="right">{$MOD.LBL_CLASSIFICATION_FILTER}</th>
																<td width="35%">
																
																		<input style="width:322px" type="text" name="classification_name" id="classification_name" class="sqsEnabled sqsNoAutofill" />
																		<input type="hidden" name="classification_id"  id="classification_id" />
																		<script type="text/javascript"> 
																			SUGAR.util.doWhen("typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['UserWizard_classification_name']) != 'undefined'", enableQS ); 
																		</script> 
																		<script language="javascript"> 
																		{literal} 
																		if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;} 
																		sqs_objects['UserWizard_classification_name']={"id":"UserWizard_classification_name","form":"UserWizard","method":"query","modules":["oss_Classification"],"group":"or","field_list":["name","id"],"populate_list":["classification_name","classification_id"],"conditions":[{"name":"name","op":"like_custom","begin":"%","end":"%", "value":"" },{"name":"description","op":"like_custom","begin":"%","end":"%", "value":"" }],"order":"name","limit":"30","no_match_text":"No Match"};	{/literal}             
																		</script> 
																			<span class="id-ff multiple">{literal}
																				<button onclick='open_popup(
																				"oss_Classification", 
																				600, 
																				400, 
																				"", 
																				true, 
																				false,
																				
																				{"call_back_function":"set_return_mytargetclass","form_name":"UserWizard","field_to_name_array":{"id":"classification_id","name":"classification_name"},"passthru_data":{"child_field":"oss_classification_leads","return_url":"index.php%3Fmodule%3DLeads%26action%3DSubPanelViewer%26subpanel%3Doss_classification_leads%26record%3D89518964-b47c-6cc1-4b1d-50c5e9f06d19%26sugar_body_only%3D1"}}, 
																				"multiselect", 
																				true
																				);' {/literal}value="Select" class="button firstChild" accesskey="T" title="Select [Alt+T]" tabindex="122" id="btn_lead_name" name="btn_lead_name" type="button">
																				
																				<img src="themes/default/images/id-ff-select.png">
																				</button>
																				<button value="Clear" onclick="this.form.classification_name.value = ''; this.form.classification_id.value = '';" class="button lastChild" accesskey="C" title="Clear [Alt+C]" tabindex="122" id="btn_clr_lead_name" name="btn_clr_lead_name" type="button">
																				<img src="themes/default/images/id-ff-clear.png"></button>
																				</span>
																				<img id='loading_img' style="display:none" src="themes/default/images/img_loading.gif" border="0" />
																 
																</td>
																<td width="5%">
																	<slot>
																		<input type=button value=">>" onclick="swapSelected();" /><br/>
																		<input type=button value="<<" onclick="$('#my_target_classifications option:selected').remove();" />
																	</slot>
																</td>
																<td width="55%">
																	<select name="my_target_classifications[]" id="my_target_classifications" multiple="true" >
																	{if $SAVED_TARGET_CLASS_HTML neq ''}
																	{$SAVED_TARGET_CLASS_HTML}
																	{/if}
																	</select>
																	
																	<textarea style="display:none" name="selected_classifications" id ="selected_classifications"></textarea>
																</td>
																</tr>
																</table>
																
																</td>
															</tr>
														</table>
													</div>
												</td>
											</tr>
										</table>

									</div>


									<div class="nav-buttons">
										<input title="  Skip  " class="button"
											type="button" name="next_tab1"
											value="  Skip  "
											onclick="window.location.href={if $RETURN_MODULE neq '' and $RETURN_ACTION neq 'mytargetclass'}'index.php?module={$RETURN_MODULE}&action={$RETURN_ACTION}{if $TRANSFER_ACTION neq ""}&transfer_action={$TRANSFER_ACTION}{/if}'{else}'index.php?module=Home&action=index'{/if}"
											/> <input
											title="Save"
											class="button primary" type="submit" name="save"
											value="  Save  " onmouseover="{literal}$('#my_target_classifications option').attr('selected','selected');$('#selected_classifications').html('');$('#my_target_classifications option').each(function(idx,elm){str = $(elm).val();$('#selected_classifications').html($('#selected_classifications').html()+'|'+str);});{/literal}" />&nbsp;
									</div>
								</div>




							</div>

							</div>

							{literal}
							<script type='text/javascript'>
<!--
var SugarWizard = new function()
{
    this.currentScreen = 'target_class';

    this.handleKeyStroke = function(e)
    {
        // get the key pressed
        var key;
        if (window.event) {
            key = window.event.keyCode;
        }
        else if(e.which) {
            key = e.which
        }

        switch(key) {
        case 13:
            primaryButton = YAHOO.util.Selector.query('input.primary',SugarWizard.currentScreen,true);
            primaryButton.click();
            break;
        }
    }

    this.changeScreen = function(screen,skipCheck)
    {
        if ( !skipCheck ) {
            clear_all_errors();
            var form = document.getElementById('UserWizard');
            var isError = false;

            switch(this.currentScreen) {
            case 'personalinfo':
                if ( document.getElementById('last_name').value == '' ) {
                    add_error_style('UserWizard',form.last_name.name,
                        '{/literal}{$APP.ERR_MISSING_REQUIRED_FIELDS} {$MOD.LBL_LAST_NAME}{literal}' );
                    isError = true;
                }
                {/literal}
                {if $REQUIRED_EMAIL_ADDRESS}
                {literal}
                if ( document.getElementById('email1').value == '' ) {
                    document.getElementById('email1').focus();
                    add_error_style('UserWizard',form.email1.name,
                        '{/literal}{$APP.ERR_MISSING_REQUIRED_FIELDS} {$MOD.LBL_EMAIL}{literal}' );
                    isError = true;
                }
                {/literal}
                {/if}
                {literal}
                break;
            }
            if (isError == true)
                return false;
        }

        document.getElementById(this.currentScreen).style.display = 'none';
        document.getElementById(screen).style.display = 'block';

        this.currentScreen = screen;
    }
}
{/literal}
{if $SKIP_WELCOME}
SugarWizard.changeScreen('target_class');
{else}
SugarWizard.changeScreen('target_class');
{/if}
{literal}


EmailMan = {};

function overlay(reqtitle, body, type) {
    var config = { };
    config.type = type;
    config.title = reqtitle;
    config.msg = body;
    YAHOO.SUGAR.MessageBox.show(config);
}

function hideOverlay() {
	YAHOO.SUGAR.MessageBox.hide();
}

function set_return_mytargetclass(popup_reply_data){

	//set_return_mytargetclass
	
	if(typeof(popup_reply_data.select_entire_list) != 'Undefined' && popup_reply_data.select_entire_list == 1){
		//load all clsfication to multiselect
		
		handleRequests('select_all=1');
	
	}else if(typeof(popup_reply_data.selection_list) != 'undefined'){
	
		
		var sel_list = popup_reply_data.selection_list;
		
		$('#my_target_classifications option').attr('selected','selected');
		var kkl=''
		
		for(lBl in popup_reply_data.selection_list){
			kkl = kkl+"selected_list[]="+popup_reply_data.selection_list[lBl]+"&";
		}
		
		
		handleRequests(kkl+$('#my_target_classifications').serialize());
		
		
	}else{
		
		set_return(popup_reply_data);
	}
	
	
	
	
}

function handleRequests(dataVar){
	
	
	$.ajax({url:'index.php?module=Users&action=mytargetclass&handleRequest=1&to_pdf=1'
			,type:"post"
			,data : dataVar
			,beforeSend:function(){
				$('#loading_img').show();
				$('#input[name=save]').attr('disabled',true)
			}
			,complete:function (data){
			
				$('#my_target_classifications').html(data.responseText);
				$('#loading_img').hide()
				$('#input[name=save]').attr('disabled',false)
			}
			})

}

function swapSelected(){

	$('#my_target_classifications option').attr('selected','selected').promise().done(function(){
	
	dataVar='selected_list[]='+$('input[name=classification_id]').val()+'&'+$('#my_target_classifications').serialize();
	
	handleRequests(dataVar);
	$('input[name=classification_id]').val('');
	$('input[name=classification_name]').val('');
	});
}
-->
</script>
							{/literal} {$JAVASCRIPT} {literal}
<script type="text/javascript" language="Javascript">

addToValidate('UserWizard', 'my_target_classifications', 'select', true,'Target Classifications');							
function saveClassification(){
var bReturn = 0;
$('#my_target_classifications option').attr('selected','selected').promise().done(function (){

bReturn = check_form('UserWizard')
});
return bReturn ;

}

				
{/literal}

</script>
</form>
{literal}
<style>
select[multiple]{
width:100%;height:200px
}
option:selected{
background-color:#FFF
}
#success{
 color: green;
 font-weight: bold;
 font-size: 15px;
 background-color: #FAFAD2;
 float: left;
 text-align: center;
 width: 100%;
 padding: 0.5% 0 0.5%;
}
.userWizWelcome p{
padding: 7px;
font-size: 13px;
}
div.screen div.edit.view {
height: 516px;
}
.yui-ac-content{
width:auto
}
</style>

{/literal}
