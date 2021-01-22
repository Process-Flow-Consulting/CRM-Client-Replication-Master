<?php
/**
 * Description of view and use for information of client contact
 * @date 09-01-2014
 * @author mohit Kumar Gupta
 */
require_once 'include/MVC/View/SugarView.php';

class ViewClient_contact_detail extends SugarView {

    function ViewClient_contact_detail() {
        parent::SugarView();
    }

    function display() {
        $result = array();
        if (isset($_REQUEST['clientContactId']) && !empty($_REQUEST['clientContactId'])) {                    	
            $contact = new Contact();
            $contact->retrieve($_REQUEST['clientContactId']);
            $result['phone'] = $contact->phone_work;
            $result['fax'] = $contact->phone_fax;
            $result['email'] = $contact->email1;
            $result['billing_address_street'] = $contact->primary_address_street;
            $result['billing_address_city'] = $contact->primary_address_city;
            $result['billing_address_state'] = $contact->primary_address_state;
            $result['billing_address_postalcode'] = $contact->primary_address_postalcode;
            
            echo json_encode($result);
        }
    }

}

?>
