$(document).ready(function(){
	
	//check if it is create new Project Opportunity 		
	if($('#account_name').length == 0 && $('input[name=record]').val()==''){	
	   $('#lead_state,#county,#lead_zip_code,#lead_union_c,#lead_non_union,#lead_prevailing_wage,#lead_type').on(
	    'change',
	    checkAssignedUser);	
	    
	    $('#county_div').on('change',function(){
			$('#county').on('change',checkAssignedUser)
	    });
    }
       
    
    var timeOut;
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
				       $('input[id^=EditView_team_name_collection]').val(res.team_name);
				       $('input[id^=id_EditView_team_name_collection]').val(res.team_id);
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
	     	
});

function set_return_oppaccounts(popup_reply_data){
    set_return(popup_reply_data);    
    getAccountsDefualtContact(popup_reply_data.name_to_value_array.account_id)  ;              
}
