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
require_once('include/MVC/View/SugarView.php');

class AOS_QuotesViewTaxrate extends SugarView {

    function AOS_QuotesViewCopyproposal() {
        parent::SugarView();
    }

    function preDisplay() {
        parent::preDisplay();
    }

    function display() {
        if (!isset($_REQUEST['taxrate_id']) && empty($_REQUEST['taxrate_id']) ){
            sugar_die("Unauthorized access to this area.");
        }

        global  $db, $current_user;
        
        $taxrate_id = $_REQUEST['taxrate_id'];
        
        $sqlTaxRates = "SELECT id, value FROM aos_taxrates WHERE deleted = 0 AND status = 'Active' AND id = '".$taxrate_id."' ";
		$resultTaxRates = $db->query($sqlTaxRates);
		$rowTaxRates = $db->fetchByAssoc($resultTaxRates);
		
		if(!empty($rowTaxRates['id'])){
		    echo json_encode( array('result' => 'success', 'tax_rate' => $rowTaxRates['value']));
		    die;
		}
		
		
		$taxRateTotal = 0.00;
		
		$sqlTaxGroups = "SELECT id, name FROM oss_itemsalestaxgroup WHERE deleted = 0 AND is_active = '1'  AND id = '".$taxrate_id."' ";
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
		    
		    echo json_encode( array('result' => 'success', 'tax_rate' => $taxRateTotal) );
		    die;
		    
		}
		
		echo json_encode( array('result' => 'error', 'tax_rate' => '0.00') );
		
    }
    
}
?>