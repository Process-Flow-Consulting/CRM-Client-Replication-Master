<?php
$module_name = 'oss_ProposalTracker';
$listViewDefs [$module_name] = 
array (
  'EMAIL_SUBJECT' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_EMAIL_SUBJECT',
    'width' => '10%',
    'default' => true,
  ),
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'FIRST_VIEWED' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_FIRST_VIEWED',
    'width' => '10%',
    'default' => true,
  ),
  'LAST_VIEWED' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_LAST_VIEWED',
    'width' => '10%',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'HITS' => 
  array (
    'type' => 'int',
    'label' => 'LBL_HITS ',
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
