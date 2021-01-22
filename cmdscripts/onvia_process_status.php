<?php
$process_path = '../upload/process/';  
$lock_file = $process_path.'onvia_process_lock';
$error_file = $process_path.'onvia_process_error';
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
