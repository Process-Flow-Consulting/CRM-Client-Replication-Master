<?php
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


require_once('include/SugarObjects/templates/person/Person.php');
require_once('include/MVC/SugarModule.php');
require_once 'custom/modules/Accounts/accounts_filter_result.php';
require_once 'custom/modules/Contacts/customContact.php';

$filePath = 'modules/Home/QuickSearch.php';
require_once($filePath);

/**
 * quicksearchQuery class, handles AJAX calls from quicksearch.js
 *
 * @copyright  2004-2007 SugarCRM Inc.
 * @license    http://www.sugarcrm.com/crm/products/sugar-professional-eula.html  SugarCRM Professional End User License
 * @since      Class available since Release 4.5.1
 */
class quicksearchQueryCustom
{
    /**
     * Condition operators
     * @var string
     */
    const CONDITION_CONTAINS    = 'contains';
    const CONDITION_LIKE_CUSTOM = 'like_custom';
    const CONDITION_EQUAL       = 'equal';

    protected $extra_where;

    /**
     * Query a module for a list of items
     *
     * @param array $args
     * example for querying Account module with 'a':
     * array ('modules' => array('Accounts'), // module to use
     *        'field_list' => array('name', 'id'), // fields to select
     *        'group' => 'or', // how the conditions should be combined
     *        'conditions' => array(array( // array of where conditions to use
     *                              'name' => 'name', // field
     *                              'op' => 'like_custom', // operation
     *                              'end' => '%', // end of the query
     *                              'value' => 'a',  // query value
     *                              )
     *                        ),
     *        'order' => 'name', // order by
     *        'limit' => '30', // limit, number of records to return
     *       )
     * @return array list of elements returned
     */
    public function query($args)
    {
        $args = $this->prepareArguments($args);
        $args = $this->updateQueryArguments($args);
        $data = $this->getRawResults($args);

        return $this->getFormattedJsonResults($data, $args);
    }

    /**
     * get_contact_array
     *
     */
    public function get_contact_array($args)
    {
        $args    = $this->prepareArguments($args);
        $args    = $this->updateContactArrayArguments($args);
        $data    = $this->getRawResults($args);
        $results = $this->prepareResults($data, $args);

        return $this->getFilteredJsonResults($results);
    }

    /**
     * Returns the list of users, faster than using query method for Users module
     *
     * @param array $args arguments used to construct query, see query() for example
     * @return array list of users returned
     */
    public function get_user_array($args)
    {
        $condition = $args['conditions'][0]['value'];
        $results   = $this->getUserResults($condition,$args);

        return $this->getJsonEncodedData($results);
    }

    /**
     * Returns non private teams as search results
     *
     * @param array $args
     * @return array
     */
    public function get_non_private_teams_array($args)
    {
        $args = $this->prepareArguments($args);
        $args = $this->updateTeamArrayArguments($args);
        $data = $this->getRawResults($args);

        return $this->getFormattedJsonResults($data, $args);
    }

    /**
     * Returns search results from external API
     *
     * @param array $args
     * @return array
     */
    public function externalApi($args)
    {
        require_once('include/externalAPI/ExternalAPIFactory.php');
        $data = array();
        try {
            $api = ExternalAPIFactory::loadAPI($args['api']);
            $data['fields']     = $api->searchDoc($_REQUEST['query']);
            $data['totalCount'] = count($data['fields']);
        } catch(Exception $ex) {
            $GLOBALS['log']->error($ex->getMessage());
        }

        return $this->getJsonEncodedData($data);
    }

    function fts_query()
    {
        require_once('include/SugarSearchEngine/SugarSearchEngineFactory.php');
        $_REQUEST['q'] = trim($_REQUEST['term']);
        $view = new ViewFts();
        $view->init();
        echo $view->display(TRUE, TRUE);
    }

