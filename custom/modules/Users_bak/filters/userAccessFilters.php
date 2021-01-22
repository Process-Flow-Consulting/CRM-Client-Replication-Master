<?php
if (! defined('sugarEntry') || ! sugarEntry)
    die('Not A Valid Entry Point');
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

/**
 * CLASS : userAccessFilters
 * Purpose : Class definition for manipulating the user filters
 * for current logged in user.
 */

require_once ('modules/oss_user_filters/oss_user_filters.php');
require_once ('custom/modules/Users/role_config.php');

class userAccessFilters
{

    public $arMappings = array(
            'team_member' => 'assigned_user_id',
            'zip' => 'billing_address_postalcode',
            'county' => 'county_id',
            'state' => 'billing_address_state'
    );

    /**
     *
     * @method : userAccessFilters
     * @return : array (saved filters)
     * @uses : Constructor Definition
     */
    function userAccessFilters ()
    {}

    /**
     *
     * @method : getCurrentUserFilters
     * @return : array (saved filters)
     * @uses : Get all the saved filters for logged in
     *       user.
     */
    public static function getCurrentUserFilters ()
    {
        global $current_user;
        $arSavedUserFilters = array();
        
        $obUserFilters = new oss_user_filters();
        $arUserFilters = $obUserFilters->get_full_list('', 
                'assigned_user_id = "' . $current_user->id . '"');
        if (count($arUserFilters) > 0) {
            foreach ($arUserFilters as $obUserFilter) {
                
                $arSavedUserFilters[$obUserFilter->filter_type][] = $obUserFilter->filter_value;
            }
        }
        
        return $arSavedUserFilters;
    }

