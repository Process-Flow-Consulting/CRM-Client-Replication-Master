<?php

require_once 'modules/Leads/Lead.php';

class CustomLead extends Lead {

    function __construct() {
        parent::Lead();
    }

    /*function mark_deleted($id) {
        global $current_user;

        $this->retrieve($id);
        #####################
		### ACCESS FILTER ###
		if( !$current_user->is_admin){
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			
			$bAccess = userAccessFilters::isLeadAccessable($id,true);
			if(!$bAccess){
				//if this record is not with in current users filters do 
				// not delete
				return;				
			}
		}
		### END OF ACCESS FILTER ###
		############################
        

        if ($this->lead_source != 'bb') {

            $date_modified = $GLOBALS['timedate']->nowDb();
            if (isset($_SESSION['show_deleted'])) {
                $this->mark_undeleted($id);
            } else {
                // call the custom business logic
                $custom_logic_arguments['id'] = $id;
                $this->call_custom_logic("before_delete", $custom_logic_arguments);

                if (isset($this->field_defs['modified_user_id'])) {
                    if (!empty($current_user)) {
                        $this->modified_user_id = $current_user->id;
                    } else {
                        $this->modified_user_id = 1;
                    }
                    $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' where id='$id'";
                } else
                    $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified' where id='$id'";
                $this->db->query($query, true, "Error marking record deleted: ");
                $this->deleted = 1;
                $this->mark_relationships_deleted($id);

                // Take the item off the recently viewed lists
                $tracker = new Tracker();
                $tracker->makeInvisibleForAll($id);

                // call the custom business logic
                $this->call_custom_logic("after_delete", $custom_logic_arguments);
            }
        }
    }*/

}

?>
