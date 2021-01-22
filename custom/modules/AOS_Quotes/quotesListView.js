var submitted = false;
var handleCancel = function(){
 	this.hide();	
}	

var handleYesQCVerify = function(){

	if(!submitted){
	submitted = true;
	
	}else{
		//do not submit again
		return false;
	}
	if( (typeof(this.proposal_id) != 'undefined') && (this.proposal_id != '')
			|| (typeof(this.selected_status) != 'undefined') && (this.selected_status != '') ){
		var redirect_url = 'index.php?module=Opportunities&action=verifyproposal&uid='+this.proposal_id+'&status='+this.selected_status+'&return_module=Quotes';
		window.location.href = redirect_url;
		return false;
	}
	
	isDifferentProject();
	return false;
}

var handleYesProjectVerify = function(){
	if(!submitted){
		submitted = true;
	}else{
		//do not submit again
		return false;
	}
	document.MassUpdate.submit();
	return false;
}	


var un_verify_message = SUGAR.language.get('Quotes', 'LBL_UN_VERIFY_MESSAGE');
var pending_verify_message = SUGAR.language.get('Quotes', 'LBL_PENDING_VERIFY_MESSAGE');
var all_verified_message = SUGAR.language.get('Quotes', 'LBL_ALL_VERIFIED_MESSAGE');
var different_project_message = SUGAR.language.get('Quotes', 'LBL_DIFFERENT_PROJECT_MESSAGE');
var group_verify_messgage = SUGAR.language.get('Quotes', 'LBL_GROUP_VERIFY_MESSAGE');

var single_un_verify_message = SUGAR.language.get('Quotes', 'LBL_SINGLE_UN_VERIFY_MESSAGE');
var single_pending_verify_message = SUGAR.language.get('Quotes', 'LBL_SINGLE_PENDING_VERIFY_MESSAGE');

var mySimpleDialog ='';

function getSimpleDialog(){
	
	if (typeof(mySimpleDialog) != 'undefined' && mySimpleDialog != ''){
		mySimpleDialog.destroy(); 
	}
	mySimpleDialog = new YAHOO.widget.SimpleDialog('dlg', { 
	    width: '40em', 
	    effect:{
	        effect: YAHOO.widget.ContainerEffect.FADE,
	        duration: 0.25
	    }, 
	    fixedcenter: true,
	    modal: true,
	    visible: false,
	    draggable: false
	});
    
	mySimpleDialog.setHeader('Warning!');
	mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
	return mySimpleDialog;

}

/**
 * @to verify proposals on Listview
 * 
 */
function verifySelectedProposals(){
	
	submitted = false;
	var checkedItems = new Array();
	var checkedCount = 0;
	var theForm = document.MassUpdate;
	inputs_array = theForm.elements;
	
	for(var wp = 0 ; wp < inputs_array.length; wp++) {
		if(inputs_array[wp].name == "mass[]") {
			if(inputs_array[wp].checked == true){

				var checkedItem  = inputs_array[wp].value;
				var checked_status = document.getElementById(checkedItem+'_status').value;
				
				if(checkedCount == 0){
					var selected_status = checked_status;
				}
				
				if(selected_status != checked_status){

					mySimpleDialog = getSimpleDialog(); 
					mySimpleDialog.setBody(group_verify_messgage);
					
				    var myButtons = [
				    { text: 'OK', handler: handleCancel }    
				    ];
				    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
				    mySimpleDialog.render(document.body);    
				    mySimpleDialog.show();
					return false;
					
				}else{
					checkedItems[checkedCount] = checkedItem;
					checkedCount++;
				}
			}
		}
	}
	
	if( (checkedCount > 0) 
			&&  ( (typeof(selected_status) != 'undefined') ||  (selected_status != ''))){

		if( selected_status == 'u' || selected_status == 'p'){

			theForm.uid.value = checkedItems.join(',');
			var statusInput = document.createElement('input');
			statusInput.name = 'status';
			statusInput.type = 'hidden';
			statusInput.value = 'index';
			document.MassUpdate.appendChild(statusInput);
			theForm.status.value = selected_status;
			theForm.action.value = 'verifyproposal';
			theForm.module.value = 'Opportunities';
			
			mySimpleDialog = getSimpleDialog(); 
			
			if( selected_status == 'u'){
				mySimpleDialog.setBody(un_verify_message); //show unverified messaage
			}else{
				mySimpleDialog.setBody(pending_verify_message); //show messaage
			}
			
		    var myButtons = [
		    { text: 'OK', handler: handleYesQCVerify },
		    { text: 'Cancel', handler: handleCancel }	    
		    ];
		    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
			return false;
			
		}else if(selected_status == 'v'){
			
			mySimpleDialog = getSimpleDialog(); 
			
			mySimpleDialog.setBody(all_verified_message); //show verified messaage
			
		    var myButtons = [
		    { text: 'OK', handler: handleCancel },	    
		    ];
		    
		    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
			return false;
			
		}else{
			
			return false;
		}
	} else if( checkedCount < 1 ){
		alert("Please select at least one record.");
		return false;
	}
}

/**
 * @to verifify single proposal (inline)
 * @param proposal id - proposal id to be verified
 * @return void
 */
function verifyProposal(proposal_id){
	
	submitted = false;
	var selected_status = document.getElementById(proposal_id+'_status').value;

	if( selected_status == 'u' || selected_status == 'p'){
		
		mySimpleDialog = getSimpleDialog(); //show dialog
		
		if( selected_status == 'u'){
			mySimpleDialog.setBody(single_un_verify_message); //show unverified messaage
		}else{
			mySimpleDialog.setBody(single_pending_verify_message); //show messaage
		}
		
		mySimpleDialog.selected_status = selected_status;
		mySimpleDialog.proposal_id = proposal_id;
		
	    var myButtons = [
	    { text: 'OK', handler: handleYesQCVerify },
	    { text: 'Cancel', handler: handleCancel }    
	    ];
	    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
	    mySimpleDialog.render(document.body);    
	    mySimpleDialog.show();
		return false;
		
	}else{		
		return false;
	}
}

/**
 * @to verify if selected Proposal belongs to different projects
 * @return boolean
 */
function isDifferentProject(){
	
	var select_entire_list  = document.MassUpdate.select_entire_list.value;
	var uids =  document.MassUpdate.uid.value;
	var URL = "index.php?module=Quotes&action=checkrelatedproject&to_pdf=1";
	var dataObj = {};
	
	if(select_entire_list == '1'){
		dataObj['entire_list'] = '1';
	}else{
		dataObj['uid'] = uids;
	}
	
	$.ajax({
		type: "POST",
		url: URL,
		data: dataObj,
	}).done( function(msg){
		
		if(msg == 0){
			submitted = false;
			mySimpleDialog = getSimpleDialog(); //show dialog
			mySimpleDialog.setBody(different_project_message); //show messaage
			
		    var myButtons = [
		    { text: 'OK', handler: handleYesProjectVerify },
		    { text: 'Cancel', handler: handleCancel }    
		    ];
		    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
			return false;
			
		}else{
			document.MassUpdate.submit();
			return false;
		}
	});
}