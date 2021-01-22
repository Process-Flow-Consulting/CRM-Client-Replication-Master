<?php
$timeStamp = $_REQUEST['currentTime'];
$process_path = '../upload/process/';
$lock_file = $process_path.'opp_process_lock_'.$timeStamp;
$status_file = $process_path.'opp_convert_status_'.$timeStamp;
if (file_exists($lock_file)) {
    $text = file_get_contents($status_file);
    
    // Retrieve Updated and Imported Count
    $iu_arr = explode("|", $text);
    $insertedOpp = $iu_arr[0];
    $parentoppId = $iu_arr[1];
    
    $pid = file_get_contents($lock_file);
    if (posix_kill($pid, 0)) {
        echo 'running_' . $text;
    } else {
        echo 'finished_' . $text;
    }
} else {
    echo 'finished_completed';
}
?>