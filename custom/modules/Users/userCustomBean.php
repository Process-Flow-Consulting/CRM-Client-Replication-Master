<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/master-subscription-agreement
* By installing or using this file, You have unconditionally agreed to the
* terms and conditions of the License, and You may not use this file except in
* compliance with the License.  Under the terms of the license, You shall not,
* among other things: 1) sublicense, resell, rent, lease, redistribute, assign
* or otherwise transfer Your rights to the Software, and 2) use the Software
* for timesharing or service bureau purposes such as hosting the Software for
* commercial gain and/or for the benefit of a third party.  Use of the Software
* may be subject to applicable fees and any use of the Software without first
* paying applicable fees is strictly prohibited.  You do not have the right to
* remove SugarCRM copyrights from the source code or user interface.
*
* All copies of the Covered Code must include on each user interface screen:
*  (i) the "Powered by SugarCRM" logo and
*  (ii) the SugarCRM copyright notice
* in the same form as they appear in the distribution.  See full license for
* requirements.
*
* Your Warranty, Limitations of liability and Indemnity are expressly stated
* in the License.  Please refer to the License for the specific language
* governing these rights and limitations under the License.  Portions created
* by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

/*********************************************************************************

* Description: TODO:  To be written.
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
* All Rights Reserved.
* Contributor(s): ______________________________________..
********************************************************************************/
require_once 'modules/Users/User.php';
require_once 'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php';
/**
 * Class is used for extends the main user file
 * @modified By Mohit Kumar Gupta 12-03-2014
 */
