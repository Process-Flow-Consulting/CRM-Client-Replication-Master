<?php
if (! defined ( 'sugarEntry' ))define ( 'sugarEntry', true );

//ini_set('display_errors',1);
require_once ('include/entryPoint.php');
require_once('modules/Import/sources/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/CsvAutoDetect.php');
require_once('include/upload_file.php');
require_once ('custom/include/common_functions.php');
require_once ('custom/modules/Leads/import_csv/ImportCSV.class.php');

global $current_user;
$current_user->retrieve ( $_SERVER ['argv'] [4] );
set_time_limit ( 0 );

if ($_SERVER ['argv'] [1] != 'none') {
	$uploadLeadFileName = $_SERVER ['argv'] [1];
}else{
    $uploadLeadFileName = '';
}

if ($_SERVER ['argv'] [2] != 'none') {
    $uploadBidderFileName = $_SERVER ['argv'] [2];
}else{
    $uploadBidderFileName = '';
}

if (!empty($_SERVER ['argv'] [3])) {
    $importSource = $_SERVER ['argv'] [3];
}else{
    $importSource = '';
}

$GLOBALS['log']->fatal($uploadLeadFileName);
$GLOBALS['log']->fatal($uploadBidderFileName);
$GLOBALS['log']->fatal($importSource);

$process_path = 'upload/process/';
$error_file = $process_path . $current_user->id . "_import_csv_process_error";

if(!empty($uploadLeadFileName)){
    $file = $uploadLeadFileName;
    $file_stream = "";
    $file_pointer = fopen ( $error_file, "w" );
    $file_stream = fopen ( $file, "r" );
    
    if ( empty($file_stream)) {
    	$output_text = "Can't open Lead CSV file.";
    	fwrite ( $file_pointer, $output_text );
    	fclose ( $file_pointer );
    	sugar_die ( $output_text );
    }
}


if(!empty($uploadBidderFileName)){
    $file = $uploadBidderFileName;
    $file_stream = "";
    $file_pointer = fopen ( $error_file, "w" );
    $file_stream = fopen ( $file, "r" );

    if ( empty($file_stream)) {
        $output_text = "Can't open Bidder CSV file.";
        fwrite ( $file_pointer, $output_text );
        fclose ( $file_pointer );
        sugar_die ( $output_text );
    }
}

// Call ImportCSV.
$importCSVObj = new ImportCSV();
$importCSVObj->insertData($uploadLeadFileName, $uploadBidderFileName, $importSource);

?>