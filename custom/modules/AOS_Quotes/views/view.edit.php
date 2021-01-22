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
 * to provide customization specific to the AOS_Quotes module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/MVC/View/views/view.edit.php');
require_once('include/SugarTinyMCE.php');
class AOS_QuotesViewEdit extends ViewEdit
{
    
    function __construct(){
    	parent::ViewEdit();
    	$this->useForSubpanel = true;
    }
    /**
     * @see ViewEdit::preDisplay()
     */
	function preDisplay(){
	    global $mod_strings;
	    //restrict user to edit proposal,if propsal and instance level sales tax settings are different
	    //@modifed by Mohit Kumar Gupta 27-06-2014
	    $salesTaxFlag = $this->bean->sales_tax_flag;
	    $salesTaxSttings = salesTaxSettings();
	    if ($salesTaxFlag != $salesTaxSttings) {
	        if ($salesTaxFlag == 'per_item') {
	        	sugar_die($mod_strings['ERROR_SALES_TAX_TOTAL_LINE_ITEM']);
	        } else if ($salesTaxFlag == 'total_item') {
	            sugar_die($mod_strings['ERROR_SALES_TAX_PER_LINE_ITEM']);
	        }
	    }
		$metadataFile = $this->getMetaDataFile();
		$this->ev = new EditView();
		$this->ev->ss =& $this->ss;
		$this->ev->setup($this->module, $this->bean, $metadataFile, 'custom/modules/AOS_Quotes/tpls/EditView.tpl');
		Parent::preDisplay();
	}
	
