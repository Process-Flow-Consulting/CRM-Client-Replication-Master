<?php

require_once 'include/MVC/View/views/view.edit.php';
require_once ('custom/modules/Users/filters/instancePackage.class.php');

class OpportunitiesViewEdit extends ViewEdit {
	
	function OpportunitiesViewEdit() {
		//$this->useForSubpanel = true;
		//$this->useModuleQuickCreateTemplate = false;
		parent::ViewEdit ();
	}
	
	/**
	 * Override preDisplay method for change vardefs file based on opportunity type.
	 * @see ViewEdit::preDisplay()
	 */
	function preDisplay() {
		//vardefs file for project opportunity.
		$metadataFile = 'custom/modules/Opportunities/metadata/editviewdefs.php';
		if(!empty($_REQUEST['parent_id']) || !empty($this->bean->parent_opportunity_id)){
			//vardefs file for client opportunity.
			$metadataFile = 'custom/modules/Opportunities/metadata/editviewdefs_sub.php';
		}
		$this->ev = new EditView ();
		$this->ev->ss = & $this->ss;
		$this->ev->setup ( $this->module, $this->bean, $metadataFile, 'include/EditView/EditView.tpl' );
	}
	
	function display() {
		global $timedate;
		global $mod_strings, $app_list_strings;
		global $app_strings, $current_user;
		
		$parent_opp_id = '';
		$sub_opp = 0;
		if(!empty($this->bean->parent_opportunity_id)){
			$parent_opp_id = $this->bean->parent_opportunity_id;
			$sub_opp = 1;
		} 
		
		/*
		 * use to auto update Team Name and primary team radio button when Re-Assigning Project Opportunity BSI-783
		 * Modified by Mohit Kumar Gupta 16-10-2015
		*/
		$parent_opp_id_team = (empty($parent_opp_id) && !empty($_REQUEST['parent_id'])) ? $_REQUEST['parent_id'] : $parent_opp_id;
		
		//Set parent id to parent_opportunity_id field.
		if($_REQUEST['parent_id']!='' || !empty($this->bean->parent_opportunity_id)){
			$this->bean->field_defs ['sales_stage'] ['options'] = 'client_sales_stage_dom';
			
			if(!isset($_REQUEST['record']) && empty($_REQUEST['record'])){
				$parent_opportunity = new Opportunity();
				$parent_opportunity->disable_row_level_security = true;
				$parent_opportunity->retrieve($_REQUEST['parent_id']);
				$this->bean->name = $parent_opportunity->name;
				
				require_once 'custom/include/OssTimeDate.php';
				$oss_timedate = new OssTimeDate ();
				$bid_due_date_time = $oss_timedate->convertDBDateForDisplay ( $parent_opportunity->date_closed, $parent_opportunity->bid_due_timezone, true );
				$this->bean->date_closed = $bid_due_date_time;
				
				$this->bean->bid_due_timezone = $parent_opportunity->bid_due_timezone;
				$this->bean->amount = $parent_opportunity->amount;
				$this->bean->parent_opportunity_id = $parent_opportunity->id;
				$this->bean->opportunity_name = $parent_opportunity->name;			
				
				/**
				 * Modified By : Ashutosh 
				 * Date : 9 Sept 2013
				 * Purpose : to set assigned user id , sales stage and default team
				 *           for new client opportunity 
				 */			
				$this->bean->assigned_user_id = $parent_opportunity->assigned_user_id ;
				$obUser = BeanFactory::getBean('Users',$parent_opportunity->assigned_user_id );
																
				$this->bean->sales_stage = $parent_opportunity->sales_stage ;
				
				$this->ss->assign('PROJECT_LEAD_ID',$parent_opportunity->project_lead_id);
			}
			
		}else{
			// change sales stage dropdown for project opportunity.
			$this->bean->field_defs ['sales_stage'] ['options'] = 'project_sales_stage_dom';
			unset($this->bean->field_defs['account_name']);
			
			
			$county = '';
			$county .= '<div id="county_div">';
			$county .= '<select title="County" id="lead_county" name="lead_county">';
			$county .= '<option value="0" label=""></option>';
			$county .= '</select>';
			$county .= '</div>';
			
			
			$selected_structure = '';
			if(isset($this->bean->lead_structure)){
				$selected_structure = $this->bean->lead_structure;
			}
			
			$structure = '';
			$structure .= '<select id="lead_structure" name="lead_structure">';
			$structure .= '<option value=""></option>';
			$structure .= '<optgroup style="background:#ececec" label="Residential Building"></optgroup>';
			foreach ($app_list_strings['structure_residential'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '<optgroup style="background:#ececec" label="Non-Residential Building"></optgroup>';
			foreach ($app_list_strings['structure_non_residential'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '<optgroup style="background:#ececec" label="Non-Building Construction"></optgroup>';
			foreach ($app_list_strings['structure_non_building'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '</select>';
			
			
			$selected_county = '';
			if(isset($this->bean->lead_county)){
				$selected_county = $this->bean->lead_county;
			}
			
			$this->ss->assign('lead_county',$county);
			$this->ss->assign('lead_structure', $structure);
			
		}
		
		if($_REQUEST['parent_id'] !='' && empty($this->bean->parent_opportunity_id) 
				&& $_REQUEST['parent_type'] == 'Opportunities'){
			$this->bean->field_defs['parent_opportunity_id']['value'] = $_REQUEST['parent_id'];
		}
		
		// validate package data for new records
		if (! isset ( $_REQUEST ['record'] )) {
			$obPackage = new instancePackage ();
			if ($obPackage->validateOpportunities ()) {
				sugar_die ( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
			}
		}		
		
		// Convert Bid Due Date based on TimeZone
		if (! empty ( $this->bean->id )) {
			require_once 'custom/include/OssTimeDate.php';
			$oss_timedate = new OssTimeDate ();
			$bid_due_date_time = $oss_timedate->convertDBDateForDisplay ( $this->bean->date_closed, $this->bean->bid_due_timezone, true );
			$this->bean->date_closed = $bid_due_date_time;
		}
		
		$clientOpporCount = 0;
		$salesStage = '';		
		$opprId = $this->bean->id;		
		
		if ( !empty($_REQUEST['record']) && empty ( $this->bean->parent_opportunity_id )) {			
			$salesStage = $this->bean->sales_stage;
			// Get client opportunity count.
			$where = " opportunities.parent_opportunity_id = '" . $opprId . "' ";
			$subOpp = $this->bean->get_full_list ( "", $where );
			//Changes made by parveen badoni on 03/07/2014 to provide valid argument for foreach.
			if(!empty($subOpp)) {
			foreach ( $subOpp as $sOpp ) {
				$clientOpporCount ++;
			}
		}
		}

		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate ();
		
		$sales_stage_js = <<<EOQ
		<script type='text/javascript'>    	
    	 
    	 /*
    	 * Sales Stage 'Bid Submitted' has been removed from the Options
    	 *
    	 YAHOO.util.Event.addListener('client_bid_status', 'change', function(){
            if(this.value=='Awarded'){
              document.getElementById('sales_stage').value = 'Bid Submitted';
            }
          });
          */
          
          
          var sub_opp = '$sub_opp';         
          function check_form_custom(frm){			
				if(document.getElementById('quickEditWindow')){
					if(sub_opp==1){
						frm = 'form_DCQuickCreateSub_Opportunities';
					}
				}
				document.forms[frm].action.value = 'Save';			
				var record_id = document.forms[frm].record.value;				
				var parent_opp_id = '$parent_opp_id';									
				if(check_form(frm)){
					if(record_id != '' && parent_opp_id == ''){
						check_project_form(frm);
					}else{
						if(frm == 'form_DCQuickCreate_Opportunities' || frm == 'form_DCQuickCreateSub_Opportunities'){
							DCMenu.save(frm, 'Opportunities_subpanel_save_button')
						}else{
							SUGAR.ajaxUI.submitForm(frm);
						}
					}					
				}	
			}
			
    	function check_project_form(formName){    	
    		
    		var preSalesStage =  '$salesStage';
    		var newSalesStage = document.forms[formName].sales_stage.value;
    		var opprId =  '$opprId';
    		var clientOpporCount = $clientOpporCount;
    		var oppAsssignedUser = '{$this->bean->assigned_user_id}';    		
    		        
    		//check if opportunity amount is changed by user in project opportunity edit 
    		var pre_amt_val = unformatNumber($('#copy_amount').val(), num_grp_sep, dec_sep);
    		var amt_val = unformatNumber($('#amount').val(), num_grp_sep, dec_sep)
    		if(clientOpporCount >0 && $('input[name=record]').val() != '' && parseFloat(pre_amt_val) != parseFloat(amt_val)){
                if(confirm(SUGAR.language.get('Opportunities','LBL_WARNING_OPPORTUNITIES_AMOUNT_CHANGE'))){
    		        //set this val to copy
    		        $('#copy_amount').val('copy');
                }    		   
	        }
    		        
	        if(oppAsssignedUser != '' && oppAsssignedUser != $('#assigned_user_id').val() )
	        {
    		   //Modified by Mohit Kumar Gupta 16-10-2015
    		   //A confirmation message should be prompt every time a user changes the assignment of project opportunity BSI-782
    		   /*var bIsSameUser = false;
    		   $.ajax({ url:'index.php?module=Opportunities&action=getoppassigned&to_pdf=1&record='+opprId,
    		            async:false,
    		            complete:function(res){    		                
    		                resPonse = JSON.parse(res.responseText);
    		               bIsSameUser = resPonse.status; 
	                      }
	                });
    		        
	          //if(bIsSameUser && confirm(SUGAR.language.get('Opportunities','LBL_WARNING_OPPORTUNITIES_ASSIGNMENT_CHANGE')))
    		  */
              if(confirm(SUGAR.language.get('Opportunities','LBL_WARNING_OPPORTUNITIES_ASSIGNMENT_CHANGE')))    		        
    		  {
    		        $('<input>').attr({
                                type: 'hidden',
                                id: 'change_client_op_assigned',
                                name: 'change_client_op_assigned',
    		                    value : true
                            }).appendTo('#'+formName);
	          }
            }
    			   		
    		if(preSalesStage!=newSalesStage && newSalesStage == 'Lost (closed)' && clientOpporCount > 0)
    		{
    			if(confirm('By changing the Project Sales Stage to Lost (Closed) the sales stage for each client on this opportunity will be set to Lost (Closed).')){
    					if(formName == 'form_DCQuickCreate_Opportunities'){
							DCMenu.save(formName, 'Opportunities_subpanel_save_button');
						}else{
    						SUGAR.ajaxUI.submitForm(formName);
    					}    					
    			}else{
    				return false;
    			}
    		
    		}else if(preSalesStage!=newSalesStage && newSalesStage == 'Won (closed)' && clientOpporCount > 1){
    		
    			if(confirm('You are trying to change the project sales stage for this opportunity to Won (Closed), to do this, please change the sales stage of the Client you won the project with to Won (Closed).')){
    				window.location.href = "index.php?module=Opportunities&action=DetailView&record="+opprId+"&ClubbedView=1"
    			}else{
    				return false;
    			}
    			
    		}else{    			
    			if(formName == 'form_DCQuickCreate_Opportunities'){
					DCMenu.save(formName, 'Opportunities_subpanel_save_button');
				}else{
    				SUGAR.ajaxUI.submitForm(formName);
    			}
    		}
    	}
    	
    	</script>
EOQ;
		
		echo $sales_stage_js;	

		/*
		 // access control for parent/child Opportunity
		if (empty ( $this->bean->parent_opportunity_id )) {
		
		$this->ss->assign ( 'CUSTOM_STATUS_LABEL', $mod_strings ['LBL_MY_PROJECT_STATUS'] );
		$this->ss->assign ( 'CUSTOM_STATUS_VALUE', '<select name="my_project_status" id="my_project_status">' . get_select_options_with_id ( $app_list_strings ['my_project_status_dom'], $this->bean->my_project_status ) . '</select>' );
		//$this->ss->assign ( 'ONCLICK_EVENT', 'onclick="this.form.action.value=\'Save\'; if(check_project_form(\'EditView\'))SUGAR.ajaxUI.submitForm(this.form);return false;"' );
		
		
			
		} else {
		$this->ss->assign ( 'CUSTOM_STATUS_LABEL', $mod_strings ['LBL_CLIENT_BID_STATUS'] );
		$this->ss->assign ( 'CUSTOM_STATUS_VALUE', '<select name="client_bid_status" id="client_bid_status">' . get_select_options_with_id ( $app_list_strings ['client_bid_status_dom'], $this->bean->client_bid_status ) . '</select>' );
		$this->ss->assign ( 'ONCLICK_EVENT', 'onclick="this.form.action.value=\'Save\'; if(check_form(\'EditView\'))SUGAR.ajaxUI.submitForm(this.form);return false;"' );
		}
		*/
		
		//Remove filter tmporary.
		// USER FILTERS ACCESS RULE
		/* if (! $current_user->is_admin) {
		require_once ('custom/modules/Users/filters/userAccessFilters.php');
		$bIsParent = (trim ( $this->bean->parent_opportunity_id ) == '') ? true : false;
		userAccessFilters::isOpporunityAccessable ( $this->bean->id, false, $bIsParent );
		} 
		*/
		
		//delete cache template
		require_once('include/TemplateHandler/TemplateHandler.php');
		$this->th = new TemplateHandler();
		$this->th->ss =& $this->ss;
		$this->tpl = 'include/EditView/EditView.tpl';
		$this->focus = $this->bean;
		$this->th->deleteTemplate($this->module, 'EditView');
		
		
		//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
		//create client opportunity from subpanel of client or contacts
		if($_REQUEST['parent_type'] == 'Accounts'){
			$this->bean->account_id = $_REQUEST['parent_id'];
			$this->bean->account_name = $_REQUEST['parent_name'];
			
			if(!empty($_REQUEST['customcontact_id']) && !empty($_REQUEST['customcontact_name'])){
				$this->bean->contact_id = $_REQUEST['customcontact_id'];
				$this->bean->contact_name = $_REQUEST['customcontact_name'];
			}
		}
		
		$time_format = $timedate->get_user_time_format($current_user);
		
		parent::display ();
		
		
		//specific to project opportunity
		if(empty($this->bean->parent_opportunity_id)){
			
			echo "<script type='text/javascript'>
				    var s_county ='$selected_county';
					function getCounty(stateAbbr,selCounty){
                    var callback = {
                        success:function(o){
                            //alert(o.responseText);
                            document.getElementById(\"county_div\").innerHTML = o.responseText;
                        }
                        }
                        var connectionObject = YAHOO.util.Connect.asyncRequest (\"GET\", \"index.php?entryPoint=CountyAjaxCall&state_abbr=\"+stateAbbr+\"&selected_county=\"+selCounty+\"&fieldname=lead_county\", callback);

                    }
					YAHOO.util.Event.onAvailable('lead_state',function(){
					var stateVal = document.getElementById('lead_state').value;					
	                getCounty(stateVal,s_county);
				});	
	                
					</script>";
		}
		
		//specific to client opportunity
		if(!empty($this->bean->parent_opportunity_id)){
		
			echo '<style>
			.action_buttons .button{
				margin-right: 10px;		
			}
			.yui-ac-content {
			    position: absolute;
			    border: 1px solid #808080;
			    width: auto;
			    background: #fff;
			    overflow: hidden;
			    z-index: 9050;
			}
			</style>';
			
			
			echo "<script type='text/javascript'>
			
			function return_to_project(){
				var parent_id = document.getElementById('parent_opportunity_id').value;
				if(parent_id !=''){
					location.href='index.php?module=Opportunities&action=DetailView&record='+parent_id+'&ClubbedView=1';
				}
				
			}
			
			if(document.getElementById('btn_contact_name'))
			document.getElementById('btn_contact_name').onclick = function(){
			var client_id = document.getElementById('account_id').value;		
			var popup_request_data = {
				'call_back_function' : 'set_contact_returns',
				'form_name' : 'EditView',
				'field_to_name_array' : {
				'id' : 'id',
				'name' : 'name',
				'account_id' : 'account_id',
				'account_name' : 'account_name',
				},
			};
			
			//if(client_id != ''){
				open_popup('Contacts', 600, 400, '&account_id='+client_id, true, false, popup_request_data);
			//	}else{
			//		alert('Please select Client first');
			//	return false;
			//	}
			}
			
			function set_contact_returns(popup_reply_data){
				var name_to_value_array = popup_reply_data.name_to_value_array;
				var id = name_to_value_array['id'];
				var contact = name_to_value_array['name'];
				var account_id = name_to_value_array['account_id'];
				var account_name = name_to_value_array['account_name'];				
				document.getElementById('contact_name').value=contact;
				document.getElementById('contact_id').value = id;
				document.getElementById('account_name').value=account_name;
				document.getElementById('account_id').value = account_id;
				//suggest assigned user
				suggestAssignedUser();
			}
					
					
			document.getElementById('btn_opportunity_name').onclick = function(){
				var opportunity_id = document.getElementById('parent_opportunity_id').value;		
				var popup_request_data = {
					'call_back_function' : 'set_parent_opportunity_returns',
					'form_name' : 'EditView',
					'field_to_name_array' : {
					'id' : 'id',
					'name' : 'name',
					'date_closed' : 'date_closed',
					'bid_due_timezone' : 'bid_due_timezone',
					'amount' : 'amount',
					'sales_stage' : 'sales_stage',
					'lead_source' : 'lead_source',
					'date_closed_tz' : 'date_closed_tz',
					'project_lead_id' : 'project_lead_id',
					},
				};
						
				open_popup( 'Opportunities',  600,  400,  '&parent_opportunity_only=true',  true,  false, popup_request_data, 'single', true );	
			}
					
			function set_parent_opportunity_returns(popup_reply_data){
				
				var name_to_value_array = popup_reply_data.name_to_value_array;
				var id = name_to_value_array['id'];
				var name = name_to_value_array['name'];
				var date_closed = name_to_value_array['date_closed'];
				var bid_due_timezone = name_to_value_array['bid_due_timezone'];
				var amount = name_to_value_array['amount'];
				var sales_stage = name_to_value_array['sales_stage'];
				var lead_source = name_to_value_array['lead_source'];
				var date_closed_tz = name_to_value_array['date_closed_tz'];
				var project_lead_id = name_to_value_array['project_lead_id']; 
									
				document.getElementById('parent_opportunity_id').value = id;
				document.getElementById('opportunity_name').value = name;
				document.getElementById('project_lead_id').value = project_lead_id;
 				
				/*
				if(confirm('Do you want to overwrite data with project opportunity data ?')) 
				*/{
					document.getElementById('name').value = name;
					document.getElementById('bid_due_timezone').value = bid_due_timezone;
					document.getElementById('amount').value = amount;
					
					var client_sales_stage_dom = SUGAR.language.languages['app_list_strings']['client_sales_stage_dom'];
					for (i in client_sales_stage_dom){
						if( client_sales_stage_dom[i] == sales_stage){
							document.getElementById('sales_stage').value = i;
						}
					}
	
					var lead_source_dom = SUGAR.language.languages['app_list_strings']['lead_source_dom'];
					for (i in lead_source_dom){
					   if(lead_source_dom[i] == lead_source){
					        document.getElementById('lead_source').value = i;
					   }
					}
					
					date_closed_tz = SUGAR.util.DateUtils.parse(date_closed_tz);
					date_closed_tz = SUGAR.util.DateUtils.formatDate(date_closed_tz, true);
					
					var combo_date_closed = new Datetimecombo(date_closed_tz, 'date_closed', '$time_format', 0, '', false, true);
					text = combo_date_closed.html();
					document.getElementById('date_closed_time_section').innerHTML = text;
					
					document.getElementById('date_closed').value = date_closed_tz;
					
				}
			}
			
			sqs_objects['EditView_opportunity_name'] = '';
			
			YAHOO.util.Event.onContentReady('parent_opportunity_id', function(){
	
			        var container1 = document.createElement('div');
			        container1.innerHTML = '';  
			        container1.id = 'parentOpportunityContainer';
					
					YAHOO.util.Dom.insertAfter(container1 ,YAHOO.util.Dom.get('opportunity_name'));                   
			            
			        YAHOO.example.classification = function() {
			        
			        // instantiate remote data source
			        var oDS = new YAHOO.util.XHRDataSource('index.php?'); 
			        oDS.connMethodPost = 1;
			        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON; 
			        oDS.responseSchema = {
								resultsList:'fields',
								total:'totalCount',
								fields:['name','id','date_closed','bid_due_timezone','amount','sales_stage','lead_source','date_closed_tz','project_lead_id'],
								metaNode:'fields',
								metaFields:{total:'totalCount',fields:'fields'}
					};
			        oDS.maxCacheEntries = 10;         
			    
			        var oAC = new YAHOO.widget.AutoComplete('opportunity_name', 'parentOpportunityContainer', oDS);
					oAC.resultTypeList = false;                
			        oAC.useShadow = true;
					
					var myHiddenField = YAHOO.util.Dom.get('parent_opportunity_id');
					var myHandler = function(sType, aArgs) {
							
							var myAC = aArgs[0]; // reference back to the AC instance 
							var elLI = aArgs[1]; // reference to the selected LI element
							var oData = aArgs[2]; // object literal of selected item's result data 
							// update hidden form field with the selected item's ID
							myHiddenField.value = oData.id;
							//filll additional fields
							YAHOO.util.Dom.get('name').value = oData.name;
							YAHOO.util.Dom.get('bid_due_timezone').value = oData.bid_due_timezone;
							YAHOO.util.Dom.get('amount').value = oData.amount;
							YAHOO.util.Dom.get('sales_stage').value = oData.sales_stage;
							YAHOO.util.Dom.get('lead_source').value = oData.lead_source;
							YAHOO.util.Dom.get('project_lead_id').value = oData.project_lead_id;
							
							var date_closed_tz = SUGAR.util.DateUtils.parse(oData.date_closed_tz);
							date_closed_tz = SUGAR.util.DateUtils.formatDate(date_closed_tz, true);
							
							var combo_date_closed = new Datetimecombo(date_closed_tz, 'date_closed', '$time_format', 0, '', false, true);
							text = combo_date_closed.html();
							document.getElementById('date_closed_time_section').innerHTML = text;
							
							YAHOO.util.Dom.get('date_closed').value = date_closed_tz;
					};
					
			        oAC.itemSelectEvent.subscribe(myHandler);			
					var opportunity_search = document.getElementById('opportunity_name').value;
							
			        oAC.generateRequest = function(sQuery) {
			        	return  'to_pdf=true&module=Opportunities&action=parentoppautofill&q='+sQuery+'&order=name&parent_opportunity_only=true&limit=30';
				    };          
			            
			        return {
			            oDS: oDS,
			            oAC: oAC,                    
			        };
			    }();
			});
			
			//suggest assigned user
			function suggestAssignedUser(){		
			    		
			    if(typeof document.EditView == 'undefined'){			    
			        EditView = 'form_DCQuickCreateSub_Opportunities';
			        document.EditView = document.form_DCQuickCreateSub_Opportunities;
			        
		        }else{
		            EditView = $(document.EditView).attr('name');		            
		        }
			    
				var client_id = document.EditView.account_id.value;
				var contact_id = document.EditView.contact_id.value;
				var parent_id = document.EditView.parent_opportunity_id.value;
				
				var callback = {
					success: function(o){
						//console.log(o);
						if(o.responseText != ''){
							var response = eval('('+o.responseText+')');
							if(response.id != ''){
								document.EditView.assigned_user_id.value = response.id;
							}
							if(response.name != ''){
								document.EditView.assigned_user_name.value = response.name;
							}
							// if(response.team_id != ''){
							    
								// document.getElementById('id_'+EditView+'_team_name_collection_0').value =response.team_id;
 							// }
 							// if(response.team_name != ''){
 			
 								// document.getElementById(EditView+'_team_name_collection_0').value = replaceHTMLChars(response.team_name);
 			
							// }
							//console.log(response);
						}
					}
				}	
				var connectionObject = YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Opportunities&action=assigneduser&to_pdf=true&client_id='+client_id+'&contact_id='+contact_id+'&parent_id='+parent_id, callback);
				
				
			}			
			//$('.yui-ac-bd').live('click', function(e){
				//setTimeout('suggestAssignedUser()',888);			
			//});
			
			
			</script>";
			
	
			
			//hirak : date : 12-10-2012
			global $db;
			$proposal_count_sql = " SELECT count(*)c, quotes.proposal_delivery_method, quotes.proposal_verified
					FROM quotes_opportunities
					INNER JOIN quotes ON quotes.id = quotes_opportunities.quote_id
					AND quotes.deleted = 0 
					WHERE quotes_opportunities.opportunity_id = '".$this->bean->id."' AND quotes_opportunities.deleted = 0 
					AND ( quotes.proposal_delivery_method = 'E' OR
							quotes.proposal_delivery_method = 'F' OR quotes.proposal_delivery_method = 'EF' )";
			$proposal_count_result = $db->query($proposal_count_sql);
			$proposal_count_row = $db->fetchByAssoc($proposal_count_result);
			$proposal_count = $proposal_count_row['c'];
			$proposal_verified = $proposal_count_row['proposal_verified'];
			
			
			if( ($proposal_count > 0 ) && ($proposal_verified != 1) ){
				
				echo "<script type='text/javascript'>
					document.getElementById('sales_stage').disabled  = true;
				</script>";
				
			}else if( ( $proposal_count > 0 ) && ($proposal_verified == 1) ){
				
				echo "<script type='text/javascript'>
						
					 document.getElementById('sales_stage').children[0].disabled='disabled';
					 document.getElementById('sales_stage').children[1].disabled='disabled';
					 document.getElementById('sales_stage').children[2].disabled='disabled';
					 //document.getElementById('sales_stage').children[3].disabled='disabled';
					 document.getElementById('sales_stage').children[6].disabled='disabled';
					 document.getElementById('sales_stage').children[8].disabled='disabled';
				
				</script>";
				
			}
			
			$proposal_count_sql = " SELECT count(*)c FROM quotes_opportunities
					WHERE quotes_opportunities.opportunity_id = '".$this->bean->id."'
							AND quotes_opportunities.deleted = 0 ";
			$proposal_count_result = $db->query($proposal_count_sql);
			$proposal_count_row = $db->fetchByAssoc($proposal_count_result);
			$proposal_count = $proposal_count_row['c'];
			
			if($proposal_count < 1){
				echo "<script type='text/javascript'>
			
					 document.getElementById('sales_stage').children[6].disabled='disabled';
					 document.getElementById('sales_stage').children[7].disabled='disabled';
					 document.getElementById('sales_stage').children[8].disabled='disabled';
			
				</script>";
			}
		
		}
		/**
		 * Override the contacts quick search [sqs] 
		 * to display only related contacts
		 * Added By : Ashutosh
		 * Date 16 Dec 2013 
		 */
echo <<<EQQ
                <script type='text/javascript'>
                clientId=  $('#account_id').val();
                sqs_objects['EditView_contact_name']={
                "form":"EditView",
                "method":"get_default_contact_array",
                "modules":["Contacts"],
                "field_list":["salutation","first_name","last_name","id"],
                "populate_list":['contact_name','contact_id','contact_id','contact_id'],
                "required_list":["contact_id"],
                "group":"or",
                "conditions":[
                {"name":"first_name",
                "op":"like_custom",
                "end":"%",
                "value":""
                },
                {"name":"last_name",
                "op":"like_custom",
                "end":"%",
                "value":""
                }
		        ,
                {"name":"salutation",
                "op":"like_custom",
                "end":"%",
                "value":""
                }
                ],
                "order":"last_name",
                "limit":"30",
                "no_match_text":"No Match",
                "account_contact_id" : clientId,
				"post_onblur_function" : "suggestAssignedUser();",
                }
SUGAR.util.doWhen("typeof(sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name']) != 'undefined'",function(){		        
 sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name']['method'] = "get_default_contact_array";

 sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name'] ["conditions"] = [{"name":"first_name", "op":"like_custom","end":"%","value":""
                },
                {"name":"last_name",
                "op":"like_custom",
                "end":"%",
                "value":""
                } ,
                {"name":"salutation",
                "op":"like_custom",
                "end":"%",
                "value":""
                }
                ];
		sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name']['account_contact_id'] = $('#account_id').val();
 sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name']['post_onblur_function'] = "suggestAssignedUser();";
});

SUGAR.util.doWhen("typeof(sqs_objects['form_EmailQCView_Opportunities_contact_name']) != 'undefined'",function(){
 sqs_objects['form_EmailQCView_Opportunities_contact_name']['method'] = "get_default_contact_array";
 sqs_objects['form_EmailQCView_Opportunities_contact_name']["conditions"] = [{"name":"first_name", "op":"like_custom","end":"%","value":""
                },
                {"name":"last_name",
                "op":"like_custom",
                "end":"%",
                "value":""
                } ,
                {"name":"salutation",
                "op":"like_custom",
                "end":"%",
                "value":""
                }
                ];

});
				        
		        
//function to fill default client contact		        
function getAccountsDefualtContact(a_id){
    var a_id = document.EditView.account_id.value;
    var reqData = new Object();	    
    var a_id = (typeof a_id == 'undefined')?a_id:$('#account_id').val()	        
    reqData.url = 'index.php?module=Accounts&action=default_contacts&to_pdf=true&account_id='+a_id;
    reqData.async = false;		            
    ajaxStatus.showStatus('Loading ...');   		        
    reqData.complete= function(resp,data){		        
      aResp = JSON.parse(resp.responseText);
      $('#contact_id').val(aResp.contact_id)
      $('#contact_name').val(aResp.contact_name);
      ajaxStatus.hideStatus();	        
    }
    
    $.ajax(reqData).done(function(){suggestAssignedUser()});
    //update client id of sqs object of client contact for edit view	
	if( typeof sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name'] != 'undefined' )
    sqs_objects['form_DCQuickCreateSub_Opportunities_contact_name']['account_contact_id'] = a_id;
	
	//update client id of sqs object of client contact for quick create view
    if( typeof sqs_objects['EditView_contact_name'] != 'undefined' )
    sqs_objects['EditView_contact_name']['account_contact_id'] = a_id;
}
		       
$(document).ready(function(){
    if( typeof sqs_objects['form_DCQuickCreateSub_Opportunities_account_name'] != 'undefined' )
    sqs_objects['form_DCQuickCreateSub_Opportunities_account_name']['post_onblur_function'] = 'getAccountsDefualtContact();';
    if( typeof sqs_objects['EditView_account_name'] != 'undefined' )
    sqs_objects['EditView_account_name']['post_onblur_function'] = 'getAccountsDefualtContact();';
	
	if( typeof sqs_objects['form_DCQuickCreateSub_Opportunities_assigned_user_name'] != 'undefined' )
    sqs_objects['form_DCQuickCreateSub_Opportunities_assigned_user_name']['post_onblur_function'] = 'set_return_oppassigneduser();';
    if( typeof sqs_objects['EditView_assigned_user_name'] != 'undefined' )
    sqs_objects['EditView_assigned_user_name']['post_onblur_function'] = 'set_return_oppassigneduser();';
	if( typeof sqs_objects['form_DCQuickCreate_Opportunities_assigned_user_name'] != 'undefined' )
    sqs_objects['form_DCQuickCreate_Opportunities_assigned_user_name']['post_onblur_function'] = 'set_return_oppassigneduser();';		
});	

//function to set private team on change of assigend user
function set_return_oppassigneduser(popup_reply_data){
	if(typeof document.EditView == 'undefined'){
		if(typeof document.form_DCQuickCreateSub_Opportunities == 'undefined'){
			EditView = 'form_DCQuickCreate_Opportunities';
			document.EditView = document.form_DCQuickCreate_Opportunities;
		} else {
			EditView = 'form_DCQuickCreateSub_Opportunities';
			document.EditView = document.form_DCQuickCreateSub_Opportunities;
		}			    					       
	}else{
	    EditView = $(document.EditView).attr('name');		            
	}
	//if callback function called from popup then name_to_value_array have object value
	//else if callback function called from SQS it is have undefined value
	if(typeof popup_reply_data.name_to_value_array != 'undefined'){
		set_return(popup_reply_data);
		var assignUserId = popup_reply_data.name_to_value_array.assigned_user_id;
	} else {
		var assignUserId = document.EditView.assigned_user_id.value;
	} 
	if(assignUserId !='undefined'){        
		jQuery.ajax({
	        type: "POST",
	     	url: "index.php?module=Opportunities&action=userprivateteam&to_pdf=true",
	        data: {assigned_user_id: assignUserId},
			dataType: "json",
	       	cache: false,
	        success: function (resonseData) {                
                var teamId = resonseData.team_id;
                var teamName = resonseData.team_name;
                var parent_opp_id = '$parent_opp_id_team';
                        
                /*               
                 * Auto Update Team Name and primary team radio button when Re-Assigning Project Opportunity BSI-783
                 * Modified by Mohit Kumar Gupta 16-10-2015             
                */        
                //if it is a client opportunity        
                if(parent_opp_id !=''){
	                document.getElementById('id_'+EditView+'_team_name_collection_0').value = teamId;
				    document.getElementById(EditView+'_team_name_collection_0').value = teamName;
	            } else {
                    //if it is a project opportunity                        
	                var createNewTeam = true;
                    var elementIndex = '';
                    //select all input fields which have the name start with 'team_name_collection_'
                    $('input[name^=team_name_collection_]').each(
                    	function(idx,elm){
                            //ignore the fields of mass update form on quick edit form
                            if (elm.id.search('MassUpdate_team_name_collection_') == '-1'){
    	                        var elementNameArr = elm.name.split("team_name_collection_");
                                elementIndex = elementNameArr[1];
                                if(document.getElementById('id_'+elm.id).value == teamId) {                           
                                   createNewTeam = false;
        	                       return false;
        	                    } 
    	                    }                                              
                    	}
                    );
            
                    //If selected user team does not exists in displayed team then create a new text box with new team
                    if(createNewTeam) {
    	               if(elementIndex != ''){
                            collection[EditView+'_team_name'].add(); 
                            if(collection[EditView+'_team_name'].more_status){
                                collection[EditView+'_team_name'].js_more();
    	                    }
                            elementIndex = ++elementIndex;
                            document.getElementById('id_'+EditView+'_team_name_collection_'+elementIndex).value = teamId;
    				        document.getElementById(EditView+'_team_name_collection_'+elementIndex).value = teamName;
    	               }
    	            }
    
                    //Change the primary team radio selection to that team
                    if(elementIndex !='') {
                        $("#primary_team_name_collection_"+elementIndex).prop("checked", true);
    	            }
	            }                                         
	        }
    	});	
	}	
}
</script>
EQQ;
	}

}

?>
