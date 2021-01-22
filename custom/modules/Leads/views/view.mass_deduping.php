<?php
/**
 * Action class to list the Suggested Duplicates for a project lead
 * @author : Ashutosh
 * 
 */
require_once ('include/MVC/View/views/view.list.php');
require_once ('custom/include/common_functions.php');
require_once ('custom/modules/Leads/bbProjectLeads.php');
class LeadsViewMass_deduping extends ViewList
{
    /**
     * Constructer
     */
    function __construct()
    {
        parent::ViewList();
    }
    
    /**
     * display method to render the view
     */
    function display()
    {
        global $timedate, $app_list_strings;
        
        if (isset($_REQUEST['save_link_projects']) && $_REQUEST['save_link_projects'] == '1') {
            $this->linkMassDupeLeads($_REQUEST);
        }
        //
        $arSelectedProjectLeads = $_REQUEST['mass'];
        
        $this->order = (isset($_REQUEST['odr'])) ? $_REQUEST['odr'] : 'ASC';
        $this->orderBy = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : 'project_title';
        
        $arFilters = Array (
                'project_title' => 1,
                'state' => 1,
                'city' => 1,
                'lead_version' => 1,
                'prev_bid_to' => 1,
                'new_total' => 1,
                'bids_due_tz' => 1,
                'lead_plans' => 1,
                'date_modified' => 1,
                'status' => 1,
                'classification' => 1,
                'fav_bidders_only' => 1,
                'pre_bid_meeting' => 1,
                'contact_no' => 1 
        );
        
        $params = array (
                'favorites' => 1 
        );
        
        foreach ($arSelectedProjectLeads as $stProjectLeadId) {
            /*
             * $obLeadToDedupe = new Lead(); $obLeadToDedupe->retrieve(
             * $stProjectLeadId);
             */
            
            $obCurProjectLeads = new bbProjectLeads();
            $obProjectLeads = new Lead();
            $obProjectLeads->retrieve($stProjectLeadId);
            
            $stAction = $_REQUEST['action'];
            unset($_REQUEST['action']);
            
            // get details for current lead like lead version
            $stPlquery = $obCurProjectLeads->create_new_list_query('', ' leads.id = "' . $stProjectLeadId . '"', $arFilters, $params, false);
            $arCurLeadObj = $obProjectLeads->process_full_list_query($stPlquery, '');
            $_REQUEST['action'] = $stAction;
            
            $arListOfLeadToDupe[] = array_shift($arCurLeadObj);
            $arListOfDuplicate[] = $this->getDuplicateLeads($obProjectLeads);
        }
        
        $this->order = ($this->order == 'ASC') ? 'DESC' : 'ASC';
        $this->ss->assign('order', $this->order);
        
        $this->ss->assign('STATE_DOM', $app_list_strings['state_dom']);
        $this->ss->assign('timedate', $timedate);
        $this->ss->assign('OB_LEAD_DATA', $arListOfLeadToDupe);
        $this->ss->assign('AR_DUP_LIST_DATA', $arListOfDuplicate);
        $this->ss->display('custom/modules/Leads/tpls/mass_deduping.tpl');
    }
    /**
     * Function to find duplicate leads based on bluebook criteria
     *
     * @param Leads $obLeadToDedupe            
     * @return Array of duplicate leads
     */
    function getDuplicateLeads($obLeadToDedupe)
    {
        $order_by = "";
        $show_deleted = "0";
        $check_dates = "";
        
        require_once ('custom/modules/Leads/bbProjectLeads.php');
        
        $obCurProjectLeads = new bbProjectLeads();
        $obProjectLeads = new bbProjectLeads();
        $arFilters = Array (
                'project_title' => 1,
                'state' => 1,
                'city' => 1,
                'lead_version' => 1,
                'prev_bid_to' => 1,
                'new_total' => 1,
                'bids_due_tz' => 1,
                'lead_plans' => 1,
                'date_modified' => 1,
                'status' => 1,
                'classification' => 1,
                'fav_bidders_only' => 1,
                'pre_bid_meeting' => 1,
                'contact_no' => 1 
        );
        
        $params = array (
                'favorites' => 1 
        );
        
        // check type of order
        $order = $this->order;
        $orderBy = $this->orderBy;
        
        // exclude parent leads, bid due date should between +-7 days
        // prepare duplicate criteria array
        $arDupOrCriteriaFields = array (
                'leads.type' => $obLeadToDedupe->type 
        );
        
        $arDupAndCriteriaFields = array (
                'leads.structure' => $obLeadToDedupe->structure,
                'leads.state' => $obLeadToDedupe->state 
        );
        $where = ' ';
        
        // current lead id should not be as part of the list
        $stBidsDueCondition = (trim($obLeadToDedupe->bids_due) != '') ? '( TIMESTAMP(leads.bids_due) BETWEEN  DATE_SUB(TIMESTAMP("' . $obLeadToDedupe->fetched_row['bids_due'] . '") , INTERVAL +7 DAY) AND DATE_SUB(TIMESTAMP("' . $obLeadToDedupe->fetched_row['bids_due'] . '") , INTERVAL -7 DAY) OR leads.asap = 1 )' : '( (leads.bids_due IS NULL OR leads.bids_due="" OR leads.bids_due="0000-00-00 00:00:00" ) OR leads.asap = 1  )';
        ;
        
        $where .= ' (
						(leads.type = "' . $obLeadToDedupe->type . '"
						OR SUBSTRING("' . $obLeadToDedupe->project_title . '", 1, 8) = SUBSTRING(leads.project_title, 1, 8)
						OR leads.structure = "' . $obLeadToDedupe->structure . '"
						)
						AND
						(
							' . $stBidsDueCondition . '
						)
						AND
						(
							leads.state = "' . $obLeadToDedupe->state . '"
							AND
							leads.city = "' . $obLeadToDedupe->city . '"
						)
		
					) AND leads.id <> "' . $obLeadToDedupe->id . '"';
        
