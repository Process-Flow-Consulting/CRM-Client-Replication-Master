<?php
define("sugarEntry", true);
require_once('include/entryPoint.php');
global $db;
function getData($url) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 3000000000 );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 3000000000 );
	$output = curl_exec ( $ch );
	curl_close ( $ch );
	return $output;
}
$existingSql = "SELECT A.id,A.mi_contact_id FROM contacts A JOIN email_addr_bean_rel B ON A.id=B.bean_id JOIN email_addresses C ON C.id=B.email_address_id WHERE (A.mi_contact_id != '' AND A.mi_contact_id IS NOT NULL) AND A.deleted = 0 AND B.bean_module='Contacts' AND C.email_address='bbdev@604dev.com' AND B.deleted=0 AND C.deleted=0";
$existingResult = $db->query ( $existingSql );
while($existingRow = $db->fetchByAssoc ( $existingResult )){
	if(!empty($existingRow)){
		$url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetcontact_json.p?contact_bb_id=".$existingRow['mi_contact_id'];				
		$data_json = getData ( $url );
		$data_array = json_decode ( $data_json, true );
		foreach ( $data_array ['response'] ['Contact'] as $contact ) {
			$mi_contact_id = $contact ['contact_bb_id'];
			isset ( $contact ['email1'] ) ? $client_email = $contact ['email1'] : $client_email = '';
			if (! empty ( $mi_contact_id ) && !empty($client_email)) {
				$select = "SELECT B.id FROM email_addr_bean_rel A JOIN email_addresses B ON A.email_address_id=B.id WHERE A.bean_module='Contacts' AND A.bean_id='".$existingRow['id']."' AND A.deleted=0 AND B.email_address='bbdev@604dev.com'";
				$res = $db->query($select);
				$row = $db->fetchByAssoc($res);
				if(!empty($row)){
					$update = "UPDATE email_addresses SET email_address='".$client_email."',email_address_caps='".strtoupper($client_email)."' WHERE id='".$row['id']."' and deleted=0";
					$db->query($update);
				}
			}
		}
	}
}


		
	
		
	
echo "Done";
?>