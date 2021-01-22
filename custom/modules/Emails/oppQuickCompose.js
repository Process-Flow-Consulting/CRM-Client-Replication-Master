
SUGAR.util.doWhen("typeof(sqs_objects['form_EmailQCView_Opportunities_account_name']) != 'undefined'",function(){
	sqs_objects['form_EmailQCView_Opportunities_account_name']['post_onblur_function'] = 'getAccountsDefualtContact';
});

SUGAR.util.doWhen("typeof(sqs_objects['form_EmailQCView_Opportunities_contact_name']) != 'undefined'",function(){
	sqs_objects['form_EmailQCView_Opportunities_contact_name'].method= 'get_default_contact_array';
	sqs_objects['form_EmailQCView_Opportunities_contact_name']["post_onblur_function"]= 'suggestAssignedUser';
	sqs_objects['form_EmailQCView_Opportunities_contact_name']["conditions"] =[ {"name":"first_name",
                "op":"contains",
                "end":"%",
                "value":""
                },
                {"name":"last_name",
                "op":"contains",
                "end":"%",
                "value":""
                }
		        ,
                {"name":"salutation",
                "op":"contains",
                "end":"%",
                "value":""
                }
                ];
});


if(document.form_EmailQCView_Opportunities != 'undefined' && $('input[name=record]').val()==''){	

   $('#lead_state,#county,#lead_zip_code,#lead_union_c,#lead_non_union,#lead_prevailing_wage,#lead_type').on(
    'change',
    checkAssignedUser);	
    
    $('#county_div').on('change',function(){
		$('#county').on('change',checkAssignedUser)
    });
}

function getAccountsDefualtContact(a_id){
    
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

    if( typeof sqs_objects['form_EmailQCView_Opportunities_contact_name'] != 'undefined' )
    sqs_objects['form_EmailQCView_Opportunities_contact_name']['account_contact_id'] = $('#account_id').val();
}

//suggest assigned user
function suggestAssignedUser(){		
     EditView = 'form_EmailQCView_Opportunities';
     document.EditView = document.form_EmailQCView_Opportunities;    
	var client_id = $('#account_id').val();
	var contact_id = $('#contact_id').val();
	var parent_id = $('#parent_opportunity_id').val();
	
	var callback = {
		success: function(o){
			//console.log(o);
			if(o.responseText != ''){
				var response = eval('('+o.responseText+')');
				
				//if it is 1 then coming from client opportunity else 0 then coming from project opportunity
				var parent_id_len = $('#parent_opportunity_id').length;
				if(response.id != ''){
					if(parent_id_len != 1){
						document.EditView.cop_assigned_user_id.value = response.id;
					} else {
						document.EditView.assigned_user_id.value = response.id;
					}
				}
				if(response.name != ''){
					if(parent_id_len != 1){
						document.EditView.cop_assigned_user_name.value = response.name;
					} else {
						document.EditView.assigned_user_name.value = response.name;
					}
				}
				if(response.team_id != ''){
					if(parent_id_len != 1){
						document.getElementById('id_'+EditView+'_copTeamSet_collection_0').value =response.team_id;
					} else {
						document.getElementById('id_'+EditView+'_team_name_collection_0').value =response.team_id;
					}					
				}
				if(response.team_name != ''){
					if(parent_id_len != 1){
						document.getElementById(EditView+'_copTeamSet_collection_0').value = replaceHTMLChars(response.team_name);
					} else {
						document.getElementById(EditView+'_team_name_collection_0').value = replaceHTMLChars(response.team_name);
					}					
				}
				//console.log(response);
			}
		}
	}	
	var connectionObject = YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Opportunities&action=assigneduser&to_pdf=true&client_id='+client_id+'&contact_id='+contact_id+'&parent_id='+parent_id, callback);
	
	
}			
//$('.yui-ac-bd').live('click', function(e){
//	setTimeout('suggestAssignedUser()',888);
					
