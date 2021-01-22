<?php 
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
require_once 'modules/AOS_ProductTemplates/AOS_ProductTemplates.php';
/**
 * customize for list view changes
 * @author Mohit Kumar Gupta
 * @date 22-oct-2013
 */
class customProductTemplate extends AOS_ProductTemplates{
	
	var $rel_types = "aos_producttypes";
	/**
	 * default method for the class
	 */
	 public function __construct()
    {
        parent::__construct();
    }
	/**
	 * customized sugar bean function for markup field on list view
	 * @see ProductTemplate::fill_in_additional_list_fields()
	 */
	function fill_in_additional_list_fields() {
		
		global $current_user, $db, $sugar_config;
		$markupSymbol = $sugar_config['default_currency_symbol'];
		$thousandSep = $current_user->getPreference("num_grp_sep");
		$decimalPoint = $current_user->getPreference("dec_sep");
		$currencyId = $current_user->getPreference("currency");
		$query = "SELECT symbol FROM currencies WHERE id = '".$currencyId."' ";
		$result = $db->query($query);
		$row = $db->fetchByAssoc($result);
		if (!empty($row['symbol'])) {
			$markupSymbol = $row['symbol'];
		}
		$percentSign = str_replace("In ",'',$GLOBALS['mod_strings']['LBL_IN_PERCENTAGE']);
	      // 	$this->markup = number_format($this->markup,$current_user->getPreference("default_currency_significant_digits"),$decimalPoint,$thousandSep);
		if (isset($this->markup) && $this->markup_inper == 1) {
			//$this->markup = $this->markup.$percentSign;
		} else if (isset($this->markup)){
			//$this->markup = $markupSymbol.$this->markup;
		}	
		$this->get_type();
	}
	function get_type(){
		$query = "SELECT t1.name from $this->rel_types t1, $this->table_name p1 where t1.id = p1.type_id and p1.id = '$this->id' and p1.deleted=0 and t1.deleted=0";
		$result = $this->db->query($query,true," Error filling in additional detail fields: ");

		// Get the id and the name.
		$row = $this->db->fetchByAssoc($result);

		if($row != null)
		{
			$this->type_name = $row['name'];
		}
		else
		{
			$this->type_name = '';
		}
	}
}