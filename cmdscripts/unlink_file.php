<?php
$projectOppId = (isset($_REQUEST['projectOppId']) && trim($_REQUEST['projectOppId']) !='') ? $_REQUEST['projectOppId'] : '';

$process_path = '../upload/process/';
$lock_file = $process_path.'opp_import_process_lock';
$status_file = $process_path.'opp_import_import_status';
if ($projectOppId != '') {
    $lock_file = $process_path.'opp_import_process_lock_'.$projectOppId;
    $status_file = $process_path.'opp_import_import_status_'.$projectOppId;
    unlink($lock_file);
    unlink($status_file);
}
echo "success";
?>