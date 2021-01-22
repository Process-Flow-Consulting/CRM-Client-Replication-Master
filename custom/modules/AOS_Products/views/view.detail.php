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


require_once('include/MVC/View/views/view.detail.php');
require_once 'custom/include/common_functions.php';

class AOS_ProductsViewDetail extends ViewDetail {

 	function AOS_ProductsViewDetail(){
 		parent::ViewDetail();
 	}
 	
 	function display() {
	    
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
	    
	    //$account_proview_link = $this->setAccountProviewLink($this->bean);
	    $account_proview_link = proview_url(array('url'=>$this->bean->account_proview_url));
	    $account_name = '<a href="index.php?module=Accounts&action=DetailView&record='.$this->bean->account_id.'">'.$this->bean->account_name.'</a>';
	    
	    $this->ss->assign('ACCOUNT_NAME',$account_proview_link.'&nbsp;'.$account_name);
	    
	   	    
 		parent::display();
 	}
//  	public function setAccountProviewLink(&$focus){
 	
//  		if($focus->account_proview_url != '')
//  		{
//  			$focus->account_proview_url = $focus->account_proview_url;
//  			if (preg_match('/^[^:\/]*:\/\/.*/', $focus->account_proview_url)) {
//  				$focus->account_proview_url= $focus->account_proview_url;
//  			} else {
//  				$focus->account_proview_url = 'http://' . $focus->account_proview_url;
//  			}
 	
//  			$focus->account_proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->account_proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//  		}
 		
//  		else{
//  			$focus->account_proview_url = '';
//  			//$focus->account_proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//  		}
 	
//  		return $focus->account_proview_url;
//  	}
}