    /**
     *
     * @method : getCurrentUserFiltersClauses
     * @return : array (saved filters join and where conditions)
     * @uses : Get all the saved filters for logged in
     *       user.
     */
    public static function getCurrentUserFiltersClauses ()
    {
        global $current_user;
        $arSavedUserFilters = array();
        
        $obUserFilters = new oss_user_filters();
        $arUserFilters = $obUserFilters->get_full_list('', 
                'oss_user_filters.assigned_user_id = "' . $current_user->id . '" 
				AND oss_user_filters.filter_type="joins_and_where" ');
        
        if (count($arUserFilters) > 0) {
            foreach ($arUserFilters as $obUserFilter) {
                
                $arSavedUserFilters = $obUserFilter->filter_clauses;
            }
        }
        // check wheather fileters are set for this user
        $arUserFilterData = self::getCurrentUserFilters();
        // leave geo_filter_for joins_and_where indeces
        unset($arUserFilterData['geo_filter_for'], 
                $arUserFilterData['joins_and_where']);
        $arSavedUserFilters = (count($arUserFilterData) > 0) ? $arSavedUserFilters : "";
        return $arSavedUserFilters;
    }

    /**
     *
     * @method : getLeadFilterClause
     * @return : string (SQL)
     * @uses : to get the current saved filters SQL
     *       further this sql will be used to get the
     *       leads as per the filteration criterea
     *       for Project location
     */
    public static function getLeadFilterClause ()
    {
        global $current_user;
        $arFilterSql = array();
        $arFilterData = self::getCurrentUserFiltersClauses($current_user->id);
        
        $arFilterData = json_decode(base64_decode($arFilterData));
        
        return (isset($arFilterData->leads)) ? $arFilterData->leads : '';
    }

    /**
     *
     * @method : getOpporutnityFilterWehreClause
     * @uses : To get ehe filter sql based on the geo location for Opportunities
     * @return : string
     */
    function getOpporutnityFilterWehreClause ($bForParent = false)
    {
        global $current_user;
        $arFilterData = array();
        $arFilterData = self::getCurrentUserFiltersClauses($current_user->id);
        
        // var_dump($arFilterData);
        $arFilterData = json_decode(base64_decode($arFilterData));
        
        return (isset($arFilterData->opportunties)) ? $arFilterData->opportunties : '';
    }

    /**
     *
     * @method : isLeadAccessable
     * @uses : to check if a record is accesable as per the filters set for
     *       current user
     * @param : $stLeadId,
     *            $bDieWithError
     * @return : bool
     *        
     */
    public static function isLeadAccessable ($stLeadId, $bDieWithError = false)
    {
        global $db, $current_user;
        
        $bReturn = false;
        // get the filter sql
        $obFilterSql = self::getLeadFilterClause();
        
        if ($obFilterSql != '') {
            
            $obLeads = new Lead();
            $arSql = $obLeads->create_new_list_query('', '', array(), array(), 0, '', 1);
            $arSql['from'] .= $obFilterSql->listview->joins;
            $arSql['where'] .= $obFilterSql->listview->where;
            
            $stGetSql = $arSql['select'] . ' ' . $arSql['from'] . ' ' .
                     $arSql['where'] . ' AND (leads.id ="' . $stLeadId .
                     '" OR  leads.parent_lead_id ="' . $stLeadId . '" ) ';
            
            $rsResult = $db->query($stGetSql);
            $arResult = $db->fetchByAssoc($rsResult);
            
            if (is_array($arResult) && count($arResult) > 0) {
                $bReturn = true;
            } else {
                $bReturn = false;
            }
        } else {
            // if no filter set allow them to access the lead
            $bReturn = true;
        }
        if (! $bReturn && ! $bDieWithError) {
            sugar_die('You are not authorized to view this record.');
        }
        return $bReturn;
    }

    /**
     *
     * @method : isOpporunityAccessable
     * @return : bool
     * @uses : to check if a record is accesable as per the filters set for
     *       current user
     */
    public static function isOpporunityAccessable ($stLeadId, 
            $bDieWithError = false, $bForParent = false)
    {
        global $db, $current_user;
        $bReturn = false;
        // get the filter sql
        
        $obFilterSql = self::getOpporutnityFilterWehreClause($bForParent);
        
        if ($obFilterSql != '') {
            
            $obOpp = new Opportunity();
            $arOppSql = $obOpp->create_new_list_query('', '', array(), array(), 0, '', 1);
            
            if (! $bForParent) {
                $arOppSql['from'] .= ' LEFT JOIN accounts_opportunities ON accounts_opportunities.opportunity_id = opportunities.id AND accounts_opportunities.deleted =0
			 	LEFT JOIN accounts on accounts.id = accounts_opportunities.account_id and accounts.deleted =0 ' .
                         $obFilterSql->summaryview->joins;
                $arOppSql['where'] .= $obFilterSql->summaryview->where .
                         ' AND  opportunities.id= "' . $stLeadId . '" ';
                
                $stGetSql = $arOppSql['select'] . ' ' . $arOppSql['from'] . ' ' .
                         $arOppSql['where'];
            } else {
                
                $arOppSql['from'] .= ' INNER JOIN ( SELECT COALESCE(opportunities.parent_opportunity_id,opportunities.id) id
			 	FROM opportunities ' .
                         $obFilterSql->listview->joins . '
			 	WHERE 1=1
			 	' . $obFilterSql->listview->where . '
			 	GROUP BY COALESCE(opportunities.parent_opportunity_id,opportunities.id)
			 	)tmpOppFilter ON
			 	opportunities.id=tmpOppFilter.id and opportunities.deleted =0  ';
                $arOppSql['where'] .= ' AND  opportunities.id= "' . $stLeadId .
                         '" ';
                
                $stGetSql = $arOppSql['select'] . ' ' . $arOppSql['from'] . ' ' .
                         $arOppSql['where'];
            }
            // echo '<pre>';print_r($stGetSql);die;
            $rsResult = $db->query($stGetSql);
            
            $arResult = $db->fetchByAssoc($rsResult);
            if (is_array($arResult) && count($arResult) > 0) {
                $bReturn = true;
            } else {
                $bReturn = false;
            }
        } else {
            $bReturn = false;
        }
        if (! $bReturn && $stFilterSql !== '' && ! $bDieWithError) {
            sugar_die('You are not authorized to view this record.');
        }
        return $bReturn;
    }

