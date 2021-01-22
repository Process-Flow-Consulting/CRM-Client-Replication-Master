<?php
/**
 * Added New View to link Project Leads manualy.
 * @author ashutosh
 *
 */
require_once('custom/include/common_functions.php');
class CustomLeadsViewLink_projects extends ViewList
{
    /*
     * Constructor for the View
     */
    function CustomLeadsViewLink_projects()
    {
        parent::ViewList();
    }
    /**
     * display method to render the view
     * 
     * @see ViewList::display()
     *
     */
    function display()
    {
        global $timedate, $app_list_strings;
        
        // link project Leads
        if (isset($_POST['save_link_projects']) && $_POST['save_link_projects'] == 1) {
            
            $this->linkDupeLeads($_REQUEST);
        }
        
        $arSelectIds = (isset($_REQUEST['uid']) && trim($_REQUEST['uid']) != '') ? explode(',', $_REQUEST['uid']) : $_REQUEST['mass'];
        
        $stCompareIds = "'" . implode("','", $arSelectIds) . "'";
        
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
        $obLeads = new Lead();
        
        $first_order = (isset($_REQUEST['primary_lead']) && trim($_REQUEST['primary_lead']) != '') ? ' if(leads.id  ="' . $_REQUEST['primary_lead'] . '" ,1 ,2)  ASC ' : ' leads.date_entered ASC ';
        
        $order = (isset($_REQUEST['odr'])) ? $_REQUEST['odr'] : 'ASC';
        $orderBy = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : 'project_title';
        $second_order = ' ,' . $orderBy . ' ' . $order;
        
        $arSQL = $obLeads->create_new_list_query($first_order . $second_order, " leads.id IN ({$stCompareIds}) ", $arFilters, $params, false, '', 1);
        
        $arSQL['select'] .= ' ,getBidsDueDate(leads.bids_due,leads.bid_due_timezone) bids_due_tz 
							 ,project_lead_lookup.online_link_count lead_plans 
							 ,COALESCE(project_lead_lookup.lead_version,1) lead_version';
        $arSQL['from'] .= ' left join project_lead_lookup on leads.id = project_lead_lookup.project_lead_id ';
        $stSQL = $arSQL['select'] . ' ' . $arSQL['from'] . ' ' . $arSQL['where'] . ' ' . $arSQL['order_by'];
        
        $arCurLeadObj = $obLeads->process_full_list_query($stSQL, '');
        
        $order = ($order == 'ASC') ? 'DESC' : 'ASC';
        $this->ss->assign('order', $order);
        $this->ss->assign('STATE_DOM', $app_list_strings['state_dom']);
        $this->ss->assign('timedate', $timedate);
        $this->ss->assign('ST_ASSIGN_IDS', implode(',', $arSelectIds));
        $this->ss->assign('AR_DUP_LIST_DATA', $arCurLeadObj);
        $this->ss->display('custom/modules/Leads/tpls/link_projects.tpl');
    }
    
    /**
     * Constructor for the View
     * $arRequest (current REQUEST Variable)
     */
    function linkDupeLeads($arRequest)
    {
        $arFinalDupe = array();
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
                
                //set update prev bid to flag
                setPreviousBidToUpdate();
            }
        }
        
        // render the deduped results
        $this->ss->assign('AR_DUPE_DETAILS', $arFinalDupe);
        $this->ss->assign('AR_DUPE_MAP', $arDupeMap);
        $this->ss->display('custom/modules/Leads/tpls/mass_dedupe_details.tpl');
        
        die();
    }
}