    /**
     * Internal function to construct where clauses
     *
     * @param Object $focus
     * @param array $args
     * @return string
     */
    protected function constructWhere($focus, $args)
    {
        global $db, $locale, $current_user;

        $table = $focus->getTableName();
        if (!empty($table)) {
            $table_prefix = $db->getValidDBName($table).".";
        } else {
            $table_prefix = '';
        }
        $conditionArray = array();

        if (!is_array($args['conditions'])) {
            $args['conditions'] = array();
        }

        foreach($args['conditions'] as $condition)
        {
            switch ($condition['op'])
            {
                case self::CONDITION_CONTAINS:
                    array_push(
                        $conditionArray,
                        sprintf(
                            "%s like '%%%s%%'",
                            $table_prefix . $db->getValidDBName($condition['name']),
                            $db->quote($condition['value']
                    )));
                    break;

                case self::CONDITION_LIKE_CUSTOM:
                    $like = '';
                    if (!empty($condition['begin'])) {
                        $like .= $db->quote($condition['begin']);
                    }
                    $like .= $db->quote($condition['value']);

                    if (!empty($condition['end'])) {
                        $like .= $db->quote($condition['end']);
                    }

                    if ($focus instanceof Person){
                        $nameFormat = $locale->getLocaleFormatMacro($current_user);

                        if (strpos($nameFormat,'l') > strpos($nameFormat,'f')) {
                            array_push(
                                $conditionArray,
                                $db->concat($table, array('first_name','last_name')) . " like '$like'"
                            );
                        } else {
                            array_push(
                                $conditionArray,
                                $db->concat($table, array('last_name','first_name')) . " like '$like'"
                            );
                        }
                    }
                    elseif ($focus instanceof Team) {
                        array_push(
                            $conditionArray,
                            '(' . $table_prefix . $db->getValidDBName($condition['name']) . sprintf(" like '%s%%'", $db->quote($condition['value'])) . ' or ' . $table_prefix . 'name_2' . sprintf(" like '%s%%'", $db->quote($condition['value'])) . ')'
                        );

                        $condition['exclude_private_teams'] = true;
                    }
                    else {
                        array_push(
                            $conditionArray,
                            $table_prefix . $db->getValidDBName($condition['name']) . sprintf(" like '%s'", $like)
                        );
                    }
                    break;

                case self::CONDITION_EQUAL:
                    if ($condition['value']) {
                        array_push(
                            $conditionArray,
                            sprintf("(%s = '%s')", $db->getValidDBName($condition['name']), $db->quote($condition['value']))
                            );
                    }
                    break;

                default:
                    array_push(
                        $conditionArray,
                        $table_prefix.$db->getValidDBName($condition['name']) . sprintf(" like '%s%%'", $db->quote($condition['value']))
                    );
            }
        }

        $whereClause = sprintf('(%s)', implode(" {$args['group']} ", $conditionArray));
        if(!empty($this->extra_where)) {
            $whereClause .= " AND ({$this->extra_where})";
        }

        if ($table == 'users') {
            $whereClause .= sprintf(" AND users.status='Active'");
        }else if($table == 'accounts') {
        	$whereClause .= sprintf(" AND accounts.visibility='1'");
        }else if ($table == 'contacts') {
        	$whereClause .= sprintf(" AND contacts.visibility='1'");
        	 // get contact list corresponding to account id
        	 // @modified By Mohit Kumar Gupta
        	 // @date 16-Dec-2013
        	if (!empty($args['account_contact_id'])) {
        		$whereClause .= sprintf(" AND account_id = '".$args['account_contact_id']."'");
        	}
        }else if ($table == 'opportunities'){ //separeate client and project opportunity -- hirak
        	
        	if(isset($args['parent_opportunity_only']) && ( $args['parent_opportunity_only'] == '1') )
        	{
        		$whereClause .= sprintf(" AND opportunities.parent_opportunity_id IS NULL ");
        	}
        	
        	if(isset($args['parent_opportunity_only']) && ( $args['parent_opportunity_only'] == '0') )
        	{
        		$whereClause .= sprintf(" AND opportunities.parent_opportunity_id IS NOT NULL ");
        	}
        }

        
        return $whereClause;
    }

