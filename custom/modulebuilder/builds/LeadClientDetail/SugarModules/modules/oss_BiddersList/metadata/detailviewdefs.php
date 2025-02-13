<?php
$module_name = 'oss_BiddersList';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
        ),
      ),
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
            'name' => 'oss_bidderslist_leads_name',
            'label' => 'LBL_OSS_BIDDERSLIST_LEADS_FROM_LEADS_TITLE',
          ),
          1 => 
          array (
            'name' => 'oss_bidderslist_accounts_name',
            'label' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_ACCOUNTS_TITLE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'oss_bidderslist_contacts_name',
            'label' => 'LBL_OSS_BIDDERSLIST_CONTACTS_FROM_CONTACTS_TITLE',
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
            'label' => 'LBL_CONTACT_FAX ',
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
            'label' => 'LBL_BID_STATUS ',
          ),
        ),
      ),
    ),
  ),
);
;
?>
