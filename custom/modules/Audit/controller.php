<?php
require_once 'include/MVC/Controller/SugarController.php';
class AuditController extends SugarController{
	
	function __construct(){
		parent::SugarController();
	}
	
	function action_popup(){		
		require_once 'custom/modules/Audit/Popup_picker.php';
		$popup = new Popup_Picker();
		$popup->_hide_clear_button = true;
		$popup->process_page();		
	}
}