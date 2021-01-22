<?php 
require_once 'modules/oss_LeadClientDetail/oss_LeadClientDetail.php';

class customLeadClientDetail extends oss_LeadClientDetail{

	public function __construct()
    {
        parent::__construct();
    }
	
	function fill_in_additional_list_fields() {
		
		//echo '<pre>'; print_r($this->client_contact_visibility); echo '</pre>';
		
		global $db;
		
		if(!empty($this->account_id)){
			$account_name = $this->account_name;
			
			$this->account_name = proview_url(array('url'=>$this->account_proview_url)).'&nbsp;&nbsp';
			
			if($this->account_visibility != 1){
				
				if(!empty($this->account_proview_url)){
					$this->account_name .= '<a href="javascript:void(0)"  onclick="window.open(\''.$this->account_proview_url.'\',\'\',\'width=925,height=600,scrollbars=yes\')"  />';
				}else{
					$this->account_name .= '<a href="javascript:void(0)" class="no_proview">';
				}
				
			}else{
				$this->account_name .= '<a href="index.php?module=Accounts&action=DetailView&retun_module=oss_LeadClientDetail&return_action=ListView&record='.$this->account_id.'">';
			}
			$this->account_name .= $account_name;
			$this->account_name .= '</a>';
		}
		
		
		if(!empty($this->contact_id)){
			$query = "SELECT visibility FROM contacts WHERE id = '".$this->contact_id."' ";
			$result = $db->query($query);
			$row = $db->fetchByAssoc($result);
		
			$contact_name = $this->contact_name;
			
			if($row['visibility'] != 1){
				if(!empty($this->account_proview_url)){
					$this->contact_name = '<a href="javascript:void(0)"  onclick="window.open(\''.$this->account_proview_url.'\',\'\',\'width=925,height=600,scrollbars=yes\')"  />';
				}else{
					$this->contact_name = '<a href="javascript:void(0)" class="no_proview">';
				}
			}else{
				$this->contact_name = '<a href="index.php?module=Contacts&action=DetailView&retun_module=oss_LeadClientDetail&return_action=ListView&record='.$this->contact_id.'">';
			}
			$this->contact_name .= $contact_name;
			$this->contact_name .= '</a>';
		}
		
	}
	
}




?>