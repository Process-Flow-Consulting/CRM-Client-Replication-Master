<?php
global $pl_fields, $client_fields, $contact_fields;

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

$client_fields = array(
		'name' => 'name',
		'phone_office' => 'phone_office',
		'phone_fax' => 'phone_fax',
		'website' => 'website',
		'address1' => 'address1',
		'address2' => 'address2',
		'city' => 'billing_address_city',
		'state' => 'billing_address_state',
		'proview_url' => 'proview_url',
		'county_id' => 'county_id',
		'postalcode' => 'billing_address_postalcode',
		'industry' => 'industry',
		'default_message' => 'default_message',
		'delivery_method' => 'delivery_method',
		'year_established' => 'year_established',
		'gas' => 'geographical_areas_serviced',
		'tps' => 'typical_project_size',
		'noe' => 'no_of_employees',
		'prev_proj' => 'previous_projects',
		'cf1' => 'custom_field_1',
		'cf2' => 'custom_field_2',
		'email' => 'email1',
		'description' => 'description',
		'client_bb_id' => 'mi_account_id',
		//'bc' => 'bim_certified',
		//'lc' => 'leed_certified',
		'phone' => 'phone_office',
);




$contact_fields = array(
		'salutation' => 'salutation',
		'first_name' => 'first_name',
		'last_name' => 'last_name',
		'cont_title' => 'title',
		'phone_work' => 'phone_work',
		'phone_mobile' => 'phone_mobile',
		'department' => 'department',
		'fax' => 'phone_fax',
		'p_street' => 'primary_address_street',
		'p_city' => 'primary_address_city',
		'p_state' => 'primary_address_state',
		'p_postalcode' => 'primary_address_postalcode',
		'alt_street' => 'alt_address_street',
		'alt_city' => 'alt_address_city',
		'alt_state' => 'alt_address_state',
		'alt_postalcode' => 'alt_address_postalcode',
		'description' => 'description',
		'contact_bb_id' => 'mi_contact_id',
		
);

?>
