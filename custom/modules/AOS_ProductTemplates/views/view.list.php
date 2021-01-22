<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)	die ( 'Not A Valid Entry Point' );
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

require_once 'include/MVC/View/views/view.list.php';
/**
 * extends list view class
 * @author Mohit Kumar Gupta
 * @date 22-oct-2013
 *
 */
class AOS_ProductTemplatesViewList extends ViewList{	
	/**
	 * default display function and override for custom list view
	 * @see ViewList::display()
	 */
	function display(){
	    //check the access of product catalog for add or modify
	    if (!getProductCatalogUpdateAccess()) {
	    	$this->lv->quickViewLinks = false;
	    }
	    
		require_once 'custom/modules/AOS_ProductTemplates/customProductTemplate.php';
		$this->bean = new customProductTemplate();
		parent::display();	
	}
	/**
	 * override listViewProcess function for updateing ListViewGeneric.tpl
	 * for changing label for no record
	 * @author Mohit Kumar Gupta
	 * @date 17-04-2013
	 * @see ViewList::listViewProcess()
	 */
	public function listViewProcess() {	    
	    if ((empty ( $_REQUEST ['search_form_only'] ) || $_REQUEST ['search_form_only'] == false) && !getProductCatalogUpdateAccess()) {
	        $this->processSearchForm ();
	        $this->lv->setup ( $this->seed, 'custom/modules/AOS_ProductTemplates/tpls/ListViewGeneric.tpl', $this->where, $this->params );
	        echo $this->lv->display ();
	    } else {
	    	parent::listViewProcess();
	    }
	}
}