<?php

$projectOppId = (isset($_REQUEST['projectOppId']) && trim($_REQUEST['projectOppId']) !='') ? $_REQUEST['projectOppId'] : '';

$process_path = '../upload/process/';
$lock_file = $process_path.'opp_import_process_lock';
$status_file = $process_path.'opp_import_import_status';
if ($projectOppId != '') {
    $lock_file = $process_path.'opp_import_process_lock_'.$projectOppId;
    $status_file = $process_path.'opp_import_import_status_'.$projectOppId;
}

if (file_exists($lock_file)) {
    $text = file_get_contents($status_file);
    
    // Retrieve Updated and Imported Count
    $iu_arr = explode("|", $text);
    $import_total = $iu_arr[0];
    $update_total = $iu_arr[1];
    
    $content = file_get_contents($lock_file);
    $content = explode("_", $content);
    $pid = $content[0];
    $start_time = $content[1];
    $current_time = time();
    if (posix_kill($pid, 0)) {
        if ($import_total == '0' && $update_total == '0') {
            if (($current_time - $start_time) > 1800) {
                // Kill the Process.
                posix_kill($pid, 9);
                // Start New Process.
                echo 'finished_' . $text;
            } else {
                echo 'running_' . $text;
            }
        }elseif($import_total > 0 || $update_total > 0){
            if (($current_time - $start_time) > 1800) {
                // Kill the Process.
                posix_kill($pid, 9);
                // Start New Process.
                echo 'finished_' . $text;
            } else {
                echo 'running_' . $text;
            }            
        }else{
            echo 'running_' . $text;
        }
    } else {
        echo 'finished_' . $text;
    }
}
?>
