<?php
// created: 2019-11-07 14:37:45
$dictionary["oss_OnlinePlans"]["fields"]["oss_onlineplans_leads"] = array (
  'name' => 'oss_onlineplans_leads',
  'type' => 'link',
  'relationship' => 'oss_onlineplans_leads',
  'source' => 'non-db',
  'module' => 'Leads',
  'bean_name' => 'Lead',
  'vname' => 'LBL_OSS_ONLINEPLANS_LEADS_FROM_LEADS_TITLE',
  'id_name' => 'oss_onlineplans_leadsleads_ida',
);
$dictionary["oss_OnlinePlans"]["fields"]["oss_onlineplans_leads_name"] = array (
  'name' => 'oss_onlineplans_leads_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ONLINEPLANS_LEADS_FROM_LEADS_TITLE',
  'save' => true,
  'id_name' => 'oss_onlineplans_leadsleads_ida',
  'link' => 'oss_onlineplans_leads',
  'table' => 'leads',
  'module' => 'Leads',
  'rname' => 'name',
  'db_concat_fields' => 
  array (
    0 => 'first_name',
    1 => 'last_name',
  ),
);
$dictionary["oss_OnlinePlans"]["fields"]["oss_onlineplans_leadsleads_ida"] = array (
  'name' => 'oss_onlineplans_leadsleads_ida',
  'type' => 'link',
  'relationship' => 'oss_onlineplans_leads',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_ONLINEPLANS_LEADS_FROM_OSS_ONLINEPLANS_TITLE',
);
