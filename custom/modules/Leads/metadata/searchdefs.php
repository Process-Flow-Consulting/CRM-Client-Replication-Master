<?php
$searchdefs ['Leads'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'title' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_TITLE',
        'width' => '10%',
        'default' => true,
        'name' => 'title',
      ),
	  'classification' => 
      array (
        'name' => 'classification',
        'label' => 'LBL_PROJECT_CLASSIFICATION',
        'type' => 'varchar',
        'default' => true,
        'width' => '10%',
      ),
      'current_user_only' => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
      'favorites_only' => 
      array (
        'name' => 'favorites_only',
        'label' => 'LBL_FAVORITES_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
      'open_only' => 
      array (
        'name' => 'open_only',
        'label' => 'LBL_OPEN_ITEMS',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
      'include_archive_open_only' => 
      array (
        'type' => 'bool',
        'default' => true,
        'label' => 'LBL_INCLUDE_ARCHIVE',
        'width' => '10%',
        'name' => 'include_archive_open_only',
      ),
    ),
    'advanced_search' => 
    array (
      'project_title' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_PROJECT_TITLE',
        'width' => '10%',
        'default' => true,
        'name' => 'project_title',
      ),
      'address' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_ADDRESS',
        'width' => '10%',
        'default' => true,
        'name' => 'address',
      ),
      'city' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_CITY',
        'width' => '10%',
        'default' => true,
        'name' => 'city',
      ),
      'valuation' => 
      array (
        'type' => 'decimal',
        'label' => 'LBL_VALUATION',
        'width' => '10%',
        'default' => true,
        'name' => 'valuation',
      ),
	  'classification' => 
      array (
        'name' => 'classification',
        'label' => 'LBL_PROJECT_CLASSIFICATION',
        'type' => 'classification',
        'default' => true,
        'width' => '10%',
      ),
      'scope_of_work' => 
      array (
        'type' => 'text',
        'label' => 'LBL_SCOPE_KEYWORD',
        'sortable' => false,
        'width' => '10%',
        'default' => true,
        'name' => 'scope_of_work',
      ),
      'state' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_STATE',
        'width' => '10%',
        'default' => true,
        'name' => 'state',
      ),
      'primary_address_country' => 
      array (
        'name' => 'primary_address_country',
        'label' => 'LBL_COUNTRY',
        'type' => 'name',
        'options' => 'countries_dom',
        'default' => true,
        'width' => '10%',
      ),
      'bids_due' => 
      array (
        'type' => 'datetimecombo',
        'studio' => 
        array (
          'required' => true,
          'no_duplicate' => true,
        ),
        'label' => 'LBL_BIDS_DUE',
        'width' => '10%',
        'default' => true,
        'name' => 'bids_due',
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => true,
        'width' => '10%',
      ),
      'assigned_user_id' => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
        'width' => '10%',
      ),
      'type' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_TYPE',
        'width' => '10%',
        'default' => true,
        'name' => 'type',
      ),
      'structure' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_STRUCTURE',
        'width' => '10%',
        'default' => true,
        'name' => 'structure',
      ),
      'lead_source' => 
      array (
        'name' => 'lead_source',
        'default' => true,
        'width' => '10%',
      ),
      'account_name' => 
      array (
        'name' => 'account_name',
        'default' => true,
        'width' => '10%',
      ),
	  'fav_bidders_only' => 
		array(
		  'name' => 'fav_bidders_only',
		  'label' => 'LBL_FAVORITE_BIDDERS',
		  'default' => true,
		  'width' => '10%',
		  'type' => 'bool'
		),
      'date_modified' => 
      array (
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '10%',
        'default' => true,
        'name' => 'date_modified',
      ),
	  'client_classification' =>
		array (
			'name' => 'client_classification',
			'label' => 'LBL_CLIENT_CLASSIFICATION',
			'type' => 'enum',
			'default' => true,
			'width' => '10%',
			'function' =>
			array (
				'name' => 'get_classification_array',
				'params' =>
				array (
					0 => false,
				),
			),
			'displayParams' => 
			array(
				'width' => '300px !important',
			),
			
		),
	'bidders_role' =>
		array(
			'name' => 'bidders_role',
			'label' => 'LBL_BIDDERS_ROLE',
			'type' => 'enum',
			'default' => true,
			'width' => '10%',
			'displayParams' =>
			array(
				'width' => '200px !important',
			),
		),
      'square_footage' => 
      array (
        'type' => 'int',
        'label' => 'LBL_SQUARE_FOOTAGE',
        'width' => '10%',
        'default' => true,
        'name' => 'square_footage',
      ),
      'project_status' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_PROJECT_STATUS',
        'width' => '10%',
        'default' => true,
        'name' => 'project_status',
      ),
	  'labor_affiliation' =>
		array (
				'name' => 'labor_affiliation',
				'label' => 'LBL_LABOR_TYPE',
				'type' => 'bool',
				'default' => true,
				'width' => '10%',
				'displayParams' =>
				array(
						'width' => '200px !important',
				),
		),
      'favorites_only' => 
      array (
        'label' => 'LBL_FAVORITES_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
        'name' => 'favorites_only',
      ),
      'current_user_only' => 
      array (
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
        'name' => 'current_user_only',
      ),
      'include_archive_open_only' => 
      array (
        'type' => 'bool',
        'default' => true,
        'label' => 'LBL_INCLUDE_ARCHIVE',
        'width' => '10%',
        'name' => 'include_archive_open_only',
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'maxColumnsBasic' => '4',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
;
appendFieldsOnViews($editView=array(),$detailView=array(),$searchdefs['Leads'],$listViewDefs=array(),$searchFields=array(),'Leads','SearchDefs');
?>
