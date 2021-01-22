<?php

require_once 'include/MVC/View/SugarView.php';

class ViewGet_line_items extends SugarView {
	
	function ViewGet_line_items() {
		parent::SugarView ();
	}
	
	function display() {
		
		if (isset ( $_REQUEST ['proposalId'] ) && ! empty ( $_REQUEST ['proposalId'] )) {
			$proposalId = $_REQUEST ['proposalId'];
			$proposal = loadBean('AOS_Quotes');
			$proposal->retrieve($proposalId);
			
			
			$products = loadBean('AOS_Products');
			$where = " aos_products.quote_id='".$proposalId."' ";
			//@modifed by Mohit Kumar Gupta
			//@date 14-01-2014
			//change function from get list to get full list
			$product_list = $products->get_full_list('',$where);
			$line_items = array();
			$quote_info = array();
			$taxRateId = $proposal->fetched_row['taxrate_id'];
			$quote_info['subtotal'] = $proposal->subtotal;
			$quote_info['subtotal_inclusion'] = $proposal->subtotal_inclusion;
			$quote_info['taxrate_id'] = $taxRateId;
			$i=0;			
			
			foreach($product_list as $productData){
				$line_item = $productData->fetched_row;
				//$encoded_name = js_escape ( br2nl ( $line_item['name'] ) );
				$encoded_name = $line_item['name'];
				$line_items[$i] = array(
						'type' => $line_item['product_type'],
						'qty' => format_number($line_item['quantity'], 0, 0),
						'qty_show' => $line_item['qty_show'],
						'product_template_id' => $line_item['product_template_id'],
						'name' => $encoded_name,
						'title_show' => $line_item['title_show'],
						'cost_price' => number_format($line_item['cost_price'],2,'.',''),
						'price_show' => $line_item['price_show'],
						'list_price' => number_format($line_item['list_price'], 2, '.',''),
						'total' => $line_item['total'],
						'total_show' => $line_item['total_show'],
						'discount_price' => number_format($line_item['discount_price'],2,'.',''),
						'in_hours' => $line_item['in_hours'],
						'unit_price' => $line_item['unit_price'],
						'bb_tax' => $line_item['bb_tax'],
						'bb_tax_per' => $line_item['bb_tax_per'],
						//'description' => js_escape(br2nl($line_item['description'])),
						'description' => ( ($line_item['description']== null) ? "" : $line_item['description']) ,
						'desc_show' => $line_item['desc_show'],
						'discount_amount' => $line_item['discount_amount'],
						'discount_select' => $line_item['discount_select'],
						'shipping' => $line_item['bb_shipping'],
						'markup_inper' => $line_item['markup_inper'],
						'unit_measure' => $line_item['unit_measure'],
				        'unit_measure_name' => ( ($line_item['unit_measure_name']== null) ? "" : $line_item['unit_measure_name']) ,
				        'tax_class' => $line_item['tax_class'], //Tax Class - Added By Hirak
						);
				$i++;
			}
			$result=array($line_items,$quote_info,'layout_options' => unserialize(base64_decode($proposal->layout_options)),'desc'=>( $proposal->description), 'taxrate_id' => $taxRateId );		
			echo json_encode($result);	
		}
	}

}
?>