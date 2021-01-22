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


<style>
{literal}
.moduleTitle h2
{
    font-size: 18px;
}
.link {
    text-decoration:underline
}
.dashletPanelMenu.wizard, .dashletPanelMenu.wizard.yui-module.yui-overlay.yui-panel {
	box-shadow: 0 2px 10px #999999;
	-moz-box-shadow: 0 2px 10px #999999;
	-webkit-box-shadow: 0 2px 10px  #999999;
	border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	background-color: rgba(0,0,0,.2);
	border: 1px solid #999;
	text-shadow: 0px 1px #fff;
	font-size: 14px;
	clear: both;
}
.dashletPanelMenu .bd .screen {
	background-color: #fff;
	border: 1px solid #999;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-webkit-border-radius: 6px;
	padding: 10px;
}
.dashletPanelMenu.wizard .bd {
    padding: 15px;
}
.dashletPanelMenu.wizard div.hr {
    height: 5px;
    border-top: 1px solid #ccc;
}
{/literal}
</style>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>

<div class="dashletPanelMenu wizard">
    <div class="bd">
            <div class="screen">
            <div id="mod_title">{$MODULE_TITLE}</div>
            <div class="hr"></div>

            <div id = "upload_content">
            
<form enctype="multipart/form-data" name="importcsvbidders" method="POST" action="index.php" id="importcsvbidders">
<input type="hidden" name="module" value="Leads">
<input type="hidden" name="action" value="importcsvstart1">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td scope="row" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" scope="row" width="15%">
                <label for="leadfile">{$MOD.LBL_SELECT_LEADS_FILE}</label>
            </td>
            <td align="left" scope="row" colspan="2">
                <input size="20" id="leadfile" name="leadfile" type="file"/> 
            </td>
        </tr>
        <tr>
            <td scope="row" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td scope="row" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td align="left" scope="row">
                <label for="bidderfile">{$MOD.LBL_SELECT_BIDDERS_FILE}</label>
            </td>
            <td align="left" scope="row" colspan="2">
                <input size="20" id="bidderfile" name="bidderfile" type="file"/> 
           </td>
        </tr>
        <tr>
            <td scope="row" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td td align="left" scope="row">
                <label></label>
            </td>
            <td align="left" scope="row" colspan="2">
                <input size="20" id="userfile" name="userfile" type="hidden"/> 
             </td>
        </tr>
        <tr>
            <td scope="row" colspan="4"><div class="hr">&nbsp;</div></td>
        </tr>
        <tr>
            <td align="left" scope="row">
                <label for="import_source">{$MOD.LBL_IMPORT_SOURCE}</label>
            </td>
            <td align="left" scope="row" colspan="2">
                <select id="import_source" name="import_source">
                    {$IMPORT_SOURCE_OPTIONS}
                </select> 
           </td>
        </tr>
	</table>
</td>
</tr>
</table>
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
  <td align="left">
      <input title="{$MOD.LBL_NEXT}"  class="button" type="submit" name="button" value="  {$MOD.LBL_NEXT}  " id="gonext">
    </td>
</tr>
</table>
</form>
    </div>
    <div id="ajax_content"><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></div>   

</div> 
</div>
   </div>
</div>

<script>
{literal}
document.getElementById('gonext').onclick = function(){
    clear_all_errors();
    var isError = false;
    // be sure we specify a file to upload
    if (document.getElementById('importcsvbidders').leadfile.value == ""
        && document.getElementById('importcsvbidders').bidderfile.value == "") {
        add_error_style(document.getElementById('importcsvbidders').name,'userfile',"Error: Please Select a File");
        isError = true;
    }

    if (document.getElementById('importcsvbidders').import_source.value == "") {
        add_error_style(document.getElementById('importcsvbidders').name,'import_source',"Error: Please Select a source");
        isError = true;
    }

    if(!isError){
    	document.getElementById('upload_content').style.display = 'none';
        document.getElementById('ajax_content').style.display = 'block';
        document.getElementById('mod_title').innerHTML = '<h2>Uploading...</h2>';
    }
    
    return !isError;
}
{/literal}
</script>  
{literal}
<style>
#ajax_content{
    height: 150px;
    position: relative;
    display:none;
}
.ajax-loader {
    position: absolute;
    left: 50%;
    top: 20%;
    margin-left: -50px; /* -1 * image width / 2 */
    margin-top: -32px;  /* -1 * image height / 2 */
    display: block;     
}
</style>
{/literal}
