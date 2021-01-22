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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/




*}

<script type="text/javascript" src="{sugar_getjspath file='include/javascript/sugar_grp_yui_widgets.js'}"></script>
<script type='text/javascript' src="{sugar_getjspath file='include/javascript/overlibmws.js'}"></script>
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Connectors/tpls/tabs.css'}"/>

<form name='UnifiedSearchAdvancedMain' action='index.php' onsubmit="SUGAR.saveGlobalSearchSettings();" method='POST' class="search_form">
    <input type='hidden' name='module' value='Home'>
    <input type='hidden' name='query_string' value='test'>
    <input type='hidden' name='advanced' value='false'>
    <input type='hidden' name='action' value='UnifiedSearch'>
    <input type='hidden' name='search_form' value='false'>
    <input type='hidden' name='search_modules' value=''>
    <input type='hidden' name='skip_modules' value=''>
    <input type='hidden' id='showGSDiv' name='showGSDiv' value='{$SHOWGSDIV}'>
    <table width='99%' border='0' cellspacing='1'>
        <tr style='padding-bottom: 10px'>
            <td class="submitButtons" colspan='8' nowrap>
                <input id='searchFieldMain' class='searchField' type='text' size='80' name='query_string' value='{$query_string}'>
                <input type="submit" class="button primary" value="{$LBL_SEARCH_BUTTON_LABEL}">&nbsp;

              <!--  <a href="#" onclick="javascript:toggleInlineSearch();" style="font-size:12px; font-weight:bold; text-decoration:none; text-shadow:0 1px #FFFFFF;">{$MOD.LBL_SELECT_MODULES}&nbsp;
            {if $SHOWGSDIV == 'yes'}
                    <img src='{sugar_getimagepath file="basic_search.gif"}' id='up_down_img' border=0>
			{else}
                    <img src='{sugar_getimagepath file="advanced_search.gif"}' id='up_down_img' border=0>
			{/if}
                </a> -->
            </td><td class="" width="*">
                <script src="{sugar_getjspath file='include/javascript/sugar_grp_overlib.js'}" type="text/javascript"></script>
                <div style="float:left;width: 50px">
                    <img border="0" src="{sugar_getimagepath file="help-dashlet.png"}" onmouseover="return overlib(SUGAR.language.get('app_strings', 'GLOBAL_SEARCHH_HELP_TEXT'), STICKY, MOUSEOFF,1000,WIDTH, 700, LEFT,CAPTION,'&lt;div style=\'float:left\'&gt;'+SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TITLE')+'&lt;/div&gt;', CLOSETEXT, '&lt;div style=\'float: right\'&gt;&lt;img border=0 style=\'margin-left:2px; margin-right: 2px;\' src=themes/Sugar/images/close.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=896855794&gt;&lt;/div&gt;',CLOSETITLE, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP'), CLOSECLICK,FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass');" class="help-search" _sett_="2">
                    <div style="width:100px;position: absolute; visibility: hidden; z-index: 1000; left: 525px; top: 0px; background-image: none;" id="overDiv">
                
            </div>
        </div>
            </td>
        </tr>
        <tr height='5'><td></td></tr>
        <tr style='padding-top: 10px;'>
            <td colspan='8' nowrap'>
                <div id='inlineGlobalSearch' class='add_table' {if $SHOWGSDIV != 'yes'}style="display:none;"{/if}>
                    <table id="GlobalSearchSettings" class="GlobalSearchSettings edit view" style='margin-bottom:0px;' border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="2">
		    	{sugar_translate label="LBL_SELECT_MODULES_TITLE" module="Administration"}
                            </td>
                        </tr>
                        <tr>
                            <td width='1%'>
                                <div id="enabled_div"></div>
                            </td>
                            <td>
                                <div id="disabled_div"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
{literal}
function toggleInlineSearch()
{
    if (document.getElementById('inlineGlobalSearch').style.display == 'none')
    {
                SUGAR.globalSearchEnabledTable.render();
                SUGAR.globalSearchDisabledTable.render();
        document.getElementById('showGSDiv').value = 'yes'		
        document.getElementById('inlineGlobalSearch').style.display = '';
{/literal}	
        document.getElementById('up_down_img').src='{sugar_getimagepath file="basic_search.gif"}';
{literal}
    }else{
{/literal}			
        document.getElementById('up_down_img').src='{sugar_getimagepath file="advanced_search.gif"}';
{literal}			
        document.getElementById('showGSDiv').value = 'no';		
        document.getElementById('inlineGlobalSearch').style.display = 'none';		
    }    
}
{/literal}


var get = YAHOO.util.Dom.get;
var enabled_modules = {$enabled_modules};
var disabled_modules = {$disabled_modules};
var lblEnabled = '{sugar_translate label="LBL_ACTIVE_MODULES" module="Administration"}';
var lblDisabled = '{sugar_translate label="LBL_DISABLED_MODULES" module="Administration"}';
{literal}
SUGAR.saveGlobalSearchSettings = function()
{
        var enabledTable = SUGAR.globalSearchEnabledTable;
        var modules = "";
        for(var i=0; i < enabledTable.getRecordSet().getLength(); i++){
                var data = enabledTable.getRecord(i).getData();
                if (data.module && data.module != '')
                    modules += "," + data.module;
        }
        modules = modules == "" ? modules : modules.substr(1);
        document.forms['UnifiedSearchAdvancedMain'].elements['search_modules'].value = modules;
}
{/literal}

document.getElementById("inlineGlobalSearch").style.display={if $SHOWGSDIV == 'yes'}"";{else}"none";{/if}

