<?php
require_once 'include/MVC/View/SugarView.php';

class OpportunitiesViewParentoppautofill extends SugarView{
	
	function OpportunitiesViewParentoppautofill(){
		parent::SugarView();
	}
	
	function display()
	{
		
		global $db, $locale, $current_user;
		
		require_once 'custom/modules/Opportunities/OpportunityPopupSummary.php';
		$focus = new OpportunityPopupSummary();
		
		$table = $focus->getTableName();
		if (!empty($table)) {
			$table_prefix = $db->getValidDBName($table).".";
		} else {
			$table_prefix = '';
		}
		
		$order_by = !empty($_REQUEST['order']) ? $_REQUEST['order'] : '';
		$limit   = !empty($_REQUEST['limit']) ? intval($_REQUEST['limit']) : '';
		$like = !empty($_REQUEST['q']) ? $_REQUEST['q'] : '';
		$data    = array();

        if ($focus->ACLAccess('ListView', true)) {
        	
        	$where = $table_prefix . $db->getValidDBName('name') . sprintf(" like '%s'", $like.'%');

        	$query = $focus->create_new_list_query($order_by, $where,array(),array(), 0,'',false,null,false);
        	$result = $focus->process_list_query($query, 0, $limit, -1, $where);
        	
        	$results = $result['list'];
        	
        	$data['totalCount'] = count($data);
        	$data['fields']     = array();
        	
        	for ($i = 0; $i < count($results); $i++) {
        		$data['fields'][$i] = array();
        		$data['fields'][$i]['module'] = $results[$i]->object_name;
        		$data['fields'][$i]['name'] = $results[$i]->name;
        		$data['fields'][$i]['id'] = $results[$i]->id;
        		$data['fields'][$i]['date_closed'] = $results[$i]->date_closed;
        		$data['fields'][$i]['bid_due_timezone'] = $results[$i]->bid_due_timezone;
        		$data['fields'][$i]['amount'] = $results[$i]->amount;
        		$data['fields'][$i]['sales_stage'] = $results[$i]->sales_stage;
        		$data['fields'][$i]['lead_source'] = $results[$i]->lead_source;
        		$data['fields'][$i]['date_closed_tz'] = $results[$i]->date_closed_tz;
        		$data['fields'][$i]['project_lead_id'] = $results[$i]->project_lead_id;
        	}
        }
        
       // echo '<pre>'; print_r($data); echo '</pre>';
       // echo json_encode($data);
        echo json_encode($data);
        
	}
}
