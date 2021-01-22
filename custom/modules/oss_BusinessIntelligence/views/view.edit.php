<?php
require_once('include/MVC/View/views/view.edit.php');

class oss_BusinessIntelligenceViewEdit extends ViewEdit{
	
	function oss_BusinessIntelligenceViewEdit(){
		parent::ViewEdit();
	}
	
	function display(){
		
		//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
		
		global $current_user, $app_list_strings;	
			
		$description = '';
		
		//organize the edit view for bb description
		if(isset($_REQUEST['record']) && !empty($_REQUEST['record']))
		{
			$type_order =$this->bean->type_order;
			$account_id =$this->bean->account_id;

			$query = " SELECT oss_businessintelligence.image_url, 
					oss_businessintelligence.image_description, 
					oss_businessintelligence.description 
					FROM oss_businessintelligence
					WHERE  oss_businessintelligence.type_order = '".$type_order."'  
							AND oss_businessintelligence.account_id = '".$account_id."' 
									AND  oss_businessintelligence.my_only != 1 
									 	AND oss_businessintelligence.deleted = 0 
											ORDER BY oss_businessintelligence.sort_order ";
			
			$result = $this->bean->db->query($query);
		
			$description_array = array();
				
			$url_pattern = '!((http|https|ftp)://|(www\.))([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+)+\.(?:jpe?g|png|gif)!Ui';
				
			while($bi_list = $this->bean->db->fetchByAssoc($result)){
					
				$tempDescription = array();
					
				if(!empty($bi_list['image_url']) && $bi_list['image_url'] != 'http://'){
					$tempDescription[] = '<img border="0" src="'.$bi_list['image_url'].'">';
				}
				if(!empty($bi_list['image_description'])){
					$tempDescription[] = $bi_list['image_description'];
				}
				if(!empty($bi_list['description'])){
		
					$tempDescription[] = preg_replace_callback($url_pattern, "oss_BusinessIntelligenceViewEdit::replace_image_url", $bi_list['description']);
						
				}
					
				$description_array[] = implode(",", $tempDescription);
			}
				
			$description = implode("<br>", $description_array);
		
		}else{
			
			//if creating Bi from client subpanel fill the client name and id (Bug Fix)
			if(isset($_REQUEST['accounts_filter_result_id']) && !empty($_REQUEST['accounts_filter_result_id'])){
			
				$accounts_filter_result_id = $_REQUEST['accounts_filter_result_id'];
				$accounts_filter_result_name = $_REQUEST['accounts_filter_result_name'];
			
				$this->bean->account_id = $accounts_filter_result_id;
				$this->bean->account_name = $accounts_filter_result_name;
			
			}
			
			
			//if creating Bi from contact subpanel fill the contact name and id (Bug Fix)
			if(isset($_REQUEST['customcontact_id']) && !empty($_REQUEST['customcontact_id'])){
					
				$customcontact_id = $_REQUEST['customcontact_id'];
				$customcontact_name = $_REQUEST['customcontact_name'];
					
				$this->bean->contact_id = $customcontact_id;
				$this->bean->contact_name = $customcontact_name;
					
			}
			
			
		}
		
		$this->ss->assign("BB_DESCRIPTION",$description);
		
		$this->ev->process();
		
		echo $this->ev->display(); 
		
		
		
		//if not creating bi disable  the feature to change bi type
		if(isset($_REQUEST['record']) && !empty($_REQUEST['record']))
		{
			echo '<script langugae="text/javascript">
		
				document.EditView.name.disabled = true;
				document.EditView.name.style.color = "#444444";
					
			</script>';
		}
		
		//if bb description exist disable the feature to change related client
		if(isset($this->bean->description) && !empty($this->bean->description))
		{
			echo '<script langugae="text/javascript">
				document.EditView.account_name.disabled = true;
				document.EditView.btn_account_name.disabled = true;
				document.EditView.btn_clr_account_name.disabled = true;
				document.EditView.account_name.style.color = "#444444";
			</script>';
		}
		
		$return_module = $_REQUEST['return_module'];
		$return_id = $_REQUEST['return_id'];
		$return_action = $_REQUEST['return_action'];	
		
		//on cancel redirect to the related client detail view
		if(empty($return_module) && !empty($this->bean->account_id))
		{
			$return_module = 'Accounts';
			$return_id = $this->bean->account_id;
			$return_action = 'DetailView';
			
		}
		if(empty($return_module) && !empty($this->bean->contact_id))
		{	
			$return_module = 'Contacts';
			$return_id = $this->bean->contact_id;
			$return_action = 'DetailView';
		}
		
		if(!empty($return_module)){
			
			echo '<script langugae="text/javascript">
					document.getElementById(\'CANCEL_HEADER\').onclick = redirectBI;
					document.getElementById(\'CANCEL_FOOTER\').onclick = redirectBI;
					function redirectBI(){
						SUGAR.ajaxUI.loadContent(\'index.php?module='.$return_module.'&action='.$return_action.'&record='.$return_id.'\');
						return false;
					}
			</script>';
		}
		
		//specific to client
		echo "<script type='text/javascript'>
		document.getElementById('btn_contact_name').onclick = function()
		{
			var client_id = document.getElementById('account_id').value;
			var popup_request_data = {
					'call_back_function' : 'set_contact_returns',
					'form_name' : 'EditView',
					'field_to_name_array' : {
						'id' : 'id',
						'name' : 'name',
						'account_id' : 'account_id',
						'account_name' : 'account_name',
					},
			};
			open_popup('Contacts', 600, 400, '&account_id='+client_id, true, false, popup_request_data);
		}
		
		function set_contact_returns(popup_reply_data){
			var name_to_value_array = popup_reply_data.name_to_value_array;
			var id = name_to_value_array['id'];
			var contact = name_to_value_array['name'];
			var account_id = name_to_value_array['account_id'];
			var account_name = name_to_value_array['account_name'];
			document.getElementById('contact_name').value=contact;
			document.getElementById('contact_id').value = id;
			document.getElementById('account_name').value=account_name;
			document.getElementById('account_id').value = account_id;
		}		
		</script>";
		
	}
	//replace image urls with image
	private static function replace_image_url($matches){
	
		if($matches[1] == 'www.')
			return '<img border="0" src="http://'.$matches[0].'">';
		else
			return '<img border="0" src="'.$matches[0].'">';
	}
}