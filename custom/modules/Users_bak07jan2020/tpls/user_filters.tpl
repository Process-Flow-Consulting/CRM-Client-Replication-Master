<script type="text/javascript" src="{sugar_getjspath file='custom/modules/Users/js/quicksearch.js}"></script>


<div class="user_groups" id="user_groups_container" border="10">
<table width="100%"  cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div class="edit view">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                {if $USER_EDIT_VIEW neq true}
                    <tr>
                        <th width="100%" align="left" scope="row" colspan="4">
                        <img align="left" src="{sugar_getimagepath file='projectpipeline_bb_wizard.png'}" border=0 width="320" height="54" />
                        {*    <h2><slot>{$MOD.LBL_BBWIZ_TITLE}</slot></h2>	*}</th>
                    </tr>
                    
                    <tr>
                        <td class="centerMiddle"align="left" scope="row" colspan="4">
                      
                        {$MOD.LBL_USER_FILTER_NOTE}
                       </td>
                    </tr> 
				{/if}
				<tr>
					<td  class="centerMiddle">
						&nbsp;
					</td>
				</tr>
				<tr>
                    <th colspan=4  class="centerMiddle" scope="row" >                    	                       
                       		 {html_radios name="geo_filter" options=$GEO_OPTION selected=$GEO_OPTION_SELECTED disabled=1 }                   
                   	</th>
                </tr>
                <tr>
					<td  class="centerMiddle">
						&nbsp;
					</td>
				</tr>
                    {* if its from user edit view *}
                    {if $USER_EDIT_VIEW eq true}
                    <tr>
                	    <td colspan="4"  class="centerMiddle" >
                	    <div style="padding:5px 0">
                	    	<span >
                	    		<b> User :</b>
                	    		{$USER_NAME}
                	    	</span></div>
                	    	<div>
                	    	<b>Role :</b>
                	    	
		                    <slot>
		                        <select  name='user_role' id="user_roles_dom" >
		                            {html_options options=$DOM_USER_ROLES  selected=$SELECTED_ROLE}
		                        </select>
		                    </slot></div>
		                    
		                    
	                    </td>
                    </tr>                    
                    {else}
                    <tr>
                    	<td colspan="4"  class="centerMiddle" scope="row">
                    	{$MOD.LBL_USER_SELECT_INFO}
                    	</td>
                    </tr>
                    
                    <tr><td colspan="4"><div id="save_status"></div></td></tr>
                    <tr>                        
                        <td colspan="4"  class="centerMiddle" >
                    <slot>
                        <select  name='user' id="users" >
                            {html_options options=$DOM_USERS selected=$SELECTED_USER }
                        </select>
                    </slot>
                    <b>Role :</b>
                    <span id="role_type">{$SELECTED_USER_ROLE}</span>
                    </td>
                    </tr>
                    {/if}
                    	
                     <tr>
                        <td class="centerMiddle" colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                    	<td width="5%">&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    	<td  width="5%" >&nbsp;</td>
                    	<td  width="45%">&nbsp;</td>
                    </tr>
                    <tr id="state_row_filter">
                        <td class="centerMiddle"scope="row" nowrap="nowrap">
                    <slot>
                    <label><input type="radio" name="geo_filter_for"  {if $GEO_FILTER_FOR eq 'state'} checked  {/if} value="state" />            {$MOD.LBL_STATE_FILTER}:
                    </label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <select  multiple="true"  tabindex='14' id="state_filters" name='state_filters'  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if}>
    				{html_options options=$DOM_STATE }
                        </select>
                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot>
                        <input type=button value=">>"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} onclick="javascript:swapSelected('state_filters','state_apply');/*manageCourties()*/"  /><br/>
                        <input type=button value="<<"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} onclick="javascript:swapSelected('state_apply','state_filters');/*manageCourties()*/">
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <select  multiple="true" id="state_apply" name="state_apply[]"  {if $GEO_FILTER_FOR neq 'state'} disabled  {/if} >
							{html_options options=$STATE_OPTOIONS }
                        </select>
                    </slot>
                    </td>
                    </tr>
                    <tr>
                        <td class="centerMiddle"colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr  id="county_row_filter">                        
                        <td class="centerMiddle"scope="row" nowrap="nowrap">
                    <slot>
                       <label> 
                       <input type="radio" name="geo_filter_for" value="county" {if $GEO_FILTER_FOR eq 'county'} checked  {/if} />       
                        {$MOD.LBL_COUNTY_FILTER}:
                       </label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                    	<div style="padding:5px 0">
                    	{php}
                    	
                    	$this->assign('ALL_STATE',$GLOBALS['app_list_strings']['state_dom']);
                    	{/php}
                        <select id="state_county" name="state_county" onchange="getCounty(this.value,'');" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} >
                           {* html_options options=$DOM_STATE *}
                           {html_options options=$ALL_STATE}
							{*html_options options=$STATE_OPTOIONS *}
							
                        </select>
                        </div>
                        <div id="county_div">
                        <select class="county" id="county" multiple="true" name="state" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if}>
                        
                        </select>
                        </div>
                        <input type="hidden" id="symbol" value="">
                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                       <slot> 
                       <input id="count_swap_lft" type=button value=">>"  {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} /> <br/>
                       <input id="count_swap_rgt" type=button value="<<"  {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} />
                     </slot>
                    </td>
                    <td class="centerMiddle">
                    <select id="county_filters" multiple="true" name="county_filters[]"  multiple="true" {if $GEO_FILTER_FOR neq 'county'} disabled  {/if} >
						{html_options options=$COUNTY_OPTOIONS}
                        </select>
                    </td>
                    </tr>
                    <tr>
                        <td class="centerMiddle"colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr  id="zip_row_filter">
                        <td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot>
						<label>	
						<input type="radio" name="geo_filter_for" value="zip" {if $GEO_FILTER_FOR eq 'zip'} checked  {/if}  /> 
						{$MOD.LBL_ZIP_FILTER}:
						</label>
                    </slot>
                    </td>
                    <td class="centerMiddle">
                    <slot>
                        <input id="user_zip_val" type="text" name="zip_filter"  {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if} />
                    </slot>
                    </td>
                	 <td class="centerMiddle" scope="row" nowrap="nowrap">
                        <slot> 
                       <input type=button value=">>" onclick="javascript:swapSelected('user_zip_val','zip_filters')" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}   /><br/>
                       <input type=button value="<<" onclick="javascript:swapSelected('zip_filters','user_zip_val')" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}  />
                     </slot>
                    </td>
                    <td class="centerMiddle">
                    <select id="zip_filters" name=zip_filters[]" multiple="true" {if $GEO_FILTER_FOR neq 'zip'} disabled  {/if}  >
							{html_options options=$ZIP_OPTOIONS }
                        </select>
                    </td>
                    </tr>
                    {if $GEO_OPTION_SELECTED neq 'client_location'}
                    <tr>
                        <td class="centerMiddle" colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="centerMiddle"  scope="row" nowrap="nowrap">
                    <slot>
								{$MOD.LBL_TYPE_FILTER}:
                    </slot>
                    </td>
                    <td class="centerMiddle" >
                    <slot>
                        <select multiple="true" name='pl_leads_type' id='user_pl_type'  >
                        {html_options options=$DOM_TYPE_PL }
                        </select>

                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                       <slot> 
                       <input type=button value=">>" onclick="javascript:swapSelected('user_pl_type','pl_type_filter')"  /><br/>
                       <input type=button value="<<" onclick="javascript:swapSelected('pl_type_filter','user_pl_type')">
                     </slot>
                    </td>
                    <td class="centerMiddle">
                    	<select id="pl_type_filter" name="type_filters[]" multiple="true" >
							{html_options options=$TYPE_OPTOIONS }
                        </select>
                    </td>
                    </tr>
                    
                    {/if}
                    <tr>
                        <td colspan="4">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                        <td class="centerMiddle"  scope="row" nowrap="nowrap">
                    <slot>
								{$MOD.LBL_CLASSIFICATION_FILTER}:
                    </slot>
                    </td>
                    <td class="centerMiddle" >
                    <slot>
                        {literal}                
                      
                        <script type="text/javascript"> 
						/*SUGAR.util.doWhen("typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['EditView_classification_name']) != 'undefined'", enableQS );
						SUGAR.util.doWhen("typeof(sqs_objects) != 'undefined' && typeof(sqs_objects['UserWizard_classification_name']) != 'undefined'", enableQS );
							  
						 
						if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;} 
						sqs_objects['EditView_classification_name']={"id":"EditView_classification_name","form":"EditView","method":"query","modules":["oss_Classification"],"group":"or","field_list":["name","id"],"populate_list":["classification_name","classification_id"],"conditions":[{"name":"name","op":"like_custom","end":"%", "value":"" }],"order":"name","limit":"30","no_match_text":"No Match"};
						sqs_objects['UserWizard_classification_name']={"id":"UserWizard_classification_name","form":"UserWizard","method":"query","modules":["oss_Classification"],"group":"or","field_list":["name","id"],"populate_list":["classification_name","classification_id"],"conditions":[{"name":"name","op":"like_custom","end":"%", "value":"" }],"order":"name","limit":"30","no_match_text":"No Match"};	             
						
						
						$("#classification_name").autocomplete({minLength: 0,
						    source: function (req,el){
						          $.ajax({
							      url: "index.php?to_pdf=true&module=Home&action=quicksearchQuery&q="+req.term,
							      dataType: "xml",
							      data:{data:'{"id":"EditView_classification_name","form":"EditView","method":"query","modules":["oss_Classification"],"group":"or","field_list":["name","id"],"populate_list":["classification_name","classification_id"],"conditions":[{"name":"name","op":"like_custom","end":"%","value":""}],"order":"name","limit":"30","no_match_text":"No Match"}'},
							      success: function( xmlResponse ) {
							      alert(xmlResponse);
							        var data = $( "geoname", xmlResponse ).map(function() {
									alert($('name', this).text());
							          return {
							            value: $( "name", this ).text() + ", " +
							                   ( $.trim( $( "countryName", this ).text() ) || "(unknown country)" ),
							            id: $( "geonameId", this ).text()
							          };
							        }).get();
							      }
							    });
						    },focus: function(event, ui) {
						    	//$(this).val(ui.item.label);
						    	//return false;
						    },
						    select: function(event, ui) {
						        //$(this).val(ui.item.label);
						        //$(this).next("classification_id).val(ui.item.value);
						       // return false;
						    }
						}).data("autocomplete")._renderItem = function(ul, item) {
						  //  return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
						};*/
						</script>
						     
						<input type="text" autocomplete="off" title="" value="" size="" id="classification_name"  class="sqsEnabled sqsNoAutofill" name="classification_name">
						
						<input type="hidden" value="" id="classification_id" name="classification_id">
						<span class="id-ff multiple">
						<button onclick='open_popup(
						"oss_Classification", 
						600, 
						400, 
						"&user_wizard=true", 
						true, 
						false, 
						{"call_back_function":"set_return","form_name":{/literal}{if $USER_EDIT_VIEW}"EditView"{else}"UserWizard"{/if}{literal},"field_to_name_array":{"id":"classification_id","name":"classification_name"}}, 
						"single", 
						true
						);' value="Select" class="button firstChild" accesskey="T" title="Select [Alt+T]" tabindex="122" id="btn_lead_name" name="btn_lead_name" type="button">
						
						<img src="themes/default/images/id-ff-select.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=1820889813">
						</button>
						<button value="Clear" onclick="this.form.classification_name.value = ''; this.form.classification_id.value = '';" class="button lastChild" accesskey="C" title="Clear [Alt+C]" tabindex="122" id="btn_clr_lead_name" name="btn_clr_lead_name" type="button">
						<img src="themes/default/images/id-ff-clear.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=1460853148"></button>
						</span>
						{/literal}
                    </slot>
                    </td>
                    <td class="centerMiddle" scope="row" nowrap="nowrap">
                        <input name="classification_swap" type="button" value=">>" id="classification_swap" ><br/>
                        <input name="classification_swap_right" type="button" value="<<" onclick="javascript:swapSelected('classification_filters','classification_name')" />
                    </td>
                    <td class="centerMiddle">
                    	<select id="classification_filters" name="classification_filters[]" multiple="true" >
                    	{html_options options=$CLASSIFICATION_OPTOIONS}
                        </select>
                    </td>
                </tr>
                 {if $GEO_OPTION_SELECTED neq 'client_location'}
                <tr>
                	<td class="centerMiddle" colspan="4"><hr/></td>
                </tr>
                <tr>
	                <td class="centerMiddle" scope="row" nowrap="nowrap">
                    <slot>
	                	{$MOD.LBL_LABOR_FILTER}:
	                	</slot>
	                </td>
	                <td class="centerMiddle"colspan="3">
	                {html_checkboxes name=labor_filters  options=$DOM_LABOUR_OTIONS selected=$LABOUR_OPTOIONS }
	                </td>
	                
                </tr>
                {/if}
                <tr>
                	<td class="centerMiddle"colspan="4"><hr/></td>
                </tr>
                <tr>
                <td colspan="4">
                	<div id="team_members">
                		{if $IS_TEAM_MANAGER}
                		{include file="custom/modules/Users/tpls/team_members.tpl"}
                		{/if}
                	</div>
                </td>
                
                </tr>
                
        </table>
    </div>
