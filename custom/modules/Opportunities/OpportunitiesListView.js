var mySimpleDialogDelete ='';
function getSimpleDialogDelete(){
	if (typeof(mySimpleDialogDelete) != 'undefined' && mySimpleDialogDelete != ''){
		mySimpleDialogDelete.destroy(); 
	}
	mySimpleDialogDelete = new YAHOO.widget.SimpleDialog('dlg', { 
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
    
	mySimpleDialogDelete.setHeader('Warning!');
	mySimpleDialogDelete.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
	return mySimpleDialogDelete;
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
	
	document.MassUpdate.action.value =  'deleterelateddata';
	document.MassUpdate.submit();
	return false;
}

var project_opportunity_delete_warning = SUGAR.language.get('Opportunities', 'LBL_DELETE_LIST_VIEW');
var project_opportunity_selected = SUGAR.language.get('Opportunities', 'LBL_SELECTED_DELETE_LIST_VIEW'); 

function deleteOpportunities(mode, no_record_txt, del){
	
	formValid = check_form('MassUpdate');
	if(!formValid && !del) return false;

	if (document.MassUpdate.select_entire_list &&
		document.MassUpdate.select_entire_list.value == 1)
		mode = 'entire';
	else
		mode = 'selected';

	var ar = new Array();

	switch(mode) {
		case 'selected':
			for(var wp = 0; wp < document.MassUpdate.elements.length; wp++) {
				var reg_for_existing_uid = new RegExp('^'+RegExp.escape(document.MassUpdate.elements[wp].value)+'[\s]*,|,[\s]*'+RegExp.escape(document.MassUpdate.elements[wp].value)+'[\s]*,|,[\s]*'+RegExp.escape(document.MassUpdate.elements[wp].value)+'$|^'+RegExp.escape(document.MassUpdate.elements[wp].value)+'$');
				//when the uid is already in document.MassUpdate.uid.value, we should not add it to ar.
				if(typeof document.MassUpdate.elements[wp].name != 'undefined'
					&& document.MassUpdate.elements[wp].name == 'mass[]'
						&& document.MassUpdate.elements[wp].checked
						&& !reg_for_existing_uid.test(document.MassUpdate.uid.value)) {
							ar.push(document.MassUpdate.elements[wp].value);
				}
			}
			if(document.MassUpdate.uid.value != '') document.MassUpdate.uid.value += ',';
			document.MassUpdate.uid.value += ar.join(',');
			if(document.MassUpdate.uid.value == '') {
				alert(no_record_txt);
				return false;
			}
			if(typeof(current_admin_id)!='undefined' && document.MassUpdate.module!= 'undefined' && document.MassUpdate.module.value == 'Users' && (document.MassUpdate.is_admin.value!='' || document.MassUpdate.status.value!='')) {
				var reg_for_current_admin_id = new RegExp('^'+current_admin_id+'[\s]*,|,[\s]*'+current_admin_id+'[\s]*,|,[\s]*'+current_admin_id+'$|^'+current_admin_id+'$');
				if(reg_for_current_admin_id.test(document.MassUpdate.uid.value)) {
					//if current user is admin, we should not allow massupdate the user_type and status of himself
					alert(SUGAR.language.get('Users','LBL_LAST_ADMIN_NOTICE'));
					return false;
				}
			}
			break;
		case 'entire':
			var entireInput = document.createElement('input');
			entireInput.name = 'entire';
			entireInput.type = 'hidden';
			entireInput.value = 'index';
			document.MassUpdate.appendChild(entireInput);
			//confirm(no_record_txt);
			if(document.MassUpdate.module!= 'undefined' && document.MassUpdate.module.value == 'Users' && (document.MassUpdate.is_admin.value!='' || document.MassUpdate.status.value!='')) {
				alert(SUGAR.language.get('Users','LBL_LAST_ADMIN_NOTICE'));
				return false;
			}
			break;
	}
	
	mySimpleDialogDelete = getSimpleDialogDelete(); //show dialog
	mySimpleDialogDelete.setBody(project_opportunity_delete_warning+ sugarListView.get_num_selected() + project_opportunity_selected);
	mySimpleDialogDelete.type = 'Project';
	
	var myButtons = [
	{ text: 'OK', handler: handleYesQCVerify },
	{ text: 'Cancel', handler: handleCancel }    
	];
	mySimpleDialogDelete.cfg.queueProperty('buttons', myButtons);  
	mySimpleDialogDelete.render(document.body);    
	mySimpleDialogDelete.show();
	return false;
}