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

require_once ('include/MVC/View/views/view.list.php');
require_once 'custom/modules/Contacts/CustomListViewSmarty.php';
require_once 'custom/include/common_functions.php';

class ContactsViewList extends ViewList {
	
	public function ContactsViewList() {
		parent::ViewList ();
	}
	public function preDisplay() {
		//parent::preDisplay ();
		$this->lv = new CustomListViewSmarty();
		$this->lv->targetList = true;
		//to allow browser's back button for search
    	header('Expires: -1');                
        header( 'Cache-Control: must-revalidate, post-check=3600, pre-check=3600' );
	}
	public function listViewProcess() {
		$this->processSearchForm ();
		
		$this->lv->searchColumns = $this->searchForm->searchColumns;
		
		$custom_where = " contacts.visibility = '1' ";
		
		
		if ($this->where != '' && $custom_where != '') {
			$this->where .= ' AND ';
		}
		
		$this->where .= $custom_where;
		
		//bug fix - if not saved search
		if(!isset($_REQUEST['saved_search_select']) || empty($_REQUEST['saved_search_select']) ){
			unset($_SESSION['LastSavedView'][$this->module]);
		}
		
		
		if (! $this->headers)
			return;
		
		if (empty ( $_REQUEST ['search_form_only'] ) || $_REQUEST ['search_form_only'] == false) {
			$this->lv->setup ( $this->seed, 'include/ListView/ListViewGeneric.tpl', $this->where, $this->params );
			$savedSearchName = empty ( $_REQUEST ['saved_search_select_name'] ) ? '' : (' - ' . $_REQUEST ['saved_search_select_name']);
			// echo
			// get_form_header($GLOBALS['mod_strings']['LBL_LIST_FORM_TITLE'] .
			// $savedSearchName, '', false);
			echo $this->lv->display ();
		}
	}
	
	function display(){
		
		require_once 'custom/modules/Contacts/customContact.php';
		$this->bean = new customContact ();
		
		parent:: display();
		
		echo <<<EOQ
<script type='text/javascript'>
		
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
