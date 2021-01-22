<?php
require_once 'include/ListView/ListViewSmarty.php';

class CustomListViewSmarty extends ListViewSmarty{
	
	function CustomViewSmarty(){
		parent::ListViewSmarty();
	}
	
	/**
	 * Display the actions link
	 *
	 * @param  string $id link id attribute, defaults to 'actions_link'
	 * @return string HTML source
	 */
	protected function buildActionsLink( $id = 'actions_link',  $location = 'top')
	{
	    global $app_strings;
		$closeText = SugarThemeRegistry::current()->getImage('close_inline', 'border=0', null, null, ".gif", $app_strings['LBL_CLOSEINLINE']);
		$moreDetailImage = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
		$menuItems = array();
		$menuItems[] = $this->buildBulkActionButton($location);
		// delete
		if ( ACLController::checkAccess($this->seed->module_dir,'delete',true) && $this->delete )
			$menuItems[] = $this->buildDeleteLink($location);
		// compose email
        if ( $this->email )
			$menuItems[] = $this->buildComposeEmailLink($this->data['pageData']['offsets']['total'], $location);
		// mass update
		$mass = $this->getMassUpdate();
		$mass->setSugarBean($this->seed);
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
            $menuItems[] = $this->buildMassUpdateLink($location);
		// merge
		if ( $this->mailMerge )
		    $menuItems[] = $this->buildMergeLink(null, $location);
		if ( $this->mergeduplicates )
		    $menuItems[] = $this->buildMergeDuplicatesLink($location);
		// add to target list
		if ( $this->targetList && ACLController::checkAccess('ProspectLists','edit',true) )
		    $menuItems[] = $this->buildTargetList($location);
		// export
		if ( ACLController::checkAccess($this->seed->module_dir,'export',true) && $this->export )
			$menuItems[] = $this->buildExportLink($location);
		
		// create client opportunity
		if ( ( ACLController::checkAccess($this->seed->module_dir,'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
			$menuItems[] = $this->buildCreateClientOpportunityLink($location);
		

		foreach ( $this->actionsMenuExtraItems as $item )
		    $menuItems[] = $item;

		$link = array(
            'class' => 'clickMenu selectActions fancymenu',
            'id' => 'selectActions',
            'name' => 'selectActions',
            'buttons' => $menuItems,
            'flat' => false,
        );
		
		echo  <<<EOHTML
<script type="text/javascript">
		
function createClientOpportunity(){
		
	if (document.MassUpdate.select_entire_list.value == 1) {
        alert('You can only create client opportunities for one project opportunity at a time. Please select one project opportunity to create a client opportunity for.');
		return false;
    }else if(sugarListView.get_checks_count() > 1) {
		   alert('You can only create client opportunities for one project opportunity at a time. Please select one project opportunity to create a client opportunity for.');
		   return false;
    }else if(sugarListView.get_checks_count() < 1) {
        alert('Please select 1 record to proceed.');
		return false;
	}else{
       var inputs = document.MassUpdate.elements;
	   var ar = new Array();
		for(i = 0; i < inputs.length; i++) {
			if(inputs[i].name == 'mass[]' && inputs[i].checked && typeof(inputs[i].value) != 'function') {
				var parent_id = inputs[i].value;
			}
		}
       location.href = 'index.php?module=Opportunities&action=EditView&parent_id='+parent_id;
    }
}
</script>
EOHTML;
		
        return $link;

		
	}
	/**
	 * Builds the create client opportunity link
	 *
	 * @return string HTML
	 */
	protected function buildCreateClientOpportunityLink($loc = 'top')
	{
		global $mod_strings;
		$onClick = "createClientOpportunity(this)";
		return "<a href='javascript:void(0)' id=\"opportunity_listview_". $loc ."\"  onclick=\"$onClick\">{$mod_strings['LBL_CREATE_CLIENT_OPPORTUNITY_LINK']}</a>";
	}
	
	
	/**
	 * Builds the delete link -- Overwrite Parent Function
	 *
	 * @return string HTML
	 */
	protected function buildDeleteLink($loc = 'top')
	{
		global $app_strings;
		return "<a href='javascript:void(0)' id=\"delete_listview_". $loc ."\" onclick=\"return deleteOpportunities('selected', '{$app_strings['LBL_LISTVIEW_NO_SELECTED']}', 1)\">{$app_strings['LBL_DELETE_BUTTON_LABEL']}</a>";
	}

}