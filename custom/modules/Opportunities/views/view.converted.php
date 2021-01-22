<?php

require_once 'include/MVC/View/SugarView.php';
class ViewConverted extends SugarView{
	
	function ViewConverted(){
		parent::SugarView();
	}
	
	function display(){
		echo "Convert to Opportunity Successful! This opportunity has been assigned according to the user filters set by your system administrator. Click on the Project Leads tab to resume lead review.";
	}
}
?>