<?php
// created: 2013-10-15 13:13:30
$dictionary["Opportunity"]["fields"]["oss_zone_opportunities_1"] = array (
  'name' => 'oss_zone_opportunities_1',
  'type' => 'link',
  'relationship' => 'oss_zone_opportunities_1',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OSS_ZONE_TITLE',
);

$dictionary["Opportunity"]["fields"]["zone_name"] = array (
  'name' => 'zone_name',
  'type' => 'enum',
//  'relationship' => 'oss_zone_opportunities_1',
  'function' => array('name'=>'get_all_zones','return'=>'html'),
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OSS_ZONE_TITLE',
);

