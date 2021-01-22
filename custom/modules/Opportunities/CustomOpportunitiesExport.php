<?php
if (!defined('sugarEntry') || !sugarEntry)
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

/**
 * *******************************************************************************
 *
 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * ******************************************************************************
 */
    
// include the base class
require_once 'custom/modules/Opportunities/OpportunitySummary.php';
class CustomOpportunitiesExport extends OpportunitySummary
{
    
    /**
     * Overriden the method
     * @modified by Mohit Kumar Gupta
     * @date 13/08/2014
     * @see Opportunity::create_export_query()
     */
    function create_export_query(&$order_by, &$where, $relate_link_join = '')
    {
        
        //Get the team filter and replace project opportunity with client opportunity
        $teamQuery = '';
        $this->add_team_security_where_clause($teamQuery);
        if (!empty($teamQuery)) {
        	$teamQuery = str_replace('opportunities', 'sub_opp', $teamQuery);
        }
        
        //SQL for export 
        $stExportSql = "select 
            opportunities.name opportunity_parent,
            sub_opp.name child_opportunity,
            getBidsDueDate(sub_opp.date_closed,
                    sub_opp.bid_due_timezone) date_closed_tz,
            sub_opp.bid_due_timezone,
            sub_opp.amount_usdollar,
            sub_opp.sales_stage,
            sub_opp.client_bid_status,
            accounts.name account_name,
            accounts.address1,
            accounts.billing_address_city,
            accounts.billing_address_state,
            accounts.billing_address_postalcode,
            LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name, ''),
                                    ' ',
                                    IFNULL(contacts.last_name, '')))) contact_name,
            contacts.phone_work_ext,
            contacts.phone_work,
            contacts.phone_fax,
            email_addresses.email_address,
            opportunities.lead_address,
            opportunities.lead_state,
            opportunities.lead_received,
            oss_county.name lead_county,
            opportunities.lead_structure,
            opportunities.lead_city,
            opportunities.lead_type,
            opportunities.lead_zip_code,
            opportunities.lead_owner,
            opportunities.lead_project_status,
            opportunities.lead_union_c,
            opportunities.lead_non_union,
            opportunities.lead_prevailing_wage,
            opportunities.lead_start_date,
            opportunities.lead_square_footage,
            opportunities.lead_end_date,
            opportunities.lead_contact_no,
            opportunities.lead_stories_below_grade,
            opportunities.lead_valuation,
            opportunities.lead_scope_of_work,
            opportunities.lead_stories_above_grade,
            opportunities.lead_number_of_buildings,
            opportunities.is_archived,
            opportunities.custom_field_1,
            opportunities.custom_field_2,
            opportunities.team_id,
            opportunities.team_set_id,
            teams.name as team_name,
            LTRIM(RTRIM(CONCAT(IFNULL(users.first_name, ''),
                                    ' ',
                                    IFNULL(users.last_name, '')))) assigned_user_name
        FROM
            opportunities
                RIGHT JOIN
            opportunities sub_opp ON opportunities.id = sub_opp.parent_opportunity_id
                AND sub_opp.deleted = 0 ".$teamQuery." 
                LEFT JOIN
            accounts_opportunities ON sub_opp.id = accounts_opportunities.opportunity_id
                AND accounts_opportunities.deleted = 0
                LEFT JOIN
            accounts accounts ON accounts.id = accounts_opportunities.account_id
                AND accounts.deleted = 0
                LEFT JOIN
            contacts ON sub_opp.contact_id = contacts.id
                AND contacts.deleted = 0
                LEFT JOIN
            oss_county ON oss_county.id = opportunities.lead_county
                and oss_county.deleted = 0
                LEFT JOIN
            users ON sub_opp.assigned_user_id = users.id
                AND users.deleted = 0
                LEFT JOIN 
            team_sets ts ON opportunities.team_set_id=ts.id AND ts.deleted=0 
                LEFT JOIN 
            teams teams ON teams.id=ts.id AND teams.deleted=0 AND teams.deleted=0
                LEFT JOIN
            email_addr_bean_rel ON contacts.id = email_addr_bean_rel.bean_id
                AND email_addr_bean_rel.bean_module = 'Contacts'
                And email_addr_bean_rel.deleted = 0
                And email_addr_bean_rel.primary_address = 1
                LEFT JOIN
            email_addresses ON email_addresses.id = email_addr_bean_rel.email_address_id";
        
        //add current post to request array in case of export data on selection of select all
        //Modified By mohit Kumar Gupta 11-Aug-2014
        if (!empty($_REQUEST['current_post'])) {
            $currentPost = unserialize(base64_decode($_REQUEST['current_post']));
            $_REQUEST = array_merge($_REQUEST, $currentPost);
        }
        
        // check if zone name is added in search criteria
        if ((isset($_REQUEST['zone_name_advanced']) && count($_REQUEST['zone_name_advanced']) >0  )) {
             
            $arAllSelectedZones = $_REQUEST['zone_name_advanced'];
        }
        
        // check if zone name is passed from zone report
        if(isset($_REQUEST['zone_name']) && trim($_REQUEST['zone_name']) != ''  ){
            $arAllSelectedZones = array($_REQUEST['zone_name']);
        }
        
        if (isset($arAllSelectedZones) && count($arAllSelectedZones) > 0) {
            $stExportSql .= ' LEFT JOIN oss_zone_opportunities_1_c  zonerel ON  oss_zone_opportunities_1opportunities_idb = opportunities.id AND zonerel.deleted =0
                                 LEFT JOIN oss_zone  ON zonerel.oss_zone_opportunities_1oss_zone_ida=oss_zone.id AND oss_zone.deleted =0     ';
        }
        
        $filter = array();
        
        //SQL for export from list view query
        $stOppSQL = parent::create_new_list_query($order_by, $where, $filter, array(), 0, '', true, $this, true);
        $stOppSQL['where'] .= (!empty($stOppSQL['where'])) ? ' AND sub_opp.parent_opportunity_id IS NOT NULL' : ' sub_opp.parent_opportunity_id IS NOT NULL';
        $stExportSql .= $stOppSQL['where'];
        return $stExportSql;
    }
    
    
    /**
     * Sample Import Template
     * @param String $order_by
     * @param String $where
     * @param string $relate_link_join
     * @return string
     */   
    function create_export_query_sample(&$order_by, &$where, $relate_link_join = '')
    {
        //Set Appropriate where clause
        $stWhere = ($where == '') ? '' : ' AND ' . $where;
        //SQL for export
        $stExportSql = "select
            opportunities.name opportunity_parent,
            child_opp.name child_opportunity,
            getBidsDueDate(child_opp.date_closed,
                    child_opp.bid_due_timezone) date_closed_tz,
            child_opp.bid_due_timezone,
            child_opp.amount_usdollar,
            child_opp.sales_stage,
            child_opp.client_bid_status,
            accounts.name account_name,
            LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name, ''),
                                    ' ',
                                    IFNULL(contacts.last_name, '')))) contact_name,
            opportunities.lead_address,
            opportunities.lead_state,
            opportunities.lead_received,
            oss_county.name lead_county,
            opportunities.lead_structure,
            opportunities.lead_city,
            opportunities.lead_type,
            opportunities.lead_zip_code,
            opportunities.lead_owner,
            opportunities.lead_project_status,
            opportunities.lead_union_c,
            opportunities.lead_non_union,
            opportunities.lead_prevailing_wage,
            opportunities.lead_start_date,
            opportunities.lead_square_footage,
            opportunities.lead_end_date,
            opportunities.lead_contact_no,
            opportunities.lead_stories_below_grade,
            opportunities.lead_valuation,
            opportunities.lead_scope_of_work,
            opportunities.lead_stories_above_grade,
            opportunities.lead_number_of_buildings,
            opportunities.is_archived,
            opportunities.custom_field_1,
            opportunities.custom_field_2,
            LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name, ''),
                                        ' ',
                                        IFNULL(jt1.last_name, '')))) assigned_user_name,
               
            LTRIM(RTRIM(CONCAT(IFNULL(jt2.first_name, ''),
                                    ' ',
                                    IFNULL(jt2.last_name, '')))) created_by_name,
            LTRIM(RTRIM(CONCAT(IFNULL(jt3.first_name, ''),
                                    ' ',
                                    IFNULL(jt3.last_name, '')))) modified_by_name
        FROM
            opportunities
                RIGHT JOIN
            opportunities child_opp ON opportunities.id = child_opp.parent_opportunity_id
                AND child_opp.deleted = 0
                LEFT JOIN
            accounts_opportunities ON child_opp.id = accounts_opportunities.opportunity_id
                AND accounts_opportunities.deleted = 0
                LEFT JOIN
            accounts accounts ON accounts.id = accounts_opportunities.account_id
                AND accounts.deleted = 0
                LEFT JOIN
            contacts ON child_opp.contact_id = contacts.id
                AND contacts.deleted = 0
                LEFT JOIN
            oss_county ON oss_county.id = opportunities.lead_county
                and oss_county.deleted = 0
                LEFT JOIN
            users jt1 ON child_opp.assigned_user_id = jt1.id
                AND jt1.deleted = 0
                LEFT JOIN
            users jt2 ON accounts.created_by = jt2.id
                AND jt2.deleted = 0
                LEFT JOIN
            users jt3 ON accounts.modified_user_id = jt3.id
                AND jt3.deleted = 0
        WHERE
            1 = 1 " . $stWhere;
    
        return $stExportSql;
    }
}