<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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

 * Description: This file is used to override the default Meta-data EditView behavior
 * to provide customization specific to the Contacts module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/MVC/View/views/view.edit.php');

class AOS_ProductTemplatesViewEdit extends ViewEdit 
{   
 	public function AOS_ProductTemplatesViewEdit()
 	{
 		
 		parent::ViewEdit();
 		$this->useModuleQuickCreateTemplate = true;
 		$this->useForSubpanel = true;
 		
 		//check the access of product catalog for add or modify
 		//Modify By Mohit Kumar Gupta
 		//@date 17-04-2014
 		if (!getProductCatalogUpdateAccess()) {
 		    if (isset($_REQUEST['record']) && $_REQUEST['record'] !='') {
 		    	SugarApplication::redirect('index.php?module=AOS_ProductTemplates&action=DetailView&record='.$_REQUEST['record']);
 		    } else {
 		        SugarApplication::redirect('index.php?module=AOS_ProductTemplates&action=index');
 		    }
 		}
 	}
 	public function display() 
 	{
 		$disableType = 0;
 		
		$unitOfmeasure = getSavedUnitOfMeasure();
 	    $unitOfmeasureList = get_select_options_with_id($unitOfmeasure, $this->bean->unit_measure);
 	    $this->ss->assign('UNIT_MEASURE_LIST',$unitOfmeasureList);
 	     /* if (isset($_REQUEST['record']) && $_REQUEST['record'] !='') {
 	     	if($this->bean->quickbook_type)
 			$disableType = 1;	     	
 	     } */
        parent::display();

		echo "<script type='text/javascript'> 
		  
		  YAHOO.util.Event.addListener('type_id', 'change', changeCost);
		  YAHOO.util.Event.onDOMReady(function(){
				changeCost();
		  });

		  function changeCost(){
		  
				var type_value = document.EditView.type_id;
				var type_text = type_value.options[type_value.selectedIndex].text;
			  
				if(type_text=='Inclusions' || type_text=='Exclusions'){
				
				var cost_price_label = document.getElementById('cost_price_label');
				if(cost_price_label.childNodes.length > 3){
					cost_price_label.removeChild(cost_price_label.childNodes[3]);
				}                        
				
				var discount_price_label = document.getElementById('discount_price_label');
				if(discount_price_label.childNodes.length > 3){
					discount_price_label.removeChild(discount_price_label.childNodes[3]);
				}
				removeFromValidate('EditView', 'cost_price', 'currency', true,'Price/Rate' );
				removeFromValidate('EditView', 'discount_price', 'currency', true,'Unit Price' );                         
			  
			 }else{

				var cost_price_label = document.getElementById('cost_price_label');
				if(cost_price_label.childNodes.length < 4){
					var required = document.createElement(\"span\");
					required.setAttribute('class','required'); 
					required.innerHTML = \"*\";
					cost_price_label.insertBefore(required,cost_price_label.childNodes[3]);
				}                                  
				
				var discount_price_label = document.getElementById('discount_price_label');
				if(discount_price_label.childNodes.length < 4){ 
					var required = document.createElement(\"span\");
					required.setAttribute('class','required'); 
					required.innerHTML = \"*\";
					discount_price_label.insertBefore(required,discount_price_label.childNodes[3]);
				}
				
				addToValidate('EditView', 'cost_price', 'currency', true,'Price/Rate' );
				addToValidate('EditView', 'discount_price', 'currency', true,'Unit Price' );
				
			  }
		  }
		  
		  YUI().use('node','event',function(Y){
		  
				
				Y.all('input.primary').each(function (k){
							
							k.set('onmouseover',calculatePrice)
				}                			
				);
		  
				
				Y.one('#cost_price').on('blur',calculatePrice)
				Y.one('#markup_inper').on('click',calculatePrice)              		
				Y.one('#markup').on('blur',calculatePrice)
				Y.one('#quantity').on('blur',calculatePrice)
				
				function calculatePrice(){ 							
												 
					var precision = '2';
					var uPrice = Y.one('#discount_price');
					var cPrice = Y.one('#cost_price');
					var markup = Y.one('#markup');
					var qty = Y.one('#quantity');
					var tPrice = Y.one('#total_cost');
					
					
					rate = unformatNumber(cPrice.get('value'), num_grp_sep, dec_sep)
					qtyVal = (unformatNumber(qty.get('value'), num_grp_sep, dec_sep) == 0)?'1':unformatNumber(qty.get('value'), num_grp_sep, dec_sep);                  			  
					markupVal  = unformatNumber(markup.get('value'), num_grp_sep, dec_sep)
					  
					if(Y.one('#markup_inper').get('checked')){
					  
						stTotal = toDecimal((qtyVal*rate)*(1+(markupVal/100)),precision);
						tPrice.set('value',formatNumber(stTotal,num_grp_sep, dec_sep, precision, precision));
						perUnit = stTotal/qtyVal;                  			  
						uPrice.set('value',formatNumber(perUnit,num_grp_sep, dec_sep, precision, precision));							              			                 			
					}else{
					  
					  
					  stTotal = toDecimal((qtyVal*rate)+markupVal,precision);                  			    
					  tPrice.set('value',formatNumber(stTotal,num_grp_sep, dec_sep, precision, precision));
					  
					  perUnit = stTotal/qtyVal;
					  
					  uPrice.set('value',formatNumber(perUnit,num_grp_sep, dec_sep, precision, precision));						
					
					}
				}

				
			});
		</script>";
 	}	
}

?>
