<?php
$module_name = 'AOS_Contracts';
$viewdefs [$module_name] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => false,
      'syncDetailEditViews' => false,
      'tabDefs' => 
      array (
        'DEFAULT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 'name',
          1 => 
          array (
            'name' => 'status',
            'studio' => 'visible',
            'label' => 'LBL_STATUS',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'reference_code',
            'label' => 'LBL_REFERENCE_CODE ',
          ),
          1 => 
          array (
            'name' => 'start_date',
            'label' => 'LBL_START_DATE',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'contract_account',
            'label' => 'LBL_CONTRACT_ACCOUNT',
          ),
          1 => 
          array (
            'name' => 'end_date',
            'label' => 'LBL_END_DATE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'opportunity',
            'label' => 'LBL_OPPORTUNITY',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'type',
            'studio' => 'true',
            'comment' => 'The dropdown options for Contract types',
            'label' => 'LBL_TYPE',
          ),
          1 => 
          array (
            'name' => 'customer_signed_date',
            'label' => 'LBL_CUSTOMER_SIGNED_DATE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'currency_id',
            'studio' => 'visible',
            'label' => 'LBL_CURRENCY',
          ),
          1 => 
          array (
            'name' => 'company_signed_date',
            'label' => 'LBL_COMPANY_SIGNED_DATE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'total_contract_value',
            'label' => 'LBL_TOTAL_CONTRACT_VALUE',
          ),
          1 => 
          array (
            'name' => 'expiration_notice',
            'studio' => 'true',
            'comment' => 'Date to issue an expiration notice (useful for workflow rules)',
            'label' => 'LBL_EXPIRATION_NOTICE',
          ),
        ),
        7 => 
        array (
          0 => 'description',
        ),
      ),
    ),
  ),
);
;
?>
