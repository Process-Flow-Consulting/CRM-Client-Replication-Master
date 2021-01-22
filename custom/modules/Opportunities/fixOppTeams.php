<?php
//Create a TeamSet bean - no BeanFactory
require_once('modules/Teams/TeamSet.php');
require_once 'custom/modules/Opportunities/OpportunityHooks.php';

global $db;

Echo '<h1> For Clinet Opportunities</h1>';

//set teams for Client Opportunities
$stGetClientOpp = 'SELECT id from opportunities where parent_opportunity_id is not null and deleted=0';
$rsResult = $db->query($stGetClientOpp);

$bean = new Opportunity();
$teamSetBean = new TeamSet();

$bean->disable_row_level_security = true;

while($arRow = $db->fetchByAssoc($rsResult )){

//Retrieve the bean
$bean->retrieve($arRow['id']);

//Retrieve the teams from the team_set_id
$teams = $teamSetBean->getTeams($bean->team_set_id);
$arAssignedTeamsIds = array_keys($teams);

echo '<br/><b>Opportunity ID </b>'.$arRow['id'].'<br/>';
pr($arAssignedTeamsIds);

//get assigned User details and private team
/*$obAssignedUser = BeanFactory::getBean('Users',$focus->assigned_user_id );
$stAssignedUsersPrivateTeam =  $obAssignedUser->getPrivateTeam();*/

$bean->load_relationship('teams');
if(count($arAssignedTeamsIds) == 1 && $arAssignedTeamsIds[0] == '1'){
    echo '<br/>replaceing cop team ';
    $obAssignedUser = BeanFactory::getBean('Users',$bean->assigned_user_id );
    $stAssignedUsersPrivateTeam =  $obAssignedUser->getPrivateTeam();
    $bean->team_id = $stAssignedUsersPrivateTeam;
    $bean->team_set_id = $stAssignedUsersPrivateTeam;
   // $bean->teams->remove(array(1));
    //Replace the teams
    //$bean->teams->replace( array($stAssignedUsersPrivateTeam ) );
   
    
}else{
    echo '<br/>removing cop team';
    // Remove the Global team
    $bean->teams->remove(array(1));
}
 $bean->save();
}
Echo '<h1> For Project Opportunities</h1>';
//remove global teams from project opportunities 
$stGetProjOpp = 'SELECT id from opportunities where parent_opportunity_id is null and deleted=0';
$rsProjOpp = $db->query($stGetProjOpp);
while($arRow = $db->fetchByAssoc($rsProjOpp )){
    //Retrieve the bean
    $bean->retrieve($arRow['id']);
    //Retrieve the teams from the team_set_id
    $obOppHook = new OpportunityHooks();
    $obOppHook->UpdateTeams($bean);
   /*  $teams = $teamSetBean->getTeams($bean->team_set_id);
    $arAssignedTeamsIds = array_keys($teams);
    $bean->load_relationship('teams');
    echo '<b>Opportunity ID </b>'.$arRow['id'].'<br/>';
    pr($arAssignedTeamsIds);
    if(count($arAssignedTeamsIds) == 1 && $arAssignedTeamsIds[0] == '1'){
        echo '<br/>replaceing cop team ';
        $obAssignedUser = BeanFactory::getBean('Users',$bean->assigned_user_id );
        $stAssignedUsersPrivateTeam =  $obAssignedUser->getPrivateTeam();
        $bean->team_id = $stAssignedUsersPrivateTeam;
        $bean->team_set_id = $stAssignedUsersPrivateTeam;
       // $bean->teams->remove(array(1));
        //Replace the teams
        //$bean->teams->replace( array($stAssignedUsersPrivateTeam ) );
        
        
    }else{
        echo '<br/>removing cop team ';
        // Remove the Global team
        $bean->teams->remove(array(1));
    }
    $bean->save(); */
}



function pr($a){
	
    echo '<pre>';
    print_r($a);
    echo '</pre>';
}