    /**
 	 * @see SugarView::display()
 	 */
 	public function display()
 	{
 	    require_once('modules/AOS_Quotes/Layouts.php');
		require_once('include/EditView/EditView2.php');


		global $beanFiles;
		require_once($beanFiles['AOS_Quotes']);
		require_once($beanFiles['AOS_Products']);		

		global $mod_strings;
		global $app_strings;
		global $app_list_strings;
		global $current_user;
		global $timedate;
		global $locale;
		global $db;
		


		$original_quote = new AOS_Quotes();
		if($this->ev->isDuplicate)
		{
			$this->bean->id = "";
			$this->bean->quote_num = "";
			$this->bean->number = "";
			$original_quote->retrieve($_REQUEST['record']);
		}
		// Added by Ashutosh to set the layout options
		if(isset( $this->bean->layout_options) && trim( $this->bean->layout_options) != ''){
					 $arProposalLayoutOptions = unserialize(base64_decode($this->bean->layout_options));
					 
	    }else{
		
			//set layout options 
			$arProposalLayoutOptions = array( 'line_items' => 1,
					'line_itmes_subtotal' => 1,
					'inclusions' =>1,
					'inclusion_subtotal' => 1,
					'exclusions' => 1,
					'exclusions_total' => 1,
					'exclusions_tax' => 1,
					'exclusions_shipping' => 1,
					'exclusions_grand_total' => 1,
					'alternates' => 1,
					'description_panel' => 1,
					'description_placement' => 'bottom',
			);
	   }
	   
	   $disabledUnitOfMeasure = '';
	   //if unit of measure set to quickbooks and product template is disabled for add/edit
		if (!getProductCatalogUpdateAccess()) {
	       $disabledUnitOfMeasure = ' disabled="true"';
	   	} 
	 
		$arLayoutRadios = array('top' => 'On Top','bottom' => 'On Bottom');
		$tiny = new SugarTinyMCE();		
	    $tiny->buttonConfigs['default']['buttonConfig2'] = $tiny->buttonConfigs['default']['email_compose_light'];
	    $tiny->defaultConfig['width'] = 250;
	     $tiny->defaultConfig['init_instance_callback'] = 'fillFormStringValue';
		$this->ss->assign("tinyjs", $tiny->getInstance('description'));
		$this->ss->assign('LAYOUT_PLCEMENT_RADIO_OPTION',$arLayoutRadios);
		$this->ss->assign('LAYOUT_OPTIONS',$arProposalLayoutOptions);
		
		//for IE we will display html file element
		if (isset($_SERVER['HTTP_USER_AGENT']) &&
		(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
		{
		    $this->ss->assign('IS_IE',true);
		}else {
		    $this->ss->assign('IS_IE',false);
		}
		
		
		/** Tax Class -- Added By Hirak **/
		//get the sales tax settings
		if(empty($this->bean->sales_tax_flag)){
		    $salesTaxFlag = salesTaxSettings();
		}else{
		    $salesTaxFlag = $this->bean->sales_tax_flag;
		}
		$this->ss->assign('SALES_TAX_FLAG', $salesTaxFlag);
		$this->ev->defs['templateMeta']['form']['hidden'][] = '<input type="hidden" name="sales_tax_flag" id="sales_tax_flag" value="'.$salesTaxFlag.'" >';
		$this->ss->assign('AR_TAX_CLASS',$app_list_strings['tax_class_dom']);
		
		//$arTaxRate = array( '' => '');
		$sqlTaxRates = "SELECT id, name FROM aos_taxrates WHERE deleted = 0 AND status = 'Active' ORDER BY list_order";
		$resultTaxRates = $db->query($sqlTaxRates);
		while ($rowTaxRates = $db->fetchByAssoc($resultTaxRates)){
		    $arTaxRate[$rowTaxRates['id']] = $rowTaxRates['name'];
		}
		
		$sqlTaxGroups = "SELECT id, name FROM oss_itemsalestaxgroup WHERE deleted = 0 AND is_active = '1' ORDER BY date_entered";
		$resultTaxGroups = $db->query($sqlTaxGroups);
		while ($rowTaxGroups = $db->fetchByAssoc($resultTaxGroups)){
		    $arTaxRate[$rowTaxGroups['id']] = $rowTaxGroups['name'];
		}
		
		$taxRateId = '';
		if(empty( $this->bean->taxrate_id) && !empty ($this->bean->fetched_row['taxrate_id']) ){
		    $taxRateId = $this->bean->fetched_row['taxrate_id'];
		}else{
		    $taxRateId = $this->bean->taxrate_id;
		}
		
		$tax_rate = 0.00;
		
		$arTaxRateKeys = array_keys($arTaxRate);
		$taxRateId = (!empty($taxRateId)) ? $taxRateId : $arTaxRateKeys[0];
		
		if(!empty($taxRateId)){
		
    		$sqlTaxRates = "SELECT id, value FROM aos_taxrates WHERE deleted = 0 AND status = 'Active' AND id = '".$taxRateId."' ";
    		$resultTaxRates = $db->query($sqlTaxRates);
    		$rowTaxRates = $db->fetchByAssoc($resultTaxRates);
    		if(!empty($rowTaxRates['id'])){
    		    $tax_rate =  $rowTaxRates['value'];;
    		}else{
    		    $taxRateTotal = 0.00;
    		    $sqlTaxGroups = "SELECT id, name FROM oss_itemsalestaxgroup WHERE deleted = 0 AND is_active = '1' AND id = '".$taxRateId."' ";
    		    $resultTaxGroups = $db->query($sqlTaxGroups);
    		    $rowTaxGroups = $db->fetchByAssoc($resultTaxGroups);
    		    if(!empty($rowTaxGroups['id'])){
    		        $taxGroup = new oss_ItemSalesTaxGroup();
    		        $taxGroup->retrieve($rowTaxGroups['id']);
    		        $taxGroup->load_relationship('taxrate_taxgroup');
    		        $taxRates = $taxGroup->taxrate_taxgroup->get();
    		        foreach ($taxRates as $taxRate){
    		            $sqlTaxRates = "SELECT id, value FROM aos_taxrates WHERE deleted = 0 AND status = 'Active' AND id = '".$taxRate."' ";
    		            $resultTaxRates = $db->query($sqlTaxRates);
    		            $rowTaxRates = $db->fetchByAssoc($resultTaxRates);
    		    
    		            $taxRateTotal = $taxRateTotal + $rowTaxRates['value'];
    		    
    		        }
    		        $tax_rate =  $taxRateTotal;
    		    }
    		}
		}
		$tax_rate = number_format($tax_rate, 2);
		$this->ss->assign('TAX_RATE', $tax_rate);
		$this->ss->assign('TAX_RATE_PER', '('.$tax_rate.' %)' );
		
		$this->ss->assign('AR_TAX_RATES',get_select_options_with_id($arTaxRate, $taxRateId ));
		/** Tax Class -- Added By Hirak **/
		
		
		/** Tax Class -- Edited By Hirak **/
		// Create the javascript code to render the rows
		$add_row = '';
		if(!empty($this->bean->id)){
			$product = new AOS_Products();
			$where = " aos_products.quote_id='".$this->bean->id."' ";
			$products = $product->get_full_list('indexs',$where);
			if (count ( $products ) > 0) {
				foreach ( $products as $prod ) {
					$table_name = $prod->product_type . '_table';
					$prod_desc = js_escape(br2nl($prod->description));
					//Modified by Mohit Kumar Gupta
					//for handling special character in name field
					//@date 22-oct-2013
					$prodName = js_escape(br2nl($prod->name));					
					$add_row .= "addLineItemRow('$prod->id','$prod->product_type','$prod->quantity','$prod->qty_show','$prod->product_template_id','$prodName','$prod->title_show','" . number_format ( $prod->cost_price, 2, '.', '' ) . "','$prod->price_show','" . number_format ( $prod->list_price, 2, '.', '' ) . "','". number_format ( $prod->total, 2, '.', '' )."','$prod->total_show','". number_format($prod->discount_price, 2, '.', ''). "','$prod->in_hours','". number_format($prod->unit_price, 2, '.', ''). "','','".number_format($prod->bb_tax, 2, '.', '')."','$prod->bb_tax_per','','$table_name','','','','$prod_desc','$prod->desc_show','','".number_format($prod->discount_amount, 2, '.', '')."','$prod->discount_select','','$prod->bb_shipping','$prod->markup_inper','0','$prod->unit_measure','$prod->unit_measure_name', '$prod->tax_class');";
				}
			}
			
		}else{
			$li_desc='To begin creating your proposal, click on Add Item for either "Line Items", "Inclusions" or "Exclusions"';
			$inc_desc = 'There are three distinct elements for proposal creation within Project Pipeline <br>';
			$inc_desc .= '1.) "Add Item" is for any item that needs to be related to quantity or rate.<br>';
			$inc_desc .= '2.) "Inclusion" is for any item that is included on the proposal but does not need to be related to quantity or rate.<br>';
			$inc_desc .= '3.) "Exclusion" is for any item that IS NOT a part of your proposal.';
			$exc_desc = 'All Created "Line Items", "Inclusions" and "Exclusions" are stored (by Title) in your product catalog for future selection and for reporting capabilities.';
			$add_row .="addLineItemRow('','line_items','1','1','','This is Sample Text.','1','0.00','1','0.00','0.00','1','0.00','','0.00','','0.00','0.00','','line_items_table','','','','$li_desc','1','','0.00','','','0.00','0','0','','', 'Taxable');";
			$add_row .="addLineItemRow('','inclusions','1','1','','This is Sample Text.','1','0.00','1','0.00','0.00','1','0.00','','0.00','','0.00','0.00','','inclusions_table','','','','$inc_desc','1','','0.00','','','0.00','0','0','','', 'Taxable');";
			$add_row .="addLineItemRow('','exclusions','1','1','','This is Sample Text.','1','0.00','1','0.00','0.00','1','0.00','','0.00','','0.00','0.00','','exclusions_table','','','','$exc_desc','1','','0.00','','','0.00','0','0','','', 'Taxable');";
		}
		/** Tax Class -- Edited By Hirak **/
		
		$add_row = 'function add_rows_on_load() {' . $add_row . '}';
		$this->ss->assign("ADD_ROWS", $add_row);

		$str = "<script language=\"javascript\">
		YAHOO.util.Event.addListener(window, 'load', add_rows_on_load);
		</script>";
		$this->ss->assign('SAVED_SEARCH_SELECTS', $str);
		
		$attached_document = array();
		$attached_documents = '';
		
		//$this->bean->load_relationship('documents');
		//$docs = $this->bean->documents->get();
		$docs = 0;
		$document_uploaded = '';
		if(count($docs) > 0){
			$document_uploaded = 'true';
			
			//show document name if documents attached
			foreach($docs as $doc){
				
				$document = new Document();
				$document->retrieve($doc);
				if(trim($document->name) != '')
				$attached_document[] = $document->name;
				unset($document);
			}
			
			if(count($attached_document) > 0){
				$attached_documents = $mod_strings['LBL_ATTACHED_DOCUMENTS'].": ".implode(", ", $attached_document);
			}
		}
		
		$this->ss->assign('ATTACHED_DOCUMENTS', $attached_documents);
		$this->ss->assign('document_uploaded', $document_uploaded);     
		
		$verify_proposal = 0;		
		if($this->bean->proposal_verified==1){
			$verify_proposal = 1;
		}
		$this->ss->assign('verify_proposal',$verify_proposal);
		if(!empty($this->bean->id)){					
			
			
			require_once 'custom/include/OssTimeDate.php';
			$oss_timedate = new OssTimeDate();			
			
			//date time proposal scheduled delivery
			$bid_due_date_time = $oss_timedate->convertDBDateForDisplay($this->bean->date_time_delivery, $this->bean->delivery_timezone, true);
			$this->bean->date_time_delivery = $bid_due_date_time;
			
			//date time proposal sent
			$date_time_sent = $oss_timedate->convertDBDateForDisplay($this->bean->date_time_sent, $this->bean->delivery_timezone, true);
			$this->bean->date_time_sent = $date_time_sent;
			
			//date time proposal received
			$date_time_received = $oss_timedate->convertDBDateForDisplay($this->bean->date_time_received, $this->bean->delivery_timezone, true);
			$this->bean->date_time_received = $date_time_received;
			
			//date time proposal opened
			$date_time_opened = $oss_timedate->convertDBDateForDisplay($this->bean->date_time_opened, $this->bean->delivery_timezone, true);
			$this->bean->date_time_opened = $date_time_opened;
			
			//Get Last modified date from quote and produt audit table			
			/*$audit_sql = "SELECT MAX(distinct(date_created)) last_modified_date FROM quotes_audit UNION SELECT MAX(distinct(date_created)) last_modified_date FROM products_audit";
			$audit_query = $db->query($audit_sql);
			$audit_result_array = array();
			while ($audit_result = $db->fetchByAssoc($audit_query)){
				 $audit_result_array[]=$audit_result['last_modified_date'];
			}				
			$this->ss->assign('proposal_audit_date', $audit_result_array[0]);
			$this->ss->assign('product_audit_date', $audit_result_array[1]);*/
			
			$bDisplayCancelIcon = false;
			
			if($this->bean->proposal_verified == '1' && $this->bean->date_time_delivery != '' && $obProposal->proposal_delivery_method != 'M'){
				
				$bDisplayCancelIcon = true;
				
				
			}
			$this->ss->assign('displayCancelIcon',$bDisplayCancelIcon);
			$this->ss->assign('subtotal',number_format($this->bean->subtotal_amount,2,'.',''));
			$this->ss->assign('subtotal_inclusion', number_format($this->bean->subtotal_inclusion,2,'.',''));
			$this->ss->assign('grand_subtotal',number_format($this->bean->grand_subtotal,2,'.',''));
			$this->ss->assign('tax',number_format($this->bean->tax,2,'.',''));
			$this->ss->assign('shipping',number_format($this->bean->shipping,2,'.',''));
			$this->ss->assign('total',number_format($this->bean->total,2,'.',''));
			
		}
		
		//set opportunity amount
		if(!empty($this->bean->opportunity_id)){
            $sqlOpportunity = " SELECT amount FROM opportunities WHERE id = '".$this->bean->opportunity_id."' AND deleted = 0";
            $resultOpportunity = $db->query($sqlOpportunity);
            $rowOpportunity = $db->fetchByAssoc($resultOpportunity);
            $this->ss->assign('OPPORTUNITY_AMOUNT',number_format($rowOpportunity['amount'],2,'.',''));
		}

		//check the access of product catalog for add or modify
		$disableProductCatalog = 0;
		if (!getProductCatalogUpdateAccess()) {
		    $disableProductCatalog = 1;
		}
		$this->ss->assign('DISABLEPRODUCTCATALOG',$disableProductCatalog);
		$proposal_unit_measure_dom  = getSavedUnitOfMeasure();
		$this->ss->assign('PROPOSAL_AMOUNT',number_format($this->bean->proposal_amount,2,'.',''));
		$this->ss->assign('AR_UNIT_MEASURE',$proposal_unit_measure_dom);
		$this->ss->assign('AR_UNIT_MEASURE_JSON',json_encode($proposal_unit_measure_dom));
		$this->ss->assign("UNIT_OF_MEASURE_DISABLED",$disabledUnitOfMeasure);
		
 		parent::display();
		
		//BBSMP-236 -- Start
		/* echo "<script>
		$('#detailpanel_1').before('<h4><span id=\'show_verify_message\' style=\'font-size: 12px; margin: 0px; padding: 0 0 0 30px; color: #FF0000;display: none;\'>Note: Proposals will not be sent until the Proposal has been verified.</span></h4>');
		</script>"; */
		//BBSMP-236 -- End
		
 		echo "<script type=\"text/javascript\">
 				
function check_form_custom(){	
			
 				
 	var schdeule_interval_error_message = '".$mod_strings['ERROR_SCHEDULE_PROPOSAL_INTERVAL']."';			

 	var proposal_delivery_method_val = '';
	proposal_delivery_method_val = $('input:radio[name=proposal_delivery_method]:checked').val();

 	//proposal delivery method must be selected
 	if( typeof(proposal_delivery_method_val) == 'undefined' || proposal_delivery_method_val==''){
 			mySimpleDialog = getSimpleDialog();
 			mySimpleDialog.setHeader('Error!');
			mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_ERROR);
			mySimpleDialog.setBody('You must select a delivery method for your proposal');
			var myButtons = [
				{ text: 'OK', handler: handleCancel }		    
			];
			mySimpleDialog.cfg.queueProperty('buttons', myButtons);
 			mySimpleDialog.render(document.body);
 			mySimpleDialog.show();
 			return false;
 	}
 			
	removeFromValidate('EditView', 'contact_email', 'email', true,'Contact Email' );
	removeFromValidate('EditView', 'contact_fax', 'phone', true,'Contact Fax');
	addToValidate('EditView', 'date_time_delivery', 'date', true ,'Delivery Date/Time');
 			
 	//hirak - date : 11-10-2012
	if(proposal_delivery_method_val == 'E'){		
		addToValidate('EditView', 'contact_email', 'email', true,'Contact Email');		
	}

	if(proposal_delivery_method_val == 'F'){
		addToValidate('EditView', 'contact_fax', 'phone', true,'Contact Fax');
	}
	
	if(proposal_delivery_method_val == 'EF'){		
		addToValidate('EditView', 'contact_email', 'email', true,'Contact Email');
		addToValidate('EditView', 'contact_fax', 'phone', true,'Contact Fax');		
	}else{
 		addToValidate('EditView', 'contact_fax', 'phone', false,'Contact Fax');
 	}

	removeFromValidate('EditView', 'delivery_timezone', 'enum', true,'Delivery Time Zone' );
 	
	if(document.getElementById('date_time_delivery').value != ''){	
		addToValidate('EditView', 'delivery_timezone', 'enum', true,'Delivery Time Zone' );
	}
 	
 	if(proposal_delivery_method_val == 'M'){
 		removeFromValidate('EditView', 'contact_email', 'email', true,'Contact Email' );
		removeFromValidate('EditView', 'contact_fax', 'phone', true,'Contact Fax');	
 		removeFromValidate('EditView', 'delivery_timezone', 'enum', true,'Delivery Time Zone' );
 		removeFromValidate('EditView', 'date_time_delivery', 'date', true ,'Delivery Date/Time');
 	}
 	

 	
	//hirak - date : 11-10-2012
 	if(document.getElementById('date_time_delivery').value != '' 
 				&& document.getElementById('delivery_timezone').value != '' 
 				&& (proposal_delivery_method_val == 'E' 
 				|| proposal_delivery_method_val == 'F'
 				|| proposal_delivery_method_val == 'EF') )
 	{
 		var scheduled_date_time_delivery = document.getElementById('date_time_delivery').value;
 		var scheduled_delivery_time_zone = document.getElementById('delivery_timezone').value;
 		var emailAddr = document.getElementById('contact_email').value;
 		var callback = {
                success:function(o){
 				//alert(o.responseText)
 			response = JSON.parse(o.responseText);
 		
                //alert(response.datePrevious+'  '+response.emailOptout);
 				if(response.datePrevious == 1){
 					
 					mySimpleDialog = getSimpleDialog();
 					mySimpleDialog.setHeader('Error!');
					mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_ERROR);
			    	mySimpleDialog.setBody(schdeule_interval_error_message);
				    var myButtons = [
				    { text: 'OK', handler: handleCancel }		    
				    ];
				    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
				    mySimpleDialog.render(document.body);    
				    mySimpleDialog.show();
 					return false;
 				
 				}else if(response.emailOptout == 1){
 					mySimpleDialog = getSimpleDialog();
 					mySimpleDialog.setHeader('Warning!');
					mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_ERROR);
			    	mySimpleDialog.setBody(SUGAR.language.get('AOS_Quotes','LBL_OPT_OUT_MSG'));
				    var myButtons = [
				    { text: 'OK', handler: handleCancel }		    
				    ];
				    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
				    mySimpleDialog.render(document.body);    
				    mySimpleDialog.show();
 					return false;
 				
 				}
 			else {
					if(check_form('EditView')){
 							 save_updated();
 					}
 				}
                return false;
              }
        }
        YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=AOS_Quotes&action=date_diff&date_time='+scheduled_date_time_delivery+'&to_pdf=true&timezone='+scheduled_delivery_time_zone+'&contact_email='+emailAddr, callback);
        return false;
 	
 	}else{						
 		return check_form('EditView');
    }
} 				

var handleCancel = function(){
 	this.hide();	
}; 			
 			
var reverify_message = '".$mod_strings['LBL_REVERIFY_MSG']."';
var mySimpleDialog ='';
function getSimpleDialog(){
if (typeof(mySimpleDialog) != 'undefined' && mySimpleDialog != ''){
	mySimpleDialog.destroy(); 
}
	mySimpleDialog = new YAHOO.widget.SimpleDialog('dlg', { 
    width: '40em', 
    effect:{
        effect: YAHOO.widget.ContainerEffect.FADE,
        duration: 0.25
    }, 
    fixedcenter: true,
    modal: true,
    visible: false,
    draggable: false
});
    
mySimpleDialog.setHeader('Warning!');
mySimpleDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
return mySimpleDialog;
}

//Tax Class -- Edited By Hirak
var restricted_array = new Array('pre_form_string','date_time_delivery','date_time_delivery_date','container_date_time_delivery_trigger','delivery_timezone','container_date_time_sent_trigger','container_date_time_received_trigger','container_date_time_opened_trigger','arrow_team_name','action','dlg','skip_delivery_date','pre_cost_price','pre_unit_price','pre_markup','pre_desc','product_tpl_id','product_tpl_name','pop_product_name','product_show','product_desc_show','in_hours','radio_hours','pop_quantity','quantity_show','radio_price','radio_rate','pop_price','pop_markup_inper','pop_markup','pop_unit_price','pop_total','pop_total_show','pop_tax_amount','pop_shipping','pop_discount','pop_discount_price','pop_disc_inper','pop_save','pop_delete','pop_cancel','pre_quantity','pre_markup_inper','pop_tax','pre_pc_name','in_hours_hnd','in_rates','pop_price_show','description_placement','pop_unit_measure','pre_unit_measure', 'pop_tax_class', 'pre_tax_class');
//Tax Class -- Edited By Hirak

var checkbox_array = new Array('delivery_method_email','delivery_method_fax','delivery_method_both');

function checkInArray(arr,str){
	match=false;
	var array_count = arr.length;	
	for(var i=0; i<array_count; i++){
		if(arr[i]==str){
			match=true;
		}
	}
	return match;
}

function getFormString(){
	var form_string = '';
	var ele = $('input[type=hidden],input[type=text], textarea');
	for(var i=0; i<ele.length; i++){    
		var field_name = ele[i].id;
			if(ele[i].id == ''){
		    	field_name = ele[i].name;
			}			
		if(checkInArray(restricted_array,field_name)!=true){
			field_value = ele[i].value;
                    
           //  if(checkInArray(checkbox_array,field_name) == true){
                 
				if(ele[i].type == 'checkbox'){
				   if(ele[i].checked==true){
				      field_value = '1';
				   }else{
				 	  field_value = '0';
				   }
			    }
			//}
		
			form_string += '&'+field_name+'='+field_value;
		}		  	
	}
	//Add Select Fields
	var quote_stage = document.getElementById('stage').value;
	var billing_address_state = document.getElementById('billing_address_state').value;
	var street_address = document.getElementById('billing_address_street').value;
	var desc = '';
	if(document.forms['EditView']){
//alert(tinyMCE.activeEditor);
            if(tinyMCE.activeEditor == null ){
                desc = document.forms['EditView'].description.value;
            }else{
     	        desc = tinyMCE.activeEditor.getContent();
            }
 	//desc = document.forms['EditView'].description.value;
	//desc = tinyMCE.activeEditor.getContent();
 	}	
	form_string += '&quote_stage='+quote_stage+'&billing_address_state='+billing_address_state+'&billing_address_street='+street_address+'&description='+desc+'&description_placement='+$('input[name=description_placement]:checked').val();
	return form_string;
}

function fillFormStringValue(){	
	var form_value = getFormString();
	document.getElementById('pre_form_string').value = form_value;
	var btn_save = YAHOO.util.Selector.query('input[name=button]');
	for(var i=0; i<btn_save.length; i++){
		if(btn_save[i].id == 'SAVE'){
			btn_save[i].disabled=false;
 		}
 	}	
}

var handleYesQCVerify = function(){
 	document.getElementById('is_form_updated').value = '1';
 	this.hide();
 	document.forms['form_DCQuickCreate_AOS_Quotes'].action.value='Save';
 	return DCMenu.save(document.forms['form_DCQuickCreate_AOS_Quotes'].id, 'AOS_Quotes_subpanel_save_button'); 		
};
window.onload=fillFormStringValue;

$(document).ready(function(){
fillFormStringValue();
});
       

