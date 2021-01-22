<?php

class oss_OnlinePlansViewQuickedit extends ViewQuickedit{
	
	function oss_OnlinePlansViewQuickedit(){
		parent::ViewQuickedit();
	}
	
	function display(){
		
		$mi_oss_onlineplans_id = trim($this->bean->mi_oss_onlineplans_id);
		
		if(!empty($mi_oss_onlineplans_id)){
			sugar_die('You are not authorized to edit this record.');
		}
		parent::display();
	}
	
} 