//});

 function checkAssignedUser(){  
	  $.ajax({ url:'index.php?module=Opportunities&action=assigneduser&for_project_opp=1&to_pdf=1',
		       type : "POST",
		       data : $('#lead_state,#county,#lead_zip_code,#lead_union_c,#lead_non_union,#lead_prevailing_wage,#lead_type').serialize(),
		       beforeSend: function(){
				   ajaxStatus.showStatus('Loading...')
			   },
	           complete:function (response){
				   res = JSON.parse(response.responseText);
				   var pre_user =  $('#assigned_user_name').val()
				   if(typeof(res.id) != 'undefined'){
				       $('#assigned_user_name').val(res.name);
				       $('#assigned_user_id').val(res.id);
				       $('input[id^=form_EmailQCView_Opportunities_team_name_collection]').val(res.team_name);
				       $('input[id^=id_form_EmailQCView_Opportunities_team_name_collection]').val(res.team_id);
				       //Set assigned user id and name for all clients on clients to opportunity conversion screen start
				       //@modified By Mohit Kumar Gupta
				       //@date 09-dec-2013				     
				       if($('input[name=action]').val() == 'save_accounts_opportunity') {
				    	   var assignee_name = YAHOO.util.Selector.query('input[id^=assigned_user_name_]');
							for (var i = 0; i < assignee_name.length; i++){
								assignee_name[i].value = res.name;
							}
						
							var assignee_id = YAHOO.util.Selector.query('input[id^=assigned_user_id_]');
							for (var i = 0; i < assignee_id.length; i++){
								assignee_id[i].value = res.id;
							}
				       }
				       //Set assigned user id and name for all clients on clients to opportunity conversion screen end
			       }
				   if(typeof(res.id) != 'undefined' && pre_user != res.name){
					   clearTimeout(timeOut);
					   ajaxStatus.showStatus('User Assignment Changed to '+res.name)
					   timeOut = setTimeout(' ajaxStatus.hideStatus()',2000);
			       }else{
						ajaxStatus.hideStatus()
				   }
			   }
		  });
	}

//function to set private team on change of assigend user
 function set_return_oppassigneduser(popup_reply_data){
	EditView = 'form_EmailQCView_Opportunities';
	document.EditView = document.form_EmailQCView_Opportunities;
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
     			document.getElementById('id_'+EditView+'_team_name_collection_0').value =resonseData.team_id;
 				document.getElementById(EditView+'_team_name_collection_0').value = resonseData.team_name; 
 	        }
     	});	
 	}	
 }

//function to set private team on change of assigend user
 function set_return_copassigneduser(popup_reply_data){
	 EditView = 'form_EmailQCView_Opportunities';
	 document.EditView = document.form_EmailQCView_Opportunities;
 	//if callback function called from popup then name_to_value_array have object value
 	//else if callback function called from SQS it is have undefined value
 	if(typeof popup_reply_data.name_to_value_array != 'undefined'){
 		set_return(popup_reply_data);
 		var assignUserId = popup_reply_data.name_to_value_array.cop_assigned_user_id;
 	} else {
 		var assignUserId = document.EditView.cop_assigned_user_id.value;
 	} 
 	if(assignUserId !='undefined'){
 		jQuery.ajax({
 	        type: "POST",
 	     	url: "index.php?module=Opportunities&action=userprivateteam&to_pdf=true",
 	        data: {assigned_user_id: assignUserId},
 			dataType: "json",
 	       	cache: false,
 	        success: function (resonseData) { 	        	
     			document.getElementById('id_'+EditView+'_copTeamSet_collection_0').value =resonseData.team_id;
 				document.getElementById(EditView+'_copTeamSet_collection_0').value = resonseData.team_name; 
 	        }
     	});	
 	}	
 }
 SUGAR.util.doWhen("typeof(sqs_objects['form_EmailQCView_Opportunities_cop_assigned_user_name']) != 'undefined'",enableQS);
 SUGAR.util.doWhen("typeof(sqs_objects['form_EmailQCView_Opportunities_assigned_user_name']) != 'undefined'",function(){sqs_objects['form_EmailQCView_Opportunities_assigned_user_name']['post_onblur_function'] = 'set_return_oppassigneduser';});
 sqs_objects['form_EmailQCView_Opportunities_cop_assigned_user_name'] = {
    "form": "form_EmailQCView_Opportunities",
    "method": "get_user_array",
    "field_list": ["user_name", "id"],
    "populate_list": ["cop_assigned_user_name", "cop_assigned_user_id"],
    "required_list": ["cop_assigned_user_id"],
    "conditions": [{
        "name": "user_name",
        "op": "like_custom",
        "end": "%",
        "value": ""
    }],
    "limit": "30",
    "no_match_text": "No Match",
    "lead_reviewer": "false",
    "post_onblur_function":"set_return_copassigneduser"
};