<?php

require_once 'include/SearchForm/SearchForm2.php';

class cusSearchForm extends SearchForm {

    var $arSearchWildCards = array('+', '-', '~', '(', ')', '*', '"', '"');

    function SearchForm($seed, $moduleName) {

        parent::SearchForm($seed, $moduleName);
    }
   
    function generateSearchWhere($add_custom_fields = false, $module='') {
        global $timedate;

        $this->searchColumns = array();
        $values = $this->searchFields;

        $where_clauses = array();
        $like_char = '%';
        $table_name = $this->seed->object_name;
        $this->seed->fill_in_additional_detail_fields();

        //rrs check for team_id
        if (!empty($this->searchFields['team_id']) && !empty($this->searchFields['team_id']['value'])) {
            if (!empty($this->searchFields['team_name'])) {
                unset($this->searchFields['team_name']);
            }
        }

        foreach ($this->searchFields as $field => $parms) {
            $customField = false;
            // Jenny - Bug 7462: We need a type check here to avoid database errors
            // when searching for numeric fields. This is a temporary fix until we have
            // a generic search form validation mechanism.
            $type = (!empty($this->seed->field_name_map[$field]['type'])) ? $this->seed->field_name_map[$field]['type'] : '';

            if (!empty($parms['enable_range_search']) && empty($type)) {
                if (preg_match('/^start_range_(.*?)$/', $field, $match)) {
                    $real_field = $match[1];
                    $start_field = 'start_range_' . $real_field;
                    $end_field = 'end_range_' . $real_field;

                    if (isset($this->searchFields[$start_field]['value']) && isset($this->searchFields[$end_field]['value'])) {
                        $this->searchFields[$real_field]['value'] = $this->searchFields[$start_field]['value'] . '<>' . $this->searchFields[$end_field]['value'];
                        $this->searchFields[$real_field]['operator'] = 'between';
                        $parms['value'] = $this->searchFields[$real_field]['value'];
                        $parms['operator'] = 'between';
                        $field = $real_field;
                        unset($this->searchFields[$end_field]['value']);
                    }
                } else if (preg_match('/^range_(.*?)$/', $field, $match) && isset($this->searchFields[$field]['value'])) {
                    $real_field = $match[1];

                    //Special case for datetime and datetimecombo fields.  By setting the type here we allow an actual between search
                    if ($parms['operator'] == '=') {
                        $field_type = isset($this->seed->field_name_map[$real_field]['type']) ? $this->seed->field_name_map[$real_field]['type'] : '';
                        if ($field_type == 'datetimecombo' || $field_type == 'datetime') {
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

            if (!empty($this->seed->field_name_map[$field]['source'])
                    && ($this->seed->field_name_map[$field]['source'] == 'custom_fields' ||
                    //Non-db custom fields, such as custom relates
                    ($this->seed->field_name_map[$field]['source'] == 'non-db'
                    && (!empty($this->seed->field_name_map[$field]['custom_module']) ||
                    isset($this->seed->field_name_map[$field]['ext2']))))) {
                $customField = true;
            }

            if ($type == 'int') {
                if (!empty($parms['value'])) {
                    $tempVal = explode(',', $parms['value']);
                    $newVal = '';
                    foreach ($tempVal as $key => $val) {
                        if (!empty($newVal))
                            $newVal .= ',';
                        if (!empty($val) && !(is_numeric($val)))
                            $newVal .= - 1;
                        else
                            $newVal .= $val;
                    }
                    $parms['value'] = $newVal;
                }
            }

            //Navjeet- 6/24/08 checkboxes have been changed to dropdowns, so we can query unchecked checkboxes! Bug: 21648.
            // elseif($type == 'bool' && empty($parms['value']) && preg_match("/current_user_only/", string subject, array subpatterns, int flags, [int offset])) {
            //     continue;
            // }
            //
            elseif ($type == 'html' && $customField) {
                continue;
            }
            ####################################################
            ### CUSTOMIZATION : to remove the wildcards     ####
            ###     remove all the fulltext search wildcards####
            ####################################################
            $stFullTextKeyWord = $parms['value'];
            //$parms['value'] = str_replace($this->arSearchWildCards, '', $parms['value']);


            if (isset($parms['value']) && $parms['value'] != "") {

                $operator = 'like';
                if (!empty($parms['operator'])) {
                    $operator = $parms['operator'];
                }

                if (is_array($parms['value'])) {
                    $field_value = '';

                    // always construct the where clause for multiselects using the 'like' form to handle combinations of multiple $vals and multiple $parms
                    if (/* $GLOBALS['db']->dbType != 'mysql' && */!empty($this->seed->field_name_map[$field]['isMultiSelect']) && $this->seed->field_name_map[$field]['isMultiSelect']) {
                        // construct the query for multenums
                        // use the 'like' query for all mssql and oracle examples as both custom and OOB multienums are implemented with types that cannot be used with an 'in'
                        $operator = 'custom_enum';
                        $table_name = $this->seed->table_name;
                        if ($customField)
                            $table_name .= "_cstm";
                        $db_field = $table_name . "." . $field;

                        foreach ($parms['value'] as $key => $val) {
                            if ($val != ' ' and $val != '') {
                                $qVal = $GLOBALS['db']->quote($val);
                                if (!empty($field_value)) {
                                    $field_value .= ' or ';
                                }
                                $field_value .= "$db_field like '%^$qVal^%'";
                            } else {
                                $field_value .= '(' . $db_field . ' IS NULL or ' . $db_field . "='^^' or " . $db_field . "='')";
                            }
                        }
                    } else {
                        $operator = $operator != 'subquery' ? 'in' : $operator;
                        foreach ($parms['value'] as $key => $val) {
                            if ($val != ' ' and $val != '') {
                                if (!empty($field_value)) {
                                    $field_value .= ',';
                                }
                                $field_value .= "'" . $GLOBALS['db']->quote($val) . "'";
                            }
                            // Bug 41209: adding a new operator "isnull" here
                            // to handle the case when blank is selected from dropdown.
                            // In that case, $val is empty.
                            // When $val is empty, we need to use "IS NULL",
                            // as "in (null)" won't work
                            else if ($operator == 'in') {
                                $operator = 'isnull';
                            }
                        }
                    }
                } else {
                    $field_value = $GLOBALS['db']->quote($parms['value']);
                }

                //set db_fields array.
                if (!isset($parms['db_field'])) {
                    $parms['db_field'] = array($field);
                }

                //This if-else block handles the shortcut checkbox selections for "My Items" and "Closed Only"
                if (!empty($parms['my_items'])) {
                    if ($parms['value'] == false) {
                        continue;
                    } else {
                        //my items is checked.
                        global $current_user;
                        $field_value = $GLOBALS['db']->quote($current_user->id);
                        $operator = '=';
                    }
                } else if (!empty($parms['closed_values']) && is_array($parms['closed_values'])) {
                    if ($parms['value'] == false) {
                        continue;
                    } else {
                        $field_value = '';
                        foreach ($parms['closed_values'] as $closed_value) {
                            $field_value .= ",'" . $GLOBALS['db']->quote($closed_value) . "'";
                        }
                        $field_value = substr($field_value, 1);
                    }
                }

                $where = '';
                $itr = 0;

                if ($field_value != '' || $operator == 'isnull') {

                    $this->searchColumns [strtoupper($field)] = $field;
//Changes made by parveen badoni on 03/07/2014 to check if argument supplied in foreach is valid or not. 
			if(isset($parms['db_field'])) {
                    foreach ($parms['db_field'] as $db_field) {
                        if (strstr($db_field, '.') === false) {
                            //Try to get the table for relate fields from link defs
                            if ($type == 'relate' && !empty($this->seed->field_name_map[$field]['link'])
                                    && !empty($this->seed->field_name_map[$field]['rname'])) {
                                $link = $this->seed->field_name_map[$field]['link'];
                                $relname = $link['relationship'];
                                if (($this->seed->load_relationship($link))) {
                                    //Martin fix #27494
                                    $db_field = $this->seed->field_name_map[$field]['name'];
                                } else {
                                    //Best Guess for table name
                                    $db_field = strtolower($link['module']) . '.' . $db_field;
                                }
                            } else if ($type == 'parent') {
                                if (!empty($this->searchFields['parent_type'])) {
                                    $parentType = $this->searchFields['parent_type'];
                                    $rel_module = $parentType['value'];
                                    global $beanFiles, $beanList;
                                    if (!empty($beanFiles[$beanList[$rel_module]])) {
                                        require_once($beanFiles[$beanList[$rel_module]]);
                                        $rel_seed = new $beanList[$rel_module]();
                                        $db_field = 'parent_' . $rel_module . '_' . $rel_seed->table_name . '.name';
                                    }
                                }
                            }
                            // Relate fields in custom modules and custom relate fields
                            else if ($type == 'relate' && $customField && !empty($this->seed->field_name_map[$field]['module'])) {
                                $db_field = !empty($this->seed->field_name_map[$field]['name']) ? $this->seed->field_name_map[$field]['name'] : 'name';
                            } else if (!$customField) {
                                if (!empty($this->seed->field_name_map[$field]['db_concat_fields']))
                                    $db_field = db_concat($this->seed->table_name, $this->seed->field_name_map[$db_field]['db_concat_fields']);
                                else
                                    $db_field = $this->seed->table_name . "." . $db_field;
                            }else {
                                if (!empty($this->seed->field_name_map[$field]['db_concat_fields']))
                                    $db_field = db_concat($this->seed->table_name . "_cstm.", $this->seed->field_name_map[$db_field]['db_concat_fields']);
                                else
                                    $db_field = $this->seed->table_name . "_cstm." . $db_field;
                            }
                        }

                        if ($type == 'date') {
                            // Collin - Have mysql as first because it's usually the case
                            // The regular expression check is to circumvent special case YYYY-MM
                            if ($GLOBALS['db']->dbType == 'mysql') {
                                if (preg_match('/^\d{4}.\d{1,2}$/', $field_value) == 0) {
                                    $field_value = $timedate->to_db_date($field_value, false);
                                    $operator = '=';
                                } else {
                                    $operator = 'db_date';
                                }
                            } else if ($GLOBALS['db']->dbType == 'mssql') {
                                if (preg_match('/^\d{4}.\d{1,2}$/', $field_value) == 0) {
                                    $field_value = "Convert(DateTime, '" . $timedate->to_db_date($field_value, false) . "')";
                                }
                                $operator = 'db_date';
                            } else {
                                $field_value = $timedate->to_db_date($field_value, false);
                                $operator = '=';
                            }
                        }

                        if ($type == 'datetime' || $type == 'datetimecombo') {
                            try {
                                $dates = $timedate->getDayStartEndGMT($field_value);
                                $field_value = $dates["start"] . "<>" . $dates["end"];
                                $operator = 'between';
                            } catch (Exception $timeException) {
                                //In the event that a date value is given that cannot be correctly processed by getDayStartEndGMT method,
                                //just skip searching on this field and continue.  This may occur if user switches locale date formats
                                //in another browser screen, but re-runs a search with the previous format on another screen
                                $GLOBALS['log']->error($timeException->getMessage());
                                continue;
                            }
                        }

                        if ($type == 'decimal' || $type == 'float' || $type == 'currency' || (!empty($parms['enable_range_search']) && empty($parms['is_date_field']))) {
                            require_once('modules/Currencies/Currency.php');

                            //we need to handle formatting either a single value or 2 values in case the 'between' search option is set
                            //start by splitting the string if the between operator exists
                            $fieldARR = explode('<>', $field_value);
                            //set the first pass through boolean
                            $first_between = true;

                            foreach ($fieldARR as $fk => $fv) {
                                //reset the field value, it will be rebuild in the foreach loop below
                                $tmpfield_value = unformat_number($fv);

                                if ($type == 'currency' && stripos($field, '_usdollar') !== FALSE) {
                                    // It's a US Dollar field, we need to do some conversions from the user's local currency
                                    $currency_id = $GLOBALS['current_user']->getPreference('currency');
                                    if (empty($currency_id)) {
                                        $currency_id = -99;
                                    }
                                    if ($currency_id != -99) {
                                        $currency = new Currency();
                                        $currency->retrieve($currency_id);
                                        $field_value = $currency->convertToDollar($tmpfield_value);
                                    }
                                }

                                //recreate the field value
                                if ($first_between) {
                                    //set the field value with the new formatted temp value
                                    $field_value = $tmpfield_value;
                                } else {
                                    //this is a between query, so append the between operator and add the second formatted temp value
                                    $field_value .= '<>' . $tmpfield_value;
                                }
                                //set the first pass through variable to false
                                $first_between = false;
                            }

                            if (!empty($parms['enable_range_search']) && $parms['operator'] == '=') {
                                // Databases can't really search for floating point numbers, because they can't be accurately described in binary,
                                // So we have to fuzz out the math a little bit
                                $field_value = ($field_value - 0.01) . "<>" . ($field_value + 0.01);
                                $operator = 'between';
                            }
                        }

                        if (preg_match("/favorites_only.*/", $field)) {
                            if ($field_value == '1') {
                                $field_value = $GLOBALS['current_user']->id;
                            } else {
                                continue 2;
                            }
                        }


                        $itr++;
                        if (!empty($where)) {
                            $where .= " OR ";
                        }
                        
                        switch (strtolower($operator)) {
                            case 'subquery':
                                $in = 'IN';
                                if (isset($parms['subquery_in_clause'])) {
                                    if (!is_array($parms['subquery_in_clause'])) {
                                        $in = $parms['subquery_in_clause'];
                                    } elseif (isset($parms['subquery_in_clause'][$field_value])) {
                                        $in = $parms['subquery_in_clause'][$field_value];
                                    }
                                }
                                $sq = $parms['subquery'];
                                if (is_array($sq)) {
                                    $and_or = ' AND ';
                                    if (isset($sq['OR'])) {
                                        $and_or = ' OR ';
                                    }
                                    $first = true;
                                    foreach ($sq as $q) {
                                        if (empty($q) || strlen($q) < 2)
                                            continue;
                                        if (!$first) {
                                            $where .= $and_or;
                                        }
                                        $where .= " {$db_field} $in ({$q} '{$field_value}%') ";
                                        $first = false;
                                    }
                                } elseif (!empty($parms['query_type']) && $parms['query_type'] == 'format') {
                                    $stringFormatParams = array(0 => $field_value, 1 => $GLOBALS['current_user']->id);
                                    $where .= "{$db_field} $in (" . string_format($parms['subquery'], $stringFormatParams) . ")";
                                } else {
                                    ###################################################
                                    ## CUSTOMIZATION : to add fulltext keywords     ###
                                    ##                                              ###
                                    ###################################################
                                   /* if($field == 'email'){
                                        
                                      //  $parms['subquery'] = str_replace( 'ea.email_address LIKE',' MATCH(ea.email_address)  ',$parms['subquery']);

                                        //$where .= "{$db_field} $in ({$parms['subquery']} AGAINST  ('{$field_value}' IN BOOLEAN MODE) )";

                                    }else
                                    {*/
                                        $where .= "{$db_field} $in ({$parms['subquery']} '{$field_value}%')";
                                  //  }
                                    ###################################################
                                    ## CUSTOMIZATION : to add fulltext keywords     ###
                                    ##                                              ###
                                    ###################################################
                                        
                                    
                                }

                                break;

                            case 'like':
                                if ($type == 'bool' && $field_value == 0) {
                                    $where .= $db_field . " = '0' OR " . $db_field . " IS NULL";
                                } else {
                                    //check to see if this is coming from unified search or not
                                    $UnifiedSearch = !empty($parms['force_unifiedsearch']);
                                    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'UnifiedSearch') {
                                        $UnifiedSearch = true;
                                    }

                                    ###################################################
                                    ## CUSTOMIZATION : to add fulltext keywords     ###
                                    ##                                              ###
                                    ###################################################
                                    /* if ($UnifiedSearch && strpos($db_field, 'name') !== false) {
                                        $arFullTextSearchField[] = $db_field;
                                        //$where .= " MATCH(" . $db_field . " ) AGAINST ('" . $stFullTextKeyWord . "' IN BOOLEAN MODE)"; //  '" . $field_value . $like_char . "'";
                                    } else */
                                    ###################################################
                                    ## END CUSTOMIZATION : to add fulltext keywords ###
                                    ##                                              ### 
                                    ###################################################
                                    //check to see if this is a universal search, AND the field name is "last_name"
                                    if ($UnifiedSearch && strpos($db_field, 'last_name') !== false) {
                                        //split the string value, and the db field name
                                        $string = explode(' ', $field_value);
                                        $column_name = explode('.', $db_field);
                                        //when a search is done with a space, we concatenate and search against the full name.
                                        if (count($string) > 1) {
                                            //add where clause against concatenated fields
                                           // $where .= $GLOBALS['db']->concat($column_name[0], array('first_name', 'last_name')) . " LIKE '{$field_value}%'";
                                           // $where .= ' OR ' . $GLOBALS['db']->concat($column_name[0], array('last_name', 'first_name')) . " LIKE '{$field_value}%'";
                                        } else {
                                            //no space was found, add normal where clause
                                            $where .= $db_field . " like '" . $field_value . $like_char . "'";
                                        }
                                    } else {

                                        //Check if this is a first_name, last_name search
                                        if (isset($this->seed->field_name_map) && isset($this->seed->field_name_map[$db_field])) {
                                            $vardefEntry = $this->seed->field_name_map[$db_field];
                                            if (!empty($vardefEntry['db_concat_fields']) && in_array('first_name', $vardefEntry['db_concat_fields']) && in_array('last_name', $vardefEntry['db_concat_fields'])) {
                                                if (!empty($GLOBALS['app_list_strings']['salutation_dom']) && is_array($GLOBALS['app_list_strings']['salutation_dom'])) {
                                                    foreach ($GLOBALS['app_list_strings']['salutation_dom'] as $salutation) {
                                                        if (!empty($salutation) && strpos($field_value, $salutation) == 0) {
                                                            $field_value = trim(substr($field_value, strlen($salutation)));
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //field is not last name or this is not from global unified search, so do normal where clause
                                        $where .= $db_field . " like '" . $like_char .  $field_value . $like_char . "'";
                                    }
                                }
                                break;
                            case 'not in':
                                $where .= $db_field . ' not in (' . $field_value . ')';
                                break;
                            case 'in':
                                $where .= $db_field . ' in (' . $field_value . ')';
                                break;
                            case '=':
                                if ($type == 'bool' && $field_value == 0) {
                                    $where .= $db_field . " = '0' OR " . $db_field . " IS NULL";
                                } else {
                                    $where .= $db_field . " = '" . $field_value . "'";
                                }
                                break;
                            case 'db_date':
                                if (preg_match('/^\d{4}.\d{1,2}$/', $field_value) == 0) {
                                    $where .= $db_field . " = " . $field_value;
                                } else {
                                    // Create correct date_format conversion String
                                    if ($GLOBALS['db']->dbType == 'oci8') {
                                        $where .= db_convert($db_field, 'date_format', array("'YYYY-MM'")) . " = '" . $field_value . "'";
                                    } else {
                                        $where .= db_convert($db_field, 'date_format', array("'%Y-%m'")) . " = '" . $field_value . "'";
                                    }
                                }
                                break;
                            // tyoung bug 15971 - need to add these special cases into the $where query
                            case 'custom_enum':
                                $where .= $field_value;
                                break;
                            case 'between':
                                $field_value = explode('<>', $field_value);
                                $where .= $db_field . " >= '" . $field_value[0] . "' AND " . $db_field . " <= '" . $field_value[1] . "'";
                                break;
                            case 'innerjoin':
                                $this->seed->listview_inner_join[] = $parms['innerjoin'] . " '" . $parms['value'] . "%')";
                                break;
                            case 'not_equal':
                                $where .= $db_field . " != '" . $field_value . "'";
                                break;
                            case 'greater_than':
                                $where .= $db_field . " > '" . $field_value . "'";
                                break;
                            case 'greater_than_equals':
                                $where .= $db_field . " >= '" . $field_value . "'";
                                break;
                            case 'less_than':
                                $where .= $db_field . " < '" . $field_value . "'";
                                break;
                            case 'less_than_equals':
                                $where .= $db_field . " <= '" . $field_value . "'";
                                break;
                            case 'last_7_days':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",10) BETWEEN LEFT((current_date - interval '7' day),10) AND LEFT(current_date,10)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEDIFF ( d ,  " . $db_field . " , GETDATE() ) <= 7 and DATEDIFF ( d ,  " . $db_field . " , GETDATE() ) >= 0";
                                }
                                break;
                            case 'next_7_days':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",10)  BETWEEN LEFT(current_date,10) AND LEFT((current_date + interval '7' day),10)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEDIFF ( d , GETDATE() ,  " . $db_field . " ) <= 7 and DATEDIFF ( d , GETDATE() ,  " . $db_field . " ) >= 0";
                                }
                                break;
                            case 'next_month':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",7) = LEFT( (current_date  + interval '1' month),7)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "(LEFT( " . $db_field . ",4) = LEFT( (DATEADD(mm,1,GETDATE())),4)) and (DATEPART(yy, DATEADD(mm,1,GETDATE())) = DATEPART(yy, DATEADD(mm,1," . $db_field . ")))";
                                }
                                break;
                            case 'last_month':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",7) = LEFT( (current_date  - interval '1' month),7)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "LEFT(" . $db_field . ",4) = LEFT((DATEADD(mm,-1,GETDATE())),4) and DATEPART(yy," . $db_field . ") = DATEPART(yy, GETDATE())";
                                }
                                break;
                            case 'this_month':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",7) = LEFT( current_date,7)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "LEFT (" . $db_field . ",4) = LEFT( GETDATE(),4) and DATEPART(yy," . $db_field . ") = DATEPART(yy, GETDATE())";
                                }
                                break;
                            case 'last_30_days':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",10) BETWEEN LEFT((current_date - interval '30' day),10) AND LEFT(current_date,10)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEDIFF ( d ,  " . $db_field . " , GETDATE() ) <= 30 and DATEDIFF ( d ,  " . $db_field . " , GETDATE() ) >= 0";
                                }
                                break;
                            case 'next_30_days':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= $db_field . " BETWEEN (current_date) AND (current_date + interval '1' month)";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEDIFF ( d , GETDATE() ,  " . $db_field . " ) <= 30 and DATEDIFF ( d , GETDATE() ,  " . $db_field . " ) >= 0";
                                }
                                break;
                            case 'this_year':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",4) = EXTRACT(YEAR FROM ( current_date ))";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEPART(yy," . $db_field . ") = DATEPART(yy, GETDATE())";
                                }
                                break;
                            case 'last_year':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",4) = EXTRACT(YEAR FROM ( current_date  - interval '1' year))";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEPART(yy," . $db_field . ") = DATEPART(yy,( dateadd(yy,-1,GETDATE())))";
                                }
                                break;
                            case 'next_year':
                                if ($GLOBALS['db']->dbType == 'mysql') {
                                    $where .= "LEFT(" . $db_field . ",4) = EXTRACT(YEAR FROM ( current_date  + interval '1' year))";
                                } elseif ($GLOBALS['db']->dbType == 'mssql') {
                                    $where .= "DATEPART(yy," . $db_field . ") = DATEPART(yy,( dateadd(yy, 1,GETDATE())))";
                                }
                                break;
                            case 'isnull':
                                // OOTB fields are NULL, custom fields are blank
                                $where .= '(' . $db_field . ' IS NULL or ' . $db_field . "='')";
                                if ($field_value != '')
                                    $where .= ' OR ' . $db_field . " in (" . $field_value . ')';
                                break;
                        }
                    }
				}
                }



                if (!empty($where)) {
                    if ($itr > 1) {
                        array_push($where_clauses, '( ' . $where . ' )');
                    } else {
                        array_push($where_clauses, $where);
                    }
                }
            }
        }
        
