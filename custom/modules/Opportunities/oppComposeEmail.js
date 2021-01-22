function removeDivNode(obj,emailName){
	
	//$(obj).parent().parent().remove();
	$(obj).closest('div.vR').remove();
	
	//clear the removed email from to address field start
	var tt = $('#addressTO0').val().split(',');
	var ref_ar= new Array();
	for(idx in tt){
		if(tt[idx] != emailName){
			ref_ar[idx] = tt[idx];
		}
	}
	$('#addressTO0').val(ref_ar.join(','));
	//clear the removed email from to address field end
	
	if($('.vR').length == 0){
		SUGAR.quickCompose.parentPanel.hide();
		
	}
}

function openComposeEmail(){
   overLoadMethods();
   var uid= '';
   var opid = '';   
   //validate if atleast one item is checked
   if($('input[name^=mass_email]:checked').length ==0){
	   alert(SUGAR.language.get('app_strings','LBL_LISTVIEW_NO_SELECTED'))
	   return;
   }   
   //validate not should be more than 20
   if($('input[name^=mass_email]:checked').length >20){
	   alert(SUGAR.language.get('Opportunities','LBL_MAX_RECORD'))
	   return;
   }   
   $('input[name^=mass_email]:checked').each(function(i,e){
	   //change for making checkbox selection available for mass update feature
	   if($(e).attr('notSet') != 'notset'){
		   uid += $(e).val()+',';
		   opid+=$(e).attr('oppval')+',';
	   }	   
   });
   //Grab the Quick Compose package for the listview    
    var callback =
        { cache:false,
          success: function(o) {
              var resp = YAHOO.lang.JSON.parse(o.responseText);
              var quickComposePackage = new Object();
              quickComposePackage.composePackage = resp;
              quickComposePackage.composePackage.parent_type  = 'ClientOpportunities';
              quickComposePackage.composePackage.parent_name  = $('input[name=opportunity_name]:first').val();
              quickComposePackage.composePackage.parent_id = o.responseText;//$('input[name=record]:first').val();                           
              quickComposePackage.fullComposeUrl = 'index.php?module=Emails&action=Compose&ListView=true' +
                                                   '&uid='+uid+'&action_module=Accounts';
              SUGAR.quickCompose.init(quickComposePackage);
          }
        }
        YAHOO.util.Connect.asyncRequest('POST','index.php?module=Emails&action=Compose&uid='+uid+'&action_module=Accounts&ajaxCall=true&ListView=true&to_pdf=true&forQuickCreateOppMails=true', callback,null);	
}
function overLoadMethods(){
SUGAR.util.doWhen("typeof SE != 'undefined' && typeof SE.templates != 'undefined'", function(){
SE.templates['compose'] = '<div id="composeLayout{idx}" class="ylayout-inactive-content"></div>' +
'<div id="composeOverFrame{idx}" style="height:100%;width:100%">' +
'	<form id="emailCompose{idx}" name="ComposeEditView{idx}" action="index.php" method="POST">' +
'		<input type="hidden" id="email_id{idx}" name="email_id" value="">' +
'		<input type="hidden" id="uid{idx}" name="uid" value="">' +
'		<input type="hidden" id="ieId{idx}" name="ieId" value="">' +
'		<input type="hidden" id="mbox{idx}" name="mbox" value="">' +
'		<input type="hidden" id="type{idx}" name="type" value="">' +
'		<input type="hidden" id="composeLayoutId" name="composeLayoutId" value="shouldNotSeeMe">' +
'		<input type="hidden" id="composeType" name="composeType">' +
'		<input type="hidden" id="fromAccount" name="fromAccount">' +
'		<input type="hidden" id="sendSubject" name="sendSubject">' +
'		<input type="hidden" id="sendDescription" name="sendDescription">' +
'		<input type="hidden" id="sendTo" name="sendTo">' +
'		<input type="hidden" id="sendBcc" name="sendBcc">' +
'		<input type="hidden" id="sendCc" name="sendCc">' +
'		<input type="hidden" id="setEditor" name="setEditor">' +
'		<input type="hidden" id="selectedTeam" name="selectedTeam">' +
'		<input type="hidden" id="teamIds" name="teamIds">' +
'		<input type="hidden" id="primaryteam" name="primaryteam">' +
'		<input type="hidden" id="saveToSugar" name="saveToSugar">' +
'		<input type="hidden" id="parent_id" name="parent_id">' +
'		<input type="hidden" id="parent_type" name="parent_type">' +
'		<input type="hidden" id="attachments" name="attachments">' +
'		<input type="hidden" id="documents" name="documents">' +
'		<input type="hidden" id="outbound_email{idx}" name="outbound_email">' +
'		<input type="hidden" id="templateAttachments" name="templateAttachments">' +
'		<input type="hidden" id="templateAttachmentsRemove{idx}" name="templateAttachmentsRemove">' +
'		<table id="composeHeaderTable{idx}" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">' +
'			<tr>' +
'				<th><table cellpadding="0" cellspacing="0" border="0"><tbody><tr ><td style="padding: 0px !important;margin:0px; !important" >' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.sendEmail({idx}, false);"><img src="index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=icon_email_send.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_SEND}</button>' +
//'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.saveDraft({idx}, false);"><img src="index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=icon_email_save.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_SAVE_DRAFT}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.showAttachmentPanel({idx}, false);"><img src="index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=icon_email_attach.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_ATTACHMENT}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.showOptionsPanel({idx}, false);"><img src="index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=icon_email_options.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_OPTIONS}</button>' +
'</td><td style="display:none;padding: 0px !important;margin:0px; !important">&nbsp;&nbsp;{mod_strings.LBL_EMAIL_RELATE}:&nbsp;&nbsp;<select class="select" id="data_parent_type{idx}" onchange="document.getElementById(\'data_parent_name{idx}\').value=\'\';document.getElementById(\'data_parent_id{idx}\').value=\'\'; SUGAR.email2.composeLayout.enableQuickSearchRelate(\'{idx}\');" name="data_parent_type{idx}">{linkbeans_options}</select>' + 
'&nbsp;</td><td style="display:none;padding: 0px !important;margin:0px; !important"><input id="data_parent_id{idx}" name="data_parent_id{idx}" type="hidden" value="">' +
'<input class="sqsEnabled" id="data_parent_name{idx}" name="data_parent_name{idx}" type="text" value="">&nbsp;<button type="button" class="button" onclick="SUGAR.email2.composeLayout.callopenpopupForEmail2({idx});"><img src="index.php?entryPoint=getImage&themeName=default&imageName=id-ff-select.png" align="absmiddle" border="0"></button>' +
'			</td></tr></tbody></table></th>'     +
'			</tr>' +
'			<tr>' +
'				<td>' +
'					<div style="margin:5px;">' +
'					<table cellpadding="4" cellspacing="0" border="0" width="100%">' +
'						<tr>' +
'							<td class="emailUILabel" NOWRAP >' +
'								<label for="addressFrom{idx}">{app_strings.LBL_EMAIL_FROM}:</label>' +
'							</td>' +
'							<td class="emailUIField" NOWRAP>' +
'								<div>' +
'									&nbsp;&nbsp;<select style="width: 500px;" class="ac_input" id="addressFrom{idx}" name="addressFrom{idx}"></select>' +
'								</div>' +
'							</td>' +
'						</tr>' +
'						<tr>' +
'							<td class="emailUILabel" NOWRAP>' +
//'								<button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressTO{idx}\')">' + 
'                                   {app_strings.LBL_EMAIL_TO}:' +
//'                               </button>' + 
'							</td>' +
'							<td class="emailUIField" NOWRAP>' +
'								<div class="ac_autocomplete" id="to_add_container">' +
'									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="addressTO{idx}" title="{app_strings.LBL_EMAIL_TO}" name="addressTO{idx}" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
'									<span class="rolloverEmail"> <a id="MoreaddressTO{idx}" href="#" style="display: none;">+<span id="DetailaddressTO{idx}">&nbsp;</span></a> </span>' +
'									<div class="ac_container" id="addressToAC{idx}"></div>' +
'								</div>' +
'							</td>' +
'						</tr>' +
//'						<tr id="add_addr_options_tr{idx}">' +
//'							<td class="emailUILabel" NOWRAP>&nbsp;</td><td class="emailUIField" valign="top" NOWRAP>&nbsp;&nbsp;<span id="cc_span{idx}"><a href="#" onclick="SE.composeLayout.showHiddenAddress(\'cc\',\'{idx}\');">{mod_strings.LBL_ADD_CC}</a></span><span id="bcc_cc_sep{idx}">&nbsp;{mod_strings.LBL_ADD_CC_BCC_SEP}&nbsp;</span><span id="bcc_span{idx}"><a href="#" onclick="SE.composeLayout.showHiddenAddress(\'bcc\',\'{idx}\');">{mod_strings.LBL_ADD_BCC}</a></span></td>'+
//'						</tr>'+
'						<tr class="yui-hidden" id="cc_tr{idx}">' +
'							<td class="emailUILabel" NOWRAP>' +
'                               <button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressCC{idx}\')">' + 
'								{app_strings.LBL_EMAIL_CC}:' +
'                               </button>' + 
'							</td>' +
'							<td class="emailUIField" NOWRAP>' +
'								<div class="ac_autocomplete">' +
'									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="addressCC{idx}" name="addressCC{idx}"   title="{app_strings.LBL_EMAIL_CC}" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
'									<span class="rolloverEmail"> <a id="MoreaddressCC{idx}" href="#"  style="display: none;">+<span id="DetailaddressCC{idx}">&nbsp;</span></a> </span>' + 
'									<div class="ac_container" id="addressCcAC{idx}"></div>' +
'								</div>' +
'							</td>' +
'						</tr>' +
'						<tr class="yui-hidden" id="bcc_tr{idx}">' +
'							<td class="emailUILabel" NOWRAP>' +
'                               <button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressBCC{idx}\')">' + 
'                               {app_strings.LBL_EMAIL_BCC}:' +
'                               </button>' + 
'							</td>' +
'							<td class="emailUIField" NOWRAP>' +
'								<div class="ac_autocomplete">' +
'									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="addressBCC{idx}" name="addressBCC{idx}" title="{app_strings.LBL_EMAIL_BCC}" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
'									<span class="rolloverEmail"> <a id="MoreaddressBCC{idx}" href="#" style="display: none;">+<span id="DetailaddressBCC{idx}">&nbsp;</span></a> </span>' +
'									<div class="ac_container" id="addressBccAC{idx}"></div>' +
'								</div>' +
'							</td>' +
'						</tr>' +
'						<tr>' +
'							<td class="emailUILabel" NOWRAP width="1%">' +
'								<label for="emailSubject{idx}">{app_strings.LBL_EMAIL_SUBJECT}:</label>' +
'							</td>' +
'							<td class="emailUIField" NOWRAP width="99%">' +
'								<div class="ac_autocomplete">' +
'									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="emailSubject{idx}" name="subject{idx}" value="" maxlength="'+SUGAR.email2.composeLayout.subjectMaxlen+'">' +
'								</div>' +
'							</td>' +
'						</tr>' +
'					</table>' +
'					</div>' +
'				</td>'	 +
'			</tr>' +
'		</table>' +
'		<textarea id="htmleditor{idx}" name="htmleditor{idx}" style="width:100%; height: 100px;"></textarea>' +
'		<div id="divAttachments{idx}" class="ylayout-inactive-content">' +
'			<div style="padding:5px;">' +
'				<table cellpadding="2" cellspacing="0" border="0">' +
'					<tr>' +
'						<th>' +
'							<b>{app_strings.LBL_EMAIL_ATTACHMENTS}</b>' +
'							<br />' +
'							&nbsp;' +
'						</th>' +
'					</tr>' +
'					<tr>' +
'						<td>' +
'							<input type="button" name="add_file_button" onclick="SUGAR.email2.composeLayout.addFileField();" value="{mod_strings.LBL_ADD_FILE}" class="button" />' +
'							<div id="addedFiles{idx}" name="addedFiles{idx}"></div>' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<td>' +
'							&nbsp;' +
'							<br />' +
'							&nbsp;' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<th>' +
'							<b>{app_strings.LBL_EMAIL_ATTACHMENTS2}</b>' +
'							<br />' +
'							&nbsp;' +
'						</th>' +
'					</tr>' +
'					<tr>' +
'						<td>' +
'							<input type="button" name="add_document_button" onclick="SUGAR.email2.composeLayout.addDocumentField({idx});" value="{mod_strings.LBL_ADD_DOCUMENT}" class="button" />' +
'							<div id="addedDocuments{idx}"></div>' + //<input name="document{idx}0" id="document{idx}0" type="hidden" /><input name="documentId{idx}0" id="documentId{idx}0" type="hidden" /><input name="documentName{idx}0" id="documentName{idx}0" disabled size="30" type="text" /><input type="button" id="documentSelect{idx}0" onclick="SUGAR.email2.selectDocument({idx}0, this);" class="button" value="{app_strings.LBL_EMAIL_SELECT}" /><input type="button" id="documentRemove{idx}0" onclick="SUGAR.email2.deleteDocumentField({idx}0, this);" class="button" value="{app_strings.LBL_EMAIL_REMOVE}" /><br /></div>' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<td>' +
'							&nbsp;' +
'							<br />' +
'							&nbsp;' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<th>' +
'							<div id="templateAttachmentsTitle{idx}" style="display:none"><b>{app_strings.LBL_EMAIL_ATTACHMENTS3}</b></div>' +
'							<br />' +
'							&nbsp;' +
'						</th>' +
'					</tr>' +
'					<tr>' +
'						<td>' +
'							<div id="addedTemplateAttachments{idx}"></div>' +
'						</td>' +
'					</tr>' +
'				</table>' +
'			</div>' +
'		</div>' +
'	</form>' +
'		<div id="divOptions{idx}" class="ylayout-inactive-content"' +
'             <div style="padding:5px;">' +
'			<form name="composeOptionsForm{idx}" id="composeOptionsForm{idx}">' + 
'				<table border="0" width="100%">' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<b>{app_strings.LBL_EMAIL_TEMPLATES}:</b>' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<select name="email_template{idx}" id="email_template{idx}"  onchange="SUGAR.email2.composeLayout.applyEmailTemplate(\'{idx}\', this.options[this.selectedIndex].value);"></select>' +
'						</td>' +
'					</tr>' +
'				</table>' +
'				<br />' +
'				<table border="0" width="100%">' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<b>{app_strings.LBL_EMAIL_SIGNATURES}:</b>' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<select name="signatures{idx}" id="signatures{idx}" onchange="SUGAR.email2.composeLayout.setSignature(\'{idx}\');"></select>' +
'						</td>' +
'					</tr>' +
'				</table>' +
'				<br />' +
'				<table border="0" width="100%">' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<b>{app_strings.LBL_EMAIL_TEAMS}:</b>' +
'						</td>' +
'					</tr>' +
'					<tr>' +
'						<td id="teamOptions{idx}" NOWRAP style="padding:2px;">' +
'							&nbsp;' +
'						</td>' +
'					</tr>' +
'				</table>' +
'				<br />' +
'				<table border="0" width="100%">' +
'					<tr>' +
'						<td NOWRAP style="padding:2px;">' +
'							<input type="checkbox" id="setEditor{idx}" name="setEditor{idx}" value="1" onclick="SUGAR.email2.composeLayout.renderTinyMCEToolBar(\'{idx}\', this.checked);"/>&nbsp;' +
'							<b>{mod_strings.LBL_SEND_IN_PLAIN_TEXT}</b>' +
'						</td>' +
'					</tr>' +
'				</table>' +
'         </form>' +
'			</div> ' +
'		</div>' +
'</div>';

});