    /**
     *
     * @method : getBiddersFilterClause
     * @return : sql if filter is set as client
     * @uses :: to get bidders list filter caluse
     */
    function getBiddersFilterClause ()
    {
        global $current_user;
        $stFinalSql = '';
        // check if location filter is saved for this istance
        $admin = new Administration();
        $admin = $admin->retrieveSettings('instance', true);
        
        if (isset($admin->settings['instance_geo_filter'])) {
            switch ($admin->settings['instance_geo_filter']) {
                
                case 'project_location':
                    $stFinalSql = self::_BiddersFilterClauseProjectLocation();
                    break;
                
                case 'client_location':
                    $stFinalSql = self::_BiddersFilterClauseClientLocation();
                    break;
            }
        }
        
        return $stFinalSql;
    }

    /**
     *
     * @method _BiddersFilterClauseProjectLocation
     * @uses :function to get bidders filter JOINS if geo location is Project *
     * @return string
     */
    private static function _BiddersFilterClauseProjectLocation ()
    {
        global $current_user;
        $arFilterData = array();
        $arFilterData = self::getCurrentUserFiltersClauses($current_user->id);
        
        $arFilterData = json_decode(base64_decode($arFilterData));
        
        $arFilterData->leads->listview->joins;
        
        $arFilterData->leads->listview->joins = str_replace(' leads.', 
                ' PROJECTLEADALIAS.', $arFilterData->leads->listview->joins);
        $arFilterData->leads->listview->where = str_replace('leads.', 
                ' PROJECTLEADALIAS.', $arFilterData->leads->listview->where);
        
        return $arFilterData;
    }

