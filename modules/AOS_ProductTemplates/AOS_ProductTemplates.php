<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */


class AOS_ProductTemplates extends Basic
{
    public $new_schema = true;
    public $module_dir = 'AOS_ProductTemplates';
    public $object_name = 'AOS_ProductTemplates';
    public $table_name = 'aos_producttemplates';
    public $importable = true;

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $SecurityGroups;
    public $mft_part_num;
    public $vendor_part_num;
    public $date_cost_price;
    public $cost_price;
    public $currency_id;
    public $discount_price;
    public $list_price;
    public $cost_usdollar;
    public $discount_usdollar;
    public $list_usdollar;
    public $currency;
    public $status;
    public $tax_class;
    public $date_available;
    public $website;
    public $qty_in_stock;
    public $support_name;
    public $support_description;
    public $support_contact;
    public $support_term;
    public $pricing_formula;
    public $pricing_factor;
    public $markup;
    public $markup_inper;
    public $quantity;
    public $total_cost;
    public $editsequence;
    public $quickbooks_id;
    public $push_to_qb;
    public $quickbook_type;
    public $qb_sale_purchse_type;
    public $quickbook_assets_account;
    public $quickbook_cogs_account;
    public $quickbook_income_account;
    public $quickbook_expense_account;
    public $weight;
	
    public function bean_implements($interface)
    {
        switch($interface)
        {
            case 'ACL':
                return true;
        }

        return false;
    }
	function save($check_notify = FALSE) {		
		
		$currency = new Currency();
		$currency->retrieve($this->currency_id);

        // Bug #52052: Calculated Fields don't get into POST (inputs are disabled)
        // So if i.e. "discount_price" is Calculated Fields we have find out it's value first
       // $this->updateCalculatedFields();
		
		//US DOLLAR
		if(isset($this->discount_price)){
			$this->discount_usdollar = $currency->convertToDollar($this->discount_price);
		}
		if(isset($this->list_price)){
			$this->list_usdollar = $currency->convertToDollar($this->list_price);
		}
		if(isset($this->cost_price)){
			$this->cost_usdollar = $currency->convertToDollar($this->cost_price);
		}		
		parent::save($check_notify);	
	}
}
function getProductTypes($focus, $field='type_id', $value,$view='DetailView') {
	if($view == 'EditView' || $view == 'MassUpdate' || $view == 'QuickCreate') {
		
		$type = new AOS_ProductTypes();
		$html = "<select id=\"$field\" name=\"$field\">";
	    $html .= get_select_options_with_id($type->get_product_types(), $focus->type_id);
	    $html .= '</select>';
	    return $html;
	} else if(preg_match('/SearchForm_(basic|advanced)_search/', $view, $matches)) {
	   $id = $field.'_'.$matches[1];
	   
	   $type = new AOS_ProductTypes();
       if(isset($_REQUEST[$id])) {
       	  return get_select_options_with_id($type->get_product_types(), $_REQUEST[$id]);		
       }
	   return get_select_options_with_id($type->get_product_types(), $focus->type_id);		
	}
	
	return $focus->type_name;		
}

function getPricingFormula($focus, $field='pricing_formula', $value, $view='DetailView') {
	require_once('modules/AOS_ProductTemplates/Formulas.php');
	if($view == 'EditView' || $view == 'MassUpdate') {
		global $app_list_strings;
	    $html = "<select id=\"$field\" name=\"$field\"";
	    if($view != 'MassUpdate')
	    	$html .= " language=\"javascript\" onchange=\"show_factor(); set_discount_price(this.form);\"";
	    $html .= ">";
	    $html .= get_select_options_with_id($app_list_strings['pricing_formula_dom'], $focus->pricing_formula);
	    $html .= "</select>";
        $html .= "<input type=\"hidden\" name=\"pricing_factor\" id=\"pricing_factor\" value=\"1\">";
		$formulas = get_formula_details($focus->pricing_factor);
		$html .= get_edit($formulas, $focus->pricing_formula);
	    return $html;	
	}
	return get_detail($focus->pricing_formula, $focus->pricing_factor);
}

function getManufacturers($focus, $field='manufacturer_id', $value, $view='DetailView') {

	if($view == 'EditView' || $view == 'MassUpdate' || $view == 'QuickCreate') {
	   $html = "<select id=\"$field\" name=\"$field\">";
	   
	   $manufacturer = new AOS_Manufacturers();
	   $html .= get_select_options_with_id($manufacturer->get_manufacturers(), $focus->manufacturer_id);	
	   $html .= "</select>";
	   return $html;
	} else if(preg_match('/SearchForm_(basic|advanced)_search/', $view, $matches)) {
	   $id = $field.'_'.$matches[1];
	   
	   $manufacturer = new AOS_Manufacturers();

       if(isset($_REQUEST[$id])) {
       	  return get_select_options_with_id($manufacturer->get_manufacturers(), $_REQUEST[$id]);		
       }
	   return get_select_options_with_id($manufacturer->get_manufacturers(), $focus->manufacturer_id);		
	}
	return $focus->manufacturer_name;
}

function getCategories($focus, $field='aos_product_category_id', $value,$view='DetailView') {
    if($view == 'EditView' || $view == 'MassUpdate' || $view == 'QuickCreate') {
	   $html = "<select id=\"$field\" name=\"$field\">";
	   
	   $category = new AOS_Product_Categories();
	   $html .= get_select_options_with_id($category->get_product_categories(true), $focus->aos_product_category_id);
	   $html .= "</select>";
	   return $html;       
    } else if(preg_match('/SearchForm_(basic|advanced)_search/', $view, $matches)) {
	   $id = $field.'_'.$matches[1];
	   
	   $category = new AOS_Product_Categories();
       $cats = $category->get_product_categories(true);
       array_shift($cats);
       if(isset($_REQUEST[$id])) {
       	  return get_select_options_with_id($cats, $_REQUEST[$id]);		
       }
	   return get_select_options_with_id($cats, $focus->aos_product_category_id);		
	}
    
    return $focus->aos_product_category_name;	
	
}

function getSupportTerms($focus, $field='support_term', $value,$view='DetailView') {
    if($view == 'EditView' || $view == 'MassUpdate' || $view == 'QuickCreate') {
	   $html = "<select id=\"$field\" name=\"$field\">";
	   global $app_list_strings;
	   $the_term_dom = $app_list_strings['support_term_dom'];
	   array_unshift($the_term_dom,'');
	   $html .= get_select_options_with_id($the_term_dom,$focus->support_term);
	   $html .= "</select>";
	   return $html;       
    }  else if(preg_match('/SearchForm_(basic|advanced)_search/', $view, $matches)) {
	   $id = $field.'_'.$matches[1];
	   global $app_list_strings;
	   $the_term_dom = $app_list_strings['support_term_dom'];

       if(isset($_REQUEST[$id])) {
       	  return get_select_options_with_id($the_term_dom, $_REQUEST[$id]);		
       }
	   return get_select_options_with_id($the_term_dom, $focus->support_term);		
	}
    return $focus->support_term;
}