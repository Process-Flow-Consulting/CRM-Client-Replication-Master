<?php
require_once 'include/MVC/View/views/view.list.php';
require_once('custom/include/common_functions.php');
require_once 'custom/modules/Opportunities/CustomListViewSmarty.php';
require_once 'custom/modules/Opportunities/CustomListViewData.php';
require_once('custom/include/SearchForm/PlOppSearchForm2.php');

class OpportunitiesViewList extends ViewList {

    function OpportunityViewList() {
        parent::ViewList();
    }
	
	//BBSMP-242 -- START
	function listViewPrepare() {    
		if($_REQUEST['orderBy']) { 
			$_REQUEST['orderBy'] = strtoupper('date_modified');  
			$_REQUEST['sortOrder'] = 'DESC'; 
			$_REQUEST['overrideOrder'] = true;
		} 
		parent::listViewPrepare(); 
	}
	//BBSMP-242 -- END
    
    function preDisplay(){    	
    	$this->lv = new CustomListViewSmarty();
    	$this->lv->lvd = new CustomListViewData();
    	//$this->lv->quickViewLinks  = false;
    }
    
    
    function display(){
    	global $current_user;
    	//require_once('include/SugarCharts/SugarChartFactory.php');
    	//$sugarChart = SugarChartFactory::getInstance();
    	//echo $resources = $sugarChart->getChartResources();
    	//echo '<script type="text/javascript">SUGAR.loadChart = true;</script>';	
    	
        require_once 'custom/modules/Opportunities/OpportunitySummary.php';
        $this->bean = new OpportunitySummary();
        
        //fix -- county not popuate inn save search
        if(isset($_REQUEST['saved_search_select']) && !empty($_REQUEST['saved_search_select']) ){
            $savedSearch = new SavedSearch();
            $retrieveSavedSearch = $savedSearch->retrieveSavedSearch($_REQUEST['saved_search_select']);
            $savedSearchOptions = $savedSearch->populateRequest();
        }else{
            unset($_SESSION['LastSavedView'][$this->module]);
        }
        
        //if there are counties specified in advance search then show them as selected
        if(isset($_REQUEST['lead_county_advanced'])){
            $SELECTED_COUNTIES = json_encode($_REQUEST['lead_county_advanced']);
        }else{
            $SELECTED_COUNTIES = json_encode(array(''));
        }
        
        //Modfified by Mohit kumar Gupta 29-06-2015
        //display the link pull bidboard opportunity from BBHub
        $pull_from_menu = 0;
		if(isset($_REQUEST['pull_opportunity'])){
			$pull_from_menu = 1;
		}
                
        parent::display();
        
        echo '<script type="text/javascript" src="custom/modules/Opportunities/OpportunitiesListView.js"></script>';
        
        if(empty($_REQUEST['searchFormTab'])){
        	 	 
        	echo '<script type="text/javascript">
        	if( typeof document.getElementById("open_only_basic") != \'undefined\') 
        	    document.getElementById("open_only_basic").checked = true;
        	</script>';
        
        } 
        //added for showing docment subpanel
        echo '<script type="text/javascript" src="include/SubPanel/SubPanelTiles.js"></script>';
        
        
        //javascript for showing popup
echo <<<EQQ
<style>		
.yui-dialog .container-close{
 	top: 6px;
	background: url("index.php?entryPoint=getImage&themeName=Sugar&themeName=Sugar&themeName=Sugar&imageName=sugar-yui-sprites.png") repeat-x scroll 6px -67px transparent;
	overflow: hidden;
    text-decoration: none;
    text-indent: -10000em;
	cursor: pointer;
    height: 15px;
    position: absolute;
    right: 6px;
	width: 25px;		   
 }
		
.yui-module .hd, .yui-panel .hd{		
	 background-image: -moz-linear-gradient(center top , #EEEEEE 0%, #CCCCCC 100%);
	 border-bottom: 1px solid #999999;
	 border-top: 1px solid #FFFFFF;
	 color: #666666;
	 padding: 6px 8px;
	 text-shadow: 0 1px #FFFFFF;
	 font-size: 12px;
	 font-weight: bold;
	 line-height: 1.8;
	 margin: 0;
}		


		
.search td select.datetimecombo_time{
		width: 40px !important;
}
.search td input[type="text"]#date_closed_advanced_date{
	width: 100px !important;
}
</style>
 <script type='text/javascript' src="include/javascript/overlibmws.js"></script>
         <script type='text/javascript'>
         var pull_from_menu = '$pull_from_menu';
         var current_user_id = '$current_user->id';
         
        /**
         * show online plans popup on opportunity list view
         * @author Mohit Kumar Gupta
         * @date 25-02-2014
        */ 
        function open_urls(event,URL,titleName){	
 			titleName = decodeURIComponent(titleName).replace(/\+/g, ' ');		
			target = event.target?event.target:event.srcElement;
            plid = target.id;
            cont = document.getElementById('url'+plid);
           			
            if(false && cont.innerHTML != '')
            {
                showPopup(cont.innerHTML,titleName);
                SUGAR.util.evalScript(cont.innerHTML);
                $("#overDiv").css("zIndex","1031");
            }else{
ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
            var sUrl = URL;
            var callback = {
                    success: function(o) {                    
		                document.getElementById('url'+plid).innerHTML = o.responseText;                        
		                document.getElementById('url'+plid).style.display='none';
						showPopup(o.responseText,titleName);
						ajaxStatus.hideStatus();
						SUGAR.util.evalScript(o.responseText);
                        $("#overDiv").css("zIndex","1031");					
		            },
                    failure: function(o) {
                    
                    }
                }

                var transaction = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback, null);
            }
        }
        
        
function showPopup(txt,TitleText){
overlib(
txt
,STICKY
,10
,WIDTH
,600
, CENTER
,CAPTION
,'<div style="float:left">'+TitleText+'</div>'
, CLOSETEXT
, '<div style=\'float: right\'><img border=0 style=\'margin-left:2px; margin-right: 2px;\' src=themes/Sugar/images/close.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=896855794></div>'
,CLOSETITLE
, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP')
, CLOSECLICK
,FGCLASS
, 'olFgClass'
, CGCLASS
, 'olCgClass'
, BGCLASS
, 'olBgClass'
, TEXTFONTCLASS
, 'olFontClass'
, CAPTIONFONTCLASS
, 'olCapFontClass');
}

    /**
     * show bid board upadates opup on opportunity list view
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */    		
    function showPopupBidBoard(lead_id,opportunity_id){        
		$('#dlg1').remove();
		var callback = {
    		success:function(o){   
    		    showPopupOppDetail(o.responseText,'Bid Board Updates','82%','openBbd'); 			
    		}
    	}
		YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Leads&action=relatedpl&to_pdf=true&lead_id='+lead_id+'&from=opportunity&opportunity_id='+opportunity_id, callback);
	    return false;	       
    }
	/**
     * show project lead on updated popup on opportunity list view
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */	
	function showPLDetailModal(lead_id,project_opportunity_id){    		
		var callback = {
    		success:function(o){   
    		    showPopupOppDetail(o.responseText,'Project Lead Details','90%','openPl'); 							
    		}
    	}
		YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Leads&action=pldetails&to_pdf=true&lead_id='+lead_id+'&project_opportunity_id='+project_opportunity_id, callback);
		return false;
    }
	/**
     * show project opportunity on updated popup on opportunity list view
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */	
	function showPODetailModal(project_opportunity_id){
    	$('#dlg2').remove();		
		var callback = {
    		success:function(o){    		
    		    showPopupOppDetail(o.responseText,'Project Opportunity Details','82%','openPo'); 	
    		}
    	}
		YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Opportunities&action=podetails&to_pdf=true&project_opportunity_id='+project_opportunity_id, callback);
		return false;
    }
	/**
     * show opportunity additional details on opportunity list view
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */	
	function showAtAGlance(opportunity_id,title){ 
        $("#tab_ataglance").remove(); 	
		var tab_body = '<div id="tab_ataglance" class="yui-navset"><ul class="yui-nav"><li class="selected"><a href="#tab1"><em>Report</em></a></li><li><a href="#tab2"><em>Graph</em></a></li></ul><div class="yui-content"><div>Loading...</div>';
		tab_body += '<div>Loading...</div></div></div>';
        showPopupOppDetail(tab_body,'Project Opportunity Details - '+title,'98%','open');
		var myTabs = new YAHOO.widget.TabView("tab_ataglance");
		var tab0 = myTabs.getTab(0);
		
		var callback = {
    		success:function(o){				
				tab0.set('content', o.responseText);				
    		}
    	}
		YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Opportunities&action=ataglance&to_pdf=true&opportunity_id='+opportunity_id, callback);
		
		
		var tab1 = myTabs.getTab(1);
		tab1.addListener('click', handleClickTab1);
		
		function handleClickTab1(e) {  
    		var callbackGraph = {
    		success:function(o){				
				tab1.set('content', o.responseText);				
				SUGAR.util.evalScript(o.responseText);		
    		}
    	}
		YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=Opportunities&to_pdf=true&action=ataglance_graph&opportunity_id='+opportunity_id, callbackGraph);
			
		}		
		return false;		
    }	
    /**
     * common pop up for opening all popups
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */
    function showPopupOppDetail(txt,TitleText,width,classId,height){
        TitleText=	decodeURIComponent(TitleText).replace(/\+/g, ' ');
                            
        oReturn = function(body, caption, width, theme) {
            $(".ui-dialog").find("."+classId).dialog("close");
            var bidDialog = $('<div class=classId></div>')
            .html(body)
            .dialog({
					model:true,
                    autoOpen: false,
                    title: caption,
                    width: width,
					height : height,
					position:['top',150],
            });
            bidDialog.dialog('open');       
        };       
        oReturn(txt,TitleText, width, '');
        return;
    }
    /**
     * show project document popup on opportunity list view
     * @author Mohit Kumar Gupta
     * @date 25-02-2014
    */    
	function showProjectDocument(opportunity_id,title){
		$('#tab_pd').remove();	
		var myTabs = new YAHOO.widget.TabView("tab_pd");

		var callback = {success:function(o){
				SUGAR.util.evalScript(o.responseText);
				showPopupOppDetail(o.responseText,'Project Documents - '+title,'98%','openDoc');
				}};
	var URL = 'index.php?module=Opportunities&action=projectdocument&to_pdf=true&record='+opportunity_id;
	YAHOO.util.Connect.asyncRequest ('GET', URL , callback);
	return false;
    }
	$(document).ready(function() {
		$('.showDiv').on('click',function() {
			var record = this.id;			
			$('.documentName').removeClass('on');
		 	$('.accordionContent').slideUp('normal');	
		 	$('.close').hide();
	        $('.open').show();	 	
			if($("#content_"+record).is(':hidden') == true) {
			    $("#close_"+record).show();
			    $("#open_"+record).hide();
				$("#documentName_"+record).addClass('on');
				$("#content_"+record).slideDown('normal');
				$('div[id^="content_"]').html('');
				$('#content_'+record).html('<div id="ajax_content"><center><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></center></div>');
				var callback = {
		    		success:function(o){ 			
						$('#content_'+record).html(o.responseText);
		    		}
		    	}
				var URL = 'index.php?module=Opportunities&action=documentsubpanel&to_pdf=true&record='+record;
				YAHOO.util.Connect.asyncRequest ('GET', URL , callback);
			 } else {			    
                $("#close_"+record).hide();
			    $("#open_"+record).show();
            } 
			  
		 });		 
		 $('.accordionContent').hide();
		 
		 //Modfified by Mohit kumar Gupta 29-06-2015
        //display the link pull bidboard opportunity from BBHub
		 YUI().use("io-base","node", function(Y) {			
			if(pull_from_menu==1){	        	
				initiatePulling();  		    	
			}
			
			function checkProcessStatus(){
				var uri = "cmdscripts/opp_import_process_status.php";
				var cfg = {
					method: 'POST',
					data: 'user=yahoo',
					headers: {
						'Content-Type': 'application/json',
					},
					on: {
						start: function(){
							
						},
						complete:function(id,o){		        		        				
									var res = o.responseText;       					
									var res_str = res.split('_');
									var import_text_arr = res_str[1].split('|');
									var inserted_opp = trim(import_text_arr[0]);
									var updated_opp = trim(import_text_arr[1]);        				      					
									if(inserted_opp == '0' && updated_opp == '0'){
										var msg_text = 'Data streaming is in progress...';        					
									}else{
										var msg_text = 'Opportunity Imported: '+inserted_opp+' Opportunity Updated: '+updated_opp;
									}         				
									if(trim(res_str[0])=='running'){       						
										ajaxStatus.showStatus(msg_text);
										return true;
									}else if(trim(res_str[0])=='finished'){							
										ajaxStatus.showStatus(inserted_opp+' Opportunity Imported and '+ updated_opp +' Opportunity Updated Successfully.');
										setInterval(function(){    							
											window.location.href="index.php?module=Opportunities&action=index&n=" + new Date().getTime();;	
										},5000);
										
										return false;
									}       			       				       				
									
								},
						end: function(){   					
						}
					}	    
				
				};    
				var request = Y.io(uri,cfg);	
			}
			
			
			function initiatePulling(){
				var isSuccess=false;	
				var uri = "cmdscripts/PullBBHOppCommand.php?process=getNewOpportunities&userId="+current_user_id;

				// Define a function to handle the response data.
				   var cfg = {
					method: 'POST',
					data: 'user=yahoo',
					headers: {
						'Content-Type': 'application/json',
					},
					on: {
						start: function(){
							ajaxStatus.showStatus("Please wait while populating opportunities...");
						},
						complete:function(id,o){
									//how many project leads we have        				
									//var res = JSON.parse(o.responseText);
									var res = o.responseText;
									var res = trim(res);			
									if(res=='success'){							
										window.location.href="index.php?module=Opportunities&action=index";
									}else if(res=='start'){
										ajaxStatus.showStatus('Please wait while populating opportunities...');
										setInterval(function(){checkProcessStatus()},5000);							
										return false;
									}else if(res=='running'){
										ajaxStatus.showStatus('Please wait while populating opportunities...');
										setInterval(function(){checkProcessStatus()},5000);							
										return false;    
									}else{
										ajaxStatus.showStatus(res);
										return false;    
									}									
								},
						end: function(){   					
						}
					}					
				};    
				var request = Y.io(uri,cfg);
			}									
		});		
	});

	var selectedCounty = JSON.parse('$SELECTED_COUNTIES');
		
    YUI().use('node-base','io',"selector-css3",'event',function (Y){
    	
    	function loadCounties(){ 
    		
    		ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
    		
    		postParam = new Array;
    		
    		postParam  = $('#lead_state_advanced').val();
    		
    		if(postParam != null){
      			
    			//get Counties
    			var callback = {
    			    success:function(o){
    	
    						$('#lead_county_advanced').html(o.responseText);       							
    						
    	
    					}
    			};
    			
    			stPostState	= '&county_id[]='+selectedCounty.join('&county_id[]=');
    			var connectionObject = YAHOO.util.Connect.asyncRequest ("POST", "index.php?entryPoint=CountyAjaxCall&multisel=1&to_pdf=1&state_advacne[]="+postParam.join('&state_advacne[]='), callback,stPostState);
    		}
    		ajaxStatus.hideStatus();
    	}
    	
    	if( Y.one('select[name^=lead_state_advanced]') != null){     		
    		Y.on('load',function(){loadCounties()})
    		Y.one('select[name^=lead_state_advanced]').on('change',function(e){
    			loadCounties();    
    		});				
    	}
    });   

	function toggle(bid) {        
	    var ele = document.getElementById("role-div_"+bid);
	    var text = document.getElementById("displayText_"+bid);
	    if(ele.style.display == "block") {
	            ele.style.display = "none";
	            text.innerHTML = "&nbsp;<strong>+</strong>&nbsp;";
	    }
	    else {
	            ele.style.display = "block";
	            text.innerHTML = "&nbsp;<strong>-</strong>&nbsp;";
	    }
	}
	        
	$(function(){
	    //remove undesired sales stages from massupdate 
	    $('#mass_sales_stage option').each(function(i,elm){
        var arr = ["Proposal - Verified", 'Proposal - Sent', "Proposal - Unverified" ];
        if( $.inArray($(elm).val(),arr) >= 0){
        $(elm).remove();        
        }
        });  
    })
	</script>
EQQ;

    }
    

