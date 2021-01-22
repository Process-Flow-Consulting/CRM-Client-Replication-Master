<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Document']['fields']['team_set_id'] = array(
    'name' => 'team_set_id',
    'vname' => 'Team Set ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);
$dictionary['Document']['fields']['team_id'] = array(
    'name' => 'team_id',
    'vname' => 'Team ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);

$dictionary["Document"]["fields"]["documents_quotes"] = array (
    'name' => 'documents_quotes',
    'type' => 'link',
    'relationship' => 'documents_quotes',
    'source' => 'non-db',
    'vname' => 'LBL_AOS_QUOTES_SUBPANEL_TITLE'
);


$dictionary["Document"]["fields"]["documents_leads"] = array (
    'name' => 'documents_leads',
    'type' => 'link',
    'relationship' => 'documents_leads',
    'source' => 'non-db',
    'vname' => 'LBL_LEADS_SUBPANEL_TITLE'
);


// created: 2013-10-15 13:13:30
$dictionary["Document"]["fields"]["documents_products"] = array (
  'name' => 'documents_products',
  'type' => 'link',
  'relationship' => 'documents_products',
  'source' => 'non-db',
  'vname' => 'LBL_DOCUMENTS_PRODUCTS',
);



?>