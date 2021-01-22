<?php
require_once 'include/ListView/ListViewSmarty.php';

class CustomListViewSmarty extends ListViewSmarty{
	
	function __construct(){
		parent::__construct();
	}
	
	function getMassUpdate(){		
		require_once 'custom/include/CustomMassUpdate.php';
		return new CustomMassUpdate();
	}
}