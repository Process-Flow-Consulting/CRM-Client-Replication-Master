<?php
$listViewDefs['Opportunities'] = array (
        'NAME' => array (
                'width' => '20%',
                'label' => 'LBL_LIST_OPPORTUNITY_NAME',
                'link' => true,
                'default' => true 
        ),
        'DATE_CLOSED_TZ' => array (
                'type' => 'char',
                'label' => 'LBL_DATE_DATE',
                'width' => '10%',
                'default' => true 
        ),
        'AMOUNT_USDOLLAR' => array (
                'width' => '10%',
                'label' => 'LBL_LIST_AMOUNT_USDOLLAR',
                'align' => 'center',
                'default' => true,
                'currency_format' => true 
        ),
        'SALES_STAGE' => array (
                'width' => '10%',
                'label' => 'LBL_LIST_SALES_STAGE',
                'default' => true 
        ),
        'CLIENTS' => array (
                'type' => 'int',
                'label' => 'LBL_OPP_CLIENTS',
                'width' => '8%',
                'align' => 'center',
                'default' => true 
        ),
        'DATE_MODIFIED' => array (
                'type' => 'datetime',
                'label' => 'LBL_DATE_MODIFIED_LV',
                'width' => '10%',
                'default' => true 
        ),
        'PROJECT_ONLINE_PLAN' => array (
                'width' => '8%',
                'label' => 'LBL_ONLINE_PLAN',
                'default' => true,
                'sortable' => false,
                'related_fields' => array (
                        0 => 'project_lead_id',
                        1 => 'project_online_plan' 
                ) 
        ),
        'NEXT_ACTION_DATE' => array (
                'type' => 'datetimecombo',
                'label' => 'LBL_NEXT_ACTION_DATE',
                'width' => '15%',
                'default' => false 
        ),
        'BID_DUE_TIMEZONE' => array (
                'type' => 'enum',
                'label' => 'LBL_BID_DUE_TIMEZONE',
                'sortable' => true,
                'width' => '5%',
                'default' => false 
        ),
        'ACCOUNT_NAME' => array (
                'width' => '20%',
                'label' => 'LBL_LIST_ACCOUNT_NAME',
                'id' => 'ACCOUNT_ID',
                'module' => 'Accounts',
                'link' => false,
                'default' => false,
                'sortable' => false,
                'ACLTag' => 'ACCOUNT',
                'contextMenu' => array (
                        'objectType' => 'sugarAccount',
                        'metaData' => array (
                                'return_module' => 'Contacts',
                                'return_action' => 'ListView',
                                'module' => 'Accounts',
                                'parent_id' => '{$ACCOUNT_ID}',
                                'parent_name' => '{$ACCOUNT_NAME}',
                                'account_id' => '{$ACCOUNT_ID}',
                                'account_name' => '{$ACCOUNT_NAME}' 
                        ) 
                ),
                'related_fields' => array (
                        0 => 'account_id' 
                ) 
        ),
        'LEAD_PROJECT_STATUS' => array (
                'width' => '15%',
                'label' => 'LBL_PROJECT_STATUS',
                'default' => false 
        ),
        'PROBABILITY' => array (
                'width' => '15%',
                'label' => 'LBL_PROBABILITY',
                'default' => false 
        ),
        'IS_ARCHIVED' => array (
                'width' => '5%',
                'label' => 'LBL_ARCHIVE',
                'default' => false 
        ),
        'ASSIGNED_USER_NAME' => array (
                'width' => '15%',
                'label' => 'LBL_ASSIGNED_TO',
                'default' => false 
        ),
        'LEAD_SOURCE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_SOURCE',
                'default' => false 
        ),        
        'LEAD_START_DATE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_START_DATE',
                'default' => false 
        ),
        'LEAD_END_DATE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_END_DATE',
                'default' => false 
        ),
        
        'LEAD_CITY' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_CITY',
                'default' => false 
        ),
        'LEAD_STATE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_STATE',
                'default' => false 
        ),
        'LEAD_COUNTY_NAME' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_COUNTY',
                'default' => false 
        ),
        'LEAD_UNION_C' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_UNION_C',
                'default' => false 
        ),
        'LEAD_NON_UNION' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_NON_UNION',
                'default' => false 
        ),
        'LEAD_PREVAILING_WAGE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_PREVAILING_WAGE',
                'default' => false 
        ),
        'LEAD_SQUARE_FOOTAGE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_SQUARE_FOOTAGE',
                'default' => false 
        ),
        'CUSTOM_FIELD_1' => array (
                'width' => '15%',
                'label' => 'LBL_CUSTOM_FIELD_1',
                'default' => false 
        ),
        'CUSTOM_FIELD_2' => array (
                'width' => '15%',
                'label' => 'LBL_CUSTOM_FIELD_2',
                'default' => false 
        ),
        'LEAD_STRUCTURE' => array (
                'width' => '15%',
                'label' => 'LBL_LEAD_STRUCTURE',
                'default' => false 
        ),
		'LEAD_CONTACT_NO' => array (
				'width' => '15%',
				'label' => 'LBL_LEAD_CONTACT_NO',
				'default' => false
		) 
);
appendFieldsOnViews($editView=array(),$detailView=array(),$searchDefs=array(),$listViewDefs['Opportunities'],$searchFields=array(),'Opportunities','ListDefs');
?>