{literal}
SUGAR.globalSearchEnabledTable = new YAHOO.SUGAR.DragDropTable(
        "enabled_div",
        [{key:"label",  label: lblEnabled, width: 200, sortable: false},
         {key:"module", label: lblEnabled, hidden:true}],
        new YAHOO.util.LocalDataSource(enabled_modules, {
                responseSchema: {fields : [{key : "module"}, {key : "label"}]}
        }),
        {height: "200px"}
);

SUGAR.globalSearchDisabledTable = new YAHOO.SUGAR.DragDropTable(
        "disabled_div",
        [{key:"label",  label: lblDisabled, width: 200, sortable: false},
         {key:"module", label: lblDisabled, hidden:true}],
        new YAHOO.util.LocalDataSource(disabled_modules, {
                responseSchema: {fields : [{key : "module"}, {key : "label"}]}
        }),
        {height: "200px"}
);

SUGAR.globalSearchEnabledTable.disableEmptyRows = true;
SUGAR.globalSearchDisabledTable.disableEmptyRows = true;
SUGAR.globalSearchEnabledTable.addRow({module: "", label: ""});
SUGAR.globalSearchDisabledTable.addRow({module: "", label: ""});
SUGAR.globalSearchEnabledTable.render();
SUGAR.globalSearchDisabledTable.render();


function open_urls(event,URL,titleName){      
     target = event.target?event.target:event.srcElement;
            plid = target.id;
            //cont = document.getElementById('url'+plid);
               if(titleName.indexOf('Online Plan'))
            {
              //    SUGAR.ajaxUI.showLoadingPanel();
                  popupWidth = '99%'
                popupheight = '485'
             }else{     
              popupWidth = '555px'; 
              popupheight = 'auto'
             }                   
            if(false && cont.innerHTML != '')
            {
              //  showPopup(cont.innerHTML,titleName,popupWidth);
              //  SUGAR.util.evalScript(cont.innerHTML);

            }else{              
        ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
            var sUrl = URL;
            var callback = {
                    success: function(o) {                    
                        document.getElementById('url'+plid).innerHTML = o.responseText;                        
                        document.getElementById('url'+plid).style.display='none';
                        showPopup(o.responseText,titleName,popupWidth,popupheight);
                        ajaxStatus.hideStatus();
                        SUGAR.util.evalScript(o.responseText);
                    },
                    failure: function(o) {
                    
                    }
                }

                var transaction = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback, null);
            }
        }        
        
function showPopup(txt,TitleText,width,height){      
               TitleText=    decodeURIComponent(TitleText).replace(/\+/g, ' ');        
              
              
              oReturn = function(body, caption, width, theme) {
                                        $(".ui-dialog").find(".open").dialog("close");
                                        var bidDialog = $('<div class="open"></div>')
                                        .html(body)
                                        .dialog({
                                                  model:true,
                                                autoOpen: false,
                                                title: caption,
                                                width: width,
                                                  height : height,
                                                  //show: "slide",
                              //hide: "scale",                                                
                                        });
                                        bidDialog.dialog('open');

                                };
       
oReturn(txt,TitleText, width, '');
                                                
return;
        
}



var mySimpleDialog ='';
function getSimpleDialog(params){    
    if (typeof(mySimpleDialog) != 'undefined' && mySimpleDialog != ''){        
        mySimpleDialog.destroy();         
    }
        mySimpleDialog = new YAHOO.widget.SimpleDialog(params.id, { 
        width: params.width+'px', 
        effect:{
            effect: YAHOO.widget.ContainerEffect.FADE,
            duration: 0.25
        }, 
        fixedcenter: true,
        modal: true,
        visible: false,
        draggable: true
    });
        
    mySimpleDialog.setHeader(params.message);
    mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
    return mySimpleDialog;
}

var mySimpleDialog1 ='';
function getSimpleDialog1(params){    
    if (typeof(mySimpleDialog1) != 'undefined' && mySimpleDialog1 != ''){        
        mySimpleDialog1.destroy();         
    }
        mySimpleDialog1 = new YAHOO.widget.SimpleDialog(params.id, { 
        width: params.width+'px', 
        effect:{
            effect: YAHOO.widget.ContainerEffect.FADE,
            duration: 0.25
        }, 
        fixedcenter: true,
        modal: true,
        visible: false,
        draggable: true        
    });
        
    mySimpleDialog1.setHeader(params.message);
    mySimpleDialog1.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
    return mySimpleDialog1;
} 
                        
     function showPopupBidBoard(lead_id){
        $('#dlg1').find('.container-close').remove();
        var params = Array();
        params['id'] = 'dlg1';
        params['message'] = 'Bid Board Updates';
        params['width'] = '990';
        mySimpleDialog = getSimpleDialog(params);                         
        mySimpleDialog.setBody('Loading...');          
        mySimpleDialog.render(document.body);            
        mySimpleDialog.show();
        
        var callback = {
            success:function(o){                
                mySimpleDialog.setBody(o.responseText);
            }
        }
        YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Leads&action=relatedpl&to_pdf=true&lead_id='+lead_id, callback);
        return false;           
    }
        
    function showPLDetailModal(lead_id){
        $('#dlg2').find('.container-close').remove();
        var params = Array();
        params['id'] = 'dlg2';
        params['message'] = 'Project Lead Details';
        params['width'] = '800';
        mySimpleDialog1 = getSimpleDialog1(params); 
        mySimpleDialog1.setBody('Loading...');          
        mySimpleDialog1.render(document.body);    
        mySimpleDialog1.show();        
        var callback = {
            success:function(o){                
                mySimpleDialog1.setBody(o.responseText);
            }
        }
        YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Leads&action=pldetails&to_pdf=true&lead_id='+lead_id, callback);
        return false;
    }

{/literal}



</script>