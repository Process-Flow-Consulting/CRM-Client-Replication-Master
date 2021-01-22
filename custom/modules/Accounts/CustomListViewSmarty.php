<?php
require_once 'include/ListView/ListViewSmarty.php';

class CustomListViewSmarty extends ListViewSmarty{
	
	 public function __construct()
    {
        parent::__construct();
        
    }
	
	function getMassUpdate(){		
		require_once 'custom/include/CustomMassUpdate.php';
		return new CustomMassUpdate();
	}
	
	/**
	 * Display the actions link
	 *
	 * @param  string $id link id attribute, defaults to 'actions_link'
	 * @return string HTML source
	 */
	protected function buildActionsLink($id = 'actions_link', $location = 'top')
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
		
		/**
		 * @author : Mohit Kumar Gupta
		 * @date :  Jan 2013
		 * to display create opportunity option
		 */
		if ( ( ACLController::checkAccess("Opportunities",'edit',true) && ACLController::checkAccess($this->seed->module_dir,'massupdate',true) ) && $this->showMassupdateFields && $mass->doMassUpdateFieldsExistForFocus() )
			$menuItems[] = $this->buildLinkPrjectLink($location);
		
		
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
	 * @author : mohit Kumar Gupta
	 * Date : 08-Nov-2013
	 * Purpose : to add create opportunity Option on list view
	 */
	function buildLinkPrjectLink($loc = 'top'){
		global $mod_strings;
		$onClick ="
				if(document.MassUpdate.select_entire_list.value ==1){
					alert('Please select from current screen.');
					return false;
				} 
				var frm=$('#MassUpdate');
				$('#MassUpdate input[name=action]').val('accounts_opportunity');
				$('#MassUpdate input[name=module]').val('Opportunities');
				frm.submit();
		";
	
		return "<a href='javascript:void(0)' id=\"assigne_listview_". $loc ."\"  onclick=\"$onClick\">{$mod_strings['LBL_ACCOUNTS_CREATE_OPPORTUNITY']}</a>";
	
	}
}