	/**
	 * Overriden method prepareSearchForm to change the searchform class
	 * @added BY : Ashutosh 
	 * @date : 16 Sept 2013
	 * @see SugarView::prepareSearchForm()
	 */	
    function prepareSearchForm()
    {
        $this->searchForm = null;
    
        //search
        $view = 'basic_search';
        if(!empty($_REQUEST['search_form_view']) && $_REQUEST['search_form_view'] == 'advanced_search')
            $view = $_REQUEST['search_form_view'];
        $this->headers = true;
    
        if(!empty($_REQUEST['search_form_only']) && $_REQUEST['search_form_only'])
            $this->headers = false;
        elseif(!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false')
        {
            if(isset($_REQUEST['searchFormTab']) && $_REQUEST['searchFormTab'] == 'advanced_search')
            {
                $view = 'advanced_search';
            }
            else
            {
                $view = 'basic_search';
            }
        }
    
        $this->use_old_search = true;
        if ((file_exists('modules/' . $this->module . '/SearchForm.html')
                && !file_exists('modules/' . $this->module . '/metadata/searchdefs.php'))
                || (file_exists('custom/modules/' . $this->module . '/SearchForm.html')
                        && !file_exists('custom/modules/' . $this->module . '/metadata/searchdefs.php')))
        {
            require_once('include/SearchForm/SearchForm.php');
            $this->searchForm = new SearchForm($this->module, $this->seed);
        }
        else
        {
            $this->use_old_search = false;
            require_once('custom/include/SearchForm/SearchForm2.php');
    
            $searchMetaData = SearchForm::retrieveSearchDefs($this->module);
            /**
             * Applied the new method getPlOppSearchForm2 to override the searchform2.php
             * @Added By : Ashutosh
             * @date : 16 Sept 2013*/    
            $this->searchForm = $this->getPlOppSearchForm2($this->seed, $this->module, $this->action);
            $this->searchForm->setup($searchMetaData['searchdefs'], $searchMetaData['searchFields'], 'SearchFormGeneric.tpl', $view, $this->listViewDefs);
            $this->searchForm->lv = $this->lv;
        }
    }
    
    
    /**
     * new method to override the searchform2.php
     * @Added By : Ashutosh
     * @date : 16 Sept 2013 
     * @param Object $seed
     * @param object $module
     * @param string $action
     * @return PlOppSearchForm
     */
    protected function getPlOppSearchForm2($seed, $module, $action = "index"){
        
        return new PlOppSearchForm($seed, $module, $action);
        
    }
    
    protected function _getModuleTitleListParam( $browserTitle = false )
    {
    	global $current_user;
    	global $app_strings;
    	global $mod_strings;
    
    	if(!empty($GLOBALS['app_list_strings']['moduleList'][$this->module]))
    		$firstParam = $mod_strings['LBL_BID_BOARD'];
    	else
    		$firstParam = $mod_strings['LBL_BID_BOARD'];
    
    	$iconPath = $this->getModuleTitleIconPath($this->module);
    	if($this->action == "ListView" || $this->action == "index") {
    		if (!empty($iconPath) && !$browserTitle) {
    			if (SugarThemeRegistry::current()->directionality == "ltr") {
    				return "$firstParam";
    
    			} else {
    				return "$firstParam";
    				
    				}
			} else {
    				return $firstParam;
    				}
    				}
    				else {
    				if (!empty($iconPath) && !$browserTitle) {
    				//return "<a href='index.php?module={$this->module}&action=index'>$this->module</a>";
    				} else {
    				return $firstParam;
    				}
    	}
    }
}
?>
