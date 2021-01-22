<?php
// created: 2019-11-07 14:36:40
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_contacts"] = array (
  'name' => 'oss_bidderslist_contacts',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_contacts',
  'source' => 'non-db',
  'module' => 'Contacts',
  'bean_name' => 'Contact',
  'vname' => 'LBL_OSS_BIDDERSLIST_CONTACTS_FROM_CONTACTS_TITLE',
  'id_name' => 'oss_bidderslist_contactscontacts_ida',
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_contacts_name"] = array (
  'name' => 'oss_bidderslist_contacts_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_BIDDERSLIST_CONTACTS_FROM_CONTACTS_TITLE',
  'save' => true,
  'id_name' => 'oss_bidderslist_contactscontacts_ida',
  'link' => 'oss_bidderslist_contacts',
  'table' => 'contacts',
  'module' => 'Contacts',
  'rname' => 'name',
  'db_concat_fields' => 
  array (
    0 => 'first_name',
    1 => 'last_name',
  ),
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_contactscontacts_ida"] = array (
  'name' => 'oss_bidderslist_contactscontacts_ida',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_contacts',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_BIDDERSLIST_CONTACTS_FROM_OSS_BIDDERSLIST_TITLE',
);
