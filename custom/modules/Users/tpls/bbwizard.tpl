<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="SHORTCUT ICON" href="{$FAVICON_URL}">
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.LBL_WIZARD_TITLE}</title>
{$SUGAR_JS}
{$SUGAR_CSS}
{$CSS}
{literal}
<script type='text/javascript'>
function disableReturnSubmission(e) {
   var key = window.event ? window.event.keyCode : e.which;
   return (key != 13);
}
</script>
<style>
a.linkUp
{  background-image: url("custom/themes/default/images/up.png");
    background-position: -8px -9px;
    display: block;
    float: left;
    height: 12px;
    width: 16px;
    outline:none;
    }
a.linkDown
{  background-image: url("custom/themes/default/images/down.png");
    background-position: -8px -11px;
    display: block;
    float: left;
    height: 12px;
    width: 16px;
    outline:none;
    }
.iconCont{
float: left;
    padding-left: 3px;
    width: 16px;
}
.selectorCont{
float:left;width:55px
}
.inputCont{
float:left;width:35px;
}
div.inputCont input[type=text]{
float:left;width:32px;
}
.txtAlignRight{
    text-align:right
    }
    div.screen div.edit.view {
    height: auto;
    
}
.required, .error {
   // color: #FF0000;
 //   position: relative;
  //  top: -40px;
}
td.txtblue
{
color:#0B578F
}
div.loadCont #loading{
background-color: #F9EDBE;
    border: 1px solid #F0C36D;
    font-size: 13px;
    font-weight: bold;
    margin-left: 90%;
    margin-top: 20%;
    padding: 4px;
    text-align: center;
    width: 7%;
}
.loadCont{
  float: left;    
    position: fixed;
    width: 98%;
    z-index: 9999999;
}
select{
width:200px;
}
select[multiple]{
width:200px;height:150px
}
div.screen div.edit.view td.centerMiddle {
    text-align: center;
    vertical-align: middle;
    width: 4px;
} 
div.inputCont div.required{
	position: relative;
    top: -22px;
}
#team_manager_container input{
width:200px;
}
#full_pipeline_container input{
width:200px;
}
#lead_reviewer_container input{
width:200px;
}
#opp_reviewer_container input{
width:200px;
}
input[type="text"] {

    line-height: 20px;
    min-height: 20px;
    color: 

    #111110;

}


</style>
{/literal}


</head>
<body class="yui-skin-sam">
<div class="loadCont">
<div style="display:none" id='loading'></div>
</div>
<div id="main">
    <div id="content">
    
        <table style="width:auto;;" border="0" align="center"><tr><td align="center">

<form id="UserWizard" name="UserWizard" method="POST" action="" onkeypress="return disableReturnSubmission(event);">
<input type='hidden' name='action' value='bbwizard'/>
<input type='hidden' name='module' value='Users'/>
<span class='error'>{$error.main}</span>

{$overlib_includes}
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='modules/Emails/javascript/vars.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_emails.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='modules/Users/User.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='custom/modules/Users/js/sugar_3.js'}"></script>

<div class="dashletPanelMenu wizard" style="width:900px;">

<div class="bd">
 {if $CRITICAL_ERROR }
			    <div class="edit view" style="width:70%;float:left;">
			    <ul  style=" margin: 0pt auto; width: 98%; list-style-type: none; padding: 2px;">
			    	{foreach from=$CRITICAL_ERROR_MESSAGES item=stErrors}
			   	 		<li style=" color: red; width: 98%; padding: 1%; list-style-type: none; text-align: left; margin: 1px; font-weight: bold;">{$stErrors}</li>
			   		 {/foreach}
			   		 </ul>
			    </div>
			    <br clear="all"/>
			    {/if}   
		
 <div id="welcome" class="screen" style="">
 	{if $smarty.request.geofilters eq '1'}
    {include file="custom/modules/Users/tpls/geo_filters.tpl"}
    {/if}
 </div>
				
<div id="personalinfo" class="screen">

    {include file="custom/modules/Users/tpls/role_users.tpl"}
</div>
<div id="roles_assignment" class="screen"></div>



			

</div>

</div>

