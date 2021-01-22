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

<div class="dashletPanelMenu wizard">
	<div class="bd">
		<div class="screen" border="10">
			{$MODULE_TITLE}
			<div class="hr"></div>
			<form name="ManageImport" method="POST" action="" id="ManageImport"
				onsubmit="return check_and_submit()">
				<input type="hidden" name="module" value="Administration"> <input
					type="hidden" name="action" value="manage_import">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td scope="row" colspan="4"><input type="radio"
										name="select_option" {if $smarty.request.select_option
										eq 'delete' } checked="true" {/if} value="delete" />
										{$MOD.LBL_DELETE_PREVIOUS_MAPPINGS}</td>
								</tr>
								<tr>
									<td scope="row" colspan="4">&nbsp;</td>
								</tr>
								<tr id="del_options" {if $smarty.request.select_option
									neq 'delete' } style="display: none"{/if}>
									<td align="right" scope="row" width="15%" valign="top"><label
										for="mapps"> {$MOD.LBL_SELECT_MAPPINGS} :&nbsp;&nbsp; </label>
									</td>
									<td align="left" scope="row" colspan="2"><select name="mapps[]"
										id="mapps" multiple="1"> {$IMPORT_SOURCE_OPTIONS}
									</select></td>
								</tr>
								<tr>
									<td scope="row" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td scope="row" colspan="4"><input type="radio"
										name="select_option" {if $smarty.request.select_option
										eq 'create' } checked="true" {/if} value="create" />
										{$MOD.LBL_CREATE_NEW_MAPPING}</td>
								</tr>
								<tr>
									<td scope="row" colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td scope="row" colspan="4"><input type="submit" name="Go"
										value="Go" class="button primary" /> <input type="button"
										name="Go" value="Cancel"
										onclick="window.location.href='index.php?module=Administration'" />
									</td>
								</tr>
								<tr>
									<td td align="left" scope="row"><label></label></td>
									<td align="left" scope="row" colspan="2"></td>
								</tr>
								<tr>
									<td scope="row" colspan="4"><div class="hr">&nbsp;</div></td>
								</tr>
								<tr>
									<td align="left" scope="row"></td>
									<td align="left" scope="row" colspan="2"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td align="left"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
{literal}
<style>
.dashletPanelMenu .bd .screen {
    background-color: 
    #fff;
    border: 20px solid
    #cccccc;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-border-radius: 6px;
    padding: 10px;
}
.dashletPanelMenu.wizard div.hr {
    height: 5px;
    border-top: 1px solid 
    #ccc;
    background-image: -moz-linear-gradient(center top, #eeeeee 0%, #ffffff 100% );
    background-image: -webkit-gradient( linear, left top, left bottom, color-stop(0, #eeeeee), color-stop(1, #ffffff) );
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#ffffff');
    margin: 15px 0px;
}
#mapps {
	width: 200px;
	height: 150px;
}
</style>

<script type="text/javascript">
  $(document).ready(function(){ 
   addForm('ManageImport');    
	$('input[name=select_option]').on('click',function(elm){if(elm.currentTarget.value == 'delete'){ $('#del_options').css('display','');}else{$('#del_options').css('display','none');}});
  });
	
	function check_and_submit(){
	
	
	    if( typeof( $('input[name=select_option]:checked').val()) != 'undefined'){
			sel_opt = $('input[name=select_option]:checked').val();
	        if( sel_opt == 'delete')
	        {
				if($('select[name^=mapps] option:selected').length ==0){
					alert(SUGAR.language.get('Administration','LBL_SEL_DELETE_MAP'));
					return false;
				}
				
				return confirm(SUGAR.language.get('Administration','LBL_CONFIRM_DELETE_MAP'));
					
	    	    
	    	}else if(sel_opt== 'create'){
				$('#ManageImport').submit();
	    	
	    	}
	    	
	    }else{
			alert(SUGAR.language.get('Administration','LBL_OPT_TO_SELECT'))
	    }  
	    return false;
	}
</script>
{/literal}
