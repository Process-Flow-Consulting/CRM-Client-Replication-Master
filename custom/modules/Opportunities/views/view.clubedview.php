<?php


class CustomOpportunitiesViewClubedview extends SugarView{

    function CustomOpportunitiesViewClubedview() {
        
        parent::SugarView();
    }
    function display(){

        global $timedate,$current_user;
     
        //echo $this->getModuleTitle(false);

        //check type of order
        //@modified By Mohit Kumar Gupta
        //@date 17-Dec-2013	
        //deafult sorting order changed to classification
        $order = (isset($_REQUEST['odr'])) ? $_REQUEST['odr'] : 'DESC';
        $orderBy = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : 'sort_classification_default';
        //$orderBy = ($orderBy == 'sales_stage') ?  'sort_sales_default':$orderBy ;
        $orderBy = ($orderBy == 'opportunity_classification') ?  'sort_classification_default':$orderBy ;
        
        $stSearchString = (isset($_REQUEST['search_string']) && trim($_REQUEST['search_string']) != '')?$_REQUEST['search_string']:'';
        $bOnlyCurrentUser = (isset($_REQUEST['my_items']) )?true:false;
        
        $this->bean->id;
        /* $obPorjectLeadLookup = new project_lead_lookup();
        $obPorjectLeadLookup->retrieve_by_string_fields(array('project_lead_id'=>$this->bean->project_lead_id));
        $this->ss->assign('B_SHOWPLANS',!empty($obPorjectLeadLookup->online_link_count)); */
        //all all the related opportunities for this
        $order_by = '';
        $show_deleted = '0';
        $check_dates  = '0';
        $where = 'opportunities.parent_opportunity_id= "'.$this->bean->id.'"';
        //$arAllChilds = $this->bean->get_full_list('', 'opportunities.parent_opportunity_id= "'.$this->bean->id.'"');
        $params['joined_tables'] = array('accounts_opportunities');
        
        $query = $this->bean->create_new_list_query($order_by, $where,array(),$params, $show_deleted,'',1);
        
        /*$query['select'] .= ", CASE opportunities.sales_stage 
								WHEN 'Proposal - Verified' then '1' 
     							WHEN 'Proposal - Unverified' then '2'
     							WHEN 'Proposal - Pending' then '3'
     						ELSE opportunities.sales_stage  END sort_sales_default "; */
        
        /* $query['select'] .= ",oss_classification.description AS classification_name
        ,oss_classification.name AS sort_classification_default, accounts.name as client_name, accounts.proview_url as proview_url ";  */
		$query['select'] .= ",oss_classification.description AS classification_name
        ,oss_classification.name AS sort_classification_default, accounts.name as client_name, accounts.proview_url as proview_url,CASE WHEN quotes.proposal_verified = '1' AND quotes.verify_email_sent = '1'  then '1'
        WHEN  quotes.proposal_verified = '2' AND quotes.verify_email_sent = '1' then '2'
        WHEN quotes.proposal_verified = '2' AND quotes.verify_email_sent = '0' then '3'
        ELSE 4      END sort_sales_default "; 
       // $query['select'] .=' ,quotes.date_time_sent dts, quotes.date_time_received dtr, quotes.id qid ';
        $query['from'] .= ' LEFT JOIN accounts_opportunities on accounts_opportunities.opportunity_id = opportunities.id and accounts_opportunities.deleted =0
                            LEFT JOIN accounts on accounts.id = accounts_opportunities.account_id and accounts.deleted=0
							LEFT JOIN aos_quotes as quotes on quotes.opportunity_id = opportunities.id AND quotes.deleted = 0
                            LEFT JOIN oss_classification  on oss_classification.id = opportunities.opportunity_classification AND oss_classification.deleted = 0';
        
        /*
         *(select count(quote_id) pcount, qo.opportunity_id, qo.quote_id quote_id  from quotes_opportunities qo where qo.deleted=0 group by qo.opportunity_id) tmp on tmp.opportunity_id=opportunities.id
							LEFT JOIN
							(select quotes.date_time_sent dts, quotes.date_time_received dtr, quotes.id qid from quotes where quotes.deleted=0)  tmpquote  on tmpquote.qid = quote_id
         */
        
        

        
        $query['select'] .= '  ,accounts.id account_id,accounts.proview_url , accounts.name as client_name,accounts.lead_source AS source' 
        .',getBidsDueDate(quotes.date_time_sent,quotes.delivery_timezone) dts'
        .',getBidsDueDate(quotes.date_time_received,quotes.delivery_timezone) dtr'
        .',quotes.id qid, quotes.proposal_amount as prop_total, quotes.proposal_delivery_method as pdm'
        .',getBidsDueDate(quotes.date_time_opened,quotes.delivery_timezone) dto'
        .',getBidsDueDate(quotes.date_time_delivery,quotes.delivery_timezone) dtd, quotes.delivery_timezone dtz, quotes.proposal_verified, quotes.verify_email_sent '
		.', DATE_SUB( getBidsDueDate(quotes.date_time_delivery,quotes.delivery_timezone) , INTERVAL 1 HOUR ) adtd'
        /* .",(CASE opportunities.bid_due_timezone 
			WHEN 'Eastern' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-05:00')
			WHEN 'Central' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-06:00')
			WHEN 'Mountain' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-07:00')
			WHEN 'Pacific' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-08:00')
			ELSE CONVERT_TZ(opportunities.date_closed,'+00:00','-00:00') END) date_closed 
        "  */
		
        .',getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed'        
        ;
        $query['where'] .= (trim($stSearchString) != '') ? ' AND  opportunities.name LIKE "%'.$_REQUEST['search_string'].'%" ': '';
        $query['where'] .= ($bOnlyCurrentUser) ? ' AND  opportunities.assigned_user_id = "'.$current_user->id.'" ': '';
        
        
        $order = ($order == 'ASC') ? 'DESC' : 'ASC';
       
        
        //if date_time is set then apply filter in summary: Must be from Calendar
        if(isset($_REQUEST['date_time'])){
        	 $stCompareDate = date('Y-m-d H:i:s',$_REQUEST['date_time']);
        	/* $query['where'] .="
        					AND (CASE opportunities.bid_due_timezone 
			WHEN 'Eastern' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-05:00')
			WHEN 'Central' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-06:00')
			WHEN 'Mountain' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-07:00')
			WHEN 'Pacific' THEN CONVERT_TZ(opportunities.date_closed,'+00:00','-08:00')
			ELSE CONVERT_TZ(opportunities.date_closed,'+00:00','-00:00') END)= '{$stCompareDate}'
        	"; */
        	$query['where'] .= " AND getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) = '{$stCompareDate}'";
        } 
        
        ############################################
        #### USER FILTERS FOR OPPORTUNITIES ########
        ############################################
        global $current_user;
        //apply USER Filters if applicable
        //Remove filters temporary.
        /* if(!$current_user->is_admin){
        
        	require_once('custom/modules/Users/filters/userAccessFilters.php');
        	$obAccessFilters = new userAccessFilters();
        	$arUserFilters =  $obAccessFilters->getOpporutnityFilterWehreClause();
        
        	if($arUserFilters != '' && $arUserFilters->summaryview)
        	{
        		//$query['from'] = $query['from'].'  '.$arUserFilters ;
        		$query['from'] .= $arUserFilters->summaryview->joins ;
        		$query['where'] .= $arUserFilters->summaryview->where ;
        		$query['where'] .= ' GROUP BY opportunities.id ';
        
        	}
        
        } */
        ################################################
        #### EOF USER FILTERS FOR OPPORTUNITIES ########
        ################################################
        $stQuery =  $query['select'].$query['from'].$query['where']." ORDER BY $orderBy $order ";
        $arAllChilds =  $this->bean->process_full_list_query($stQuery, $check_dates);
		
        $countChildsQuery = $this->bean->create_list_count_query($stQuery);
        $countChildsResult = $this->bean->db->query($countChildsQuery);
        $countChildsRow = $this->bean->db->fetchByAssoc($countChildsResult);
        
        
/*         echo '<pre>';
        print_r($countChildsRow);
        echo '</pre>';
        foreach($arAllChilds as $all_result){			
			// added on 10-01-2012
			$this->bean->load_relationship('quotes');
			$relate_data = $this->bean->quotes->get();

			//echo "<pre>";print_r($relate_data);echo "</pre>";
			//echo "<pre>";print_r($this->bean->id);echo "</pre>";
			//echo "<pre>";print_r($all_result->id);echo "</pre>";

			//added on 11-01-2012
			$obj_opportunity = new Opportunity();
			$opportunity_data = $obj_opportunity->retrieve($all_result->id);
			$opportunity_data->load_relationship('quotes');
			$relate_data = $opportunity_data->quotes->get();
			//print_r($relate_data);
			$date_sent = '';
			$date_received = '';
			$relate_data_count = 0;
			if(count($relate_data) > 0){
				$obj_quote = new Quote();
				$quote_data = $obj_quote->retrieve($relate_data[0]);
				//echo "<pre>";print_r($quote_data);echo "</pre>";die;
				$date_sent = explode(' ', $quote_data->date_time_sent);
				$date_received = explode(' ', $quote_data->date_time_received);
				$relate_data_count= count($relate_data);
				$this->ss->assign('relate_data_count', $relate_data_count);
				$this->ss->assign('relate_data',$relate_data[0]);
				$this->ss->assign('opp_id',$this->bean->id);
				$this->ss->assign('date_sent',$date_sent[0]);
				$this->ss->assign('date_received',$date_received[0]);
			}else{
				$this->ss->assign('relate_data',$relate_data_count);
			}
		}
*/
		/* print_r('<pre>');
		print_r($arAllChilds);
		print_r('</pre>'); */
		$this->ss->assign('record',$_REQUEST['record']);
        $this->ss->assign('AR_CHILDS',$arAllChilds);
        $this->ss->assign('SUB_OP_COUNT',$countChildsRow['c']);
		$this->ss->assign('order', $order);
		$this->ss->assign('search_string', $stSearchString);
        $this->ss->assign('cur_user_only', $bOnlyCurrentUser);
        $this->ss->assign('OB_OPP_DATA',$this->bean);
        $this->ss->assign('timedate',$timedate);
		$this->ss->assign('actionsLink', $this->buildActionsLink());
		
        $this->ss->assign('project_lead_id', $this->bean->project_lead_id);
        $this->ss->assign('current_user_id', $current_user->id);
		$this->buildClientOppActionsLink();
        $this->ss->assign('clientOppActionsLink', $this->buildClientOppActionsLink());
		$this->ss->display('custom/modules/Opportunities/tpls/opportunities_clubbed.tpl');
	}