class customUsers extends User
{
    /**
     * default constructor
     */
    function __construct()
    {
        parent::User();
        //custom email address bean
        $this->emailAddress = new CustomSugarEmailAddress();
    }
    /**
     * extend create new list query function
     * @see User::create_new_list_query()
     */
    function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false, $ifListForExport = false)
    {
        require_once 'custom/modules/Users/role_config.php';
        global $arUserRoleConfig;
        
        $allUserSQL = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect, $ifListForExport);
        
        if (isset($_REQUEST['lead_reviewer']) && trim($_REQUEST['lead_reviewer']) == 'false') {
            
            $allUserSQL['from'] .= ' INNER JOIN acl_roles_users ON acl_roles_users.user_id = users.id
               AND acl_roles_users.deleted = 0';
            $allUserSQL['where'] .= ' AND acl_roles_users.role_id <> "' . $arUserRoleConfig['lead_reviewer'] . '"';
        }
        
        return $allUserSQL;
    }
    /**
     * get user array from full name
     * @param string $args
     * @param string $hide_portal_users
     * @return multitype:string
     */
    function CustomGetUserArrayFromFullName($args, $hide_portal_users = false) {
        global $locale;
        $db = DBManagerFactory::getInstance();
    
        require_once 'custom/modules/Users/role_config.php';
        global $arUserRoleConfig;
        $args = trim($args);
        if (strpos($args, ' ')) {
            $inClauses = array();
    
            $argArray = explode(' ', $args);
            foreach ($argArray as $arg) {
                $arg = $db->quote($arg);
                $inClauses[] = "(first_name LIKE '{$arg}%' OR last_name LIKE '{$arg}%')";
            }
    
            $inClause = '(' . implode('OR ', $inClauses) . ')';
    
        } else {
            $args = $db->quote($args);
            $inClause = "(first_name LIKE '{$args}%' OR last_name LIKE '{$args}%')";
        }
        
    
         $query  = "SELECT users.id, first_name, last_name, user_name FROM users
                   INNER JOIN acl_roles_users ON acl_roles_users.user_id = users.id
               AND acl_roles_users.deleted = 0
                 WHERE status='Active' AND users.deleted=0 AND ".'  acl_roles_users.role_id <> "' . $arUserRoleConfig['lead_reviewer'] . '" AND ';
        if ( $hide_portal_users ) {
            $query .= " portal_only=0 AND ";
        }
        $query .= $inClause;
        $query .= " ORDER BY last_name ASC";
    
        $r = $db->query($query);
        $ret = array();
        while($a = $db->fetchByAssoc($r)) {
            $ret[$a['id']] = $locale->getLocaleFormattedName($a['first_name'], $a['last_name']);
        }
    
    	return $ret;
    }
    
    /**
     * extends default save function of user module
     * @author Mohit Kumar Gupta
     * @date 12-03-2014
     * @see User::save()
     */
    function save($check_notify = false){
		$isUpdate = !empty($this->id) && !$this->new_with_id;

		// this will cause the logged in admin to have the licensed user count refreshed
		if (isset($_SESSION)) unset($_SESSION['license_seats_needed']);

		$query = "SELECT count(id) as total from users WHERE ".self::getLicensedUsersWhere();


		global $sugar_flavor;
        $admin = new Administration();
        $admin->retrieveSettings();
		if((isset($sugar_flavor) && $sugar_flavor != null) &&
			($sugar_flavor=='CE' || isset($admin->settings['license_enforce_user_limit']) && $admin->settings['license_enforce_user_limit'] == 1)){

	        // Begin Express License Enforcement Check
			// this will cause the logged in admin to have the licensed user count refreshed
				if( isset($_SESSION['license_seats_needed']))
			        unset($_SESSION['license_seats_needed']);
		     	if ($this->portal_only != 1 && $this->is_group != 1 && (empty($this->fetched_row) || $this->fetched_row['status'] == 'Inactive' || $this->fetched_row['status'] == '') && $this->status == 'Active'){
			        global $sugar_flavor;
					//if((isset($sugar_flavor) && $sugar_flavor != null) && ($sugar_flavor=='CE')){
			            $license_users = $admin->settings['license_users'];
			            if ($license_users != '') {
	            			global $db;
	            			//$query = "SELECT count(id) as total from users WHERE status='Active' AND deleted=0 AND is_group=0 AND portal_only=0";
							$result = $db->query($query, true, "Error filling in user array: ");
							$row = $db->fetchByAssoc($result);
				            $license_seats_needed = $row['total'] - $license_users;
			            }
				        else
				        	$license_seats_needed = -1;
				        if( $license_seats_needed >= 0 ){
				           // displayAdminError( translate('WARN_LICENSE_SEATS_MAXED', 'Administration'). ($license_seats_needed + 1) . translate('WARN_LICENSE_SEATS2', 'Administration')  );
						    if (isset($_REQUEST['action']) && $_REQUEST['action'] != 'MassUpdate' && $_REQUEST['action'] != 'Save') {
					            die(translate('WARN_LICENSE_SEATS_EDIT_USER', 'Administration'). ' ' . translate('WARN_LICENSE_SEATS2', 'Administration'));
						    }
							else if (isset($_REQUEST['action'])){ // When this is not set, we're coming from the installer.
								$sv = new SugarView();
							    $sv->init('Users');
							    $sv->renderJavascript();
							    $sv->displayHeader();
		        				$sv->errors[] = translate('WARN_LICENSE_SEATS_EDIT_USER', 'Administration'). ' ' . translate('WARN_LICENSE_SEATS2', 'Administration');
                                $sv->displayErrors();
                                $sv->displayFooter();
							    die();
						  	}
				        }
			        //}
		     	}
			}
            // End Express License Enforcement Check


		// wp: do not save user_preferences in this table, see user_preferences module
		$this->user_preferences = '';

		// if this is an admin user, do not allow is_group or portal_only flag to be set.
		if ($this->is_admin) {
			$this->is_group = 0;
			$this->portal_only = 0;
		}

		// If the 'Primary' team changed then the team widget has set 'team_id' to a new value and we should
		// assign the same value to default_team because User module uses it for setting the 'Primary' team
		if (!empty($this->team_id))
		{
		    $this->default_team = $this->team_id;
		}
		
		Person::save($check_notify);

		/* $GLOBALS['sugar_config']['disable_team_access_check'] = true;
        if($this->status != 'Reserved' && !$this->portal_only) {
		   // If this is not an update, then make sure the new user logic is executed.
            if (!$isUpdate) {
                // If this is a new user, make sure to add them to the appriate default teams
                if (!$this->team_exists) {
                    $team = new Team();
                    $team->new_user_created($this);
                }
            }else{
                //if this is an update, then we need to ensure we keep the user's
                //private team name and name_2 in sync with their name.
                $team_id = $this->getPrivateTeamID();
                if(!empty($team_id)){

                    $team = new Team();
                    $team->retrieve($team_id);
                    Team::set_team_name_from_user($team, $this);
                    $team->save();
                }
            }
		} */


        $this->savePreferencesToDB();
        return $this->id;
	}
	/**
     * extends default detail view function of user module
     * @author Mohit Kumar Gupta
     * @date 12-03-2014
	 * @see User::fill_in_additional_detail_fields()
	 */
    function fill_in_additional_detail_fields() {
        // jmorais@dri Bug #56269
        Person::fill_in_additional_detail_fields();
        // ~jmorais@dri
		global $locale;

		$query = "SELECT u1.first_name, u1.last_name from users  u1, users  u2 where u1.id = u2.reports_to_id AND u2.id = '$this->id' and u1.deleted=0";
		$result = $this->db->query($query, true, "Error filling in additional detail fields");

		$row = $this->db->fetchByAssoc($result);

		if ($row != null) {
			$this->reports_to_name = stripslashes($row['first_name'].' '.$row['last_name']);
		} else {
			$this->reports_to_name = '';
		}

        
        // Must set team_id for team widget purposes (default_team is primary team id)
       /*  if (empty($this->team_id))
        {
            $this->team_id = $this->default_team;
        } */

        //set the team info if the team id has already been set.
        //running only if team class exists will prevent breakage during upgrade/flavor conversions
        if (class_exists('Team') ) {
            // Set default_team_name for Campaigns WebToLeadCreation
            $this->default_team_name = Team::getTeamName($this->team_id);
        } else {
            //if no team id exists, set the team info to blank
            $this->default_team = '';
            $this->default_team_name = '';
            $this->team_set_id = '';
        }
        

		$this->_create_proper_name_field();
	}
}