<?php
$dictionary['Opportunity']['fields']['contact_id'] = array(
		'name' => 'contact_id',
		'vname' => 'LBL_CONTACT_ID',
		'type' => 'char',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => true,
		'reportable' => true,
		'len' => '36',
);

$dictionary['Opportunity']['fields']['contact_name'] = array(
		'required' => true,
		'source' => 'non-db',
		'name' => 'contact_name',
		'vname' => 'LBL_CONTACTS',
		'type' => 'relate',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => false,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
		'id_name' => 'contact_id',
		'ext2' => 'Contacts',
		'module' => 'Contacts',
		'rname' => 'name',
		'quicksearch' => 'enabled',
		'studio' => 'visible',
);

$dictionary["Opportunity"]["fields"]["opportunities_contact"] = array (
		'name' => 'opportunities_contact',
		'type' => 'link',
		'relationship' => 'opportunities_contact',
		'source' => 'non-db',
		'vname' => 'LBL_CONTACTS',
);

$dictionary['Opportunity']['relationships']['opportunities_contact'] = array(
		'lhs_module'=> 'Contacts', 'lhs_table'=> 'contacts', 'lhs_key' => 'id',
		'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'contact_id',
		'relationship_type'=>'one-to-many'
);