</td>
</tr>
</table>
 {* if its from user edit view *}
                    {if $USER_EDIT_VIEW neq true}
<div class="nav-buttons">
     <input title="{$MOD.LBL_WIZARD_SKIP}"
            class="button" type="button" name="create_user" value="  {$MOD.LBL_WIZARD_SKIP}  "
            onclick="window.location.href='index.php?module=Home'" />&nbsp;
            {*   <input title="{$MOD.LBL_WIZARD_BACK_BUTTON}"
            class="button" type="button" name="next_tab1" value="  {$MOD.LBL_WIZARD_BACK_BUTTON}  "
         
            onclick="SugarWizard.changeScreen('personalinfo',false);" /> *}
    
    <input title=" {if $FILTERED_NOT_APPLIED_USR_COUNT lte '1' }  {$MOD.LBL_USER_FILTER_FINISH} {else} {$MOD.LBL_USER_FILTER_NEXT} {/if} "
           class="button primary" type="button" id="user_filter" name="user_filter" value=" {if $FILTERED_NOT_APPLIED_USR_COUNT lte '1'}  {$MOD.LBL_USER_FILTER_FINISH} {else} {$MOD.LBL_USER_FILTER_NEXT} {/if} " />
           
</div>{/if}
</div>

{literal}
<script type="text/javasctipt">
var roleMap = JSON.parse('{/literal}{$AR_URER_ROLE_MAP}{literal}');
function showModel(htmltext,titleVal){
    if(document.getElementById('details_popup_div_c'))
             {
                document.getElementsByTagName('body')[0].removeChild(document.getElementById('details_popup_div_c'));	
             } 
			dialog = new YAHOO.widget.Dialog('details_popup_div', {
            	width: '600px',	
    						
            		fixedcenter : "contained",    				
            		visible : false, 
            		draggable: true,
            		effect:[{effect:YAHOO.widget.ContainerEffect.SLIDE, duration:0.2},        
            				{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.2}],
					         modal:true
					         });
				dialog.setHeader(titleVal);
				dialog.setBody(htmltext);
				dialog.setFooter('');

				var handleCancel = function() {
    				this.cancel();
				};
				var handleSubmit = function() {
      			this.cancel();
				};
				var myButtons = [{ text: "Ok", handler: handleSubmit, isDefault: true }];
				dialog.cfg.queueProperty("buttons", myButtons);
				dialog.render(document.body);
				dialog.show();
    			//dialog.configFixedCenter(null,false)
	           
	}


