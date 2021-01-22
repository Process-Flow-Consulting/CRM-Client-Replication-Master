<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

/*********************************************************************************

 * Description: This file is used to override the default Meta-data DetailView behavior
 * to provide customization specific to the Campaigns module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
/**
 * use for deatil view of product template
 * @author Mohit Kumar Gupta
 * @date 15-04-2014
 */
require_once('include/MVC/View/views/view.detail.php');

class AOS_ProductTemplatesViewDetail extends ViewDetail {
    public $productType;
    /**
     * default constructor
     */
 	function AOS_ProductTemplatesViewDetail(){
 	    $this->productType = array('3d300bbc-af00-3974-1a60-4ef4667d5755');
 		parent::ViewDetail();
 	}
 	/**
 	 * default display method
 	 * @see ViewDetail::display()
 	 */
 	function display() {
	    global $mod_strings;
	    $currency = new Currency();
	    if(isset($this->bean->currency_id) && !empty($this->bean->currency_id))
	    {
	    	$currency->retrieve($this->bean->currency_id);
	    	if( $currency->deleted != 1){
	    		$this->ss->assign('CURRENCY', $currency->iso4217 .' '.$currency->symbol);
	    	}else {
	    	    $this->ss->assign('CURRENCY', $currency->getDefaultISO4217() .' '.$currency->getDefaultCurrencySymbol());	
	    	}
	    }else{
	    	$this->ss->assign('CURRENCY', $currency->getDefaultISO4217() .' '.$currency->getDefaultCurrencySymbol());
	    }
	    
	    $this->dv->defs['templateMeta']['form']['buttons'] = array(
	        'EDIT', 
	        'DUPLICATE', 
	        'DELETE',
           // array('customCode' => '<input type="button" class="button" name="pushToQuickBook"  id="pushToQuickBook" value="{$MOD.LBL_PUSH_TOQUICKBOOK}"  onclick="return updatePushToQuickbook(\'{$id}\',\'{$fields.quickbooks_id.value}\');" >',)
	    );
	    
	    	//Only allow if type is line items
	    	if(in_array($this->bean->type_id, $this->productType)){	
	   			//add remove push to QB button
	    		addPushToQuickBooksButton($this->bean,$this->dv);
	    	}
	    //check the access of product catalog for add or modify
	    if (!getProductCatalogUpdateAccess()) {
	        unset($this->dv->defs['templateMeta']['form']['buttons'][0]);
	    }
 		parent::display();
 		echo <<<EOQ
 		<script type='text/javascript'>
 		/**
        * update push to quickbook data to database
        * @author Mohit Kumar Gupta
        * @date 15-04-2014
        */
        function updatePushToQuickbook(record,quickbooks_id){
        	if(trim(record) != ''){
        		$.ajax({
        			type: 'POST',
        			url : 'index.php?to_pdf=1&module=AOS_ProductTemplates&action=pushtoquickbook&record='+record,
        			cache: false,
        			async: true,
        			success:function (data){
        				data = JSON.parse(data);
        				r_url = data.redirect_url;
        				qbFlag = data.quickBookFlag;
        				if(qbFlag == '1'){
        					ajaxStatus.showStatus('{$mod_strings['LBL_QUICKBOOK_ALREADY_UPDATED']}');
        				} else if (qbFlag == '0') {
        					ajaxStatus.showStatus('{$mod_strings['LBL_QUICKBOOK_UPDATED']}');
        					window.location.href = r_url;
        				}
        				return true;
        			},
        			error:function(data){
        				ajaxStatus.showStatus('{$mod_strings['LBL_QUICKBOOK_ERROR_PROCESSING']}');
        				return true;
        			}
        		});
        	}
        }
 		</script>
EOQ;
 	}
}