function setToEmails(o){
	 $('#addressTO0').css('display','none');
	 $('#to_add_container').attr('style','width:665px !important;float:left');
	 $('td.emailUIField').attr('style','width:665px !important;float:left');
	 
	 allmail= JSON.parse(o.composePackage.parent_id)					  
	 toAddress = '';
	 if(allmail.email_link_map != null){
		
		 for(opp_id in allmail.email_link_map){	
				   
		   emailName =allmail.email_link_map[opp_id];
		   emailId = emailName.split("<")[1].split(">")[0];
		   formated_email = allmail.email_link_map[opp_id];	  
		   emailOptOutFlag = allmail.emailOptOut[opp_id];
		   invalidEmailFlag = allmail.invalidEmail[opp_id];
		   
		   //check for email validation
		   if( !isValidEmail( emailId ) ) {
			   invalidEmailFlag= 1;
		   }
		   
		   //set class to change the color
		   if( formated_email.indexOf('<none>')  >= 0){
			   cls ='noEmail';   
		   } else if (emailOptOutFlag == 1) {
			   cls ='optOut';
		   } else if (invalidEmailFlag == 1) {
			   cls ='invalidEmail';
		   } else{
			   cls ='';
		   }
		   
		   //set tital message to display on hover
		   if(emailOptOutFlag == 1) {
			   emailTitle = SUGAR.language.get('Opportunities','LBL_OPTED_OUT_EMAIL_SELECTED');
		   } else if (invalidEmailFlag == 1) {
			   emailTitle = SUGAR.language.get('Opportunities','LBL_INVALID_EMAIL_SELECTED');
		   } else {
			   emailTitle = emailId; 
		   }
		   
		   toAddress += '<div class="vR"><span class="vN Y7BVp '+cls+'" email="'+emailName+'">'
		   toAddress += '<div class="vT " title="'+emailTitle+'">'+emailName+'</div><div class="vM"  onclick="removeDivNode(this,\''+emailName+'\');">x'
		   toAddress += '</div></span>'
		   
		   if( formated_email.indexOf('<none>')  < 0 && emailOptOutFlag == 0 && invalidEmailFlag == 0) {
				toAddress += '<input name="to_clients[]" style="display:none" type="text" value="'+formated_email+'">'
				toAddress += '<input name="related_opp[]" style="display:none" type="text" value="'+opp_id+'">'
		   }
		   toAddress += '</div>';
		   
		 }
		 $('#to_add_container').prepend(toAddress);
	}else{
		
        //no emails for the selected clients hide the panel
        SUGAR.quickCompose.parentPanel.hide();
        //show message
        mySimpleDialog = getSimpleDialog(); 
        mySimpleDialog.setBody(SUGAR.language.get('Opportunities','LBL_NO_EMAIL_FOR_SELECTED'));
				
		var myButtons = [{ text: 'OK', handler: handleCancel }];
		mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
		mySimpleDialog.render(document.body);    
		mySimpleDialog.show();

		/**
		 * $('#container1 div.bd').css('height','auto')
		 * $('#container1').css('height','auto')
		 * $('#container1').css('width','405px')		 
		 * $('#container1 div.bd').html('Please specify email for client ');
		 */
	}
	  /**$(".noEmail div.vT ").each(function(indexVal,elm){
		$(elm).tipTip({maxWidth: 'auto',edgeOffset: 10,content: 'Missing Primary Email.',defaultPosition: 'bottom'})
	});
	
	  
	//use for displaying email title on email on hover
	$("div.vT ").each(function(indexVal,elm){
		$(elm).tipTip({maxWidth: 'auto',edgeOffset: 10,track: true,defaultPosition: 'bottom'})
	});*/

	//clean all the <none>
	var tt = $('#addressTO0').val().split(',');
	var ref_ar= new Array();
	for(idx in tt){
	if(tt[idx].indexOf('<none>') < 0){
	 ref_ar[idx] = tt[idx]
	}
	}
	$('#addressTO0').val(ref_ar.join(','))
	

}
 

