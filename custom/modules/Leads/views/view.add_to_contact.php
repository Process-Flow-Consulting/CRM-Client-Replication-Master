<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'include/MVC/Controller/SugarController.php';
require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';

class ViewAdd_to_contact extends SugarView {

    function __construct() {
        parent::SugarView();
    }

    function display() {

        if (isset($_REQUEST['client'])) {
            global $db, $sugar_config;
            $sql = "SELECT `mi_account_id` FROM `accounts` WHERE `id` = '".$_REQUEST['client']."' AND `deleted` = '0'";
            $query = $db->query($sql);
            $result = $db->fetchByAssoc($query);
            $mi_account_id = $result['mi_account_id'];
            if($mi_account_id != ''){
            $pullObj = new PullBBH($mi_account_id);
            $pullObj->insertUpdateClients();
            echo "Done";         
                        
            
            }else { 
                $client = new Account();
                $client->retrieve($_REQUEST['client']);
                $client->visibility = 1;
                $return = $client->save();
                if (!empty($return->id)) {
                    $contacts = $client->get_contacts();
                    foreach ($contacts as $contact) {
                        $contactObj = new Contact();
                        $contactObj->retrieve($contact->id);
                        $contactObj->visibility = 1;
                        $contactObj->save();
                    }
                    echo "Done";
                } else {
                    echo "Not Done";
                } 
            
           }
        }
    }  
    

}

?>