    protected function _getModuleTitleParams($browserTitle = false) {
        global $mod_strings;
        $params = parent::_getModuleTitleParams($browserTitle);        
        $params[1] = "<a href='index.php?module=Opportunities&action=DetailView&record={$this->bean->id}'>{$this->bean->name}</a>";
        $params[] = $mod_strings['LBL_PROJECT_OPPORTUNITY_SUMMARY'];
        return $params;
    }
    
    /**
     * Display the actions link
     *
     * @param  string $id link id attribute, defaults to 'actions_link'
     * @return string HTML source
     */
    protected function buildActionsLink(
    		$id = 'actions_link'
    )
    {
    	global $app_strings;
    	$closeText = SugarThemeRegistry::current()->getImage('close_inline', 'border=0', null, null, ".gif", $app_strings['LBL_CLOSEINLINE']);
    	$moreDetailImage = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
    	$menuItems = '';
    
    	// delete
    	//commnented: req date 10.10.2012
    	//if ( ACLController::checkAccess('Opportunity','delete',true))
    		//$menuItems .= $this->buildDeleteLink();
    	
    	if ( ACLController::checkAccess('Quotes','edit',true))
    		$menuItems .= $this->buildVerifyProposalsLink();
    	
    	//if ( ACLController::checkAccess('Quotes','edit',true))
    		//$menuItems .= $this->buildCreateClientOpportunityLink();
    
    	$menuItems = str_replace('"','\"',$menuItems);
    	$menuItems = str_replace(array("\r","\n"),'',$menuItems);
    
    	if ( empty($menuItems) )
    		return '';
    	
    	$record = $_REQUEST['record'];
    
    	return <<<EOHTML
<a id='$id' href="javascript:void(0)">
    {$app_strings['LBL_LINK_ACTIONS']}&nbsp;<img src='{$moreDetailImage}' border='0' />
</a>
<script type="text/javascript">
var actionLinkSelector = "#$id";
var userHoveredOverMenu = false;
    
function actions_overlib(e)
{
    overlib("{$menuItems}", CENTER, '', STICKY, MOUSEOFF, 3000, CLOSETEXT, '{$closeText}', WIDTH, 150,
        CLOSETITLE, "{$app_strings['LBL_ADDITIONAL_DETAILS_CLOSE_TITLE']}", CLOSECLICK,
        FGCLASS, 'olOptionsFgClass', CGCLASS, 'olOptionsCgClass', BGCLASS, 'olBgClass',
        TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olOptionsCapFontClass',
        CLOSEFONTCLASS, 'olOptionsCloseFontClass');
    
    e.currentTarget.focus();
    
    YUI().use('node', 'event-base', function(Y) {
        e.currentTarget.on('blur', actions_overlib_close);
        Y.all('#overDiv').on('mouseover', function(e) {
            userHoveredOverMenu = true;
        });
        Y.all('#overDiv').on('mouseout', function(e) {
            userHoveredOverMenu = false;
        });
    });
}
    
function actions_overlib_close(e) {
    if (userHoveredOverMenu == false) {
        YUI().use('node', function(Y) {
            var overDiv = Y.one("#overDiv");
            if (overDiv != null) overDiv.remove();
        });
    }
}
    
// event delegations
YUI().use('node', 'event-base', function(Y) {
    if (typeof alClickEventHandler != 'undefined')
    {
        alClickEventHandler.detach();
    }
    
    if (Y.one('div.listViewBody') != null)
    {
        var alClickEventHandler = Y.one('div.listViewBody').delegate('click', actions_overlib, actionLinkSelector);
    }
});
//form handling
function validate_delete_form(myForm){
	var checks = sugarListView.get_checks();
	var check_count = sugarListView.get_checks_count();
	var sub_op_count = document.MassUpdate.sub_op_count.value;
	
	if(check_count < 1){
		alert("Please select at least 1 record to proceed.");
		return false;
	}else if(sub_op_count==1 && check_count==1){
		alert("There is only 1 Child Opportunity. You cannot unlink that."); 
		return false;
    }else{
		if(confirm('Are you sure you want to unlink the '+check_count+' selected record(s)?')){
		
			document.MassUpdate.action.value = 'unlinkop';
			document.MassUpdate.submit();
			return true;
		}else{
			return false;
		}
	}
}
function createClientOpportunity(){
   //open_popup("Contacts",600,400,"",true,true,{"call_back_function":"set_client_id_return","form_name":"create_client_opportunity","field_to_name_array":{"id":"client_data"}},"MultiSelect",true);
  location.href = "index.php?module=Opportunities&action=EditView&parent_id=$record";
}
function set_client_id_return(popup_reply_data)
{
    var form_name = popup_reply_data.form_name;
	var name_to_value_array = popup_reply_data.name_to_value_array;
	var passthru_data = popup_reply_data.passthru_data;
	var select_entire_list = typeof( popup_reply_data.select_entire_list ) == 'undefined' ? 0 : popup_reply_data.select_entire_list;
	var current_query_by_page = popup_reply_data.current_query_by_page;
	// construct the POST request
	var query_array =  new Array();
	if (name_to_value_array != 'undefined') {
		for (var the_key in name_to_value_array)
		{
			if(the_key == 'toJSON')
			{
				/* just ignore */
			}
			else
			{
				query_array.push(name_to_value_array[the_key]);
			}
		}
	}
  	//construct the muulti select list
	var selection_list = popup_reply_data.selection_list;
	if (selection_list != 'undefined') {
		for (var the_key in selection_list)
		{
			query_array.push(selection_list[the_key])
		}
	}
   var contact_ids = query_array.join(',');
   document.create_client_opportunity.select_entire_list.value = select_entire_list;
   document.create_client_opportunity.client_data.value = contact_ids;
   document.create_client_opportunity.submit();
}
</script>
EOHTML;
    }
    
