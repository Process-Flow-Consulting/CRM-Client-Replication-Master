<?php

class AccountsController extends SugarController{
	
	function action_detailview(){
		require_once 'custom/modules/Accounts/accounts_filter_result.php';
		$AccountId = $this->bean->id;
		$this->bean = new accounts_filter_result();
		$this->bean->retrieve($AccountId);
		$this->view = 'detail';
	
	}
	function action_popup(){
	    require_once 'custom/modules/Accounts/accounts_filter_result.php';
	    $this->bean = new accounts_filter_result();
	    $this->view = 'popup';
	}
	function action_master_lookup(){
	    $this->view = 'master_lookup';
	}
	
	function action_import_client(){
	    $this->view = 'import_client';
	}
	function action_clientmerge(){
	    $this->view = 'client_dupe_merge';
	}
} 