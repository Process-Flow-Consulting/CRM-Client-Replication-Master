<?php

global $db, $app_list_strings, $sugar_config;

if(isset($_REQUEST['key']) && !empty($_REQUEST['key'])){
	//Create Key for Validation
	$key = md5($sugar_config['validation_key']);
	if($key != $_REQUEST['key']){
		sugar_die('Un-Authorised Access');
	}
}else{
	sugar_die('Un-Authorised Access');
}

$uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:'';
$is_admin = isset($_REQUEST['is_admin'])?$_REQUEST['is_admin']:'0';
$result = array();
if($is_admin==1){
	$stGetInstanceClssSQL = "SELECT value FROM config WHERE name ='target_classifications' AND category ='instance'";
	$rsGetInstanceClssSQL = $db->query ( $stGetInstanceClssSQL );
	$arGetInstanceClssSQL = $db->fetchByAssoc ( $rsGetInstanceClssSQL );
	
	if (isset ( $arGetInstanceClssSQL ['value'] ) && trim ( $arGetInstanceClssSQL ['value'] ) != '') {
			
		$arClsficationIds = json_decode ( base64_decode ( $arGetInstanceClssSQL ['value'] ) );		
		$stClasficatoinIds = ' c.id IN ("'.implode('","',$arClsficationIds).'")';
		$sql = "SELECT DISTINCT(c.name) cname FROM oss_classification c WHERE $stClasficatoinIds   AND  c.`deleted`=0";			
		
		$query = $db->query ( $sql );		
		
		while ( $row = $db->fetchByAssoc ( $query ) ) {			
			$result['Target Class'][] = $row ['cname'];		
		}
	}
	
}else{
//Get user filter details
	$sql = "SELECT uf.filter_type, uf.filter_value, c.name class_name, ct.name county,
			TRIM(CONCAT(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,''))) u_name
			FROM oss_user_filters uf
			LEFT JOIN oss_classification c ON c.id = uf.filter_value
			LEFT JOIN oss_county ct ON ct.id = uf.filter_value
			LEFT JOIN users u ON u.id = uf.filter_value			
			WHERE uf.assigned_user_id = '".$uid."' AND uf.filter_type NOT IN('geo_filter_for','joins_and_where')";

/*"UNION SELECT 'team_member' filter_type, '' filter_value, '' class_name, '' county, TRIM(CONCAT(IFNULL(u.first_name, ''),
                ' ',
                IFNULL(u.last_name, ''))) u_name 
			FROM teams 
			LEFT JOIN team_memberships tm ON tm.team_id = teams.id AND tm.deleted = 0
			LEFT JOIN users u ON u.id=tm.user_id AND u.deleted = 0
			WHERE teams.associated_user_id='".$uid."' AND u.id <> '".$uid."'"*/
$query = $db->query($sql);
$row = $db->fetchByAssoc($query);


while($row != null){
	$type = $row['filter_type'];		
	
	if($row['filter_type'] == 'classification'){
		$value = $row['class_name'];
	}elseif($row['filter_type'] == 'state'){
		$value = $app_list_strings['state_dom'][$row['filter_value']];
	}elseif($row['filter_type'] == 'county'){
		$value = $row['county'];
	}elseif($row['filter_type']=='team_member'){
		$value = $row['u_name'];
	}elseif($row['filter_type']=='labor'){
		switch($row['filter_value']){
			case '0':
				$value = 'Union';
				break;
			case '1':
				$value = 'Non Union';
				break;
			case '2':
				$value = 'Prevailing Wage';
				break;
			case '3':
				$value = 'Undefined';
				break;			
		}
	}else{
		$value = $row['filter_value'];
	}
	
	if($type == 'team_member'){
		$type = 'team member';
	}
	$result[$type][] = $value;
	$row = $db->fetchByAssoc($query);

}

}

//print_r($result);

$sugar_smarty = new Sugar_Smarty();
$sugar_smarty->assign('result',$result);
echo $sugar_smarty->fetch('custom/include/get_user_filters.tpl');
?>
