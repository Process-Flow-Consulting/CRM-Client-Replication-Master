/**
 * Function to mark/show error in a record to push from project Pipeline to Quickbooks
 * @param record_id
 * @param mod
 * @param master
 */
function PushToQuickbook(record_id,mod,master)
{	
	TitleText=	'Warning!!';   
	record = {'id':record_id,'module':mod};
    oReturn = function(body, caption, width, theme) {
                  $(".ui-dialog").find(".open").dialog("close");
		          if(master=='qb') {
			          showQbDialog(record,SUGAR.language.get('app_strings','MSG_QB_IS_MASTER'),'Warning!!','350px','auto');
		          } else if (master=='pp') {
			          showQbDialog(record,SUGAR.language.get('app_strings','MSG_PP_IS_MASTER'),'Warning!!','350px','auto');
		          } else if (master=='date') { 
			          showQbDialog(record,SUGAR.language.get('app_strings','MSG_DATEMODIFIED_IS_MASTER'),'Warning!!','250px','auto');
		          }
	              return false;
              };
    oReturn();       
}
/**
 * handle show dialog for push to quickbooks
 * @param record
 * @param txt
 * @param TitleText
 * @param width
 * @param height
 */
function showQbDialog(record,txt,TitleText,width,height) 
{    
    TitleText =	decodeURIComponent(TitleText).replace(/\+/g, ' ');        
    oReturn = function(body, caption, width, theme) {
                  $(".ui-dialog").find(".open").dialog("close");
                  var bidDialog = $('<div class="open"></div>').html(body)
                                  .dialog({
        						      modal:true,
                                      autoOpen: false,
                                      title: caption,
                                      width: width,
        						      height : height,
        						      buttons:  {
        						          Ok: function() {
        						              if (record != '') {
        						        	      handleOk(record);
        						        	  }
        						        	  $( this ).dialog( "close" );	
        						          },
        						          Cancel: function() {
        							          $( this ).dialog( "close" );
        								  }    
                                      }                                              
                                  });
                                  bidDialog.dialog('open');
              }; 
              oReturn(txt,TitleText, width, '');                                          
              return; 
}
/**
 * handle dialog selection either ok or cancel is selected
 * @param record
 */
function handleOk(record) 
{			
	var record_id = record.id;
	var mod = record.module;
	$.ajax({
      type: 'POST',
	  url : 'index.php?entryPoint=pushToQB',
	  cache: false,
	  async: true,
	  data: 'record='+record_id+'&module='+mod,
	  success:function (data) {
		  data = JSON.parse(data);
		  if (data.success == 1) {
		      qbFlag = data.quickBookFlag;
		      if (qbFlag == '1'){
			      ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_QUICKBOOK_ALREADY_UPDATED'));
		      } else if (qbFlag == '0') {
			      ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_QUICKBOOK_UPDATED'));
		      }
		  } 
		  else {
			  errStr = data.errorData;
			  dispErrorMsg='<table>';
		      dispErrorHeading ="<p style = 'color :red'>"+SUGAR.language.get('app_strings','LBL_ERROR_HEADING_MESSAGE')+'</p><br/>';
		      for(i in errStr) {
		          if (errStr[i].lineItems == 0) {
		        	  dispErrorMsg += '<td><br/>'+errStr[i].errorMsg+" !!</td></tr>";
		          } 
		          else if (errStr[i].prodTempId == "" && errStr[i].qbType == '') {
		    	      dispErrorMsg += '<tr><td width=25%><a href="index.php?action=ListView&module=ProductTemplates" >'
		                              +errStr[i].name+'</a> : </td><td width=90%>'+errStr[i].errorMsg+"</td></tr>";
		          }	
		          else if (errStr[i].qbType == '') {
		              dispErrorMsg += '<tr><td width=25%><a href="index.php?action=DetailView&module=ProductTemplates&record='+errStr[i].id+'" >'
		                              +errStr[i].name+'</a> : </td><td width=90%>'+errStr[i].errorMsg+'</td></tr>';
		          }
		          else if (errStr[i].qbType != '' && errStr[i].accounts !='') {  
		              dispErrorMsg += '<tr><td width=25%><a href="index.php?action=DetailView&module=ProductTemplates&record='+
		                              errStr[i].id+'" >'+errStr[i].name+'</a> : </td><td width=90%>'+errStr[i].errorMsg+"<b>"+errStr[i].qbType+"</b> "
		                              +errStr[i].accounts+'</td></tr>'; 	    
		          }
		      }
		      dispErrorMsg=dispErrorHeading+dispErrorMsg;
		      showQbDialog('',dispErrorMsg,'Error !!','604px','auto');				
		  }
	  },
      error:function(data) {
    	  ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_QUICKBOOK_ERROR_PROCESSING'));			
      }
	});
	$( this ).dialog( "close" );
}