<?php
global $pl_fields, $client_fields, $contact_fields,$pl_notes,$client_comment;

$pl_fields = array (
		'ProjectID' => 'project_lead_id',
		'PreBidMeetingDate' => 'pre_bid_meeting',
		'ProjectCity' => 'city',
		'ProjectCounty' => 'county_id',
		'ProjectState' => 'state',
		'ProjectDescription' => 'scope_of_work',
		'ProjectTitle' => 'project_title',
		'ProjectCountyFipsCode' => 'zip_code',	
		'SubmittalDate' => 'bids_due'
);

$pl_notes = array (
        'PublicationDate',
        'AwardNumber',
        'BidNumber',
        'BondingRequirements',
        'CategoryID',
        'CategoryName',
        'ContractAwardAmount',
        'ContractTerm',
        'ExternalDocuments',
        'InternalDocuments',
        'MaximumContractValue',
        'MinimumContractValue',
        'PlanPrice',
		'ProcurementType',
		'SetAsidePercentage',
		'SetAsideRequirements',
        'PreBidMandatory',
        'IsSpecAvailable',
        'InDbProjectID',
        'InFileProjectID'
);

$client_fields = array (		
		'OwnerID' => 'onvia_id',
		'OwnerName' => 'name',
		'OwnerPrimaryFunctionName' => 'industry',
		'OwnerWebsite' => 'proview_url'
);

$client_comment = array (
        'OwnerAnnualExpenditure',
        'OwnerEmployeeCount',
        'OwnerEnrollment',
        'OwnerPopulation',
        'OwnerPrimaryFunctionID',
        'LevelOfGovernment',
        'OwnerLastUpdatedDate'
);

$contact_fields = array(	
		'BuyerBusinessPhone' => 'phone_work',
		'BuyerCity' => 'primary_address_city',		
		'BuyerDepartment' => 'department',
		//'BuyerEmail' => 'email1',
		'BuyerFax' => 'zip_code',
		'BuyerFirstName' => 'first_name',
		'BuyerID' => 'onvia_id',
		'BuyerJobTitle' => 'title',
		'BuyerLastName' => 'last_name',
		'BuyerPostalCode' => 'primary_address_postalcode',
		'BuyerState' => 'primary_address_state',
		'BuyerFax' => 'phone_fax'	
);