{literal}
<script type='text/javascript'>
<!--
var SugarWizard = new function()
{
    this.currentScreen = 'welcome';

    this.handleKeyStroke = function(e)
    {
        // get the key pressed
        var key;
        if (window.event) {
            key = window.event.keyCode;
        }
        else if(e.which) {
            key = e.which
        }

        switch(key) {
        case 13:
            primaryButton = YAHOO.util.Selector.query('input.primary',SugarWizard.currentScreen,true);
            primaryButton.click();
            break;
        }
    }
	this.handleSubmit=function(){ 
		clear_all_errors();		
		setStatusLoading('loading');
		
		if(this.currentScreen == "personalinfo")
		{			
			YUI().use("io","io-form","node","selector-css3",function(Y) {
                        uri = 'index.php?module=Users&action=bbwizard&_to_pdf=true&save_users=1';
                        var cfg = {
                                method: 'POST',
                                form: {
                                        id: 'UserWizard',
                                        useDisabled: false
                                }
                        };
                        function onSuccess(id,response,args) {
								//Commented by Mohit Kumar Gupta 03-11-2015
								//For the error stop script on new user creation BSI-789
                                //SUGAR.util.evalScript(response.responseText);
                                stresponse = JSON.parse(response.responseText);                                
                                SUGAR.util.evalScript(stresponse.HTML);
                                if(stresponse.status == 'sucess'){
                                    	
	                                /*
	                                YAHOO.util.Dom.get('roles_assignment').innerHTML = stresponse.HTML;
	                                YAHOO.util.Dom.get('roles_assignment').style.display='block';
	
	                                document.getElementById("personalinfo").style.display = 'none';
	                                SugarWizard.currentScreen = 'roles_assignment';                                        
	                                setStatusLoading('loading');	
									var cnfLoad = {
												   cache: false
												   ,success: function(o) {
														SUGAR.util.evalScript(o.responseText);		
														document.getElementById('roles_assignment').innerHTML = o.responseText;
														hideStatusLoading('loading');
													}
													,faluire:function(){
														alert('Error with loading content.Please select a user first.');
													}
												};	
									YAHOO.util.Connect.asyncRequest("GET","index.php?module=Users&action=handle_requests&getFullDetails=1&to_pdf=true"+'&record='+document.getElementById('users').value, cnfLoad,'');							
	                         		*/ 		
	                         
	                         		Y.one('#personalinfo').setStyle('display','none');
	                         		Y.one('#welcome').setStyle('display','none');
	                         		Y.one('#roles_assignment').setStyle('display','block');
	                         		SugarWizard.currentScreen = 'roles_assignment'; 
	                       			//Y.one('#roles_assignment').set('innerHTML',stresponse.HTML);
	                                                     
	                         		SugarWizard.showFilterScreen(stresponse.saved_ids[0]);
                                }else{

	                                YAHOO.util.Dom.get(SugarWizard.currentScreen).innerHTML = stresponse.HTML;
	                                YAHOO.util.Dom.get(SugarWizard.currentScreen).style.display='block';

                                } 
                                hideStatusLoading('loading');                                
                        };
                        
                        function onFailure(id,response,args) {
                            document.getElementById("roles_assignment").innerHTML = "Error, retry...";
                        };

                		        
                        
                        Y.on('io:success', onSuccess, this);
                        Y.on('io:failure', onFailure, this);
                        var hasErrors = 0;
                        var arAddedNames = [""];
					  //validate form data
    				  Y.all('#personalinfo input[type=text],#personalinfo  input[type="password"],#personalinfo  select').each(
						function(elm){
							
							var idVal = (elm.get('id') )?elm.get('id'):Y.guid();
							elm.set('id',idVal);
							//special case for user name
							if(elm.get('value') != '' && elm.get('name').indexOf('user_name') != -1){
								
								if(arAddedNames.indexOf(elm.get('value')) != -1){
									hasErrors= 1;
									var idVal = (elm.get('id') )?elm.get('id'):Y.guid();
									elm.set('id',idVal);
									parTd = document.getElementById(idVal).parentNode;		
									//for(i in parTd.previousSibling)
									tdId = "TD"+Y.guid();
									parTd.id = tdId;
									preTd = document.getElementById(tdId).previousSibling;
									fieldVal = preTd.previousSibling.innerHTML;
									add_error_style('UserWizard', document.getElementById(idVal), "User name "+elm.get('value') +" already selected. ",1);	
								}
								arAddedNames.push(elm.get('value'))
							}
							
							if(elm.get('value') == '' && !elm.hasAttribute('readonly'))
							{ 								
								hasErrors= 1;
								var idVal = (elm.get('id') )?elm.get('id'):Y.guid();
								elm.set('id',idVal);
								parTd = document.getElementById(idVal).parentNode;		
								//for(i in parTd.previousSibling)
								tdId = "TD"+Y.guid();
								parTd.id = tdId;
								preTd = document.getElementById(tdId).previousSibling;
								fieldVal = preTd.previousSibling.innerHTML;
								add_error_style('UserWizard', document.getElementById(idVal), "Missing required field:"+fieldVal,1);
								
							}}

						
    	    		  );
    	    		 
    	    		//check user email exist in database and on the same form
    	    		//for this we have added our own class
    	    		//Mohit Kumar Gupta 13-07-2015
    	    		emailArray = [];
    	    		$(".checkUserEmailCustom").each(function(idx,elem){
    	    			if(trim(elem.value) != ''){
	    	    			var emailId = elem.id;
	    	    			var emailValue = elem.value;
	    	    			if($.inArray(emailValue,emailArray) >= 0) {
	    	    				hasErrors =1;
	    	    				ERR_MSG = 'Email already exists.';								
								add_error_style('UserWizard',document.getElementById(emailId),ERR_MSG,true);
								return false;
	    	    			} else {
	    	    				emailArray.push(emailValue);
	    	    			}
	    	    			returnFlag = checkUserEmail(emailId,emailValue); 
					  		if(returnFlag == false){
					  			hasErrors =1;
					  			return false;
					  		}		    	    			    	    				
    	    			}
    	    		});
    	    		
    				//check before submit
  	    			if(!hasErrors){	  
                      	Y.io(uri, cfg);
  	    			}else{
  	    				hideStatusLoading('loading');
  	  	  	    	}
                        
                });
			
		}
	}
    this.changeScreen = function(screen,displayGeoFilters)
    {
        if ( !displayGeoFilters ) {
            clear_all_errors();
            var form = document.getElementById('UserWizard');
            var isError = false;
            var txtCnt =  ['team_manager','admin','full_pipeline','lead_reviewer','opp_reviewer'];
            switch(this.currentScreen) {
                case 'personalinfo':                                
                
                  //allTxt = YAHOO.util.Selector.query('#personalinfo input[type=text]');
                  totalUser =0;
                  
                  for(i=0;i< txtCnt.length;i++){
                   
                	  totalUser = toInt(document.getElementById(txtCnt[i]).value) +  toInt(totalUser);
                   }
                if(totalUser > this.maxAllowed){
                    document.getElementById('wizerror').innerHTML = '{/literal}{$MOD.ERROR_USER_COUNT}{literal}'
                    isError = true;
                }else if(totalUser == 0){
                    document.getElementById('wizerror').innerHTML = '{/literal}{$MOD.ERROR_NO_USER_SELECTED}{literal}'
                    isError = true;
                }    
                 if(!isError)
                 this.handleSubmit();
                     
                break;
                case 'roles_assignment':
                	
                //user detials submited now time to display the role
                this.showFilterScreen();
                	
                break;
                default:
					document.getElementById(this.currentScreen).style.display = 'none';
					document.getElementById(screen).style.display = 'block';
					this.currentScreen = screen;									
                break;
            }
            if (isError == true)
                return false;
        }else{
        	this.showFilterScreen();
        }

        
    }
    this.showFilterScreen = function(uid){
        YUI().use('node',"io-base",function(Y){
			var URL = "index.php?module=Users&action=handle_requests&getFullDetails=1&to_pdf=true&record="+uid;
        	var cnfigure ={
                		method:"post",
                		sync:true,
                		on:{
                    		start:function(){setStatusLoading('loading');},	
                    		complete:function(id,o){ 
                        			 this.one('#roles_assignment').set('innerHTML',o.responseText);
                        			 this.one('#roles_assignment').setStyle('display','block');
                        			 SUGAR.util.evalScript(o.responseText);	
                        			 
                        			 if(typeof(uid) != 'undefined' && uid != ''){
                        			 $('#users option[value='+uid+']').attr('selected',true)
                        			 $('#users').trigger(onchangeUsers);
                        			 }
                        			 
                        			
                          			 //eval('SugarWizard.showFilterScreen("'+this.one('#users').get('value')+'")');
                        			hideStatusLoading('loading');
                        			
                        			 return 'completed';                       			
                        		},
    						end:function(){hideStatusLoading('loading');
    								return 'completed';
        							
        							}
                			}
                	}; 
            var req = Y.io(URL,cnfigure);
           	this.showFilterScreenStatus=req;
           	hideStatusLoading('loading');
        	return req; 
        });
			
    }
}
{/literal}
{if $smarty.request.geofilters eq 1}

