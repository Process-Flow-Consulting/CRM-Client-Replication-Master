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

*}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="SHORTCUT ICON" href="{$FAVICON_URL}">
<meta http-equiv="Content-Type"
	content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.LBL_WIZARD_TITLE}</title>
{literal}
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
  {$SUGAR_JS}{$SUGAR_CSS}{$CSS}
	<div id="main">
		<div id="content" >
			<table style="width: auto;" align="center" >
				<tr>
					<td align="center">
						<form id="UserWizard"   name="UserWizard"	 method="POST" action="index.php" onsubmit="return saveRoles()">
							<input type='hidden' name='action' value='resetmyroles' /> 
							<input type='hidden' name='module' value='Users' /> 
							<input type='hidden' name='return_module' value="{$RETURN_MODULE}" />
							<input type='hidden' name='return_action' value="{$RETURN_ACTION}" />
							<span class='error'>{$error.main}</span>
							<div class="dashletPanelMenu wizard">
								<div class="bd">
									<div id="target_roles" class="screen">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>
													
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
																		<p>{$MOD.LBL_MY_ROLES_TXT}</p>
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
																<th width="5%" valign="top" align="right">{$MOD.LBL_ROLES_FILTER}</th>
																<td width="42%">
																<select multiple="true" name="rolesArray" id="rolesArray">
																    {html_options options=$TOTAL_ROLES_CLASS }
																</select>
																</td>
																<td width="5%">
																	<slot>
																		<input type=button value=">>" onclick="javascript:swapSelected('rolesArray','my_global_roles');" /><br/>
																		<input type=button value="<<" onclick="javascript:swapSelected('my_global_roles','rolesArray');" />
																	</slot>
																</td>
																<td width="42%">
																	<select name="my_global_roles[]" id="my_global_roles" multiple="true" >
																	{html_options options=$SAVED_ROLES_CLASS }																
																	</select>																	
																</td>
																</tr>
																</table>
																
																</td>
															</tr>
														</table>
													
												</td>
											</tr>
										</table>
									</div>
									<div class="nav-buttons">
										<input title="  Skip  " class="button"
											type="button" name="next_tab1"
											value="  Skip  "
											onclick="window.location.href={if $RETURN_MODULE neq '' and $RETURN_ACTION neq 'resetmyroles'}'index.php?module={$RETURN_MODULE}&action={$RETURN_ACTION}'{else}'index.php?module=Home&action=index'{/if}"
											/> <input
											title="Save"
											class="button primary" type="submit" name="save"
											value="  Save  " onmouseover="{literal}$('#my_global_roles option').attr('selected','selected');{/literal}" />&nbsp;
									</div>
								</div>
							</div>
							</div>
							{literal}
							<script type='text/javascript'>
<!--
var SugarWizard = new function()
{
    this.currentScreen = 'target_roles';

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
        document.getElementById(this.currentScreen).style.display = 'none';
        document.getElementById(screen).style.display = 'block';

        this.currentScreen = screen;
    }
}
{/literal}
SugarWizard.changeScreen('target_roles');
{literal}

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

/*fucntion to swap values to multiselects*/
function swapSelected(srcId,destId){
	try{		
		YUI().use('node',"selector-css3", function (Y) {
			if(Y.one('div.validation-message'))
			{
				Y.all('div.validation-message').remove();
			}
			srcDom = document.getElementById(srcId);
			dstDom = document.getElementById(destId);
			var scNodeType = srcDom.nodeName;
			if(scNodeType == "SELECT"){
				//handle dropdowns
				Y.one("#"+srcId).get("options").each( function(){
					var selected = this.get('selected');
					var value  = this.get('value');
					var text = this.get('text');
					if(selected){
						var found = false;
						//if it already exists then no need to add
						Y.one("#"+destId).get("options").each(function(node){
							if(node.get("value") == srcDom.value){found= true;}
						});
						if(found){
							ERR_MSG = 'Already Exists.';
							add_error_style('UserWizard',document.getElementById(srcId),ERR_MSG,false );
							return;
						}
						if(value !=''){
							b = Y.Node.create('<option value="'+value+'" >'+text+'</option>');
							b.set("selected",false);
							Y.one("#"+destId).append(b);
						}
						this.remove();
					}
				});
	
			}
			sortSelect(srcDom);
			sortSelect(dstDom);
		});
	}catch(e){
	
	}
}
//call on load

function sortSelect(selElem) {
	var tmpAry = new Array();
	for (var i=0;i<selElem.options.length;i++) {
        tmpAry[i] = new Array();
        tmpAry[i][0] = selElem.options[i].text;
        tmpAry[i][1] = selElem.options[i].value;
	}
	tmpAry.sort();
	while (selElem.options.length > 0) {
	    selElem.options[0] = null;
	}
	for (var i=0;i<tmpAry.length;i++) {
        var op = new Option(tmpAry[i][0], tmpAry[i][1]);
        selElem.options[i] = op;
	}
	return;
}
-->
</script>
{/literal} 
{$JAVASCRIPT} 
{literal}
<script type="text/javascript" language="Javascript">

addToValidate('UserWizard', 'my_global_roles', 'select', true,'Roles');							
function saveRoles(){
	var bReturn = 0;
	$('#my_global_roles option').attr('selected','selected').promise().done(function (){
		bReturn = check_form('UserWizard');
	});
	return bReturn ;
}	

$(document).ready(function(){
	$('#my_global_roles option').attr('selected','selected');
});		
{/literal}

</script>
</form>
{literal}
<style>
select[multiple]{
width:100%;height:100px
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
.error:empty{
background-color: initial;
}
div.nav-buttons {
    margin-top: 1em;
    text-align: right;
}
.dashletPanelMenu.wizard .bd {
    padding: 15px;
}
.dashletPanelMenu.wizard, .dashletPanelMenu.wizard.yui-module.yui-overlay.yui-panel {
	box-shadow: 0 2px 10px #999999;
	-moz-box-shadow: 0 2px 10px #999999;
	-webkit-box-shadow: 0 2px 10px 
	#999999;
	border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	background-color:
	white;
	border: 20px solid
	#cccccc;
	text-shadow: 0px 1px
	#fff;
	font-size: 14px;
	
}}
</style>
{/literal}