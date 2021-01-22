<?php 
 //WARNING: The contents of this file are auto-generated


       $dictionary['User']['fields'] ['reports_to_name']['massupdate'] = false;
       $dictionary['User']['fields'] ['is_admin']['massupdate'] = false;
       $dictionary['User']['fields'] ['company_name'] = array(	'name' => 'company_name',
													       		'label' => 'LBL_COMPANY',
													       		'type' => 'varchar',
													       		'len' => '250'
													       		
													       		);
																
		$dictionary['User']['fields']['team_set_id'] = array(
		'name' => 'team_set_id',
		'vname' => 'LBL_TEAM_SET_ID',
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
        $dictionary['User']['fields']['picture'] = array(	'name' => 'picture',
													       		'label' => 'LBL_PICTURE_FILE',
													       		'type' => 'image',
													       		'dbType' => 'varchar',
													       		'len' => '250'
													       		);
        $dictionary['User']['fields']['default_team'] = array('name' => 'default_team',
								'vname' => 'LBL_DEFAULT_TEAM',
								'reportable' => false,
								'type' => 'varchar',
								'len' => '36',
								'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
							);														
      // $dictionary['User']['fields']['email1']['function']=array('name' => 'getEmailAddressWidgetCustom','returns' => 'html');
        


 // created: 2020-04-30 09:01:20
$dictionary['User']['fields']['default_team']['inline_edit']=true;
$dictionary['User']['fields']['default_team']['merge_filter']='disabled';
$dictionary['User']['fields']['default_team']['reportable']=true;

 

 // created: 2020-04-30 09:00:49
$dictionary['User']['fields']['team_set_id']['inline_edit']=true;
$dictionary['User']['fields']['team_set_id']['merge_filter']='disabled';

 
?>