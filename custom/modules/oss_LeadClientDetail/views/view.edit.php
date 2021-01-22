<?php
require_once 'include/MVC/View/views/view.edit.php';

class oss_LeadClientDetailViewEdit extends ViewEdit{

    function oss_LeadClientDetailViewEdit(){
        parent::ViewEdit();
        $this->useForSubpanel=true;
    }

    function  display() {
        parent::display();
        echo "<script>
                document.getElementById('btn_contact_name').onclick = function(){
		var client_id = document.getElementById('account_id').value;
		//alert(client_id);
		var popup_request_data = {
							        'call_back_function' : 'set_contact_returns',
							        'form_name' : 'EditView',
			        				 'field_to_name_array' : {
			           				 'id' : 'id',
                                                                 'name' : 'name',
                                                                 'email1': 'email1',
                                                                 'phone_work':'phone_work',
                                                                 'phone_fax':'phone_fax',
			        				},

			    		};

                if(client_id != ''){
                    open_popup('Contacts', 600, 400, '&account_id='+client_id, true, false, popup_request_data);
                }else{
                    alert('Please select Client first');
                    return false;
                }


        }

		function set_contact_returns(popup_reply_data){
			var name_to_value_array = popup_reply_data.name_to_value_array;
			var id = name_to_value_array['id'];
			var contact = name_to_value_array['name'];


			document.getElementById('contact_name').value=contact;
			document.getElementById('contact_id').value = id;
                        document.getElementById('contact_email').value = name_to_value_array['email1'];
                        document.getElementById('contact_phone_no').value = name_to_value_array['phone_work'];
                        document.getElementById('contact_fax').value = name_to_value_array['phone_fax'];
		}
		window.onload = function(){
									addToValidate('EditView', 'contact_email', 'email', false ,'Please Enter Valid Email Address' );
				}
            </script>";
    }
}

?>
