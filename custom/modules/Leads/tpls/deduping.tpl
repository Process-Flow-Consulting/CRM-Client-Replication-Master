
<div class="moduleTitle">
<h2><a href="index.php?module=Leads&amp;action=index">
       <!-- <img align="absmiddle" title="Leads" alt="Leads" src="themes/Sugar/images/icon_Leads_32.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=1265611612"> --> 
	   </a><span class="pointer">»</span>Project Leads Deduping</h2>
        <span class="utils"></span>
</div>


   <br clear="all"/>  
<div class="listViewBody">


    <form name="search_form" id="search_form" class="search_form" method="post" action="index.php?module=Leads&amp;action=index">
        <div id="Leadsbasic_searchSearchForm" style="" class="edit view search basic">
{if $returnLeadListView eq '1' }
{assign var=BTN_LBL value=$APP.LBL_LINK_TO_PROJECT_AND_CONTINUE}
{else}                    
{assign var=BTN_LBL value=$APP.LBL_LINK_TO_PROJECT}
{/if}
            
            <ul class="btn_container">
            <li><input name="link_projects" {if $AR_DUP_DATA|count lte 0 } disabled {/if} onclick="linkAllSubmit()" type='button' class="button primary" value="{$BTN_LBL}" ></li>
            {if $returnLeadListView eq '1' }
            <li>
				<input  type='button' {if $AR_DUP_DATA|count lte 0 } disabled {/if} class="button" value="{$APP.LBL_LEAD_CONTINUE_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=review_opportunity&return_action=ListView&record={$OB_LEAD_DATA->id}'" />
            </li>
            {/if}
            <li>
            {if $returnLeadListView eq '1' }
                        <input  type='button' class="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=ListView'" />
                    {else}
                    	<input  type='button' class="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=DetailView&record={$OB_LEAD_DATA->id}'" />
             {/if}
             </li>
            </ul>
        </div>
    </form>



    <form id="MassUpdate" action="" method="post" name="MassUpdate" id="MassUpdate" onsubmit="">
        <input type="hidden" name="module" value="Leads" />
        <input type="hidden" name="action" value="deduping" />
        <input type="hidden" name="record" id="record" value="{$OB_LEAD_DATA->id}" />
		<input type="hidden" name="return_lead_list_view" id="return_lead_list_view" value="{$returnLeadListView}" />


        <table cellpadding="0" cellspacing="0" width="100%" border="0" class="list view">
            <tbody>
                <tr class="pagination">
                    <td colspan="8">
                    </td>
                </tr>
                <tr height="20">
                    <th scope="col" nowrap="nowrap" width="2%" class="selectCol">
                        <div>
                            <input type="checkbox" class="checkbox" name="massall" id="massall" value="" onclick="sListView.check_all(document.MassUpdate, 'mass[]', this.checked);">
                        </div>
                    </th>
                    <th scope="col" width="20%" nowrap="nowrap">
                        <div style="white-space: nowrap;" width="100%" align="left">
                            <a href='index.php?module=Leads&action=deduping&sort=project_title&record={$OB_LEAD_DATA->id}&odr={$order}&return_lead_list_view={$returnLeadListView}' class="listViewThLinkS1">
                                {$MOD.LBL_PROJECT_TITLE}
                            </a>&nbsp;&nbsp;
                           {if $smarty.request.sort eq 'project_title' and $smarty.request.odr eq 'ASC' }
                                {assign var=imageName value='arrow_up.gif'}
                            {elseif $smarty.request.sort eq 'project_title' and $smarty.request.odr eq 'DESC' }
                                {assign var=imageName value='arrow_down.gif'}
                            {else}
                            {assign var=imageName value='arrow.gif'}
                            {/if}
                            <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">

                        </div>
                    </th>
                    <th scope="col" width="15%" nowrap="nowrap">
                        <div style="white-space: nowrap;" width="100%" align="left">
                            <a href='index.php?module=Leads&action=deduping&sort=lead_version&record={$OB_LEAD_DATA->id}&odr={$order}&return_lead_list_view={$returnLeadListView}' class="listViewThLinkS1">
                                {$MOD.LBL_LEAD_VERSION}
                            </a>&nbsp;&nbsp;
                           {if $smarty.request.sort eq 'lead_version' and $smarty.request.odr eq 'ASC' }
                                {assign var=imageName value='arrow_up.gif'}
                            {elseif $smarty.request.sort eq 'lead_version' and $smarty.request.odr eq 'DESC' }
                                {assign var=imageName value='arrow_down.gif'}
                            {else}
                            {assign var=imageName value='arrow.gif'}
                            {/if}
                            <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">

                        </div>
                    </th>
                    <th scope="col" width="20%" nowrap="nowrap">
                        <div style="white-space: nowrap;" width="100%" align="left">
                            <a href="index.php?module=Leads&action=deduping&sort=state&record={$OB_LEAD_DATA->id}&odr={$order}&return_lead_list_view={$returnLeadListView}" class="listViewThLinkS1">
                                {$MOD.LBL_LOCATION}
                            </a>&nbsp;&nbsp;
                            {if $smarty.request.sort eq 'state' and $smarty.request.odr eq 'ASC' }
                                {assign var=imageName value='arrow_up.gif'}
                            {elseif $smarty.request.sort eq 'state' and $smarty.request.odr eq 'DESC' }
                                {assign var=imageName value='arrow_down.gif'}
                            {else}
                            {assign var=imageName value='arrow.gif'}
                            {/if}
                            <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">

                        </div>
                    </th>
                    <th scope="col" width="10%" nowrap="nowrap">
                        <div style="white-space: nowrap;" width="100%" align="left">
                            <a href="index.php?module=Leads&action=deduping&sort=bids_due&record={$OB_LEAD_DATA->id}&odr={$order}&return_lead_list_view={$returnLeadListView}" class="listViewThLinkS1">
                                {$MOD.LBL_BIDS_DUE}
                            </a>&nbsp;&nbsp;
                            {if $smarty.request.sort eq 'bids_due' and $smarty.request.odr eq 'ASC' }
                                {assign var=imageName value='arrow_up.gif'}
                            {elseif $smarty.request.sort eq 'bids_due' and $smarty.request.odr eq 'DESC' }
                                {assign var=imageName value='arrow_down.gif'}
                            {else}
                            {assign var=imageName value='arrow.gif'}
                            {/if}
                            <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">

                        </div>
                    </th>
                    <th scope="col" width="10%" nowrap="nowrap">
                        <div style="white-space: nowrap;" width="100%" align="left">
                            <a href='index.php?module=Leads&action=deduping&sort=pre_bid_meeting&record={$OB_LEAD_DATA->id}&odr={$order}&return_lead_list_view={$returnLeadListView}' class="listViewThLinkS1">
                               {$MOD.LBL_PRE_BID_MEETING}
                            </a>&nbsp;&nbsp;

                            {if $smarty.request.sort eq 'pre_bid_meeting' and $smarty.request.odr eq 'ASC' }
                                {assign var=imageName value='arrow_up.gif'}
                            {elseif $smarty.request.sort eq 'pre_bid_meeting' and $smarty.request.odr eq 'DESC' }
                                {assign var=imageName value='arrow_down.gif'}
                            {else}
                            {assign var=imageName value='arrow.gif'}
                            {/if}
                            <img border="0" src="{sugar_getimagepath file=$imageName}" width="8" height="10" align="absmiddle" alt="Sort">
                        </div>
                    </th>
                    <th scope="col" nowrap="nowrap" width="10%">Plans</th>
                    <th scope="col" nowrap="nowrap" width="10%">{$MOD.LBL_CONTACT_NO}</th>
                </tr>

                <tr height="20" class="oddListRowS1">
                    <td scope="col" nowrap="nowrap" width="2%" class="">

                    </td>
                    <td scope="col" width="25%" nowrap="nowrap">
                        {$OB_LEAD_DATA->project_title}
                    </td>
                    <td scope="col" width="15%" nowrap="nowrap" align="center" >
                        <div style="white-space: text-align:center;" width="100%" align="center">
                        {$OB_LEAD_OTHER_DATA->fetched_row.lead_version}
                        </div>
                    </td>
                    <td scope="col" width="15%" nowrap="nowrap">

                        {assign var=state_name value=$OB_LEAD_DATA->state}

                    {$OB_LEAD_DATA->city} {if $OB_LEAD_DATA->state neq ''},{/if} {$STATE_DOM.$state_name}
                    </td>
                    <td scope="col" width="10%" nowrap="nowrap">                                                              
                         {$timedate->to_display_date($OB_LEAD_OTHER_DATA->fetched_row.bids_due_tz, false)}                  
                         
                    </td>
                    <td scope="col" width="10%" nowrap="nowrap">
                        {$timedate->to_display_date($OB_LEAD_DATA->fetched_row.pre_bid_meeting)}

                    </td>
                    <td scope="col" nowrap="nowrap" width="10%">
					
                    
                       {if $OB_LEAD_OTHER_DATA->fetched_row.lead_plans neq '0' && $OB_LEAD_OTHER_DATA->fetched_row.lead_plans neq ''} <div id="url{$OB_LEAD_DATA->id}" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
                        <a style="font-weight:normal;color: #006BB9" href="javascript:void(0)"  onclick="javascript:open_urls('{$OB_LEAD_DATA->id}','index.php?module=Leads&action=projecturl&record={$OB_LEAD_DATA->id}&to_pdf=true&all=1','Online Plans - {$OB_LEAD_DATA->name|urlencode}')" >View</a>{/if}
                       
                        </td>
                    <td scope="col" nowrap="nowrap" width="10%">{$OB_LEAD_DATA->contact_no}</td>
                </tr>
                {foreach from=$AR_DUP_DATA item=data }
                <tr height="20" class="oddListRowS1">
                    <td  class="nowrap">
                        <input  type="checkbox" class="checkbox" name="mass[]" value="{$data->id}"/>
                    </td>
                    <td scope="row" align="left" valign="top" class="">
                        <!-- <a href="index.php?module=Leads&action=DetailView&record={$data->id}" onmouseover="javascript:lvg_nav('Leads', '{$data->id}', 'd', 1, this)" onfocus="javascript:lvg_nav('Leads', '{$data->id}', 'd', 1, this)"> -->
                       <a href="javascript:void(0);" onClick = "compareLeads('{$data->id}')">
                 	   {$data->project_title}
                        </a>
                    </td>
                    <td scope="row" align="center" valign="top" class="">
                        
                            {$data->fetched_row.lead_version}
                        
                    </td>
                    <td scope="row" align="left" valign="top" class="">       

                        {assign var=state_name value=$data->state}

                     {$data->city} {if $data->state neq ''},{/if} {$STATE_DOM.$state_name}


                    </td>
                    <td scope="row" align="left" valign="top" class="">
                        {$timedate->to_display_date($data->fetched_row.bids_due_tz, false)}

                    </td>
                    <td scope="row" align="left" valign="top" class="">

                        {$timedate->to_display_date($data->pre_bid_meeting)}

                    </td>
                    <td align="left" class="helpIcon" width="*">
					
                      
						{if $data->lead_plans neq '' }  <div id="url{$data->id}" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
                        <a href="javascript:void(0)"  onclick="javascript:open_urls('{$data->id}','index.php?module=Leads&action=projecturl&record={$data->id}&to_pdf=true&all=1','Online Plans - {$data->name|urlencode}')" >View</a>
                      {/if}
                      
                        
                    </td>
                    <td align="left">
                    {$data->contact_no}
                    </td>
                </tr>
                {foreachelse}
                <tr class="">
                    <td colspan="8">
                        {$APP.LBL_NO_DATA}
                    </td>
                </tr>
                {/foreach}
                <tr class="oddListRowS1">
                    <td colspan="8">
                    
            <ul class="btn_container">
            <li><input name="link_projects" {if $AR_DUP_DATA|count lte 0 } disabled {/if} onclick="linkAllSubmit()" type='button' class="button primary" value="{$BTN_LBL}" ></li>
            {if $returnLeadListView eq '1' }
            <li>
				<input  type='button' {if $AR_DUP_DATA|count lte 0 } disabled {/if} class="button" value="{$APP.LBL_LEAD_CONTINUE_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=review_opportunity&return_action=ListView&record={$OB_LEAD_DATA->id}'" />
            </li>
            {/if}
            <li>
            {if $returnLeadListView eq '1' }
                        <input  type='button' class="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=ListView'" />
                    {else}
                    	<input  type='button' class="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="window.location.href='index.php?module=Leads&action=DetailView&record={$OB_LEAD_DATA->id}'" />
             {/if}
             </li>
            </ul>

                    </td>
                </tr>
            </tbody>
        </table>
        {*<script src="{sugar_getjspath file='cache/include/javascript/sugar_grp_overlib.js'}" type="text/javascript"></script>*}
        <div style="width:100px;position: absolute; visibility: hidden; z-index: 1000; left: 525px; top: 0px; background-image: none;" id="overDiv">
    </form>
{literal}
<script type='text/javascript' src="include/javascript/overlibmws.js"></script>
    <script type="text/javascript">
        function linkAllSubmit(){
             elm = YAHOO.util.Selector.query('input[name^=mass]:checked')
            if(elm.length >0)
            {

                if(showModel('{/literal}{$APP.MSG_LINK_TO_PROJECT_CONFIRM}{literal}','Confirm Dedupe'))
                {
                    document.getElementById('MassUpdate').submit();
                 }
               }else
                   {
            	   alert('{/literal}{$APP.LBL_LISTVIEW_NO_SELECTED}{literal}')
                }
        }
        
		function open_urls(plid,URL,titleName){	
              
            //cont = document.getElementById('url'+plid);
           	if(titleName.indexOf('Online Plan'))
            {
              //	SUGAR.ajaxUI.showLoadingPanel();
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
		              
			  TitleText=	decodeURIComponent(TitleText).replace(/\+/g, ' ');
			  
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
		
        function showModel(htmltext,titleVal){

        	if(typeof(dialog) != 'undefined')
        	{
        		dialog.destroy();
            }
        			dialog = new YAHOO.widget.Dialog('details_popup_div', {
                    	width: '500px',	
            						
                    		fixedcenter : "contained",    				
                    		visible : true, 
                    		draggable: true,
                    		modal:true,
                    		/*effect:[{effect:YAHOO.widget.ContainerEffect.SLIDE, duration:0.2},        
                    				{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.2}],
        					  */       modal:true
        					         
        					         });
        				dialog.setHeader(titleVal);
        				dialog.setBody(htmltext);
        				dialog.setFooter('');

        				var handleCancel = function() {
					
            				this.cancel();
            				
        				};
        				var handleSubmit = function() {
        					document.getElementById('MassUpdate').submit();
                  			this.cancel();
              			//return false;
        				};
        				var myButtons = [{ text: "Continue", handler: handleSubmit, isDefault: true }
        								 ,{ text: "Cancel", handler: handleCancel, isDefault: false }
        								 ];
        				dialog.cfg.queueProperty("buttons", myButtons);
        				dialog.render(document.body);
        				dialog.show();
            			//dialog.configFixedCenter(null,false)
        	           
        	}

    	
       /*
       	* Functions to show Lead comparison
       	*/ 
       function DetailsDialog(div_id, text) {
            this.div_id = div_id;
            this.text = text;
            this.width = "800px";
            this.header = '';
            this.footer = '';
        }
        
        function header(header) {
            this.header = header;
        }

        function footer(footer) {
            this.footer = footer;
        }

        function display() {
            dialog = new YAHOO.widget.Dialog(this.div_id, {
	            width: this.width,
	            height: "",
	            fixedcenter : "contained",
				visible : true, 
				draggable: true,
	            modal:true,
         	});
            var myButtons = [
                             { text: "Link to Project", handler: handleSubmit, isDefault: true },
                             { text: "Cancel", handler: handleCancel }
                         ];
           	dialog.cfg.queueProperty("buttons", myButtons);
            dialog.setHeader(this.header);
            dialog.setBody(this.text);
            dialog.setFooter(this.footer);
            dialog.render(document.body);
            dialog.show();
           
        }
        DetailsDialog.prototype.setHeader = header;
        DetailsDialog.prototype.setFooter = footer;
        DetailsDialog.prototype.display = display; 

        var handleCancel = function() {
            this.cancel();
        };
        var handleSubmit = function() {
        	  var e= document.MassUpdate.elements.length;
        	  var cnt=0;
        	  var pdld = document.getElementById('pdld').value;
        	  for(cnt=0;cnt<e;cnt++)
        	  {
        	    if(document.MassUpdate.elements[cnt].value==pdld){
        	    	document.MassUpdate.elements[cnt].checked = true;
        	    }
        	  }
        	  this.cancel();
        };
        
        function compareLeads(lead){

			var record = document.getElementById('record').value;
        	
            var isIE = document.all?true:false;
            
            if(document.getElementById('details_popup_div_c'))
            {
                 document.getElementsByTagName('body')[0].removeChild(document.getElementById('details_popup_div_c'));	
            }
            if(lead == '')
                return false;

            ac = new DetailsDialog("details_popup_div", '<div id="details_div"></div>');
            ac.setHeader('<table width="790px"><tr><td align="left" width="400">Original Project</td><td align="left"  width="400">Potential Duplicate</th></tr></table>');
            ac.display();

            var callback = {
                       cache:false,
                       success: function(o) {
                       res = o.responseText;
                       document.getElementById('details_div').innerHTML = res;
                       SUGAR.util.evalScript(o.responseText);
                   }
            }

            document.getElementById('details_div').innerHTML = 'Loading...';

            YAHOO.util.Connect.asyncRequest('GET', 'index.php?to_pdf=true&module=Leads&action=comparelead&lead='+lead+'&record='+record,callback);

      }
    </script>
<style>
ul.btn_container{
margin:0;
padding:0;
}
ul.btn_container li{
list-style-type:inline;
display: inline;
margin: 0px;
}
</style>
{/literal}

