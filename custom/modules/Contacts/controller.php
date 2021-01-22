<?php
require_once 'modules/Contacts/controller.php';
class CustomContactsController extends ContactsController{
	
	function action_detailview() {
		global $focus;
		require_once 'custom/modules/Contacts/customContact.php';
		$stConId = $this->bean->id;
		$this->bean = new customContact();
		$this->bean->retrieve($stConId);
		$focus = $this->bean;
		$this->bean->id = $stConId;
		$this->view = 'detail';
		
	}
	
	function action_subpanelviewer(){
		global $focus,$beanList,$beanFiles;
		require_once 'custom/modules/Contacts/customContact.php';
		$beanList['customContact'] = 'customContact';
		$beanFiles['customContact'] = 'custom/modules/Contacts/customContact.php';
		$this->parent_bean = new customContact();
		
		$this->view = 'subpanvier';
	
	}
}
?>