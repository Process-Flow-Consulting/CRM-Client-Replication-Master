<?php
// created: 2019-11-07 14:57:42
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_contacts"] = array (
  'name' => 'oss_businessintelligence_contacts',
  'type' => 'link',
  'relationship' => 'oss_businessintelligence_contacts',
  'source' => 'non-db',
  'module' => 'Contacts',
  'bean_name' => 'Contact',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_CONTACTS_FROM_CONTACTS_TITLE',
  'id_name' => 'oss_businessintelligence_contactscontacts_ida',
);
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_contacts_name"] = array (
  'name' => 'oss_businessintelligence_contacts_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_CONTACTS_FROM_CONTACTS_TITLE',
  'save' => true,
  'id_name' => 'oss_businessintelligence_contactscontacts_ida',
  'link' => 'oss_businessintelligence_contacts',
  'table' => 'contacts',
  'module' => 'Contacts',
  'rname' => 'name',
  'db_concat_fields' => 
  array (
    0 => 'first_name',
    1 => 'last_name',
  ),
);
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_contactscontacts_ida"] = array (
  'name' => 'oss_businessintelligence_contactscontacts_ida',
  'type' => 'link',
  'relationship' => 'oss_businessintelligence_contacts',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_CONTACTS_FROM_OSS_BUSINESSINTELLIGENCE_TITLE',
);
