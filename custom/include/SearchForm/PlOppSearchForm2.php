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


require_once 'include/SearchForm/SearchForm2.php';
/**
 * @Added By : Ashutosh
 * @Date : 16 Sept 2013
 * @purpose : to override the method generateSearchWhere for Project Lead and opportunities
 * for an new option after today
 * 
 */
class PlOppSearchForm extends SearchForm{
	
    /**
     * @Added By : Ashutosh
     * @Date : 16 Sept 2013
     * @purpose : Constructor to call base class constructor
     *
     */
    public function SearchForm($seed, $module, $action = 'index', $options = array()){
    	parent::SearchForm($seed, $module, $action = 'index', $options = array());
    }
    
    
    /**
     * generateSearchWhere
     *
     * This function serves as the central piece of SearchForm2.php
     * It is responsible for creating the WHERE clause for a given search operation
     *
     * @param bool $add_custom_fields boolean indicating whether or not custom fields should be added
     * @param string $module Module to search against
     *
     * @return string the SQL WHERE clause based on the arguments supplied in SearchForm2 instance
     */
    public function generateSearchWhere($add_custom_fields = false, $module='') {
        global $timedate;
    
        $db = $this->seed->db;
        $this->searchColumns = array () ;
        $values = $this->searchFields;
    
        $where_clauses = array();
        $like_char = '%';
        $table_name = $this->seed->object_name;
        $this->seed->fill_in_additional_detail_fields();
    
        //rrs check for team_id
        if(!empty($this->searchFields['team_id']) && !empty($this->searchFields['team_id']['value'])){
            if(!empty($this->searchFields['team_name'])){
                unset($this->searchFields['team_name']);
            }
        }
    
        foreach($this->searchFields as $field=>$parms) {
            $customField = false;
            // Jenny - Bug 7462: We need a type check here to avoid database errors
            // when searching for numeric fields. This is a temporary fix until we have
            // a generic search form validation mechanism.
            $type = (!empty($this->seed->field_name_map[$field]['type']))?$this->seed->field_name_map[$field]['type']:'';
    
            //If range search is enabled for the field, we first check if this is the starting range
            if(!empty($parms['enable_range_search']) && empty($type))
            {
                if(preg_match('/^start_range_(.*?)$/', $field, $match))
                {
                    $real_field = $match[1];
                    $start_field = 'start_range_' . $real_field;
                    $end_field = 'end_range_' . $real_field;
    
                    if(isset($this->searchFields[$start_field]['value']) && isset($this->searchFields[$end_field]['value']))
                    {
                        $this->searchFields[$real_field]['value'] = $this->searchFields[$start_field]['value'] . '<>' . $this->searchFields[$end_field]['value'];
                        $this->searchFields[$real_field]['operator'] = 'between';
                        $parms['value'] = $this->searchFields[$real_field]['value'];
                        $parms['operator'] = 'between';
    
                        $field_type = isset($this->seed->field_name_map[$real_field]['type']) ? $this->seed->field_name_map[$real_field]['type'] : '';
                        if($field_type == 'datetimecombo' || $field_type == 'datetime')
                        {
                            $type = $field_type;
                        }
    
                        $field = $real_field;
                        unset($this->searchFields[$end_field]['value']);
                    }
                } else if (preg_match('/^range_(.*?)$/', $field, $match) && isset($this->searchFields[$field]['value'])) {
                    $real_field = $match[1];
    
                    //Special case for datetime and datetimecombo fields.  By setting the type here we allow an actual between search
                    if(in_array($parms['operator'], array('=', 'between', "not_equal", 'less_than', 'greater_than', 'less_than_equals', 'greater_than_equals')))
                    {
                        $field_type = isset($this->seed->field_name_map[$real_field]['type']) ? $this->seed->field_name_map[$real_field]['type'] : '';
                        if(strtolower($field_type) == 'readonly' && isset($this->seed->field_name_map[$real_field]['dbType'])) {
                            $field_type = $this->seed->field_name_map[$real_field]['dbType'];
                        }
                        if($field_type == 'datetimecombo' || $field_type == 'datetime' || $field_type == 'int')
                        {
                            $type = $field_type;
                        }
                    }
    
                    $this->searchFields[$real_field]['value'] = $this->searchFields[$field]['value'];
                    $this->searchFields[$real_field]['operator'] = $this->searchFields[$field]['operator'];
                    $params['value'] = $this->searchFields[$field]['value'];
                    $params['operator'] = $this->searchFields[$field]['operator'];
                    unset($this->searchFields[$field]['value']);
                    $field = $real_field;
                } else {
                    //Skip this range search field, it is the end field THIS IS NEEDED or the end range date will break the query
                    continue;
                }
            }
    
            //Test to mark whether or not the field is a custom field
            if(!empty($this->seed->field_name_map[$field]['source'])
            && ($this->seed->field_name_map[$field]['source'] == 'custom_fields' ||
                    //Non-db custom fields, such as custom relates
                    ($this->seed->field_name_map[$field]['source'] == 'non-db'
                            && (!empty($this->seed->field_name_map[$field]['custom_module']) ||
                                    isset($this->seed->field_name_map[$field]['ext2']))))){
                $customField = true;
            }
    
            if ($type == 'int' && isset($parms['value']) && !empty($parms['value'])) {
                require_once ('include/SugarFields/SugarFieldHandler.php');
                $intField = SugarFieldHandler::getSugarField('int');
                $newVal = $intField->getSearchWhereValue($parms['value']);
                $parms['value'] = $newVal;
            } elseif($type == 'html' && $customField) {
                continue;
            }
    
    
            if(isset($parms['value']) && $parms['value'] != "") {
    
                $operator = $db->isNumericType($type)?'=':'like';
                if(!empty($parms['operator'])) {
                    $operator = strtolower($parms['operator']);
                }
    
                if(is_array($parms['value'])) {
                    $field_value = '';
    
                    // always construct the where clause for multiselects using the 'like' form to handle combinations of multiple $vals and multiple $parms
                    if(!empty($this->seed->field_name_map[$field]['isMultiSelect']) && $this->seed->field_name_map[$field]['isMultiSelect']) {
                        // construct the query for multenums
                        // use the 'like' query as both custom and OOB multienums are implemented with types that cannot be used with an 'in'
                        $operator = 'custom_enum';
                        $table_name = $this->seed->table_name ;
                        if ($customField)
                            $table_name .= "_cstm" ;
                        $db_field = $table_name . "." . $field;
    
                        foreach($parms['value'] as $val) {
                            if($val != ' ' and $val != '') {
                                $qVal = $db->quote($val);
                                if (!empty($field_value)) {
                                    $field_value .= ' or ';
                                }
                                $field_value .= "$db_field like '%^$qVal^%'";
                            } else {
                                $field_value .= '('.$db_field . ' IS NULL or '.$db_field."='^^' or ".$db_field."='')";
                            }
                        }
    
                    } else {
                        $operator = $operator != 'subquery' ? 'in' : $operator;
                        foreach($parms['value'] as $val) {
                            if($val != ' ' and $val != '') {
                                if (!empty($field_value)) {
                                    $field_value .= ',';
                                }
                                $field_value .= $db->quoteType($type, $val);
                            }
                            // Bug 41209: adding a new operator "isnull" here
                            // to handle the case when blank is selected from dropdown.
                            // In that case, $val is empty.
                            // When $val is empty, we need to use "IS NULL",
                            // as "in (null)" won't work
                            else if ($operator=='in') {
                                $operator = 'isnull';
                            }
                        }
                    }
    
                } else {
                    $field_value = $parms['value'];
                }
    
                //set db_fields array.
                if(!isset($parms['db_field'])) {
                    $parms['db_field'] = array($field);
                }
    
                //This if-else block handles the shortcut checkbox selections for "My Items" and "Closed Only"
                if(!empty($parms['my_items'])) {
                    if( $parms['value'] == false ) {
                        continue;
                    } else {
                        //my items is checked.
                        global $current_user;
                        $field_value = $db->quote($current_user->id);
                        $operator = '=' ;
                    }
                } else if(!empty($parms['closed_values']) && is_array($parms['closed_values'])) {
                    if( $parms['value'] == false ) {
                        continue;
                    } else {
                        $field_value = '';
                        foreach($parms['closed_values'] as $closed_value)
                        {
                            $field_value .= "," . $db->quoted($closed_value);
                        }
                        $field_value = substr($field_value, 1);
                    }
                }
    
                $where = '';
                $itr = 0;
    
                if($field_value != '' || $operator=='isnull') {
    
                    $this->searchColumns [ strtoupper($field) ] = $field ;
                if(isset($parms['db_field'])) {
                    foreach ($parms['db_field'] as $db_field) {
                        if (strstr($db_field, '.') === false) {
                            //Try to get the table for relate fields from link defs
                            if ($type == 'relate' && !empty($this->seed->field_name_map[$field]['link'])
                            && !empty($this->seed->field_name_map[$field]['rname'])) {
                                $link = $this->seed->field_name_map[$field]['link'];
                                $relname = $link['relationship'];
                                if (($this->seed->load_relationship($link))){
                                    //Martin fix #27494
                                    $db_field = $this->seed->field_name_map[$field]['name'];
                                } else {
                                    //Best Guess for table name
                                    $db_field = strtolower($link['module']) . '.' . $db_field;
                                }
    
    
                            }
                            else if ($type == 'parent') {
                                if (!empty($this->searchFields['parent_type'])) {
                                    $parentType = $this->searchFields['parent_type'];
                                    $rel_module = $parentType['value'];
                                    global $beanFiles, $beanList;
                                    if(!empty($beanFiles[$beanList[$rel_module]])) {
                                        require_once($beanFiles[$beanList[$rel_module]]);
                                        $rel_seed = new $beanList[$rel_module]();
                                        $db_field = 'parent_' . $rel_module . '_' . $rel_seed->table_name . '.name';
                                    }
                                }
                            }
                            // Relate fields in custom modules and custom relate fields
                            else if ($type == 'relate' && $customField && !empty($this->seed->field_name_map[$field]['module'])) {
                                $db_field = !empty($this->seed->field_name_map[$field]['name'])?$this->seed->field_name_map[$field]['name']:'name';
                            }
                            else if(!$customField){
                                if ( !empty($this->seed->field_name_map[$field]['db_concat_fields']) )
                                    $db_field = $db->concat($this->seed->table_name, $this->seed->field_name_map[$db_field]['db_concat_fields']);
                                else
                                    $db_field = $this->seed->table_name .  "." . $db_field;
                            }else{
                                if ( !empty($this->seed->field_name_map[$field]['db_concat_fields']) )
                                    $db_field = $db->concat($this->seed->table_name .  "_cstm.", $this->seed->field_name_map[$db_field]['db_concat_fields']);
                                else
                                    $db_field = $this->seed->table_name .  "_cstm." . $db_field;
                            }
    
                        }
    
                        if($type == 'date') {
                            // The regular expression check is to circumvent special case YYYY-MM
                            $operator = '=';
                            if(preg_match('/^\d{4}.\d{1,2}$/', $field_value) != 0) { // preg_match returns number of matches
                                $db_field = $this->seed->db->convert($db_field, "date_format", array("%Y-%m"));
                            } else {
                                $field_value = $timedate->to_db_date($field_value, false);
                                $db_field = $this->seed->db->convert($db_field, "date_format", array("%Y-%m-%d"));
                            }
                        }
    
                        if($type == 'datetime' || $type == 'datetimecombo') {
                            try {
                                if($operator == '=' || $operator == 'between') {
                                    // FG - bug45287 - If User asked for a range, takes edges from it.
                                    $placeholderPos = strpos($field_value, "<>");
                                    if ($placeholderPos !== FALSE && $placeholderPos > 0)
                                    {
                                        $datesLimit = explode("<>", $field_value);
                                        $dateStart = $timedate->getDayStartEndGMT($datesLimit[0]);
                                        $dateEnd = $timedate->getDayStartEndGMT($datesLimit[1]);
                                        $dates = $dateStart;
                                        $dates['end'] = $dateEnd['end'];
                                        $dates['enddate'] = $dateEnd['enddate'];
                                        $dates['endtime'] = $dateEnd['endtime'];
                                    }
                                    else
                                    {
                                        $dates = $timedate->getDayStartEndGMT($field_value);
                                    }
                                    // FG - bug45287 - Note "start" and "end" are the correct interval at GMT timezone
                                    $field_value = array($dates["start"], $dates["end"]);
                                    $operator = 'between';
                                } else if($operator == 'not_equal') {
                                    $dates = $timedate->getDayStartEndGMT($field_value);
                                    $field_value = array($dates["start"], $dates["end"]);
                                    $operator = 'date_not_equal';
                                } else if($operator == 'greater_than') {
                                    $dates = $timedate->getDayStartEndGMT($field_value);
                                    $field_value = $dates["end"];
                                } else if($operator == 'less_than') {
                                    $dates = $timedate->getDayStartEndGMT($field_value);
                                    $field_value = $dates["start"];
                                } else if($operator == 'greater_than_equals') {
                                    $dates = $timedate->getDayStartEndGMT($field_value);
                                    $field_value = $dates["start"];
                                } else if($operator == 'less_than_equals') {
                                    $dates = $timedate->getDayStartEndGMT($field_value);
                                    $field_value = $dates["end"];
                                }
                            } catch(Exception $timeException) {
                                //In the event that a date value is given that cannot be correctly processed by getDayStartEndGMT method,
                                //just skip searching on this field and continue.  This may occur if user switches locale date formats
                                //in another browser screen, but re-runs a search with the previous format on another screen
                                $GLOBALS['log']->error($timeException->getMessage());
                                continue;
                            }
                        }
    
                        if($type == 'decimal' || $type == 'float' || $type == 'currency' || (!empty($parms['enable_range_search']) && empty($parms['is_date_field']))) {
                            require_once('modules/Currencies/Currency.php');
    
                            //we need to handle formatting either a single value or 2 values in case the 'between' search option is set
                            //start by splitting the string if the between operator exists
                            $fieldARR = explode('<>', $field_value);
                            //set the first pass through boolean
                            $values = array();
                            foreach($fieldARR as $fv){
                                //reset the field value, it will be rebuild in the foreach loop below
                                $tmpfield_value = unformat_number($fv);
    
                                if ( $type == 'currency' && stripos($field,'_usdollar')!==FALSE ) {
                                    // It's a US Dollar field, we need to do some conversions from the user's local currency
                                    $currency_id = $GLOBALS['current_user']->getPreference('currency');
                                    if ( empty($currency_id) ) {
                                        $currency_id = -99;
                                    }
                                    if ( $currency_id != -99 ) {
                                        $currency = new Currency();
                                        $currency->retrieve($currency_id);
                                        $tmpfield_value = $currency->convertToDollar($tmpfield_value);
                                    }
                                }
                                $values[] = $tmpfield_value;
                            }
    
                            $field_value = join('<>',$values);
    
                            if(!empty($parms['enable_range_search']) && $parms['operator'] == '=' && $type != 'int')
                            {
                                // Databases can't really search for floating point numbers, because they can't be accurately described in binary,
                                // So we have to fuzz out the math a little bit
                                $field_value = array(($field_value - 0.01) , ($field_value + 0.01));
                                $operator = 'between';
                            }
                        }
    
                        if ( preg_match("/favorites_only.*/", $field) ) {
                            if ( $field_value == '1' ) {
                                $field_value = $GLOBALS['current_user']->id;
                            }
                            else {
                                continue 2;
                            }
                        }
    
                        if($db->supports("case_sensitive") && isset($parms['query_type']) && $parms['query_type'] == 'case_insensitive') {
                            $db_field = 'upper(' . $db_field . ")";
                            $field_value = strtoupper($field_value);
                        }
    
                        $itr++;
                        if(!empty($where)) {
                            $where .= " OR ";
                        }
    
                        //Here we make a last attempt to determine the field type if possible
                        if(empty($type) && isset($parms['db_field']) && isset($parms['db_field'][0]) && isset($this->seed->field_defs[$parms['db_field'][0]]['type']))
                        {
                            $type = $this->seed->field_defs[$parms['db_field'][0]]['type'];
                        }
    
                        switch(strtolower($operator)) {
                        	case 'subquery':
                        	    $in = 'IN';
                        	    if ( isset($parms['subquery_in_clause']) ) {
                        	        if ( !is_array($parms['subquery_in_clause']) ) {
                        	            $in = $parms['subquery_in_clause'];
                        	        }
                        	        elseif ( isset($parms['subquery_in_clause'][$field_value]) ) {
                        	            $in = $parms['subquery_in_clause'][$field_value];
                        	        }
                        	    }
                        	    $sq = $parms['subquery'];
                        	    if(is_array($sq)){
                        	        $and_or = ' AND ';
                        	        if (isset($sq['OR'])){
                        	            $and_or = ' OR ';
                        	        }
                        	        $first = true;
                        	        foreach($sq as $q){
                        	            if(empty($q) || strlen($q)<2) continue;
                        	            if(!$first){
                        	                $where .= $and_or;
                        	            }
                        	            $where .= " {$db_field} $in ({$q} ".$this->seed->db->quoted($field_value.'%').") ";
                        	            $first = false;
                        	        }
                        	    }elseif(!empty($parms['query_type']) && $parms['query_type'] == 'format'){
                        	        $stringFormatParams = array(0 => $field_value, 1 => $GLOBALS['current_user']->id);
                        	        $where .= "{$db_field} $in (".string_format($parms['subquery'], $stringFormatParams).")";
                        	    }else{
                        	        //Bug#37087: Re-write our sub-query to it is executed first and contents stored in a derived table to avoid mysql executing the query
                        	        //outside in. Additional details: http://bugs.mysql.com/bug.php?id=9021
                        	        $where .= "{$db_field} $in (select * from ({$parms['subquery']} ".$this->seed->db->quoted($field_value.'%').") {$field}_derived)";
                        	    }
    
                        	    break;
    
                        	case 'like':
                        	    if($type == 'bool' && $field_value == 0) {
                        	        // Bug 43452 - FG - Added parenthesis surrounding the OR (without them the WHERE clause would be broken)
                        	        $where .=  "( " . $db_field . " = '0' OR " . $db_field . " IS NULL )";
                        	    }
                        	    else {
                        	        //check to see if this is coming from unified search or not
                        	        $UnifiedSearch = !empty($parms['force_unifiedsearch']);
                        	        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'UnifiedSearch'){
                        	            $UnifiedSearch = true;
                        	        }
    
                        	        //check to see if this is a universal search OR the field has db_concat_fields set in vardefs, AND the field name is "last_name"
                        	        //BUG 45709: Tasks Advanced Search: Contact Name field does not return matches on full names
                        	        //Frank: Adding Surabhi's fix back which seem to have gone missing in CottonCandy merge
                        	        if(($UnifiedSearch || !empty($this->seed->field_name_map[$field]['db_concat_fields'])) && (strpos($db_field, 'last_name') !== false) || strpos($db_field, 'name_2') !== false){
                        	            //split the string value, and the db field name
                        	            $string = explode(' ', $field_value);
                        	            $column_name =  explode('.', $db_field);
                        	            //when a search is done with a space, we concatenate and search against the full name.
                        	            if(count($string)>1){
                        	                //add where clause against concatenated field
                        	                $first_field = $parms['db_field'][0];
                        	                $second_field = $parms['db_field'][1];
                        	                $first_db_fields = explode('.', $first_field);
                        	                $second_db_fields = explode('.', $second_field);
                        	                if(count($first_db_fields)==2) $first_field = $first_db_fields[1];
                        	                if(count($second_db_fields)==2) $second_field = $second_db_fields[1];
                        	                $where .= $this->seed->db->concat($column_name[0],array($first_field,$second_field)) . " LIKE ".$this->seed->db->quoted($field_value.'%');
                        	                $where .= ' OR ' . $this->seed->db->concat($column_name[0],array($second_field,$first_field)) . " LIKE ".$this->seed->db->quoted($field_value.'%');
                        	            }else{
                        	                //no space was found, add normal where clause
                        	                $where .=  $db_field . " like ".$this->seed->db->quoted(sql_like_string($field_value, $like_char));
                        	            }
    
                        	        }else {
    
                        	            //Check if this is a first_name, last_name search
                        	            if(isset($this->seed->field_name_map) && isset($this->seed->field_name_map[$db_field]))
                        	            {
                        	                $vardefEntry = $this->seed->field_name_map[$db_field];
                        	                if(!empty($vardefEntry['db_concat_fields']) && in_array('first_name', $vardefEntry['db_concat_fields']) && in_array('last_name', $vardefEntry['db_concat_fields']))
                        	                {
                        	                    if(!empty($GLOBALS['app_list_strings']['salutation_dom']) && is_array($GLOBALS['app_list_strings']['salutation_dom']))
                        	                    {
                        	                        foreach($GLOBALS['app_list_strings']['salutation_dom'] as $salutation)
                        	                        {
                        	                            if(!empty($salutation) && strpos($field_value, $salutation) === 0)
                        	                            {
                        	                                $field_value = trim(substr($field_value, strlen($salutation)));
                        	                                break;
                        	                            }
                        	                        }
                        	                    }
                        	                }
                        	            }
    
                        	            //field is not last name or this is not from global unified search, so do normal where clause
                        	            $where .=  $db_field . " like ".$this->seed->db->quoted(sql_like_string($field_value, $like_char));
                        	        }
                        	    }
                        	    break;
                        	case 'not in':
                        	    $where .= $db_field . ' not in ('.$field_value.')';
                        	    break;
                        	case 'in':
                        	    $where .=  $db_field . ' in ('.$field_value.')';
                        	    break;
                        	case '=':
                        	    if($type == 'bool' && $field_value == 0) {
                        	        $where .=  "($db_field = 0 OR $db_field IS NULL)";
                        	    }
                        	    else {
                        	        $where .=  $db_field . " = ".$db->quoteType($type, $field_value);
                        	    }
                        	    break;
                        	    // tyoung bug 15971 - need to add these special cases into the $where query
                        	case 'custom_enum':
                        	    $where .= $field_value;
                        	    break;
                        	case 'between':
                        	    if(!is_array($field_value)) {
                        	        $field_value = explode('<>', $field_value);
                        	    }
                        	    $field_value[0] = $db->quoteType($type, $field_value[0]);
                        	    $field_value[1] = $db->quoteType($type, $field_value[1]);
                        	    $where .= "($db_field >= {$field_value[0]} AND $db_field <= {$field_value[1]})";
                        	    break;
                        	case 'date_not_equal':
                        	    if(!is_array($field_value)) {
                        	        $field_value = explode('<>', $field_value);
                        	    }
                        	    $field_value[0] = $db->quoteType($type, $field_value[0]);
                        	    $field_value[1] = $db->quoteType($type, $field_value[1]);
                        	    $where .= "($db_field IS NULL OR $db_field < {$field_value[0]} OR $db_field > {$field_value[1]})";
                        	    break;
                        	case 'innerjoin':
                        	    $this->seed->listview_inner_join[] = $parms['innerjoin'] . " '" . $parms['value'] . "%')";
                        	    break;
                        	case 'not_equal':
                        	    $field_value = $db->quoteType($type, $field_value);
                        	    $where .= "($db_field IS NULL OR $db_field != $field_value)";
                        	    break;
                        	case 'greater_than':
                        	    $field_value = $db->quoteType($type, $field_value);
                        	    $where .= "$db_field > $field_value";
                        	    break;
                        	case 'greater_than_equals':
                        	    $field_value = $db->quoteType($type, $field_value);
                        	    $where .= "$db_field >= $field_value";
                        	    break;
                        	case 'less_than':
                        	    $field_value = $db->quoteType($type, $field_value);
                        	    $where .= "$db_field < $field_value";
                        	    break;
                        	case 'less_than_equals':
                        	    $field_value = $db->quoteType($type, $field_value);
                        	    $where .= "$db_field <= $field_value";
                        	    break;
                    	    /**
                    	     * @Added By : Ashutosh
                    	     * @Date : 16 Sept 2013
                    	     * @purpose : for an new option after today
                    	     *
                    	     */
                        	case 'after_today':
                        	    $where .= " ($db_field >  '".$timedate->nowDbDate()."')";
                        	    break;
                        	case 'next_7_days':
                        	case 'last_7_days':
                        	case 'last_month':
                        	case 'this_month':
                        	case 'next_month':
                        	case 'last_30_days':
                        	case 'next_30_days':
                        	case 'this_year':
                        	case 'last_year':
                        	case 'next_year':
                        	    if (!empty($field) && !empty($this->seed->field_name_map[$field]['type'])) {
                        	        $where .= $this->parseDateExpression(strtolower($operator), $db_field, $this->seed->field_name_map[$field]['type']);
                        	    } else {
                        	        $where .= $this->parseDateExpression(strtolower($operator), $db_field);
                        	    }
                        	    break;
                        	case 'isnull':
                        	    $where .=  "($db_field IS NULL OR $db_field = '')";
                        	    if ($field_value != '')
                        	        $where .=  ' OR ' . $db_field . " in (".$field_value.')';
                        	    break;
                        }
                    }
                }
                }
    
                if(!empty($where)) {
                    if($itr > 1) {
                        array_push($where_clauses, '( '.$where.' )');
                    }
                    else {
                        array_push($where_clauses, $where);
                    }
                }
            }
        }
    
        return $where_clauses;
    }
    
    
}
        
        
        
