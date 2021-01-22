<?php
require_once ('include/MVC/View/SugarView.php');
require_once ('include/MVC/Controller/SugarController.php');

class ViewAutocomplete_account extends SugarView {
	
	function __construct() {
		parent::SugarView ();
	}
	
	function display() {
		
		global $beanList;
		$keyword = $_REQUEST['accounts'];
		$accounts = new $beanList['Accounts'];
		$where = " accounts.name like '".$keyword."%'";
		
		$accountRes = $accounts->get_full_list('',$where);	
		$data = '<table>
	   		<thead>
	    		<tr><th>account</th></tr>
			</thead>
			<tbody>';			
			foreach($accountRes as $account){
				$data .= '<tr><td>'.$account->name.'</td></tr>';
			}	
		$data .= '</tbody>
	       </table>';		
		
		echo $data;
	}
}
?>