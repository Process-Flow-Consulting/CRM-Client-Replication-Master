<?php
require_once 'include/MVC/View/SugarView.php';
class AOS_QuotesViewSave_product_catalog extends SugarView{

	function __construct(){
		parent::SugarView();
	}

	function display(){
		$pc = new AOS_ProductTemplates();
		if(isset($_REQUEST['product_template_id']) && !empty($_REQUEST['product_template_id'])){
			$pc->retrieve($_REQUEST['product_template_id']);
		}		
		$pc->name = $_REQUEST['pname'];
		$pc->description = $_REQUEST['pdesc'];
		$pc->cost_price = $_REQUEST['price'];
		$pc->markup = $_REQUEST['markup'];
		$pc->markup_inper = $_REQUEST['markup_inper'];
		$pc->discount_price = $_REQUEST['unit_price'];
		$pc->quantity = $_REQUEST['quantity'];		
		$pc->unit_measure = $_REQUEST['unit_measure'];
		$pc->tax_class = isset($_REQUEST['tax_class'])?$_REQUEST['tax_class']:'';
		
		// Fetch Product Type
		$pt = loadBean ( 'AOS_ProductTypes' );
		$pc_ptype = $_REQUEST ['ptype'];
		if($_REQUEST ['ptype'] =='alternates'){
			$pc_ptype = 'line_items';
		}
		$pt->retrieve_by_string_fields ( array ('name' => str_replace ( '_', ' ', $pc_ptype ) ) );
		$pc->type_id = $pt->id;	
			
		//check the access of product catalog for add or modify
		//@modified By Mohit Kumar Gupta
		//@date 18-04-2014
		if (!getProductCatalogUpdateAccess()) {
		    if (isset($_REQUEST['product_template_id']) && !empty($_REQUEST['product_template_id'])) {
		    	echo $pc->id;
		    }else {
		        echo '';
		    }		    
		} else {
		    $pc->save();
		    echo $pc->id;
		}		
	}
}
