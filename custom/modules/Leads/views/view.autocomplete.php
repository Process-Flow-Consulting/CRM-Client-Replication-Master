<?php
require_once('include/MVC/View/SugarView.php');
require_once('include/MVC/Controller/SugarController.php');

class ViewAutocomplete extends SugarView {

    function ViewAutocomplete() {

        parent::SugarView();
        
    }
    function  preDisplay() {
        parent::preDisplay();
    }
    function display() {          
		
        global $current_user;
        global $db;
       
	/* $sql = "SELECT DISTINCT name FROM oss_classification WHERE ( 
						MATCH(name) AGAINST('".$_REQUEST['classification']."*+' IN BOOLEAN MODE) OR
								MATCH(description) AGAINST('".$_REQUEST['classification']."*+' IN BOOLEAN MODE)
					) AND deleted=0 ORDER BY name"; */
	$sql = "SELECT DISTINCT name FROM oss_classification WHERE ((name like '".$_REQUEST['classification']."%') OR (description like '".$_REQUEST['classification']."%')) AND deleted=0 ORDER BY name";
	$result = $db->query($sql);
	        
	   		$data ='<table>
	   		<thead>
	    		<tr><th>classification</th></tr>
			</thead>
			<tbody>';  
	        
	        while ($row = $db->fetchByAssoc($result)) {
	        	
	        	$data .='<tr><td>'.$row['name'].'</td></tr>';
	        }
	       
	       $data .='</tbody>
	       </table>'; 
	        
	       echo $data;
        }
}
?>