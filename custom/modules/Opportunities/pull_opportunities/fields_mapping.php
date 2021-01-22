<?php
global $pl_fields, $client_field_array, $contact_field_array, $projectOppFieldArray;

$pl_fields = array(		
	'proj_title' => 'project_title',
	'sag' => 'stories_above_grade',
	'sbg' => 'stories_below_grade',
	'nob' => 'number_of_buildings',
	'sf' => 'square_footage',
	'proj_status' => 'project_status',
	'type' => 'type',
	'owner' => 'owner',
	'address' => 'address',
	'city' => 'city',
	'state' => 'state',
	'zip_code' => 'zip_code',
	'sow' => 'scope_of_work',
	'pbm' => 'pre_bid_meeting',
	'bids_due' => 'bids_due',
	'timezone' => 'bid_due_timezone',
	'asap' => 'asap',
	'lunion' => 'union_c',
	'non_union' => 'non_union',
	'pw' => 'prevailing_wage',		
	'valuation' => 'valuation',		
	'proj_source' => 'lead_source',
	'county' => 'county_id',
	'start_date' => 'start_date',
	'end_date' => 'end_date',
	'structure' => 'structure',
	'contract_no' => 'contact_no',				
);

$client_field_array = array (
	'c_name' => 'name',
	'c_phone' => 'phone_office',
	'c_fax' => 'phone_fax',
	'proview_url' => 'proview_url',
	'c_city' => 'billing_address_city',
	'c_state' => 'billing_address_state',
	'c_zip' => 'billing_address_postalcode',
	'c_county' => 'county_id',
	'client_bb_id' => 'mi_account_id',
);

$contact_field_array = array (
	'cc_fname' => 'first_name',
	'cc_lname' => 'last_name',
	'cc_phone' => 'phone_work',
	'cc_fax' => 'phone_fax',
	'contact_bb_id' => 'mi_contact_id'
);

$projectOppFieldArray = array(
    'id' => 'project_lead_id',
    'lead_source' => 'lead_source',
    'bids_due' => 'date_closed',
    'bid_due_timezone' => 'bid_due_timezone',
    'received' => 'lead_received',
    'address' => 'lead_address',
    'state' => 'lead_state',
    'structure' => 'lead_structure',
    'county_id' => 'lead_county',
    'type' => 'lead_type',
    'city' => 'lead_city',
    'owner' => 'lead_owner',
    'zip_code' => 'lead_zip_code',
    'project_status' => 'lead_project_status',
    'start_date' => 'lead_start_date',
    'end_date' => 'lead_end_date',
    'contact_no' => 'lead_contact_no',
    'valuation' => 'lead_valuation', 
    'union_c' => 'lead_union_c',
    'non_union' => 'lead_non_union',
    'prevailing_wage' => 'lead_prevailing_wage',
    'square_footage' => 'lead_square_footage',
    'stories_below_grade' => 'lead_stories_below_grade',
    'number_of_buildings' => 'lead_number_of_buildings',
    'stories_above_grade' => 'lead_stories_above_grade',
    'scope_of_work' => 'lead_scope_of_work'
);
?>
