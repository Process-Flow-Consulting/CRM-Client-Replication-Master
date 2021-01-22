<?php
$module_name = 'oss_ProposalTracker';
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
          0 => 'name',
          1 => 
          array (
            'name' => 'email_subject',
            'label' => 'LBL_EMAIL_SUBJECT',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'first_viewed',
            'label' => 'LBL_FIRST_VIEWED',
          ),
          1 => 
          array (
            'name' => 'last_viewed',
            'label' => 'LBL_LAST_VIEWED',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'hits',
            'label' => 'LBL_HITS ',
          ),
          1 => '',
        ),
      ),
    ),
  ),
);
;
?>