SugarWizard.currentScreen = 'welcome'
SugarWizard.changeScreen('welcome');
{else}
SugarWizard.changeScreen('personalinfo');
{/if}

SugarWizard.maxAllowed = '{$NUM_INSTANCE_USERS}';



</script>


</form>




<script type="text/javascript">

{* JAVASCRIPT BY ASHUTOSH *}{literal}
function handleCount(objlnk,elmId){
//try{
document.getElementById('wizerror').innerHTML  = '';
    clear_all_errors();
val = toInt(document.getElementById(elmId).value);

if(objlnk.className == 'linkUp'){
val ++;
//validate the counts
var txtCnt =  ['team_manager','admin','full_pipeline','lead_reviewer','opp_reviewer'];
/*allTxt = YAHOO.util.Selector.query('#personalinfo input[type=text]');
                  totalUser =0;
                  for(i=0;i< allTxt.length;i++){
                      if(txtCnt.indexOf(allTxt.id))
                   		{
                       		totalUser = toInt(allTxt[i].value) + toInt(totalUser);
                   		}

 }*/
totalUser =0;

for(i=0;i< txtCnt.length;i++){
 
	  totalUser = toInt(document.getElementById(txtCnt[i]).value) +  toInt(totalUser);
 }
if(SugarWizard.maxAllowed <= totalUser){
add_error_style('UserWizard',document.getElementById(elmId),
                        '{/literal}{$MOD.ERROR_USER_COUNT} {literal}',0 ); return ;
                     }
container = elmId+"_container";
setStatusLoading('loading');
var callbackOutboundTest = {
    	success	: function(o) {

    		//YAHOO.util.Dom.get(container).innerHTML = YAHOO.util.Dom.get(container).innerHTML + o.responseText;
    		iTotalCount = YAHOO.util.Selector.query("#"+container+" div[id^="+elmId+"]").length
    		if(iTotalCount >0){
    			var result = document.createElement("div");
    			result.id = elmId+(iTotalCount+1);
    			result.innerHTML = o.responseText.replace(elmId+(iTotalCount+1),'');  			
    			
    			document.getElementById(container).appendChild(result);   			
    			
    		}else{
    			YAHOO.util.Dom.get(container).innerHTML = YAHOO.util.Dom.get(container).innerHTML + o.responseText;
        	}
    		hideStatusLoading('loading')
    	}
    };
    YAHOO.util.Connect.asyncRequest("GET", "index.php?module=Users&action=bbwizard&getUserTpl=true&_to_pdf=true"+'&title='+elmId+'&count='+val, callbackOutboundTest,'');

}else if(val>0){
     
     containerCol = document.getElementById(elmId+"_container");
     containerCol.removeChild(document.getElementById(elmId+val));
     val--;
}

/*}catch(e){
    
}*/
    
return (val < 0)?0:document.getElementById(elmId).value = val; 
}