 		YAHOO.util.Event.onAvailable('date_time_delivery_minutes',function(){
 			var dtdm = document.getElementById('date_time_delivery_minutes');
 			dtdm.remove(2);
 			dtdm.remove(3);
 		}); 		 		
 		sqs_must_match = false;		
 		
 		//Submit Quick Edit Form
 		
 		function saveQuickEdit(){ 			
 			if(check_form('form_DCQuickCreate_AOS_Quotes')){
 				var pre_form_val = document.getElementById('pre_form_string').value;
 				var new_form_val = getFormString(); 
 				if(pre_form_val != new_form_val){
 					mySimpleDialog = getSimpleDialog(); 
			    	mySimpleDialog.setBody(reverify_message);
				    var myButtons = [
				    { text: 'OK', handler: handleYesQCVerify }		    
				    ];
				    mySimpleDialog.cfg.queueProperty('buttons', myButtons);  
				    mySimpleDialog.render(document.body);    
				    mySimpleDialog.show();
 					return false;
 				}else{
 					document.forms['form_DCQuickCreate_AOS_Quotes'].action.value='Save';
 					return DCMenu.save(document.forms['form_DCQuickCreate_AOS_Quotes'].id, 'AOS_Quotes_subpanel_save_button');
 				}
 			} 			
 			return false;
 		}
 		
 		
 		//Get from value from quick edit view 		
 		YAHOO.util.Event.onAvailable('from_dcmenu',function(){
 			fillFormStringValue();
 		});	
			