SUGAR.quickCompose.init = function(o) {

                          if(typeof o.menu_id != 'undefined') {
                             this.dceMenuPanel = o.menu_id;
                          } else {
                             this.dceMenuPanel = null;
                          }

              loadingMessgPanl = new YAHOO.widget.SimpleDialog('loading', {
                                width: '200px',
                                close: true,
                                modal: true,
                                visible:  true,
                                fixedcenter: true,
                        constraintoviewport: true,
                        draggable: false
                      });

              loadingMessgPanl.setHeader(SUGAR.language.get('app_strings','LBL_EMAIL_PERFORMING_TASK'));
                      loadingMessgPanl.setBody(SUGAR.language.get('app_strings','LBL_EMAIL_ONE_MOMENT'));
                      loadingMessgPanl.render(document.body);
                      loadingMessgPanl.show();

                      //If JS files havn't been loaded, perform the load.
                      if(! SUGAR.quickCompose.resourcesLoaded )
                      {  this.loadResources(o)}
                      else{
                          this.initUI(o);
					  }
                          
                  YAHOO.util.Event.onAvailable('addressTO0',setToEmails,o );
                
                };
SUGAR.quickCompose.initUI = function(options){
			var SQ = SUGAR.quickCompose;
			this.options = options;
			//Hide the loading div
			loadingMessgPanl.hide();
    		var dce_mode = (typeof this.dceMenuPanel != 'undefined' && this.dceMenuPanel != null) ? true : false;
			//Destroy the previous quick compose panel to get a clean slate
    		if (SQ.parentPanel != null)
    		{
    			//First clean up the tinyMCE instance
    			tinyMCE.execCommand('mceRemoveControl', false, SUGAR.email2.tinyInstances.currentHtmleditor);
    			SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor] = null;
    			SUGAR.email2.tinyInstances.currentHtmleditor = "";
    			SQ.parentPanel.destroy();
    			SQ.parentPanel = null;
    		}
			var theme = SUGAR.themes.theme_name;
			//The quick compose utalizes the EmailUI compose functionality which allows for multiple compose
			//tabs.  Quick compose always has only one compose screen with an index of 0.
			var idx = 0;
		    //Get template engine with template
    		if (!SE.composeLayout.composeTemplate)
    			SE.composeLayout.composeTemplate = new YAHOO.SUGAR.Template(SE.templates['compose']);
    			
    		var panel_modal = dce_mode ? false : true,
    		    panel_width = '880px',
			    panel_constrain = dce_mode ? false : true,
    		    panel_height = dce_mode ? 'auto' : 'auto',
    		    panel_shadow = dce_mode ? false : true,
    		    panel_draggable = dce_mode ? false : true,
    		    panel_resize = dce_mode ? false : true,
    		    panel_close = dce_mode ? false : true;

        	SQ.parentPanel = new YAHOO.widget.Panel("container1", {
                modal : panel_modal,
				visible : true,
            	constraintoviewport : panel_constrain,
                width : panel_width,
                height : panel_height,
                shadow : panel_shadow,
                draggable : panel_draggable,
				resize: panel_resize,
				close: panel_close
            });

        	if(!dce_mode) {
        		SQ.parentPanel.setHeader( SUGAR.language.get('app_strings','LBL_EMAIL_QUICK_COMPOSE')) ;
        	}

            SQ.parentPanel.setBody("<div class='email'><div id='htmleditordiv" + idx + "'></div></div>");

			var composePanel = SE.composeLayout.getQuickComposeLayout(SQ.parentPanel,this.options);

			if(!dce_mode) {
				var resize = new YAHOO.util.Resize('container1', {
                    handles: ['br'],
                    autoRatio: false,
                    minWidth: 400,
                    minHeight: 'auto',
                    status: false
                });

                resize.on('resize', function(args) {
                    var panelHeight = args.height;
                    this.cfg.setProperty("height", panelHeight + "px");
					var layout = SE.composeLayout[SE.composeLayout.currentInstanceId];
					layout.set("height", panelHeight - 50);
					layout.resize(true);
					SE.composeLayout.resizeEditor(SE.composeLayout.currentInstanceId);
                }, SQ.parentPanel, true);
			} else {
                SUGAR.util.doWhen("typeof SE.composeLayout[SE.composeLayout.currentInstanceId] != 'undefined'", function(){
					
                    var panelHeight = 400;
                    SQ.parentPanel.cfg.setProperty("height", panelHeight + "px");
                    var layout = SE.composeLayout[SE.composeLayout.currentInstanceId];
                    layout.set("height", panelHeight);
                    layout.resize(true);
                    SE.composeLayout.resizeEditor(SE.composeLayout.currentInstanceId);
                });
            }

			YAHOO.util.Dom.setStyle("container1", "z-index", 1);

			if (!SQ.tinyLoaded)
			{
				//TinyMCE bug, since we are loading the js file dynamically we need to let tiny know that the
				//dom event has fired.
				tinymce.dom.Event.domLoaded = true;
				tinyMCE.init({
			 		 convert_urls : false,
			         theme_advanced_toolbar_align : tinyConfig.theme_advanced_toolbar_align,
                     valid_children : tinyConfig.valid_children,
			         width: tinyConfig.width,
			         theme: tinyConfig.theme,
			         theme_advanced_toolbar_location : tinyConfig.theme_advanced_toolbar_location,
			         theme_advanced_buttons1 : tinyConfig.theme_advanced_buttons1,
			         theme_advanced_buttons2 : tinyConfig.theme_advanced_buttons2,
			         theme_advanced_buttons3 : tinyConfig.theme_advanced_buttons3,
			         plugins : tinyConfig.plugins,
			         elements : tinyConfig.elements,
			         language : tinyConfig.language,
			         extended_valid_elements : tinyConfig.extended_valid_elements,
			         mode: tinyConfig.mode,
			         strict_loading_mode : true
		    	 });
				SQ.tinyLoaded = true;
			}
			SQ.parentPanel.show();			
			//Re-declare the close function to handle appropriattely.
			SUGAR.email2.composeLayout.forceCloseCompose = function(o){SUGAR.quickCompose.parentPanel.hide(); }
			
			if(!dce_mode) {
				SQ.parentPanel.center();
			}
	};
	
	
	/**
	 * Send Email Function overriden for one to many emailing 
	 * By : Ashutosh
	 * Date : 13 Nov 2013
	 */
	SUGAR.util.doWhen("typeof SUGAR.email2 != 'undefined' && typeof SUGAR.email2.composeLayout != 'undefined'", function(){
		
	SUGAR.email2.composeLayout.sendEmail = function(idx, isDraft) {
		  //uncheck all the selected clients
         $("input[name^=mass_email]").attr('checked',false);
         
        //If the outbound account has an error message associate with it, alert the user and refuse to continue.
        var obAccountID = document.getElementById('addressFrom' + idx).value;

        if( typeof(SUGAR.email2.composeLayout.outboundAccountErrors[obAccountID]) != 'undefined' )
        {
            SUGAR.showMessageBox(app_strings.LBL_EMAIL_ERROR_DESC, SUGAR.email2.composeLayout.outboundAccountErrors[obAccountID], 'alert');
            return false;
        }


        var form = document.getElementById('emailCompose' + idx);
        var composeOptionsFormName = "composeOptionsForm" + idx;
        if (!SUGAR.collection.prototype.validateTemSet(composeOptionsFormName, 'team_name')) {
        	alert(mod_strings.LBL_EMAILS_NO_PRIMARY_TEAM_SPECIFIED);
        	return false;
        } // if


        var t = SE.util.getTiny('htmleditor' + idx);
        if (t != null || typeof(t) != "undefined") {
            var html = t.getContent();
        } else {
            var html = "<p>" + document.getElementById('htmleditor' + idx).value + "</p>";
        }

 	    var subj = document.getElementById('emailSubject' + idx).value;
        var to = trim(document.getElementById('addressTO' + idx).value);
        var cc = trim(document.getElementById('addressCC' + idx).value);
        var bcc = trim(document.getElementById('addressBCC' + idx).value);
        var email_id = document.getElementById('email_id' + idx).value;
        var composeType = document.getElementById('composeType').value;
        var parent_type = document.getElementById("parent_type").value;
        var parent_id = document.getElementById("parent_id").value;

        var el_uid = document.getElementById("uid");
        var uid = (el_uid == null) ? '' : el_uid.value;

      	var el_ieId = document.getElementById("ieId");
        var ieId = (el_ieId == null) ? '' : el_ieId.value;

        var el_mbox = document.getElementById("mbox");
        var mbox = (el_mbox == null) ? '' : el_mbox.value;

        if (!isValidEmail(to) || !isValidEmail(cc) || !isValidEmail(bcc)) {
			alert(app_strings.LBL_EMAIL_COMPOSE_INVALID_ADDRESS);
        	return false;
        }

        if (!SE.composeLayout.isParentTypeAndNameValid(idx)) {
        	return;
        } // if
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentIdValue = document.getElementById('data_parent_id' + idx).value;
        parent_id = parentIdValue;
        parent_type = parentTypeValue;

        var in_draft = (document.getElementById('type' + idx).value == 'draft') ? true : false;
        // baseline viability check

        if(to == "" && cc == '' && bcc == '' && !isDraft) {
            alert(app_strings.LBL_EMAIL_COMPOSE_ERR_NO_RECIPIENTS);
            return false;
        } else if(subj == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT)) {
                return false;
            } else {
                subj = app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT_LITERAL;
            }
        } else if(html == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_BODY)) {
                return false;
            }
        }

        SE.util.clearHiddenFieldValues('emailCompose' + idx);
		document.getElementById('data_parent_id' + idx).value = parentIdValue;
		var title = (isDraft) ? app_strings.LBL_EMAIL_SAVE_DRAFT : app_strings.LBL_EMAIL_SENDING_EMAIL;
        SUGAR.showMessageBox(title, app_strings.LBL_EMAIL_ONE_MOMENT);
        html = html.replace(/&lt;/ig, "sugarLessThan");
        html = html.replace(/&gt;/ig, "sugarGreaterThan");

        form.sendDescription.value = html;
        form.sendSubject.value = subj;
        form.sendTo.value = to;
        form.sendCc.value = cc;
        form.sendBcc.value = bcc;
        form.email_id.value = email_id;
        form.composeType.value = composeType;
        form.composeLayoutId.value = 'composeLayout' + idx;
        form.setEditor.value = (document.getElementById('setEditor' + idx).checked == false) ? 1 : 0;
        form.saveToSugar.value = 1;
        form.fromAccount.value = document.getElementById('addressFrom' + idx).value;
        form.parent_type.value = parent_type;
        form.parent_id.value = parent_id;
        form.uid.value = uid;
        form.ieId.value = ieId;
        form.mbox.value = mbox;
		var teamIdsArray = SUGAR.collection.prototype.getTeamIdsfromUI(composeOptionsFormName, 'team_name');
		form.teamIds.value = teamIdsArray.join(",");
        //form.selectedTeam.value = document.getElementById('teamOptions' + idx).value;
        form.primaryteam.value = SUGAR.collection.prototype.getPrimaryTeamidsFromUI(composeOptionsFormName, 'team_name');

        // email attachments
        var addedFiles = document.getElementById('addedFiles' + idx);
        if(addedFiles) {
            for(i=0; i<addedFiles.childNodes.length; i++) {
                var bucket = addedFiles.childNodes[i];

                for(j=0; j<bucket.childNodes.length; j++) {
                    var node = bucket.childNodes[j];
                    var nName = new String(node.name);

                    if(node.type == 'hidden' && nName.match(/email_attachment/)) {
                        if(form.attachments.value != '') {
                            form.attachments.value += "::";
                        }
                        form.attachments.value += unescape(node.value);
                    }
                }
            }
        }

        // sugar documents
        var addedDocs = document.getElementById('addedDocuments' + idx);
        if(addedDocs) {
            for(i=0; i<addedDocs.childNodes.length; i++) {
                var cNode = addedDocs.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/documentId/)) {
                        if(form.documents.value != '') {
                            form.documents.value += "::";
                        }
                        form.documents.value += node.value;
                    }
                }
            }
        }

        // template attachments
        var addedTemplateAttachments = document.getElementById('addedTemplateAttachments' + idx);
        if(addedTemplateAttachments) {
            for(i=0; i<addedTemplateAttachments.childNodes.length; i++) {
                var cNode = addedTemplateAttachments.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/templateAttachmentId/)) {
                        if(form.templateAttachments.value != "") {
                            form.templateAttachments.value += "::";
                        }
                        form.templateAttachments.value += node.value;
                    }
                }
            }
        }

        // remove attachments
        form.templateAttachmentsRemove.value = document.getElementById("templateAttachmentsRemove" + idx).value;

        YAHOO.util.Connect.setForm(form);

        AjaxObject.target = 'frameFlex';

        // sending a draft email
        if(!isDraft && in_draft) {
            // remove row
            SE.listView.removeRowByUid(email_id);
        }

        var sendCallback = (isDraft) ? AjaxObject.composeLayout.callback.saveDraft : callbackSendEmail;
        //added &oppOneToMany=1 param to execute email seperately 
        var emailUiAction = (isDraft) ? "&emailUIAction=sendEmail&saveDraft=true&oppOneToMany=1" : "&emailUIAction=sendEmail&oppOneToMany=1";
		
        AjaxObject.startRequest(sendCallback, urlStandard + emailUiAction);
        $('input[name^=mass_email]').attr('checked', false)
    }
   });
}




