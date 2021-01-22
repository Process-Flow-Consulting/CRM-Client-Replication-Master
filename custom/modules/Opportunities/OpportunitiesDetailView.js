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
	ajaxStatus.showStatus('Deleting...');
	$.ajax({
		type: "POST",
		url: "index.php?module=Opportunities&action=deleterelateddata&to_pdf=true",
		data: {'opportunity_id': this.opportunity_id, 'type' : this.type},
	}).done( function(msg){
		ajaxStatus.showStatus(msg);
		location.href='index.php?module=Opportunities&action=index';
	});
	return false;
}

var project_opportunity_delete_warning = SUGAR.language.get('Opportunities', 'LBL_WARNING_PROJECT_DELETE'); 
var client_opportunity_delete_warning = SUGAR.language.get('Opportunities', 'LBL_WARNING_CLIENT_DELETE'); 

function deleteOpportunity(opportunity_id, type){
	mySimpleDialog = getSimpleDialog(); //show dialog
	if( type == 'Project'){
		mySimpleDialog.setBody(project_opportunity_delete_warning);
	}else{
		mySimpleDialog.setBody(client_opportunity_delete_warning);
	}
	mySimpleDialog.type = type;
	mySimpleDialog.opportunity_id = opportunity_id;
	
	var myButtons = [
	{ text: 'OK', handler: handleYesQCVerify },
	{ text: 'Cancel', handler: handleCancel }    
	];
	mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
	mySimpleDialog.render(document.body);    
	mySimpleDialog.show();
	return false;
}