        // make sure no lead is associated to this
        // $where .= ' AND coalesce(countt,1) = 1 ';
        
        $order_by = $orderBy . ' ' . $order;
        $tmpActionVal = $_REQUEST['action'];
        $_REQUEST['action'] = 'deduping';
        $_REQUEST['record'] = $obLeadToDedupe->id;
        // get duplicate records based on matcing criteria
        $query = $obProjectLeads->create_new_list_query($order_by, $where, $arFilters, $params, $show_deleted);
        
        unset($_REQUEST['record']);
        $_REQUEST['action'] = $tmpActionVal;
        
        $arDupList = $obProjectLeads->process_full_list_query($query, $check_dates);
        
        return $arDupList;
    }
    
    /**
     * Function to link duplicateleads
     */
    function linkMassDupeLeads($arRequest)
    {
        foreach ($arRequest['mass'] as $stLeadId => $arDuplicateLeadId) {
            
            $arAllLeadIds = $arDuplicateLeadId;
            array_push($arAllLeadIds, $stLeadId);
            
            // get the one which was created earlieast
            $arLeadsList = $this->bean->get_full_list(' leads.date_entered ASC ', ' leads.id in ("' . implode('","', array_unique($arAllLeadIds)) . '") OR leads.parent_lead_id in ("' . implode('","', $arAllLeadIds) . '")  ');
            
            if (isset($arLeadsList[0]->id) && trim($arLeadsList[0]->id) != '') {
                
                // set all the project leads parent_lead_ids to this one
                $child_pl_change_log = false;
                foreach ($arLeadsList as $obLead) {
                    
                    // set map array for showing results
                    $arDupeMap[$obLead->id] = $obLead->project_title;
                    
                    if ($arLeadsList[0]->id != $obLead->id) {
                        if (!in_array($obLead->id, $arFinalDupe[$arLeadsList[0]->id]))
                            $arFinalDupe[$arLeadsList[0]->id][] = $obLead->id;
                        
                        $obLead->parent_lead_id = $arLeadsList[0]->id;
                        if ($obLead->change_log_flag == 1) {
                            $child_pl_change_log = true;
                        }
                    } else {
                        $obLead->parent_lead_id = NULL;
                        if ($child_pl_change_log == true) {
                            $obLead->change_log_flag = 1;
                        }
                    }
                    
                    $obLead->save();
                }
                
                // update lead version for this project lead
                updateLeadVersionBidDueDate($arLeadsList[0]->id);
                updateNewTotalBidderCount($arLeadsList[0]->id);
                updateOnlineCount($arLeadsList[0]->id);
            }
        }
        
        $this->ss->assign('AR_DUPE_DETAILS', $arFinalDupe);
        $this->ss->assign('AR_DUPE_MAP', $arDupeMap);
        $this->ss->display('custom/modules/Leads/tpls/mass_dedupe_details.tpl');
        
        die();
    }
}