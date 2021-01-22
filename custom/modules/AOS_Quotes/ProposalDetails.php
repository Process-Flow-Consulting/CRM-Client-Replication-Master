<?php
$proposalId = $_REQUEST['proposal_id'];
$proposal = new Quote();
$proposal->disable_row_level_security=true;
$proposal->retrieve($proposalId);

$userId = $proposal->assigned_user_id;
$user = new User();
$user->disable_row_level_security = true;
$user->retrieve($userId);
echo $user->email1;