<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['project_lead_lookup']['fields']['project_lead_id'] = array (
   'required' => false,
    'name' => 'project_lead_id',
   'vname' => 'LBL_LEAD_ID',
   'type' => 'varchar',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'false',
   'merge_filter' => 'enabled',  
   'calculated' => false,
   'len' => '40',
   'size' => '20',
);


$dictionary['project_lead_lookup']['indices'][] =     array('name' =>'idx_project_lead_id', 'type'=>'index', 'fields'=>array('project_lead_id'));

?>