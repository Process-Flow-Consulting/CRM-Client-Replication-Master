<?php
require_once 'include/MVC/View/SugarView.php';
class AOS_QuotesViewGet_product_catalog extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){	
			
		$pc = loadBean('AOS_ProductTemplates');
		$pc->retrieve($_REQUEST['pt_id']);
		$cost_price = 0.00;
		$unit_price = 0.00;
		$markup = 0.00;
		$quantity = 1;
		$desc = html_entity_decode($pc->description,ENT_QUOTES);
		$markup_inper = $pc->markup_inper;
		if(!empty($pc->cost_price)){
			$cost_price = number_format($pc->cost_price,2,".","");
		}
		if(!empty($pc->discount_price)){
			$unit_price = number_format($pc->discount_price,2,".","");
		}
		if(!empty($pc->markup)){
			$markup = number_format($pc->markup,2,".","");
		}
		if(!empty($pc->quantity)){
			$quantity = $pc->quantity;
		}
		$masterConfig = 0;
		if (getUnitOfMeasureSettingType() == 'quickbooks') {
			$masterConfig = 1;
		}
				
		$result = array('name' => $pc->name,'cost_price' => $cost_price,'unit_price' => $unit_price,'markup' => $markup,'desc' => $desc,'quantity' => $quantity, 'markup_inper' => $markup_inper,'unit_measure' => $pc->unit_measure,'quickbooks' => $masterConfig);
		echo json_encode($result);		
		
	}
}