/* bind event handler for swap selected classifications*/
YUI().use('node','event',"selector-css3","io-form", function (Y) {
	try{
		
		Y.on('domready',function(){
			
			//function to enable/disable 
			Y.all('input[name^=geo_filter_for]').on('click',function(e){
						
						switch(e.target.get('value')){
							case"state":
								Y.all("#state_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
								Y.all("#state_row_filter input").each(function(elm){elm.removeAttribute('disabled')})

								Y.all("#zip_row_filter select").setAttribute('disabled','disabled')
								Y.all("#zip_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
								
								Y.all("#county_row_filter select").setAttribute('disabled','disabled')
								Y.all("#county_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
								
							break;
							case "county":
								Y.all("#county_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
								Y.all("#county_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
								
								Y.all("#state_row_filter select").setAttribute('disabled','disabled')
								Y.all("#state_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
								
								Y.all("#zip_row_filter select").setAttribute('disabled','disabled')
								Y.all("#zip_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
									
							break;
							case "zip":
								Y.all("#zip_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
								Y.all("#zip_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
								
								Y.all("#state_row_filter select").setAttribute('disabled','disabled')
								Y.all("#state_row_filter input[type=button]").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
								
								Y.all("#county_row_filter select").setAttribute('disabled','disabled')
								Y.all("#county_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
							break;
						}
					});

		Y.on('contentready',onchangeUsers,'#user_groups_container')
		});
		//display user role as per the selection
		function onchangeUsers() {

			try{//on submiting the user filterform
				if(Y.one('#user_filter') != null){
					Y.one('#user_filter').on('click',
					function (e){
				
						Y.one('#UserWizard input[name=action]').set("value","save_user_filter")	;
						var allSel = ['state_apply','county_filters','zip_filters','pl_type_filter','classification_filters','tms_filter','tms' ];
						for(i=0;i<allSel.length;i++){
							if(Y.one('#'+allSel[i])){
								/*Y.log('#'+allSel[i]+' option')*/
								Y.all('#'+allSel[i]+' option').set('selected','true');
							}
						}
						
						var cfgSbmt = {
							arguments: {
       									  start: '',
        								  complete: '',
      									  end: ''
     									 },
							method:"POST",
							form:{id:Y.one('#UserWizard')},
							on:{complete:function(id,o,args){
									//chekck is there any other user left
									var resVal = JSON.parse(o.responseText)
									args.start = resVal;
									if(resVal.remaining_users == 0){
										showModel({/literal}'{$MOD.MSG_ALL_USER_FILTER_SAVED}'{literal},'Bluebook User Wizard');
										window.location = 'index.php?module=Home&action=index';
									}else if(resVal.filter_selected){
											showModel('Nothing changed or you have not selected any filter.','Bluebook User Wizard');
									}
									else{
										SugarWizard.showFilterScreen(resVal.user_id);
										document.getElementById('main').scrollIntoView(1);
									}
									hideStatusLoading('loading');
									 
								},
								end:function(id,args){}
								}						
								};
						
						Y.io('index.php?to_pdf=1',cfgSbmt);
						return false;
						
					}
					);}
					if(Y.one('#users'))
					Y.one('#users').on('change', function(e) {document.getElementById("role_type").innerHTML = roleMap[e.target.get("value")];
					//display team members
					setStatusLoading('loading');
					var callbackOutboundTest = {cache: false,start:function (){setStatusLoading('loading');},success: function(o) {
						SUGAR.util.evalScript(o.responseText);
						
						if(Y.one('#user_groups'))
						Y.one('#user_groups').set('innerHTML', o.responseText);
						if(Y.one('#roles_assignment'))
						Y.one('#roles_assignment').set('innerHTML', o.responseText);
						hideStatusLoading('loading');
					}};
					YAHOO.util.Connect.asyncRequest("GET","index.php?module=Users&action=handle_requests&getFullDetails=1&to_pdf=true"+'&record='+e.target.get("value"), callbackOutboundTest,'');
					});
					//show hide TMs container
					if(Y.one('#user_roles_dom')) {

						Y.one('#user_roles_dom').on('change',function(e){
							var role = e.target.get('value');
							if(role == 'team_manager'){
								/*console.log(Y.one('#team_members').hasChildNodes())*/
								if(YAHOO.lang.trim(Y.one('#team_members').get('innerHTML'))==''){

									var callbackOutboundTest = {cache: false,start:function (){
										setStatusLoading('loading');}
										,success: function(o) {
											Y.one('#team_members').set('innerHTML', o.responseText);
											hideStatusLoading('loading');
										}};
										YAHOO.util.Connect.asyncRequest("GET","index.php?module=Users&action=handle_requests&getTmsContainer=1&to_pdf=true", callbackOutboundTest,'');
								}else
								{
									Y.one('#team_members').set('className','');
								}

							}else
							{
								Y.one('#team_members').set('className','yui-hidden');
							}

						});
					}

					//handle classification click
					if(Y.one('#classification_swap') != null){
						
						//Y.one('#classification_swap').on('click',swap_classifications );
						Y.one('#classification_swap').setAttribute('onclick','swap_classifications()' );
					}

					//event handler for counties
					Y.one('#count_swap_lft').on('click',
					function(e){

						Y.all('#county option').each(function(elm){
							if(elm.get('selected'))
							{
								b = Y.Node.create('<option value="'+elm.get('value')+'" >'+elm.get('innerHTML')+'</option>');
								b.set('selected',false)
								//Y.log('#county_filters options:value['+elm.get('value')+']')
								//Y.log(Y.one('#county_filters').get('options').get('value['+elm.get('value')+']').getAttribute('value'))
								//if(Y.all("#county_filters option[value='"+elm.get('value')+"']"))
								//Y.log(Y.all("#county_filters option[value='"+elm.get('value')+"']"))
								if(YAHOO.util.Selector.query('#county_filters option[value="'+elm.get('value')+'"]').length == 0){
									Y.one('#county_filters').append(b)
								}else{

								}
								elm.remove();
							}

						})
					}
					);
					Y.one('#count_swap_rgt').on('click',
					function(e){
						Y.all("#county_filters option").each(function (elm){
							if(elm.get('selected'))
							{elm.remove();}
						});

						first=(Y.one('#state_county option'))?Y.one('#state_county').get('value'):'';
						//load counties
						getCounty(first,'');
					});

			}catch(e){

			}
		}

	}catch(e){

	}


});

function swap_classifications(){
         YUI().use('node',"selector-css3", function (Y){
			if(Y.one('div.validation-message'))
			{
				Y.all('div.validation-message').remove()

			}

			//e.preventDefault();
			var found = false;
			var v = Y.one("#classification_id").get("value")  ;
			var t = Y.one("#classification_name").get("value") ;
			Y.one("#classification_id").set("value",'')  ;
			Y.one("#classification_name").set("value",'') ;

			Y.one("#classification_filters").get("options").each(function(node){
				if(node.get("value") == v){found= true;}
			});
			
			
			if(!found && v !=''){
				
				b = Y.Node.create('<option value="'+v+'" >'+t+'</option>');
				b.set("selected",false);
				Y.one("#classification_filters").append(b);
			}else
			{
				if(v==''){
					ERR_MSG = 'Please select a classification.'
				}else{
					ERR_MSG = 'Already Exists.';								
				}
				add_error_style('UserWizard',document.getElementById("classification_name"),ERR_MSG,0 );
			}
		});

}


/*fucntion to swap values to multiselects*/
function swapSelected(srcId,destId){
	try{
		
		YUI().use('node',"selector-css3", function (Y) {
			if(Y.one('div.validation-message'))
		{
			Y.all('div.validation-message').remove()
			
		}
			srcDom = document.getElementById(srcId);
			dstDom = document.getElementById(destId);
			
			var scNodeType = srcDom.nodeName;
			if(scNodeType == "SELECT"){
				//handle dripdowns
				Y.one("#"+srcId).get("options").each( function(){
					// this = option from the select
					var selected = this.get('selected');
					var value  = this.get('value');
					var text = this.get('text');
					if(selected){
						if(dstDom.nodeName != 'INPUT')
						{
							var found = false;
							//if it already exists then no need to add
							Y.one("#"+destId).get("options").each(function(node){
								if(node.get("value") == srcDom.value){found= true;}
							});
							if(found && destId != 'county' ){
								
									ERR_MSG = 'Already Exists.';
								
								add_error_style('UserWizard',document.getElementById(srcId),ERR_MSG,false );
								return;
							}
							if(value !=''){
								b = Y.Node.create('<option value="'+value+'" >'+text+'</option>');
								b.set("selected",false);
								Y.one("#"+destId).append(b);
							}
							
						}
						this.remove();
					}
				});

			}
			else if (scNodeType == 'INPUT'){
				//handle input type elements
				//check if already exists in the destination mutliselect
				var found = false;
				Y.one("#"+destId).get("options").each(function(node){
					if(node.get("value") == srcDom.value){found= true;}
				});
				if(!found && srcDom.value !=''){
					b = Y.Node.create('<option value="'+srcDom.value+'" >'+srcDom.value+'</option>');
					b.set("selected",false);
					Y.one("#"+destId).append(b);
					srcDom.value='';
				}else{
					if(srcDom.value =='')
					{
						ERR_MSG = 'Please enter a value.';
					}
					else{
						ERR_MSG = 'Already Exists.';
					}
					add_error_style('UserWizard',document.getElementById(srcId),ERR_MSG,false );
				}

			}
			sortSelect(srcDom);
			sortSelect(dstDom);
		});

	}catch(e){
		
	}
}

function getCounty(stateAbbr,selCounty){
	try{
		setStatusLoading('loading');
		var postParams ='';
		YUI().use('node',"selector-css3",function(Y){


			var varName = (Y.one('#county_filters') != null)?Y.one('#county_filters').get('name'):'';
			var allVals = Y.all('#county_filters option').get('value');
			var varValues = Y.all('#county_filters option').get('value');
			for(i=0;i<varValues.length;i++){
				postParams+='&'+varName+'='+varValues[i];
			}

		});
		var callback = {
			success:function(o){

				document.getElementById("county_div").innerHTML = o.responseText;
				hideStatusLoading('loading');
			}
		}
		var connectionObject = YAHOO.util.Connect.asyncRequest ("POST", "index.php?entryPoint=CountyAjaxCall&multisel=1&state_abbr="+stateAbbr+"&selected_county="+selCounty, callback,postParams);

	}catch(e){

	}
}

function manageCourties(){

	try{
		YUI().use('node',"io-base","selector-css3",function(Y){



			postParm = {};

			first=(Y.one('#state_county')!=null)?Y.one('#state_county').get('value'):'';
			//load counties
			getCounty(first,'');


			var varName = Y.one('#state_apply').get('name');
			var varValues = Y.all('#state_apply option').get('value');
			var postParams= '';
			for(i=0;i<varValues.length;i++){
				postParams+='&'+varName+'='+varValues[i];
			}
			var varName = Y.one('#county_filters').get('name');
			var varValues = Y.all('#county_filters option').get('value');
			for(i=0;i<varValues.length;i++){
				postParams+='&'+varName+'='+varValues[i];
			}


			var uri = 'index.php?module=Users&action=handle_requests&filter_counties=1&to_pdf=1';
			var callback = {
				success: function(o) {
					var response = JSON.parse(o.responseText);

					//clear all the options
					Y.all("#county_filters option").remove();
					//assign these couties
					for(var i in response){

						b = Y.Node.create('<option value="'+i+'" >'+response[i]+'</option>');
						b.set("selected",false);
						Y.one("#county_filters").append(b);

					}
				},
				failure: function(o) {Y.log('Request Failed')},

			}

			YAHOO.util.Connect.asyncRequest('POST', uri, callback,postParams);

		});
	}
	catch(e){

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
       
YAHOO.util.Event.onContentReady("classification_id", function(){

        var container1 = document.createElement('div');
        container1.innerHTML = '';  
        container1.id = 'classificationBasicContainer';
		
		YAHOO.util.Dom.insertAfter(container1 ,YAHOO.util.Dom.get('classification_name'));                   
            
        YAHOO.example.classification = function() {
        
        // instantiate remote data source
        var oDS = new YAHOO.util.XHRDataSource("index.php?"); 
        oDS.connMethodPost = 1;
        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON; 
        oDS.responseSchema = {
					resultsList:'fields',
					total:'totalCount',
					fields:["name","id"],
					metaNode:"fields",
					metaFields:{total:'totalCount',fields:"fields"}
		};
        oDS.maxCacheEntries = 10;         
    
        var oAC = new YAHOO.widget.AutoComplete("classification_name", "classificationBasicContainer", oDS);
		oAC.resultTypeList = false;                
        oAC.useShadow = true;
		
		var myHiddenField = YAHOO.util.Dom.get("classification_id");
		var myHandler = function(sType, aArgs) {
				var myAC = aArgs[0]; // reference back to the AC instance 
				var elLI = aArgs[1]; // reference to the selected LI element
				var oData = aArgs[2]; // object literal of selected item's result data 
				// update hidden form field with the selected item's ID
				myHiddenField.value = oData.id;
		};  
        oAC.itemSelectEvent.subscribe(myHandler);

		var classification_search = document.getElementById('classification_name').value;
		

        oAC.generateRequest = function(sQuery) { 
        	return  "to_pdf=true&module=oss_Classification&action=Userwizard&q="+sQuery+"&order=name&limit=30";
	    };          
            
        return {
            oDS: oDS,
            oAC: oAC,                    
        };
    }();             
});      
</script>

<style>
select{
width:200px;
}
select[multiple]{
width:96%;height:150px
}
div.user_groups div.edit.view td.centerMiddle {
    text-align: center;
    vertical-align: middle;
    width: 4px;
} 
select[disabled], input[disabled]{
	background-color:#E8E8E8; 
	
	
}
.yui-ac-content{
width:auto
}
.yui-ac-content li{
text-align:left
}

div.screen div.edit.view {
   font-weight: 800;
   font-size: 5em;
   color: #5e5454;
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
rgba(0,0,0,.2);

border: 1px solid
#999;

text-shadow: 0px 1px

    #fff;
    font-size: 14px;
    clear: both;

}
.edit tr th {
    padding-left: 160px;
}


</style>

{/literal}