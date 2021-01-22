<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/master-subscription-agreement
* By installing or using this file, You have unconditionally agreed to the
* terms and conditions of the License, and You may not use this file except in
* compliance with the License.  Under the terms of the license, You shall not,
* among other things: 1) sublicense, resell, rent, lease, redistribute, assign
* or otherwise transfer Your rights to the Software, and 2) use the Software
* for timesharing or service bureau purposes such as hosting the Software for
* commercial gain and/or for the benefit of a third party.  Use of the Software
* may be subject to applicable fees and any use of the Software without first
* paying applicable fees is strictly prohibited.  You do not have the right to
* remove SugarCRM copyrights from the source code or user interface.
*
* All copies of the Covered Code must include on each user interface screen:
*  (i) the "Powered by SugarCRM" logo and
*  (ii) the SugarCRM copyright notice
* in the same form as they appear in the distribution.  See full license for
* requirements.
*
* Your Warranty, Limitations of liability and Indemnity are expressly stated
* in the License.  Please refer to the License for the specific language
* governing these rights and limitations under the License.  Portions created
* by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

/*********************************************************************************

* Description:  TODO: To be written.
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
* All Rights Reserved.
* Contributor(s): ______________________________________..
********************************************************************************/
/**
 * Class for Opportunity Assigned user action
 * @author Ashutosh
 *
 */

class ViewGetoppassigned extends ViewEdit{
    /**
     * Constructor for the view
     */
    function ViewGetoppassigned(){
        
        //load parent constructor
    	parent::ViewEdit();
    }
    /**
     * Render method
     * @see ViewEdit::display()
     */
    function display(){        
        global $hook_array;            
                
        //load relationship for linked opportunities
        $obRelated = $this->bean->load_relationship('opportunity_to_opportunity_var');
        //Get All the linked opportunities
        $arClientOpportunityIds =   $this->bean->opportunity_to_opportunity_var->get();
        
        //Opportunity Object for getting detail of client opportunities
        $obClientOpportunity = new Opportunity();
        
        
        //array for all opportunity assigned Users
        $arAssignedUserId[] = $this->bean->assigned_user_id;
        //get all assigned users without considering the role or assingnment
        $obClientOpportunity->disable_row_level_security = true;
        
        //flag for all assignedUserids are same
        $bIsAllUserIdSame = (count($arClientOpportunityIds) > 0)?true:false;
        
        //retrive all assinged users
        foreach($arClientOpportunityIds as $stClientOppId ){
            $obClientOpportunity->retrieve($stClientOppId);
            $arAssignedUserId[] = $obClientOpportunity->assigned_user_id;
            
            if($this->bean->assigned_user_id  != $obClientOpportunity->assigned_user_id){
                $bIsAllUserIdSame = false;
            }
        }
        //return json
        echo json_encode(array('status'=> $bIsAllUserIdSame,'assignedIds'=>$arAssignedUserId));
    }
}
