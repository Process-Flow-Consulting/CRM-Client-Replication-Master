<?php

class oss_OnlinePlansViewEdit extends ViewEdit{
	
	function oss_OnlinePlansViewEdit(){
		parent::ViewEdit();
	}
	
	function display(){
		
		$mi_oss_onlineplans_id = trim($this->bean->mi_oss_onlineplans_id);
		
		if(!empty($mi_oss_onlineplans_id)){
			sugar_die('You are not authorized to edit this record.');
		}
		
		parent::display();
		
	}
	
} 