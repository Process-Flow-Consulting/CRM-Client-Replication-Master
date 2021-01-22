<?php
require_once 'modules/Administration/Administration.php';
$obAdmin = new Administration ();
$obAdmin->disable_row_level_security = true;
$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
$account_name = $arAdminData->settings['instance_account_name'];

$root_path = "/vol/sites/";
global $sugar_config;

$site_url =  $sugar_config['site_url'];

//echo $url = $site_url."/index.php?entryPoint=ProjectLeadDirectInsert&one_bidder=0";
//$cmd = "/usr/local/zend/bin/php -f ".'/var/www/bluebook/customer/custom/modules/Leads/pull_project_lead/PullLead.php';

$one_bidder = $_REQUEST['one_bidder'];
if(!isset($_REQUEST['one_bidder'])){
	$one_bidder = "0";
}

$cmd = "/usr/local/zend/bin/php -f " .$root_path.$account_name."/pull_lead/ProjectLeadDirectInsert.php ".$one_bidder;


$outputfile = $root_path.$account_name."/custom/modules/Leads/pull_project_lead/result_output.txt";
$pidfile = $root_path.$account_name."/custom/modules/Leads/pull_project_lead/pid_output.txt";

exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile)); 

echo "success";
