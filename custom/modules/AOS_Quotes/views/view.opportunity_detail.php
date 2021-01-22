<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view
 *
 * @author satish
 */
require_once 'include/MVC/View/SugarView.php';

class ViewOpportunity_detail extends SugarView {

    function ViewOpportunity_detail() {
        parent::SugarView();
    }

    function display() {
        $result = array();
        if (isset($_REQUEST['oppId']) && !empty($_REQUEST['oppId'])) {
            
        	$oppr = new Opportunity();            
            $oppr->retrieve($_REQUEST['oppId']);
            
            /**
            * Remove Autofill Schedule Delivery Date and Timezone
            * By Satish Gupta on 09-04-2012
            */ 
            //require_once 'custom/include/OssTimeDate.php';
            //$oss_timedate = new OssTimeDate();
            //$date_closed = $oss_timedate->convertDBDateForDisplay($oppr->date_closed, $oppr->bid_due_timezone,true);
            //require_once 'custom/include/common_functions.php';            
            //$date_closed = convertDbDateToTimeZone($oppr->date_closed, $oppr->bid_due_timezone );
            
            //$dateArr = explode(" ", $date_closed);
            //$timeArr = explode(":", $dateArr[1]);
            //$result['date_closed'] = $dateArr[0];
            //$result['date_closed_hours'] = $timeArr[0];
            //$timeMeridien = substr($timeArr[1], -2, 2);
            //$timeMinutes = substr($timeArr[1], 0, 2);
            //$result['date_closed_minutes'] = $timeMinutes;
            //$result['date_closed_meridien'] = $timeMeridien;
            //$result['full_date_closed'] = $date_closed;
            //$result['time_zone'] = $oppr->bid_due_timezone;            
            
            
            $result['client_name'] = $oppr->account_name;
            $result['client_id'] = $oppr->account_id;
            $result['amount'] = $oppr->amount;
			/**
			* @modified By Mohit Kumar Gupta
			* @date 08-01-2014
			* address field also pre populated from client contact for a proposal
			*/
            $contact = new Contact();
            $contact->retrieve($oppr->contact_id);
            $result['billing_address_street'] = $contact->primary_address_street;
            $result['billing_address_city'] = $contact->primary_address_city;
            $result['billing_address_state'] = $contact->primary_address_state;
            $result['billing_address_postalcode'] = $contact->primary_address_postalcode;
            
            $lcdId = $oppr->leadclientdetail_id;
            if (!empty($lcdId)) {
                $lcd = new oss_LeadClientDetail();
                $lcd->retrieve($lcdId);
                $result['contact_name'] = $lcd->contact_name;
                $result['contact_id'] = $lcd->contact_id;
                $result['phone'] = $lcd->contact_phone_no;
                $result['fax'] = $lcd->contact_fax;
                $result['email'] = $lcd->contact_email;
            }else{
            	$result['contact_name'] = $oppr->contact_name;
            	$result['contact_id'] = $oppr->contact_id;                        	
            	
            	$result['phone'] = $contact->phone_work;
            	$result['fax'] = $contact->phone_fax;
            	$result['email'] = $contact->email1;
            }
            echo json_encode($result);
        }
    }

}

?>