    /**
     * Display the client opportunity actions link
     * @author Mohit Kumar Gupta
     * @date 01-04-2014
     * @param  string $id link id attribute, defaults to 'actions_link'
     * @return string HTML source
     */
   //BBSMP - 203 -- start
    protected function buildClientOppActionsLink($id = 'actions_link')
    {
        global $app_strings;
        $closeText = SugarThemeRegistry::current()->getImage('close_inline', 'border=0', null, null, ".gif", $app_strings['LBL_CLOSEINLINE']);
		
		$moreDetailImage = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
		
        $menuItems = '';            
        $record = $_REQUEST['record'];
        
        $menuItems .= $this->buildComposeEmailLink();
        
        $menuItems .= $this->buildCustomMassUpdateLink($record);
        $menuItems .=$this->buildPdfDownloadLink();
    
        $menuItems = str_replace('"','\"',$menuItems);
        $menuItems = str_replace(array("\r","\n"),'',$menuItems);
		
        
		if ( empty($menuItems) )
            return '';                 
    
        return <<<EOHTML
<a id='$id' href="javascript:void(0)">
    {$app_strings['LBL_LINK_ACTIONS']}&nbsp;<span class="suitepicon suitepicon-action-caret"></span>
</a>
<script type="text/javascript">
var actionLinkSelector = "#$id";
var userHoveredOverMenu = false;
    
function actions_overlib(e)
{
    overlib("{$menuItems}", CENTER, '', STICKY, MOUSEOFF, 3000, CLOSETEXT, '{$closeText}', WIDTH, 190, FIXY, 257, FIXX, 1450,
        CLOSETITLE, "{$app_strings['LBL_ADDITIONAL_DETAILS_CLOSE_TITLE']}", CLOSECLICK,
        FGCLASS, 'olOptionsFgClass', CGCLASS, 'olOptionsCgClass', BGCLASS, 'olBgClass',
        TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olOptionsCapFontClass',
        CLOSEFONTCLASS, 'olOptionsCloseFontClass');
    
    e.currentTarget.focus();
    
    YUI().use('node', 'event-base', function(Y) {
        e.currentTarget.on('blur', actions_overlib_close);
        Y.all('#overDiv').on('mouseover', function(e) {
            userHoveredOverMenu = true;
        });
        Y.all('#overDiv').on('mouseout', function(e) {
            userHoveredOverMenu = false;
        });
    });
}
    
function actions_overlib_close(e) {
    if (userHoveredOverMenu == false) {
        YUI().use('node', function(Y) {
            var overDiv = Y.one("#overDiv");
            if (overDiv != null) overDiv.remove();
        });
    }
}
    
// event delegations
YUI().use('node', 'event-base', function(Y) {
    if (typeof alClickEventHandler != 'undefined')
    {
        alClickEventHandler.detach();
    }
    
    if (Y.one('div.listViewBody') != null)
    {
        var alClickEventHandler = Y.one('div.listViewBody').delegate('click', actions_overlib, actionLinkSelector);
    }
});
</script>
EOHTML;
    }
	//BBSMP - 203 -- end
    
