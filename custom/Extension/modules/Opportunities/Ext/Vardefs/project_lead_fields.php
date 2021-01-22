<?php
$dictionary["Opportunity"]["fields"]["lead_address"] = array (
		'name' => 'lead_address',		
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_ADDRESS',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_source"] = array (
		'name' => 'lead_source',		
		'type' => 'enum',
		'vname' => 'LBL_LEAD_SOURCE',
		'import' => false,
		'options' => 'lead_source_list',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_state"] = array (
		'name' => 'lead_state',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_STATE',
		'import' => false,
		'options' => 'state_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_received"] = array (
		'name' => 'lead_received',
		'type' => 'date',
		'vname' => 'LBL_LEAD_RECEIVED',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_county"] = array (
		'name' => 'lead_county',	
		'type' => 'enum',
		'vname' => 'LBL_LEAD_COUNTY',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_structure"] = array (
		'name' => 'lead_structure',	
		'type' => 'enum',
		'vname' => 'LBL_LEAD_STRUCTURE',
		'import' => false,
		//'options' => 'all_structure_dom',
		//'function' => 'getBluebookStructureDom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_city"] = array (
		'name' => 'lead_city',	
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_CITY',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_type"] = array (
		'name' => 'lead_type',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_TYPE',
		'import' => false,
		'options' => 'project_type_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_zip_code"] = array (
		'name' => 'lead_zip_code',	
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_ZIP_CODE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_owner"] = array (
		'name' => 'lead_owner',		
		'type' => 'enum',
		'vname' => 'LBL_LEAD_OWNER',
		'import' => false,
		'options' => 'owner_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_project_status"] = array (
		'name' => 'lead_project_status',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_PROJECT_STATUS',
		'import' => false,
		'options' => 'project_status_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_union_c"] = array (
		'name' => 'lead_union_c',
		'type' => 'bool',
		'vname' => 'LBL_LEAD_UNION_C',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_non_union"] = array (
		'name' => 'lead_non_union',	
		'type' => 'bool',
		'vname' => 'LBL_LEAD_NON_UNION',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_prevailing_wage"] = array (
		'name' => 'lead_prevailing_wage',
		'type' => 'bool',
		'vname' => 'LBL_LEAD_PREVAILING_WAGE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_start_date"] = array (
		'name' => 'lead_start_date',
		'type' => 'date',
		'vname' => 'LBL_LEAD_START_DATE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
        'enable_range_search' => true,                
        'options' => 'date_range_search_dom',
);

$dictionary["Opportunity"]["fields"]["lead_square_footage"] = array (
		'name' => 'lead_square_footage',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_SQUARE_FOOTAGE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_end_date"] = array (
		'name' => 'lead_end_date',
		'type' => 'date',
		'vname' => 'LBL_LEAD_END_DATE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
        'enable_range_search' => true,
        'options' => 'date_range_search_dom',
);

$dictionary["Opportunity"]["fields"]["lead_contact_no"] = array (
		'name' => 'lead_contact_no',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_CONTACT_NO',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_stories_below_grade"] = array (
		'name' => 'lead_stories_below_grade',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_STORIES_BELOW_GRADE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_valuation"] = array (
		'name' => 'lead_valuation',
		'type' => 'decimal',
		'vname' => 'LBL_LEAD_VALUATION',
		'import' => false,
		'len' => 10,
		'precision' => 2,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_scope_of_work"] = array (
		'name' => 'lead_scope_of_work',
		'type' => 'text',
		'vname' => 'LBL_LEAD_SCOPE_OF_WORK',
		'import' => false,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_stories_above_grade"] = array (
		'name' => 'lead_stories_above_grade',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_STORIES_ABOVE_GRADE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_number_of_buildings"] = array (
		'name' => 'lead_number_of_buildings',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_NUMBER_OF_BUILDINGS',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);
