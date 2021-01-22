<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
	die ( 'Not A Valid Entry Point' );
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

require_once ('custom/include/common_functions.php');
require_once ('include/MVC/View/views/view.list.php');
require_once 'custom/modules/Accounts/CustomListViewSmarty.php';
class AccountsViewList extends ViewList {
	
	public function AccountsViewList() {
		parent::ViewList ();
				
	}
	
	public function preDisplay() {		
		//parent::preDisplay ();
		$this->lv = new CustomListViewSmarty();
		$this->lv->lvd->additionalDetails = false;
		$this->lv->lvd->additionalDetailsAjax = false;
		
		$this->lv->targetList = true;
		//to allow browser's back button for search
    	header('Expires: -1');                
        header( 'Cache-Control: must-revalidate, post-check=3600, pre-check=3600' );
	}
	
	public function listViewProcess() {
		// print_r($_REQUEST);
		$this->processSearchForm ();		
		
		$this->lv->searchColumns = $this->searchForm->searchColumns;
	
		$search_where = array ();
		
		foreach ( $_REQUEST as $field_name => $field_value ) {

		    if(in_array($field_name, array('name_basic','name_advanced'))){
		    	
		        $stFieldName = str_replace(array('_advanced','_basic'), '', $field_name );
		        $stNameSearch = "accounts." . $stFieldName . " like '" . trim($field_value). "%'";
		        //turn off full text search
		        //$stReplaceNameSearch = "( MATCH(accounts." .$stFieldName . ") AGAINST ('" . $field_value . "' IN BOOLEAN MODE) )";
		        $stReplaceNameSearch = " (accounts." . $stFieldName . " LIKE '%" . trim($field_value). "%')  ";
		        
		        $this->where = str_replace($stNameSearch, $stReplaceNameSearch, $this->where);	            
		        
		    }
		    
			if (preg_match ( "/(geographical_areas_serviced|products_brands_services|memberships_assoc_certs|custom_field_1|custom_field_2).*/", $field_name )) {
				
				$db_field = str_replace ( '_advanced', '', $field_name );
				$db_field = str_replace ( '_basic', '', $db_field );
				
				
				if ($field_value != '') {
				    
				    //convert to array if it is string only
				    //issue fixed on production instance
				    if (!is_array($field_value)) {
				        $field_value = explode ( ',', $field_value );
				    }
					
					foreach ( $field_value as $field_name ) {
						$field_where [] = "(accounts." . $db_field . " LIKE '" . $field_name . "%')";
					}
					
					$search_where [] = implode ( " OR ", $field_where );
				}
			}
			unset ( $field_where );
		}
		
		$custom_where = '';
		
		foreach ( $search_where as $where ) {
			$custom_where .= '(' . $where . ') AND ';
		}
		
		$custom_where .= " accounts.visibility = '1' ";
		
		if ($this->where != '' && $custom_where != '') {
			$this->where .= ' AND ';
		}
		
		$this->where .= $custom_where;
		
		if (! $this->headers)
			return;
		
		if (empty ( $_REQUEST ['search_form_only'] ) || $_REQUEST ['search_form_only'] == false) {
			$this->lv->setup ( $this->seed, 'custom/modules/Accounts/tpls/ListViewGeneric.tpl', $this->where, $this->params );
			$savedSearchName = empty ( $_REQUEST ['saved_search_select_name'] ) ? '' : (' - ' . $_REQUEST ['saved_search_select_name']);
			// echo
			// get_form_header($GLOBALS['mod_strings']['LBL_LIST_FORM_TITLE'] .
			// $savedSearchName, '', false);
			echo $this->lv->display ();
		}
	}
	public function display() {	
		echo "<style>
       				.yui-ac-content{
       					width: auto;
       				}
       			</style>";
		require_once 'custom/modules/Accounts/accounts_filter_result.php';
		
		$this->bean = new accounts_filter_result ();
		
		//fix -- county not popuate inn save search
		if(isset($_REQUEST['saved_search_select']) && !empty($_REQUEST['saved_search_select']) ){	
			$savedSearch = new SavedSearch();
			$retrieveSavedSearch = $savedSearch->retrieveSavedSearch($_REQUEST['saved_search_select']);
			$savedSearchOptions = $savedSearch->populateRequest();
		}else{
			unset($_SESSION['LastSavedView'][$this->module]);
		}
		
		//if there are counties specified in advance search then show them as selected
		if(isset($_REQUEST['county_id_advanced'])){
			$SELECTED_COUNTIES = json_encode($_REQUEST['county_id_advanced']);
		}else{
			$SELECTED_COUNTIES = json_encode(array(''));
		}
		
		echo '<script type="text/javascript" src="custom/include/javascript/serialize.0.2.min.js"></script>';
		
		parent::display ();
		
		echo <<<EOQ
<script type='text/javascript'>    
      
      var selectedCounty = JSON.parse('$SELECTED_COUNTIES');
		
      YUI().use('node-base','io',"selector-css3",'event',function (Y){
      		
      		function loadCounties(){ 
      			
      			ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING'));
      			
      			postParam = new Array;
      			
      			postParam  = $('#billing_address_state_advanced').val();
      			
      			if(postParam != null){
	      			
      				//get Counties
					var callback = {
					    success:function(o){
      		
      							$('#county_id_advanced').html(o.responseText);       							
								
      		
							}
					};
      				
					stPostState	= '&billing_address_state_advacne[]='+postParam.join('&billing_address_state_advacne[]=');
					stPostState	= '&county_id[]='+selectedCounty.join('&county_id[]=');
					var connectionObject = YAHOO.util.Connect.asyncRequest ("POST", "index.php?entryPoint=CountyAjaxCall&multisel=1&to_pdf=1&state_advacne[]="+postParam.join('&billing_address_state_advacne[]='), callback,stPostState);
      			}
      			ajaxStatus.hideStatus();
        	}
      		

      		if( Y.one('select[name^=billing_address_state_advanced]') != null){     		
        		Y.on('load',function(){loadCounties()})
        		Y.one('select[name^=billing_address_state_advanced]').on('change',function(e){
					loadCounties();    
				});				
			}
      });
               
       YAHOO.util.Event.onDOMReady(function(){
          if(document.getElementById('classification').value==''){
            document.getElementById('classification').value='Search by Classification..';
          }
          if(document.getElementById('classification_1').value==''){
            document.getElementById('classification_1').value='Search by Classification..';
          }
          if(document.getElementById('classification_2').value==''){
            document.getElementById('classification_2').value='Search by Classification..';
          }
       });

       YAHOO.util.Event.addListener('classification', 'click', function(){
            if(this.value=='Search by Classification..'){
                this.value='';
            }
        });
        YAHOO.util.Event.addListener('classification_1', 'click', function(){
            if(this.value=='Search by Classification..'){
                this.value='';
            }
        });
        YAHOO.util.Event.addListener('classification_2', 'click', function(){
            if(this.value=='Search by Classification..'){
                this.value='';
            }
        });
        
        YAHOO.util.Event.addListener('classification', 'blur', function(){
            if(this.value==''){
                this.value='Search by Classification..';
            }
        });
        YAHOO.util.Event.addListener('classification_1', 'blur', function(){
            if(this.value==''){
                this.value='Search by Classification..';
            }
        });
        YAHOO.util.Event.addListener('classification_2', 'blur', function(){
            if(this.value==''){
                this.value='Search by Classification..';
            }
        });
        
        YAHOO.util.Event.addListener('search_form_submit', 'click', function(){
           		//alert("hi");
            if(document.getElementById('classification').value=='Search by Classification..'){
                document.getElementById('classification').value = '';
            }
            if(document.getElementById('classification_1').value=='Search by Classification..'){
                document.getElementById('classification_1').value = '';
            }
            if(document.getElementById('classification_2').value=='Search by Classification..'){
                document.getElementById('classification_2').value = '';
            }
        });
        
        YAHOO.util.Event.addListener('search_form_clear', 'click', function(){
            document.getElementById('classification').value='Search by Classification..';
            document.getElementById('classification_1').value='Search by Classification..';
            document.getElementById('classification_2').value='Search by Classification..';

        });
        
        /* var go_btn_span = document.getElementById("go_btn_span"); 
        if(go_btn_span != null){ 		
        
	        document.getElementById('go_btn_span').innerHTML = '<input type="button" value="Search Blue Book" name="search_bluebook" class="button" onclick=\'location.href="index.php?module=Accounts&action=master_lookup"\'; id="search_bluebook_advanced" title="Search Blue Book">';
	        document.getElementById('go_btn_span').style.display = '';
	        document.getElementById('go_btn_span').style.padding = '0 20px';
           		
        }
           		
       YAHOO.util.Event.onDOMReady(function(){
        
        	//search bluebook button for basic search screen
        	var search_bluebook_button = document.getElementById("search_bluebook_basic");
			
        	if(search_bluebook_button == null){
        	
	            var nodeId = document.getElementById("advanced_search_link");
				var searchButton = document.createElement("input");
				searchButton.setAttribute('type',"button");
				searchButton.setAttribute('id',"search_bluebook_basic");
				searchButton.setAttribute('name',"search_bluebook");
				searchButton.setAttribute('value',"Search Blue Book");
				searchButton.setAttribute('class',"button");
				searchButton.setAttribute('title',"Search Blue Book");
				searchButton.setAttribute('onClick','location.href="index.php?module=Accounts&action=master_lookup"');
				nodeId.parentNode.appendChild(searchButton, nodeId);
			}
			
           	var go_btn_span = document.getElementById("go_btn_span"); 
           	if(go_btn_span != null){ 		
				//search bluebook button for advanced search screen
	        	document.getElementById('go_btn_span').innerHTML = '<input type="button" value="Search Blue Book" name="search_bluebook" class="button" onclick=\'location.href="index.php?module=Accounts&action=master_lookup"\'; id="search_bluebook_advanced" title="Search Blue Book">';
	        	document.getElementById('go_btn_span').style.display = '';
	        	document.getElementById('go_btn_span').style.padding = '0 20px';
           	}
        });
        */
       
        
        YAHOO.util.Event.onDOMReady(function(){
        
        var container = document.createElement('div');
        container.innerHTML = '';  
        container.id = 'classificationContainer';
        
        var container_1 = document.createElement('div');
        container_1.innerHTML = '';  
        container_1.id = 'classification_1Container';

        var container_2 = document.createElement('div');
        container_2.innerHTML = '';  
        container_2.id = 'classification_2Container';
        
        YAHOO.util.Dom.insertAfter(container ,YAHOO.util.Dom.get('classification'));
        YAHOO.util.Dom.insertAfter(container_1 ,YAHOO.util.Dom.get('classification_1'));
        YAHOO.util.Dom.insertAfter(container_2 ,YAHOO.util.Dom.get('classification_2'));
            
        YAHOO.example.classification = function() {
        
	var oConfigs = {
            prehighlightClassName: 'yui-ac-prehighlight',
            queryDelay: 0,
            minQueryLength: 0,
            animVert: .01,
            useIFrame: true
        }
        
        // instantiate remote data source
        var oDS = new YAHOO.util.XHRDataSource('index.php?'); 
        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_HTMLTABLE; 
        oDS.responseSchema = { 
           fields: ['name']            
        };
        
        oDS.maxCacheEntries = 10;         
    
        var oAC = new YAHOO.widget.AutoComplete('classification', 'classificationContainer', oDS, oConfigs);
        var oAC1 = new YAHOO.widget.AutoComplete('classification_1', 'classification_1Container', oDS, oConfigs);
        var oAC2 = new YAHOO.widget.AutoComplete('classification_2', 'classification_2Container', oDS, oConfigs);
        
        oAC.useShadow = true;
        oAC1.useShadow = true;
        oAC2.useShadow = true;
        
        oAC.generateRequest = function(sQuery) { 
	        return 'action=autocomplete&module=Leads&to_pdf=true&classification='+sQuery;
	    }; 
        oAC1.generateRequest = function(sQuery) { 
	        return 'action=autocomplete&module=Leads&to_pdf=true&classification='+sQuery;
	    };
        oAC2.generateRequest = function(sQuery) { 
	        return 'action=autocomplete&module=Leads&to_pdf=true&classification='+sQuery;
	    };
            
        return {
            oDS: oDS,
            oAC: oAC,
            oAC1: oAC1,
            oAC2: oAC2
        };
       }();
        });
        </script>
EOQ;
	}
}
?>
