<?php
require_once 'include/MVC/View/SugarView.php';

class LeadsViewGetconvertedcount extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){
		
		global $db;	
		$selected =  $_REQUEST['selected'];
		
		if($selected != 'all')
			$mode = "selected";
		else
			$mode = "entire";
		
		
		switch ($mode){
			
			case 'selected':
				$uids = str_replace(',', "','", $selected);
				$sql = " SELECT count(*) c FROM leads WHERE id IN ('".$uids."') AND status = 'Converted' AND deleted = 0 ";
				$result = $db->query($sql);
				$rows = $db->fetchByAssoc($result);
				$converted = $rows['c'];
				break;
			case 'entire':
				$sql = " SELECT count(*) c FROM leads WHERE status = 'Converted' AND deleted = 0 ";
				$result = $db->query($sql);
				$rows = $db->fetchByAssoc($result);
				$converted = $rows['c'];
				break;
		}
		
		echo $converted;
		
	}
	
}
