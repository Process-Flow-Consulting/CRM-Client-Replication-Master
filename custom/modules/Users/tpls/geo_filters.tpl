<script type="text/javascript" src="cache/jsLanguage/Users/en_us.js"></script>
    <table  style="" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div class="edit view">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
               <th align="left" scope="row" colspan="4">
               <img align="left" src="{sugar_getimagepath file='projectpipeline_bb_wizard.png'}" border=0 width="320" height="54" />
                {* <h2>{$MOD.LBL_BBWIZ_TITLE}</h2> *}</th>
            </tr>
            <tr>
                <td scope="row">                
                  <div class="userWizWelcome">                                    
                      {$MOD.LBL_USER_FILTER_NOTE}
		  </div>
                </td>
            </tr>
            <tr>
		<td  class="centerMiddle">&nbsp;</td>
	    </tr>
            <tr>
                <td colspan=4   scope="row" id='filters' style="text-align:right" >                    	                       
             <ul class="filter_list">
		<li>
		{html_radios name="geo_filter" options=$GEO_OPTION selected=$GEO_OPTION_SELECTED separator="</li><li>"}
				<input type="checkbox" name='for_clients' id='for_clients' {if $GEO_OPTION_SELECTED neq 'client_location'} disabled="disabled"{/if} {if $GEO_OPTION_SELECTED_FOR_CLIENTS eq 1}checked="checked"{/if} value='for_clients' /> <label for="for_clients">{$MOD.LBL_FOR_CLIENTS_CHK}</li>
		
		</li>
	     </ul>
                </td>
            </tr>
            <tr>
		<td  class="centerMiddle">
		    
		</td>
	    </tr>             
            </table>
            </div>
		<div style="float:left;text-align:right;width:99%">
		<input class="button primary" id="Save" type="button" name="Save" value="{$MOD.LBL_REASS_BUTTON_CONTINUE}" /> 
		{if $smarty.request.geofilters eq '1'}
		<input type="button" name="Cancel" value="Cancel" onclick="window.location.href = 'index.php?module=Administration&action=index'"/> 
		{/if}
		</div>
            </td>
            </tr>
</table>
            
            
<script type="text/javascript">
var savedGeofilter = '{$GEO_OPTION_SELECTED}';
var savedGeofilterApplyToCient = '{$GEO_OPTION_SELECTED_FOR_CLIENTS}';
{literal}

YUI().use('node','event','io',
function(Y){
	Y.on('domready',function(){
	{/literal}		
	{if $smarty.request.geofilters eq '1'}
	Y.delegate('click',confirmFilterSave,'#Save','input[type=button]');
	//Y.delegate('click',saveInstanceFilter,'#filters','input[type=radio]')
	{else}
	Y.delegate('click',saveInstanceFilter,'#Save','input[type=button]');
	{/if}    
			
    	{literal}
	Y.delegate('click',toggleForClientOption,'#filters','input[type=radio]')
});
			
function saveInstanceFilter(e) {

    /*if (typeof (e) == 'object') {
        siteFilter = $('input[type=radio]:checked').val();
    } else {
        siteFilter = e;
    }*/
    siteFilter = $('input[type=radio]:checked').val();
    var cfg = {
        method: "POST",
        data: 'filter_value=' + siteFilter+'&for_clients='+$('#for_clients').prop('checked'),
        on: {
            start: function () {
                setStatusLoading('loading');
                this.all('input[type=radio]').setAttribute('disabled', 'disabled');
            },
            complete: function (id, o) {
                var valRes = JSON.parse(o.responseText);
                if (valRes.redirect == 'filters') {
                    setTimeout("SugarWizard.showFilterScreen()", 100);
                   this.one('#welcome').setStyle('display', 'none');
                }else{
                	window.location = 'index.php?module=Administration&action=index';
                }
            },
            failure: function () {
                hideStatusLoading('loading');
                this.all('input[type=radio]').each(function (elm) {
                    elm.removeAttribute('disabled');
                })
            },
            end: function () {
                hideStatusLoading('loading');
                this.all('input[type=radio]').each(function (elm) {
                    elm.removeAttribute('disabled');
                })
            }
        }
    };
    Y.io('index.php?to_pdf=1&module=Users&action=save_user_filter&instance_filter=1', cfg)
};		
		

function confirmFilterSave(e) {
    var selectedVal = $('input[type=radio]:checked').val();
	var noPopup = false;
    if (savedGeofilter == selectedVal){
    	  noPopup = true;
    	 if(savedGeofilter == 'client_location' && $('#for_clients').prop('checked') != savedGeofilterApplyToCient  ){
    	  saveInstanceFilter(selectedVal,false);
    	   noPopup = true;
    	   return ;
    	 }   
   	}

    if (typeof dialog != 'undefined' ) {
	dialog.destroy();		
    }

    if (selectedVal == 'project_location') {
        frmStr = 'Client';
        toStr = 'Project';
    } else {
        frmStr = 'Project';
        toStr = 'Client';
    }

    htmltext = SUGAR.language.get('Users', 'CNF_FILTER_CHANGED').replace("%f%", frmStr + 's');
    htmltext = htmltext.replace("%t%", toStr + 's');
    
    dialog = new YAHOO.widget.Dialog('details_popup_div', {
        width: '600px',
        fixedcenter: true,
        //constraintoviewport:true,    				
        visible: false,
        draggable: true,
        effect: [{
            effect: YAHOO.widget.ContainerEffect.SLIDE,
            duration: 0.2
        }, {
            effect: YAHOO.widget.ContainerEffect.FADE,
            duration: 0.2
        }],
        modal: true
    });
    dialog.setHeader("Warning!");
    dialog.setBody(htmltext);
    dialog.setFooter('');

    var handleCancel = function () {
        radios = YAHOO.util.Selector.query('input[type=radio]')
        for (i = 0; i < radios.length; i++) {
            if (selectedVal == radios[i].value) {
                radios[i].checked = false;

            } else {
                radios[i].checked = true;

            }
        }
        
		toggleForClientOption()
        this.cancel();
    };
    var handleSubmit = function () {
	//Y.delegate('click',saveInstanceFilter,'#Save','input[type=button]');
        saveInstanceFilter(selectedVal);
        this.cancel();
    };
    var myButtons = [{
        text: "Ok",
        handler: handleSubmit,
        isDefault: true
    }, {
        text: "Cancel",
        handler: handleCancel,
        isDefault: false
    }];
    if(!noPopup){
    	dialog.cfg.queueProperty("buttons", myButtons);
    	dialog.render(document.body);
    	dialog.show();
    	//dialog.configFixedCenter(null,false)
    }else{
    	window.location.href='index.php?module=Administration&action=index';
    	//saveInstanceFilter(selectedVal);
    }
}

function toggleForClientOption(){
    var siteFilter = $('input[type=radio]:checked').val();	
    if (siteFilter == 'client_location'){
	$('#for_clients').removeAttr('disabled');
	
    }else{
        $('#for_clients').attr('disabled',true);
	    $('#for_clients').removeAttr('checked');
	}
	
	if(savedGeofilterApplyToCient== 1 && !$('#for_clients').attr('disabled')){
		$('#for_clients').attr('checked',true);
	}else{
		$('#for_clients').attr('checked',false);
	}
	
	
}
});	



{/literal}
</script>
{literal}
<style>
ul.filter_list{
margin: 0;
padding: 0;
text-align: left;
width: 79%;
float: right;
}
ul.filter_list li{
list-style: none;
width: 100%;
margin: 0;
display: inline;
}
li input[type=radio]{
margin-left:6%
}
</style>
{/literal}

