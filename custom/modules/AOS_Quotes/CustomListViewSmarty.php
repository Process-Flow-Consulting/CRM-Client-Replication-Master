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

/**
 * *******************************************************************************
 *
 * Description: Extend List View Function to add new action menu.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Hirak Chattopadhyay
 * ******************************************************************************/
require_once 'include/ListView/ListViewSmarty.php';

class CustomListViewSmarty extends ListViewSmarty{
    
    function CustomListViewSmarty(){
    	parent::ListViewSmarty();
    }
    
    /**
     * @to overwrite buildActionsLink
     * @param string $id
     * @param string $location
     * @return HTML $link
     */
    protected function buildActionsLink( $id = 'actions_link',  $location = 'top')
    {
    	global $app_strings;
    	$closeText = SugarThemeRegistry::current()->getImage('close_inline', 'border=0', null, null, ".gif", $app_strings['LBL_CLOSEINLINE']);
    	$moreDetailImage = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
    	$menuItems = '';
    
    	// delete
    	if ( ACLController::checkAccess($this->seed->module_dir,'delete',true) && $this->delete )
    		$menuItems[] = $this->buildDeleteLink($location);
    	
    	// mass update
    	$mass = $this->getMassUpdate();
    	$mass->setSugarBean($this->seed);
    	if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
    		$menuItems[] = $this->buildMassUpdateLink($location);
    	
    	// export
    	if ( ACLController::checkAccess($this->seed->module_dir,'export',true) && $this->export )
    		$menuItems[] = $this->buildExportLink($location);
    
    	//verify Proposal
    	if ( ACLController::checkAccess($this->seed->module_dir,'edit',true))
    		$menuItems[] = $this->buildVerifyProposalsLink($location);
    
    	foreach ( $this->actionsMenuExtraItems as $item )
    		$menuItems[] = $item;
    
    	$link = array(
    			'class' => 'clickMenu selectActions fancymenu',
    			'id' => 'selectActions',
    			'name' => 'selectActions',
    			'buttons' => $menuItems,
    			'flat' => false,
    	);
    
    	return $link;
    
    }
    
    /**
     * Build the Verify Proposal Link
     * @return string HTML
     */
    public function buildVerifyProposalsLink($loc = 'top')
    {
    	global $mod_strings;
    	$onClick = "verifySelectedProposals(this)";
    	return "<a href='javascript:void(0)' id=\"quote_verify_". $loc ."\" style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">{$mod_strings['LBL_VERIFY_PROPOSALS']}</a>";
    }
}