    /**
     * Builds the delete link
     *
     * @return string HTML
     */
    protected function buildDeleteLink()
    {
    	global $app_strings,$mod_strings;
    
    	return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"return validate_delete_form('MassUpdate')\">{$mod_strings['LBL_DELETE_OPPORTUITY_BUTTON_LABEL']}</a>";
    }
    /**
	 * Builds the create client opportunity link
	 *
	 * @return string HTML
	 */
	protected function buildCreateClientOpportunityLink()
	{
		global $mod_strings;
		$onClick = "createClientOpportunity(this)";
		return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">{$mod_strings['LBL_ADD_CLIENT_TO_OPPORTUNITY']}</a>";
	}
	
	/**
	 * Build the Verify Proposal Link
	 * 
	 * @return string HTML
	 */
    public function buildVerifyProposalsLink()
    {
    	global $mod_strings;
    	$onClick = "verifySelectedProposals(this)";
    	return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">Verified</a>";
    }
    
    /**
     * Build the Compose Email Link
     * @author Mohit Kumar Gupta
     * @date 01-04-2014
     * @return string HTML
     */
    public function buildComposeEmailLink()
    {
       //BBSMP - 203 -- start
        global $mod_strings;
        $onClick = "return openComposeEmail()";
        return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">Email</a><br><div style='border-bottom: 2px solid #c2c3c4;'></div>";
		//BBSMP - 203 -- end
    }
    /**
     * Build the Custom Mass Update Link
     * @author Mohit Kumar Gupta
     * @date 01-04-2014
     * @param $record string
     * @return string HTML
     */
    public function buildCustomMassUpdateLink($record='')
    {
        global $mod_strings;
        //BBSMP - 203 -- start
        $onClick = "return openCustomMassUpdateForm('".$record."')";
        return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">Mass Update</a><br><div style='border-bottom: 2px solid #c2c3c4;'></div>";
		//BBSMP - 203 -- end
    }
    /**
     * Build the PDF Download Link
     * @author Ritika Davial
     * @date 28-09-2014
     * @return string HTML
     */
    public function buildPdfDownloadLink()
    {
    	$onClick = "return pdfLinkValidation()";
    	return "<a href='javascript:void(0)' style='width: 150px' class='menuItem' onmouseover='hiliteItem(this,\"yes\");' onmouseout='unhiliteItem(this);' onclick=\"$onClick\">Download Job Information Sheet</a>";
    }
    
}
?>