        if($this->seed instanceof Lead){
            $arFullTextSearchField[] = 'leads.project_title';            
        }
        
        //Modified by Mohit Kumar Gupta 06th June 2017
        //add global search on project/client opportunity name as well with refference BSI-897
        if($this->seed instanceof Opportunity){
            $arFullTextSearchField[] = 'opportunities.name';            
        }
        
        //Modified by Mohit Kumar Gupta 06th June 2017
        //add contacts first name and last name in the search
        if($this->seed instanceof Contact){
            $arFullTextSearchField[] = 'contacts.first_name';      
            $arFullTextSearchField[] = 'contacts.last_name';
        }
        
        //rewrite conditions to match
        //$stFullTextCriteria = " MATCH(" . implode(",",$arFullTextSearchField). " ) AGAINST ('" . $stFullTextKeyWord . "' IN BOOLEAN MODE)"; //  '" . $field_value . $like_char . "'";
        
        $stFullTextCriteria = '';
//Changes made by parveen badoni on 03/07/2014 to check if argument supplied in foreach is valid or not. 
        if(is_array($arFullTextSearchField)) {
        foreach($arFullTextSearchField as $fullTextSearchField){
        	
        	if(!empty($stFullTextCriteria)){
        		$stFullTextCriteria .= " OR ";
        	}
        	
        	//$stFullTextCriteria .=  " ( MATCH(". $fullTextSearchField. " ) AGAINST ('" . $stFullTextKeyWord . "' IN BOOLEAN MODE) ) "; //  '" . $field_value . $like_char . "'";
        	$stFullTextCriteria .=  " ( ". $fullTextSearchField. " LIKE '%" . $stFullTextKeyWord . "%' ) "; //  '" . $field_value . $like_char . "'";
        }
    }
        
        /**
         * Project Lead Search based on client
         */
        if($this->seed instanceof Lead){
        	/*$stFullTextCriteria .= " OR leads.id IN (SELECT DISTINCT(lcd.lead_id) FROM
						oss_leadclientdetail lcd
						INNER JOIN accounts on accounts.id = lcd.account_id and accounts.deleted=0
						WHERE lcd.deleted=0
						AND MATCH (accounts.name) AGAINST ('" . $stFullTextKeyWord . "' IN BOOLEAN MODE))";
						*/
            $stFullTextCriteria .= " OR leads.id IN (SELECT DISTINCT(lcd.lead_id) FROM
						oss_leadclientdetail lcd
						INNER JOIN accounts on accounts.id = lcd.account_id and accounts.deleted=0
						WHERE lcd.deleted=0
						AND accounts.name LIKE '%" . $stFullTextKeyWord . "%')";
        }
        
        if(trim($stFullTextCriteria) != '')
        array_push($where_clauses,$stFullTextCriteria);
        
       
        
        return $where_clauses;
    }

    
    
    
}


?>
