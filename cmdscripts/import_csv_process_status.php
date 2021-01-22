<?php
if (! defined ( 'sugarEntry' ))define ( 'sugarEntry', true );

chdir('../');
require_once ('include/entryPoint.php');

$current_user_id = $_REQUEST['current_user'] ;


$process_path = 'upload/process/';
$lock_file = $process_path. $current_user_id . '_import_csv_process_lock';
$error_file = $process_path. $current_user_id .'_import_csv_process_error';

if(file_exists($lock_file)){
	$pid = file_get_contents($lock_file);
	$error = file_get_contents($error_file);
	if(posix_kill($pid,0)){
		if(!empty($error)){
			echo 'error_'.$error;
		}else{
			echo 'running';
		}		
	}else{
		if(!empty($error)){
			echo 'error_'.$error;
		}else{
			echo 'finished';
		}
	}
}
?>