    /**
     *
     * @method _BiddersFilterClauseClientLocation
     * @uses :function to get bidders filter JOINS if geo location is client
     * @return string
     */
    private static function _BiddersFilterClauseClientLocation ()
    {
        global $current_user;
        $arMappings = $arMappings = array(
                'team_member' => 'assigned_user_id',
                'zip' => 'billing_address_postalcode',
                'county' => 'county_id',
                'state' => 'billing_address_state'
        );
        ;
        
        $stFinalSql = '';
        $stFilterJoin = '';
        // check if location filter is saved for this istance
        $admin = new Administration();
        $admin = $admin->retrieveSettings('instance', true);
        
        if (isset($admin->settings['instance_geo_filter']) &&
                 $admin->settings['instance_geo_filter'] == 'client_location') {
            
            $arAllFilters = self::getCurrentUserFilters();
            $stSelectedGeoFilter = '';
            
            // echo '<pre>';print_r($arAllFilters);echo '</pre>';
            $stUserClause = ' = "' . $current_user->id . '"';
            $stSelectedGeoFilter = '';
            // get geographic filter value
            if (isset($arAllFilters['geo_filter_for'][0]) &&
                     trim($arAllFilters['geo_filter_for'][0]) != '') {
                
                $stSelectedGeoFilter = $arAllFilters['geo_filter_for'][0];
            }
            // get geographic filter value
            if (isset($arAllFilters['geo_filter_for'][0]) &&
                     trim($arAllFilters['geo_filter_for'][0]) != '') {
                
                // echo $arMappings[$arAllFilters ['geo_filter_for'] [0]];
                
                $stWhereClauseUndef = '';
                foreach ($arAllFilters as $stFieldName => $arFieldValues) {
                    
                    if ($stFieldName == 'classification') {
                        // for classifications get the ids of accounts which
                        // are matching with selected filter classification
                        $stFilterJoin .= 'INNER JOIN  oss_classifion_accounts_c classification on accounts.id = classification.oss_classid41cccounts_idb AND classification.deleted = 0
						INNER JOIN  oss_user_filters clsFilters ON (clsFilters.filter_type= "classification" AND classification.oss_classi48bbication_ida = clsFilters.filter_value  AND clsFilters.assigned_user_id ' .
                                 $stUserClause . ') ';
                    } elseif ($stFieldName == $stSelectedGeoFilter) {
                        $stFilterJoin .= ' INNER JOIN  oss_user_filters ' .
                                 $stFieldName . 'Filters ON (' . $stFieldName .
                                 'Filters.filter_type= "' . $stFieldName .
                                 '" AND accounts.' . $arMappings[$stFieldName] .
                                 ' =' . $stFieldName .
                                 'Filters.filter_value AND ' . $stFieldName .
                                 'Filters.assigned_user_id ' . $stUserClause . ')
						';
                    }
                }
            }
        }
        return $stFilterJoin;
    }

    /**
     *
     * @method : isTeamManager
     * @uses : to check if user has team manager role
     * @param : $stUid            
     * @return : bool
     *        
     */
    function isTeamManager ($stUid)
    {
        global $arUserRoleConfig;
        $bContinue = false;
        $arUserRoles = (ACLRole::getUserRoles($stUid, 0));
        
        // check if this user is not in team manager role
        foreach ($arUserRoles as $obUserRole) {
            
            if ($obUserRole->id == $arUserRoleConfig['team_manager']) {
                $bContinue = true;
            }
        }
        return $bContinue;
    }

    /**
     *
     * @method : getFilterClauseClient
     * @uses : method to get filter and where clause for user filter on accounts
     *       or contacts
     * @param : $arTableAliasParams
     *            (table alias mapping)
     */
    function getFilterClauseClient ($stFilterFor = 'accounts', 
            $arTableAliasParams = array())
    {
        global $current_user, $db;
        
        // check if location filter is saved for this istance
        $admin = new Administration();
        $admin = $admin->retrieveSettings('instance', true);
        $arFilterClause = array();
        
        // check user filter if client location is set
        if ((isset($admin->settings['instance_geo_filter']) &&
                 $admin->settings['instance_geo_filter'] == 'client_location') && (isset(
                        $admin->settings['instance_geo_filter_for_clients']) &&
                 $admin->settings['instance_geo_filter_for_clients'] == 1)) {
            
            $arAllFilters = self::getCurrentUserFilters();
            
            $stAccountTable = isset($arTableAliasParams['accounts']['table']) ? $arTableAliasParams['accounts']['table'] : 'accounts';
            $stContactTable = isset($arTableAliasParams['contacts']['table']) ? $arTableAliasParams['contacts']['table'] : 'contacts';
            $stClassificationTable = isset(
                    $arTableAliasParams['classification']['table']) ? $arTableAliasParams['classification']['table'] : 'oss_classifion_accounts_c';
            
            $stClassificationTableField = isset(
                    $arTableAliasParams['classification']['field']) ? $arTableAliasParams['classification']['field'] : 'oss_classi48bbication_ida';
            
            // get geographic filter value
            if (isset($arAllFilters['geo_filter_for'][0]) &&
                     trim($arAllFilters['geo_filter_for'][0]) != '') {
                // set filter string
                $stFilterString = implode('","', 
                        $arAllFilters[$arAllFilters['geo_filter_for'][0]]);
                
                if (trim($stFilterString) != '') {
                    $arFilterClauses[] = $stAccountTable . '.' .
                             $this->arMappings[$arAllFilters['geo_filter_for'][0]] .
                             ' IN ("' . $stFilterString . '")';
                }
                
                if (isset($arAllFilters['classification'])) {
                    
                    $arFilterClauses[] = $stClassificationTable .
                             ".{$stClassificationTableField} IN (\"" .
                             implode('","', $arAllFilters['classification']) .
                             '")';
                }
            }
            // check if there are any filter set
            if (count($arFilterClauses) > 0) {
                $arFilterClause['filters'] = implode(' AND ', $arFilterClauses);
            }
            // set visibility check
            $arFilterClause['visibility'] = '(' . $stAccountTable .
                     '.assigned_user_id = ' . $db->quoted($current_user->id) .
                     ' AND ' . $stAccountTable . '.visibility =1 AND ' .
                     $stAccountTable . '.deleted =0)';
            if ($stFilterFor == 'contacts') {
                $arFilterClause['visibility'] .= ' OR (' . $stContactTable .
                         '.assigned_user_id = ' . $db->quoted($current_user->id) .
                         ' AND ' . $stContactTable . '.visibility =1 AND ' .
                         $stContactTable . '.deleted =0)';
            }
        }
        
        return $arFilterClause;
    }

    /**
     *
     * @uses method to check if a client is accessable to a user.
     * @param string $stAccountId            
     * @param string $bDieWithError            
     * @return boolean
     */
    function isClientAccessable ($stAccountId = '', $bDieWithError = false)
    {
        global $db, $current_user;
        
        $bReturn = false;
        
        // get the filter sql
        $arTableAlias = array();
        $arTableAlias['classification']['table'] = 'account_cl';
        $arTableAlias['classification']['field'] = 'oss_classi48bbication_ida';
        $obFilterSql = self::getFilterClauseClient('accounts', $arTableAlias);
        
        if (count($obFilterSql) > 0) {
            
            $obAccounts = new Account();
            $where = (! empty($obFilterSql['filters'])) ? " ( " .
                     $obFilterSql['filters'] . " ) OR " : '';
            $where .= $obFilterSql['visibility'];
            $arSql = $obAccounts->create_new_list_query('', $where, array(), array(), 0, 
                    '', 1);
            $arSql['from'] .= ' LEFT JOIN oss_classifion_accounts_c as account_cl on account_cl.oss_classid41cccounts_idb = accounts.id  AND account_cl.deleted=0 ';
            if ($stAccountId != '') {
                $arSql['where'] .= " AND accounts.id='" . $stAccountId . "'";
            }
            
            $stGetSql = $arSql['select'] . ' ' . $arSql['from'] . ' ' .
                     $arSql['where'] . ' ' . $arSql['order_by'];
            $rsResult = $db->query($stGetSql);
            $arResult = $db->fetchByAssoc($rsResult);
            if (is_array($arResult) && count($arResult) > 0) {
                $bReturn = true;
            } else {
                // if filter set, not allow them to access the client
                $bReturn = false;
            }
        } else {
            // if no filter set, allow them to access the client
            $bReturn = true;
        }
        if (! $bReturn && ! $bDieWithError) {
            sugar_die('You are not authorized to view this record.');
        }
        return $bReturn;
    }

    /**
     *
     * @uses method to check if a client is accessable to a user.
     * @param string $stAccountId            
     * @param string $bDieWithError            
     * @return boolean
     */
    function isClientContactAccessable ($stContactId = '', $bDieWithError = false)
    {
        global $db, $current_user;
        
        $bReturn = false;
        
        // get the filter sql
        $arTableAlias = array();
        $arTableAlias['classification']['table'] = 'account_cl';
        $arTableAlias['classification']['field'] = 'oss_classi48bbication_ida';
        $obFilterSql = self::getFilterClauseClient('contacts', $arTableAlias);
        
        if (count($obFilterSql) > 0) {
            
            $obContacts = new Contact();
            $where = (! empty($obFilterSql['filters'])) ? " ( " .
                     $obFilterSql['filters'] . " ) OR " : '';
            $where .= $obFilterSql['visibility'];
            $arSql = $obContacts->create_new_list_query('', $where, array(), array(), 0, 
                    '', 1, '', true);
            $arSql['from'] .= ' LEFT JOIN oss_classifion_accounts_c as account_cl on account_cl.oss_classid41cccounts_idb = accounts.id  AND account_cl.deleted=0 ';
            if ($stContactId != '') {
                $arSql['where'] .= " AND contacts.id='" . $stContactId . "'";
            }
            
            $stGetSql = $arSql['select'] . ' ' . $arSql['from'] . ' ' .
                     $arSql['where'] . ' ' . $arSql['order_by'];
            $rsResult = $db->query($stGetSql);
            $arResult = $db->fetchByAssoc($rsResult);
            if (is_array($arResult) && count($arResult) > 0) {
                $bReturn = true;
            } else {
                // if filter set, not allow them to access the client
                $bReturn = false;
            }
        } else {
            // if no filter set, allow them to access the client
            $bReturn = true;
        }
        if (! $bReturn && ! $bDieWithError) {
            sugar_die('You are not authorized to view this record.');
        }
        return $bReturn;
    }
}

?>
