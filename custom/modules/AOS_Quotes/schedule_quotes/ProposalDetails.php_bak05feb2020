<?php
global $sugar_config, $timedate,$db;

require_once 'modules/AOS_Quotes/AOS_Quotes.php';
require_once 'modules/Users/User.php';
require_once $sugar_config['master_config_path']; //'/vol/certificate/master_config.php';
require_once 'custom/include/master_db/mysql.class.php';
require_once 'custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php';


$proposalId = $_REQUEST['proposal_id'];
$cancelQueueId = $_REQUEST['cQueueId'];

$proposal = new AOS_Quotes();
$proposal->disable_row_level_security=true;
$proposal->retrieve($proposalId);

$userId = $proposal->assigned_user_id;
$user = new User();
$user->disable_row_level_security = true;
$user->retrieve($userId);


//get EasyLink API Object
$obEasyLink = new easyLinkMessage( );

//Get DB Object
$cdb = $obEasyLink->__getCentralDB();

//Fetch data from Cancel Queue Module
$cQSql = "SELECT * FROM oss_cancelqueue WHERE id='".$cancelQueueId."' AND deleted = 0";
$cQQuery = $cdb->query($cQSql);
$cQRes = $cdb->fetch_assoc($cQQuery);



//Fetch Email Template
require_once 'modules/EmailTemplates/EmailTemplate.php';
$email_tpl = new EmailTemplate();
$email_tpl->retrieve_by_string_fields(array('name' => 'Proposal cancel error notification'));
$email_tpl_body = $email_tpl->body_html;
$email_tpl_subject = $email_tpl->subject;
	
$email_tpl_subject = str_replace(array('_INSTANCE_'), array($cQRes['instance_folder']),$email_tpl_subject);
	
$arEmailIds = array(PROPOSAL_SCHEDULE_ERROR_NOTIFICATION_EMAIL_ADDRESS, $user->email1);

$GLOBALS['log']->info('!!!  SENDING NOTIFICATIONS TO  ::'.implode('',$arEmailIds));

$GLOBALS['log']->info('!!! SENDING NOTIFICATIONS TO  ::'.$email_tpl_body);
if($cQRes['easy_email_xdn'] != '' && $cQRes['easy_email_mrn'] != ''){
	$searchArr = array('_INSTANCE_','_XDN_','_MRN_','_DELIVERY_DATE_','_ATTEMPTS_','_STATE_');
	$replaceArr = array($cQRes['instance_folder'],$cQRes['easy_email_xdn'],$cQRes['easy_email_mrn'],$cQRes['proposal_delivery_date'],'','');
	$email_tpl_body = str_replace($searchArr, $replaceArr, $email_tpl->body_html);
	$obEasyLink->sendNotificationEmail($arEmailIds,$email_tpl_subject,$email_tpl_body);
}
	
if($cQRes['easy_fax_xdn'] != '' && $cQRes['easy_fax_mrn'] != ''){
	$searchArr = array('_INSTANCE_','_XDN_','_MRN_','_DELIVERY_DATE_','_ATTEMPTS_','_STATE_');
	$replaceArr = array($cQRes['instance_folder'],$cQRes['easy_fax_xdn'],$cQRes['easy_fax_mrn'],$cQRes['proposal_delivery_date'],'','');
	$email_tpl_body = str_replace($searchArr, $replaceArr, $email_tpl->body_html);
	$obEasyLink->sendNotificationEmail($arEmailIds,$email_tpl_subject,$email_tpl_body);
}
