<?php
if (! defined ( 'sugarEntry' ))define ( 'sugarEntry', true );

//ini_set('display_errors',1);
require_once ('include/entryPoint.php');
require_once ('include/upload_file.php');
require_once ('custom/include/common_functions.php');
require_once ('custom/modules/Leads/views/dodge_Handler.php');
require_once ('custom/modules/Leads/import_xml/ImportDodge.class.php');

global $current_user;
$current_user->retrieve ( $_SERVER ['argv'] [3] );
set_time_limit ( 0 );

if (! empty ( $_SERVER ['argv'] [1] )) {
	$uploadFileName = $_SERVER ['argv'] [1];
}

$file = $uploadFileName;

$_REQUEST ['xml_source'] = "dodge";
$_REQUEST ['import_module'] = 'Leads';
$file_stream = "";

// Validate Dodge XML file.
$xmlHandler = new Dodge_Handler();
$source_parser = xml_parser_create ();
xml_parser_set_option ( $source_parser, XML_OPTION_CASE_FOLDING, 0 );
xml_set_object ( $source_parser, $xmlHandler );
xml_set_element_handler ( $source_parser, "start_element", "end_element" );
xml_set_character_data_handler ( $source_parser, "characters" );


$status_filename = $_SERVER ['argv'] [2];
$importObj = new ImportFile ( $status_filename );

$process_path = 'upload/process/';
$error_file = $process_path . $current_user->id . "_dodge_process_error";
$file_pointer = fopen ( $error_file, "w" );
$file_stream = fopen ( $file, "r" );

if (!empty($file_stream)) {
	
	while ( $data = fread ( $file_stream, 4096 ) ) {
		
		$this_chunk_parsed = xml_parse ( $source_parser, $data, feof ( $file_stream ) );
		if (! $this_chunk_parsed) {
			$error_code = xml_get_error_code ( $source_parser );
			$error_text = xml_error_string ( $error_code );
			$error_line = xml_get_current_line_number ( $source_parser );
			$output_text = "Parsing problem at line $error_line: $error_text";
			fwrite ( $file_pointer, $output_text );
			fclose ( $file_pointer );		
			die ( $output_text );
		}
	}
} else {
	$output_text = "Can't open XML file.";
	fwrite ( $file_pointer, $output_text );
	fclose ( $file_pointer );
	sugar_die ( $output_text );
}

if (! $xmlHandler->isValidXML) {
	$output_text = 'Invalid XML for source ' . $_REQUEST ['xml_source'] . '.';
	fwrite ( $file_pointer, $output_text );
	fclose ( $file_pointer );
	sugar_die ( $output_text );
}
if (isset ( $file_pointer )) {
	fclose ( $file_pointer );
}

// Call ImportDodge to import dodge xml.
$importXMLObj = new ImportDodge($xmlHandler->parseVals,$status_filename);
$importXMLObj->insertData();

?>