function toInt(val){
return (isNaN(parseInt(val)))?0:parseInt(val);
}
function setStatusLoading(stContainerId)
{
	document.getElementById(stContainerId).style.display='';
	document.getElementById(stContainerId).innerHTML = '<img src="themes/default/images/sqsWait.gif" />Processing..';
}
function hideStatusLoading(stContainerId)
{
	document.getElementById(stContainerId).style.display='none';
}


if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
        "use strict";
        if (this == null) {
            throw new TypeError();
        }
        var t = Object(this);
        var len = t.length >>> 0;
        if (len === 0) {
            return -1;
        }
        var n = 0;
        if (arguments.length > 1) {
            n = Number(arguments[1]);
            if (n != n) { // shortcut for verifying if it's NaN
                n = 0;
            } else if (n != 0 && n != Infinity && n != -Infinity) {
                n = (n > 0 || -1) * Math.floor(Math.abs(n));
            }
        }
        if (n >= len) {
            return -1;
        }
        var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
        for (; k < len; k++) {
            if (k in t && t[k] === searchElement) {
                return k;
            }
        }
        return -1;
    }
}

function checkUserEmail(elementId,emailId){
	var currentUserId = '';
	
	if(trim(emailId) != ''){
		var returnFlag = true;
		$.ajax({
			url:'index.php?module=Users&action=handle_requests&checkUserEmail=1&to_pdf=1',
			type:"post",
			data: {userEmailId: emailId,userId:currentUserId},
			async : false,			
			complete:function (data){
				var formName = 'UserWizard';
				$("#" + elementId).parent().find(".validation-message").remove();
				var propertyFlag = false;
				if(trim(data.responseText) > 0){					
					ERR_MSG = 'Email already exists.';									
					add_error_style(formName,document.getElementById(elementId),ERR_MSG,true);
					propertyFlag = 'true';
					returnFlag = false;
				}				
			}
		});
		return returnFlag;
	}
}

{/literal}
</script>




