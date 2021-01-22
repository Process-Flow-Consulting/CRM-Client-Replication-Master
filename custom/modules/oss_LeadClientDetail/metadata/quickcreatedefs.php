<?php
$module_name = 'oss_LeadClientDetail';
$viewdefs [$module_name] = 
array (
  'QuickCreate' => 
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
          0 => 
          array (
            'name' => 'lead_name',
            'studio' => 'visible',
            'label' => 'LEAD_NAME',
          ),
          1 => 
          array (
            'name' => 'account_name',
            'studio' => 'visible',
            'label' => 'ACCOUNT_NAME',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'contact_name',
            'studio' => 'visible',
            'label' => 'CONTACT_NAME',
          ),
          1 => 
          array (
            'name' => 'contact_phone_no',
            'label' => 'LBL_CONTACT_PHONE_NO',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'contact_fax',
            'label' => 'LBL_CONTACT_FAX',
          ),
          1 => 
          array (
            'name' => 'contact_email',
            'label' => 'LBL_CONTACT_EMAIL',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'role',
            'studio' => 'visible',
            'label' => 'LBL_ROLE',
          ),
          1 => 
          array (
            'name' => 'bid_status',
            'studio' => 'visible',
            'label' => 'LBL_BID_STATUS',
          ),
        ),
      ),
    ),
  ),
);
;
?>
