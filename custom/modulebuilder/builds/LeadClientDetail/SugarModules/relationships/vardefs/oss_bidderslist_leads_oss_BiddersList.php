<?php
// created: 2019-11-07 14:36:40
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_leads"] = array (
  'name' => 'oss_bidderslist_leads',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_leads',
  'source' => 'non-db',
  'module' => 'Leads',
  'bean_name' => 'Lead',
  'vname' => 'LBL_OSS_BIDDERSLIST_LEADS_FROM_LEADS_TITLE',
  'id_name' => 'oss_bidderslist_leadsleads_ida',
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_leads_name"] = array (
  'name' => 'oss_bidderslist_leads_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_BIDDERSLIST_LEADS_FROM_LEADS_TITLE',
  'save' => true,
  'id_name' => 'oss_bidderslist_leadsleads_ida',
  'link' => 'oss_bidderslist_leads',
  'table' => 'leads',
  'module' => 'Leads',
  'rname' => 'name',
  'db_concat_fields' => 
  array (
    0 => 'first_name',
    1 => 'last_name',
  ),
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_leadsleads_ida"] = array (
  'name' => 'oss_bidderslist_leadsleads_ida',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_leads',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_BIDDERSLIST_LEADS_FROM_OSS_BIDDERSLIST_TITLE',
);