    /**
     * Returns formatted data
     *
     * @param array $results
     * @param array $args
     * @return array
     */
    protected function formatResults($results, $args)
    {
        global $sugar_config;

        $app_list_strings = null;
        $data['totalCount'] = count($results);
        $data['fields']     = array();

        for ($i = 0; $i < count($results); $i++) {
            $data['fields'][$i] = array();
            $data['fields'][$i]['module'] = $results[$i]->object_name;

            //C.L.: Bug 43395 - For Quicksearch, do not return values with salutation and title formatting
            if($results[$i] instanceof Person)
            {
                $results[$i]->createLocaleFormattedName = false;
            }
            $listData = $results[$i]->get_list_view_data();

            foreach ($args['field_list'] as $field) {
                // handle enums
                if ((isset($results[$i]->field_name_map[$field]['type']) && $results[$i]->field_name_map[$field]['type'] == 'enum')
                    || (isset($results[$i]->field_name_map[$field]['custom_type']) && $results[$i]->field_name_map[$field]['custom_type'] == 'enum')) {

                    // get fields to match enum vals
                    if(empty($app_list_strings)) {
                        if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') $current_language = $_SESSION['authenticated_user_language'];
                        else $current_language = $sugar_config['default_language'];
                        $app_list_strings = return_app_list_strings_language($current_language);
                    }

                    //added extrat condition for states as state is a dropdown value in Project Pipeline - Ashutosh :7 July 2014
                    // match enum vals to text vals in language pack for return
                    if(!empty($app_list_strings[$results[$i]->field_name_map[$field]['options']]) && !strstr($field,'state')) {
                        $results[$i]->$field = $app_list_strings[$results[$i]->field_name_map[$field]['options']][$results[$i]->$field];
                    }
                }

                if($results[$i] instanceof Team){
                    $results[$i]->name = Team::getDisplayName($results[$i]->name, $results[$i]->name_2);
                }

                if (isset($listData[$field])) {
                    $data['fields'][$i][$field] = $listData[$field];
                } else if (isset($results[$i]->$field)) {
                    $data['fields'][$i][$field] = $results[$i]->$field;
                } else {
                    $data['fields'][$i][$field] = '';
                }
            }
        }

        if (is_array($data['fields'])) {
            foreach ($data['fields'] as $i => $recordIn) {
                if (!is_array($recordIn)) {
                    continue;
                }

                foreach ($recordIn as $col => $dataIn) {
                    if (!is_scalar($dataIn)) {
                        continue;
                    }

                    $data['fields'][$i][$col] = html_entity_decode($dataIn, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        return $data;
    }

    /**
     * Filter duplicate results from the list
     *
     * @param array $list
     * @return	array
     */
    protected function filterResults($list)
    {
        $fieldsFiltered = array();
        foreach ($list['fields'] as $field) {
            $found = false;
            foreach ($fieldsFiltered as $item) {
                if ($item === $field) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $fieldsFiltered[] = $field;
            }
        }

        $list['totalCount'] = count($fieldsFiltered);
        $list['fields']     = $fieldsFiltered;

        return $list;
    }

    /**
     * Returns raw search results. Filters should be applied later.
     *
     * @param array $args
     * @param boolean $singleSelect
     * @return array
     */
    protected function getRawResults($args, $singleSelect = false)
    {
        $orderBy = !empty($args['order']) ? $args['order'] : '';
        $limit   = !empty($args['limit']) ? intval($args['limit']) : '';
        $data    = array();

        foreach ($args['modules'] as $module) {
            $focus = SugarModule::get($module)->loadBean();

            $orderBy = $focus->db->getValidDBName(($args['order_by_name'] && $focus instanceof Person && $args['order'] == 'name') ? 'last_name' : $orderBy);

            if ($focus->ACLAccess('ListView', true)) {
                $where = $this->constructWhere($focus, $args);
                $data  = $this->updateData($data, $focus, $orderBy, $where, $limit, $singleSelect);
            }
        }

        return $data;
    }

    /**
     * Returns search results with all fixes applied
     *
     * @param array $data
     * @param array $args
     * @return array
     */
    protected function prepareResults($data, $args)
    {
        $results['totalCount'] = $count = count($data);
        $results['fields']     = array();

        for ($i = 0; $i < $count; $i++) {
            $field = array();
            $field['module'] = $data[$i]->object_name;

            $field = $this->overrideContactId($field, $data[$i], $args);
            $field = $this->updateContactName($field, $args);

            $results['fields'][$i] = $this->prepareField($field, $args);
        }

        return $results;
    }

    /**
     * Returns user search results
     *
     * @param string $condition
     * @return array
     */
    protected function getUserResults($condition,$args=array())
    {
        $users = $this->getUserArray($condition,$args);

        $results = array();
        $results['totalCount'] = count($users);
        $results['fields']     = array();

        foreach ($users as $id => $name) {
            array_push(
                $results['fields'],
                array(
                    'id' => (string) $id,
                    'user_name' => $name,
                    'module' => 'Users'
            ));
        }

        return $results;
    }

    /**
     * Merges current module search results to given list and returns it
     *
     * @param array $data
     * @param SugarBean $focus
     * @param string $orderBy
     * @param string $where
     * @param string $limit
     * @param boolean $singleSelect
     * @return array
     */
    protected function updateData($data, $focus, $orderBy, $where, $limit, $singleSelect = false)
    {
        //If sqs for client then create new list query from accounts_filter_result
        //Else if sqs for client contact then create new list query from customContact
        //Modified by Mohit Kumar Gupta 27-01-2014
    	if ($focus instanceof Account) {
    		 $focus = new accounts_filter_result();   		
    	}else if ($focus instanceof Contact) {
    	    $focus = new customContact();
    	}
        $result = $focus->get_list($orderBy, $where, 0, $limit, -1, 0, $singleSelect);

        return array_merge($data, $result['list']);
    }

    /**
     * Updates search result with proper contact name
     *
     * @param array $result
     * @param array $args
     * @return string
     */
    protected function updateContactName($result, $args)
    {
        global $locale;

        $result[$args['field_list'][0]] = $locale->getLocaleFormattedName(
            $result['first_name'],
            $result['last_name'],
            $result['salutation']
        );

        return $result;
    }

    /**
     * Overrides contact_id and reports_to_id params (to 'id')
     *
     * @param array $result
     * @param object $data
     * @param array $args
     * @return array
     */
    protected function overrideContactId($result, $data, $args)
    {
        foreach ($args['field_list'] as $field) {
            $result[$field] = (preg_match('/reports_to_id$/s',$field)
                               || preg_match('/contact_id$/s',$field))
                ? $data->id // "reports_to_id" to "id"
                : $data->$field;
        }

        return $result;
    }

    /**
     * Returns prepared arguments. Should be redefined in child classes.
     *
     * @param array $arguments
     * @return array
     */
    protected function prepareArguments($args)
    {
        global $sugar_config;

        // override query limits
        if ($sugar_config['list_max_entries_per_page'] < ($args['limit'] + 1)) {
            $sugar_config['list_max_entries_per_page'] = ($args['limit'] + 1);
        }

        $defaults = array(
            'order_by_name' => false,
        );
        //Added extra where clause if passed from sqs - Ashutosh - 8 July 2014
        $this->extra_where = (isset($args['whereExtra']))?html_entity_decode_utf8($args['whereExtra']):'';
        
        

        // Sanitize group
        /* BUG: 52684 properly check for 'and' jeff@neposystems.com */
        if(!empty($args['group'])  && strcasecmp($args['group'], 'and') == 0) {
            $args['group'] = 'AND';
        } else {
            $args['group'] = 'OR';
        }

        return array_merge($defaults, $args);
    }

    /**
     * Returns prepared field array. Should be redefined in child classes.
     *
     * @param array $field
     * @param array $args
     * @return array
     */
    protected function prepareField($field, $args)
    {
        return $field;
    }

    /**
     * Returns user array
     *
     * @param string $condition
     * @return array
     */
    protected function getUserArray($condition,$args=array())
    {
        /*
         * Added By : Ashutosh
         * Purpose: to restrict lead_reviewer
         */ 
        if($args['lead_reviewer'] == 'false'){
            require_once 'custom/modules/Users/userCustomBean.php';
            $obUser = new customUsers();
            return $results = $obUser->CustomGetUserArrayFromFullName($condition, true);
        }
        return (showFullName())
            // utils.php, if system is configured to show full name
            ? getUserArrayFromFullName($condition, true)
            : get_user_array(false, 'Active', '', false, $condition,' AND portal_only=0 ',false);
    }

    /**
     * Returns additional where condition for non private teams
     *
     * @param array $args
     * @return string
     */
    protected function getNonPrivateTeamsWhere($args)
    {
        global $db;

        $where = sprintf(
            "(teams.name like '%s%%' or teams.name_2 like '%s%%')",
            $db->quote($args['conditions'][0]['value']),
            $db->quote($args['conditions'][0]['value'])
        );

        $where .= (!empty($args['conditions'][1]) && $args['conditions'][1]['name'] == 'user_id')
            ? sprintf(
                " AND teams.id in (select team_id from team_memberships where user_id = '%s')",
                $db->quote($args['conditions'][1]['value'])
            )
            : ' AND teams.private = 0';

        return $where;
    }

    /**
     * Returns JSON encoded data
     *
     * @param array $data
     * @return string
     */
    protected function getJsonEncodedData($data)
    {
        $json = getJSONobj();

        return $json->encodeReal($data);
    }

    /**
     * Returns formatted JSON encoded search results
     *
     * @param array $args
     * @param array $results
     * @return string
     */
    protected function getFormattedJsonResults($results, $args)
    {
        $results = $this->formatResults($results, $args);

        return $this->getJsonEncodedData($results);
    }

    /**
     * Returns filtered JSON encoded search results
     *
     * @param array $results
     * @return string
     */
    protected function getFilteredJsonResults($results)
    {
        $results = $this->filterResults($results);

        return $this->getJsonEncodedData($results);
    }

    /**
     * Returns updated arguments array
     *
     * @param array $args
     * @return array
     */
    protected function updateQueryArguments($args)
    {
        $args['order_by_name'] = true;

        return $args;
    }

    /**
     * Returns updated arguments array for contact query
     *
     * @param array $args
     * @return array
     */
    protected function updateContactArrayArguments($args)
    {
        return $args;
    }

    /**
     * Returns updated arguments array for team query
     *
     * @param array $args
     * @return array
     */
    protected function updateTeamArrayArguments($args)
    {
        $this->extra_where = $this->getNonPrivateTeamsWhere($args);
        $args['modules'] = array('Teams');

        return $args;
    }
    
    /**
     * get contact list corresponding to account id
     * @author Mohit Kumar Gupta
     * @date 16-Dec-2013
     * @return JSON
     */
    public function get_default_contact_array($args)
    {
    	$args    = $this->prepareArguments($args);
    	$args    = $this->updateContactArrayArguments($args);
    	$data    = $this->getRawResults($args,true);
    	$results = $this->prepareResults($data, $args);
    
    	return $this->getFilteredJsonResults($results);
    }
}