 		function cancelProposal(propId){
		
					cfgDialog = getSimpleDialog(); 
			    	cfgDialog.setBody(SUGAR.language.get('AOS_Quotes','MSG_WARN_CONFIRM_CANCEL'));
				    var myButtons = [
				    { text: 'Ok', handler: handleYesCancelProposal,isDefault:true }
				    ,{ text: 'Cancel', handler: function(){this.hide();} }		    
				    ];
		
					//cfgDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
				    cfgDialog.cfg.queueProperty('buttons', myButtons);  
				    cfgDialog.render(document.body);    
				    cfgDialog.show();
		
				function handleYesCancelProposal(){	
		
					var cancelRequest = { onSuccess :function(o){
				
													    cnfDialog = getSimpleDialog(); 
												    	cnfDialog.setBody(o.responseText);
													    var myButtons = [
													    { text: 'Ok', handler: function(){window.location.href='index.php?module=AOS_Quotes&action=EditView&record='+propId;this.hide();},isDefault:true }
													   		    
													    ];
											
														//cnfDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
													    cnfDialog.cfg.queueProperty('buttons', myButtons);  
													    cnfDialog.render(document.body);    
													    cnfDialog.show();	
		
 													},
										  onFailure : function (){
		
														errDialog = getSimpleDialog(); 
												    	errDialog.setBody(o.responseText);
													    var myButtons = [{ text: 'Ok', handler: handleYesCancelProposal},
																		 { text: 'Retry', handler: function(){this.hide();},isDefault:true },
																		];											
														//errDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
													    errDialog.cfg.queueProperty('buttons', myButtons);  
													    errDialog.render(document.body);    
													    errDialog.show();
 													},
										  startCancelProposal : function (){	
		
														statusDialog = getSimpleDialog(); 
												    	statusDialog.setBody(SUGAR.language.get('AOS_Quotes','MSG_STATUS_CANCEL_INPROGRESS'));
													   //var myButtons = [{ text: 'Ok', handler: handleYesCancelProposal,isDefault:true }];											
														//statusDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
													    //statusDialog.cfg.queueProperty('buttons', myButtons);  
													    statusDialog.render(document.body);    
													    statusDialog.show();
 														YAHOO.util.Connect.asyncRequest ('POST', 'index.php?module=AOS_Quotes&action=cancelProposal&to_pdf=1&record='+propId, callback);
														
 										  			}
 		
 										}; 
		
					var callback = {
										success:cancelRequest.onSuccess,
										failure:cancelRequest.onFailure,
										scope: cancelRequest
								  };

					cancelRequest.startCancelProposal();
									
 					 
 				}		
 		}
        
        //validate each email for comma separated value
 	//Ashutosh - 19 June 2014
 	function isValidEmail(email){	
		if(email.toString().indexOf(';')>=0){
			return false;
		}
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-\;]{2,4})?$/;
		    emails= email.split(',');
                    
		    for(i=0;i<emails.length;i++){				
			    if( emailReg.test( emails[i].trim() ) ) {
				bReturn= true;
			    } else {
				bReturn= false;
				break;
			    }
		     }
		return bReturn;
	}
 		</script>";
 	}
}
