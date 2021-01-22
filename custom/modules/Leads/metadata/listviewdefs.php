<?php
$listViewDefs ['Leads'] = 
array (
 'PROJECT_LEAD_ID' =>
 array (
	'width' => '5%',
	'label' => 'LBL_PROJECT_LEAD_ID',
	'default' => false,
  ),		
  'PROJECT_TITLE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_PROJECT_TITLE',
    'width' => '20%',
    'link' => true,
    'default' => true,
  ),		
  'RECEIVED' => 
  array (
    'label' => 'LBL_RECEIVED',
    'width' => '20%',
    'default' => false,
  ),		
  'ADDRESS' => 
  array (
    'label' => 'LBL_ADDRESS',
    'width' => '20%',
    'default' => false,
  ),
  'STATE' => 
  array (
    'type' => 'varchar',
    'label' => 'Location',
    'sortable' => true,
    'width' => '10%',
    'default' => true,
    'related_fields' => 
    array (
      0 => 'state',
      1 => 'city',
    ),
    'customCode' => '{$STATE},{$CITY}',
  ),		
  'STRUCTURE' => 
  array (
    'label' => 'LBL_STRUCTURE',
    'width' => '20%',
    'default' => false,
  ),		
  'COUNTY' => 
  array (
    'label' => 'LBL_COUNTY',
    'width' => '20%',
    'default' => false,
  ),	
  'TYPE' => 
  array (
    'label' => 'LBL_TYPE',
    'width' => '20%',
    'default' => false,
  ),		
  'OWNER' => 
  array (
    'label' => 'LBL_OWNER',
    'width' => '20%',
    'default' => false,
  ),
 'ZIP_CODE' => 
  array (
    'label' => 'LBL_ZIP_CODE',
    'width' => '20%',
    'default' => false,
  ),
 'PRE_BID_MEETING' => 
  array (
    'label' => 'LBL_PRE_BID_MEETING',
    'width' => '20%',
    'default' => false,
  ),
 'START_DATE' => 
  array (
    'label' => 'LBL_START_DATE',
    'width' => '20%',
    'default' => false,
  ),
 'ASAP' => 
  array (
    'label' => 'LBL_ASAP',
    'width' => '20%',
    'default' => false,
  ),
 'END_DATE' => 
  array (
    'label' => 'LBL_END_DATE',
    'width' => '20%',
    'default' => false,
  ),
 'CONTACT_NO' => 
  array (
    'label' => 'LBL_CONTACT_NO',
    'width' => '20%',
    'default' => false,
  ),
 'BID_DUE_TIMEZONE' => 
  array (
    'label' => 'LBL_BID_DUE_TIMEZONE',
    'width' => '20%',
    'default' => false,
  ),
 'VALUATION' => 
  array (
    'label' => 'LBL_VALUATION',
    'width' => '8%',
    'default' => false,
  ),
 'union_c' => 
  array (
    'label' => 'LBL_UNION',
    'width' => '20%',
    'default' => false,
  ),
 'NON_UNION' => 
  array (
    'label' => 'LBL_NON_UNION',
    'width' => '20%',
    'default' => false,
  ),
 'PREVAILING_WAGE' => 
  array (
    'label' => 'LBL_PREVAILING_WAGE',
    'width' => '20%',
    'default' => false,
  ),
 'NUMBER_OF_BUILDINGS' => 
  array (
    'label' => 'LBL_NUMBER_OF_BUILDINGS',
    'width' => '20%',
    'default' => false,
  ),
 'SQUARE_FOOTAGE' => 
  array (
    'label' => 'LBL_SQUARE_FOOTAGE',
    'width' => '20%',
    'default' => false,
  ),
 'SQUARE_FOOTAGE' => 
  array (
    'label' => 'LBL_SQUARE_FOOTAGE',
    'width' => '20%',
    'default' => false,
  ),
 'STORIES_ABOVE_GRADE' => 
  array (
    'label' => 'LBL_STORIES_ABOVE_GRADE',
    'width' => '20%',
    'default' => false,
  ),
 'STORIES_BELOW_GRADE' => 
  array (
    'label' => 'LBL_STORIES_BELOW_GRADE',
    'width' => '20%',
    'default' => false,
  ),	
  'LEAD_VERSION' => 
  array (
    'type' => 'int',
    'label' => 'Lead Version(s)',
    'width' => '5%',
    'default' => true,
    'align' => 'center',
  ),
  'PREV_BID_TO' => 
  array (
    'type' => 'varchar',
    'label' => 'Prev Bid-To',
    'width' => '3%',
    'default' => true,
    'align' => 'center',
  ),
  'NEW_TOTAL' => 
  array (
    'type' => 'varchar',
    'label' => 'New-Total',
    'width' => '8%',
    'default' => true,
    'align' => 'center',
  ),
  'BIDS_DUE_TZ' => 
  array (
    'type' => 'varchar',
    'label' => '1st Bid Due',
    'width' => '8%',
    'default' => true,
  ),
  'LEAD_PLANS' => 
  array (
    'type' => 'varchar',
  	'align'=>'center',
    'label' => 'Online Plans',
    'width' => '6%', 
  	'sortable' => true,
    'default' => true,
  ),
  'DATE_MODIFIED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_MODIFIED',
    'width' => '8%',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'width' => '5%',
  	'align'=>'center',
    'label' => 'LBL_LIST_STATUS',
    'default' => true,
  ),
  'SCOPE_OF_WORK' => 
  array (
    'label' => 'LBL_SCOPE_OF_WORK',
    'width' => '20%',
    'default' => false,
  ),
  'DESCRIPTION' => 
  array (
    'label' => 'LBL_DESCRIPTION',
    'width' => '20%',
    'default' => false,
  ),
  'MODIFIED_BY_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_MODIFIED',
    'default' => false,
  ),
	'CREATED_BY_NAME' =>
	array (
		'width' => '10%',
		'label' => 'LBL_CREATED',
		'default' => false,
	),	
	'DATE_ENTERED' =>
	array (
			'width' => '10%',
			'label' => 'LBL_DATE_ENTERED',
			'default' => false,
	),
	'CITY' =>
	array (
			'width' => '10%',
			'label' => 'LBL_CITY',
			'default' => false,
	),
	'PROJECT_STATUS' =>
	array (
			'width' => '10%',
			'label' => 'LBL_PROJECT_STATUS',
			'default' => false,
	),
 /*'TEAM_NAME' => 
  array (
    'width' => '5%',
    'label' => 'LBL_LIST_TEAM',
    'default' => false,
  ),*/
    'IS_ARCHIVED' =>
    array (
        'width' => '5%',
        'label' => 'LBL_ARCHIVE',
        'default' => false,
    ),
    'ASSIGNED_USER_NAME' =>
    array (
        'width' => '5%',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'id' => 'ASSIGNED_USER_ID',
        'default' => false,
    ),
);
appendFieldsOnViews($editView=array(),$detailView=array(),$searchDefs=array(),$listViewDefs ['Leads'],$searchFields=array(),'Leads','ListDefs');
;
?>
