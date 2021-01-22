<?php

require_once 'include/MVC/View/views/view.edit.php';

class LeadsViewEdit extends ViewEdit {

    function LeadsViewEdit() {
        parent::ViewEdit();
    }

    function display() {
    	global $current_user;
		global $app_list_strings; 
		#####################
		### ACCESS FILTER ###
		if(isset($this->bean->id) && !$current_user->is_admin){
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			userAccessFilters::isLeadAccessable($this->bean->id);
		}
		### END OF ACCESS FILTER ###
		############################
        if (isset($this->bean->mi_lead_id) && ($this->bean->mi_lead_id !='' )) {
            sugar_die('You are not authorised to edit this project lead.');
        }
        
        //Convert Date Time according to Time Zone
        if(isset($_REQUEST['record'])){
        	require_once 'custom/include/OssTimeDate.php';
			$oss_timedate = new OssTimeDate();		
			$bid_due_date = $oss_timedate->convertDBDateForDisplay($this->bean->bids_due, $this->bean->bid_due_timezone, true);
        	$this->bean->bids_due = $bid_due_date;
        }

        
        /**
         * END OF PROJECT URL : no need to itrate project urls will 
         * stored in a seperate module oss_OnlinePlan 
         * 
         
        //view project url
        if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
            //domain
            $domain = explode('^', $this->bean->project_url);
            $domain_l = explode('^', $this->bean->project_url_l);
            $dom = "";

            foreach ($domain as $key1 => $val1) {
                $counterDom = $key1 + 1;

                $dom = $dom . '<label id=label_desc' . $counterDom . '> Description: </label> <input type="text" value="' . $domain_l[$key1] . '" id=project_url_l' . $counterDom . ' name=project_url_l[]> <label id=label_url' . $counterDom . '> URL: </label>   <input type="text" value="' . $val1 . '" id=project_url' . $counterDom . ' name=project_url[]>   <img name="remove" style="position:absolute;" src="index.php?entryPoint=getImage&amp;themeName=Sugar&amp;imageName=id-ff-remove.png" id="Remove' . $counterDom . '"  onclick="remove_file_field(' . $counterDom . ')">' . '<br id="bmain'.$counterDom.'"/>';
            }

            $this->ss->assign('dom',$dom);
           // $this->ev->defs['panels']['LBL_CONTACT_INFORMATION'][11][0]['customCode'] = $this->ev->defs['panels']['LBL_CONTACT_INFORMATION'][11][0]['customCode'].$dom;
            
            
        }
        
		END OF PROJECT URL 
         */


        $county = '';
        $county .= '<div id="county_div">';
        $county .= '<select title="" id="county" name="county_id">';
        $county .= '<option value="0" label=""></option>';
        $county .= '</select>';
        $county .= '</div>';

        

               
        $selected_structure = '';
        if(isset($this->bean->structure)){
            $selected_structure = $this->bean->structure;
        }

        $structure = '';
        $structure .= '<select id="structure" name="structure">';
        $structure .= '<option value=""></option>';
        $structure .= '<optgroup style="background:#ececec" label="Residential Building"></optgroup>';
        foreach ($app_list_strings['structure_residential'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $structure .= '<optgroup style="background:#ececec" label="Non-Residential Building"></optgroup>';
        foreach ($app_list_strings['structure_non_residential'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $structure .= '<optgroup style="background:#ececec" label="Non-Building Construction"></optgroup>';
        foreach ($app_list_strings['structure_non_building'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $structure .= '</select>';

        
        $selected_county = '';
        if(isset($this->bean->county_id)){
            $selected_county = $this->bean->county_id;
        }
        
        $this->ss->assign('county',$county);
        $this->ss->assign('structure', $structure);

        parent::display();

        echo '<script type="text/javascript">
            var s_county = "'.$selected_county.'";
                function edBidsDue(){
                    var elementid = document.getElementById("asap");            		
                    if(elementid.checked==true){
                        document.getElementById("bids_due").value = "";
                        document.getElementById("bids_due_date").value = "";
                        document.getElementById("bid_due_timezone").value = "";
                        
                        combo_bids_due.update();                        
                        document.getElementById("bids_due_time_section").style.display="none";
                        document.getElementById("bids_due_date").style.visibility="hidden";
                        document.getElementById("bids_due_trigger").style.visibility="hidden";
                        document.getElementById("bid_due_timezone").style.visibility="hidden";
            			removeFromValidate("EditView","bid_due_timezone");
    					removeFromValidate("EditView","bids_due_date");
                    }
                    if(elementid.checked==false){
                        document.getElementById("bids_due_time_section").style.display="block";
                        document.getElementById("bids_due_date").style.visibility="visible";
                        document.getElementById("bids_due_trigger").style.visibility="visible";
                        document.getElementById("bid_due_timezone").style.visibility="visible";
            			addToValidate("EditView", "bid_due_timezone", "enum", true,"Bids Due Time Zone" );
    					addToValidate("EditView", "bids_due_date", "date", true,"Bids Due" );
                    }
                }
                
                 YAHOO.util.Event.onDOMReady(function(){
                    edBidsDue();
                    var stateVal = document.getElementById(\'state\').value;
                    getCounty(stateVal,s_county);
                    
                    document.getElementById("asap").onclick = function(){
                        edBidsDue();
                    }                

                });               

                        if(document.getElementById(\'test_label\')){
                        document.getElementById(\'test_label\').innerHTML = \'\';
                    }

                    function getCounty(stateAbbr,selCounty){
                    var callback = {
                        success:function(o){
                            //alert(o.responseText);
                            document.getElementById("county_div").innerHTML = o.responseText;
                        }
                        }
                        var connectionObject = YAHOO.util.Connect.asyncRequest ("GET", "index.php?entryPoint=CountyAjaxCall&state_abbr="+stateAbbr+"&selected_county="+selCounty, callback);

                    }
                    
                    function getTimeZone(stateAbbr){
                        var state= SUGAR.language.get("app_list_strings", "state_dom")[stateAbbr];
                        var timezone= SUGAR.language.get("app_list_strings", "state_tz_dom")[state];
                        timezone = (typeof(timezone) != \'undefined\') ? timezone : \'\';
                        document.getElementById("bid_due_timezone").value = timezone;
                    }                    
                    
                </script>';
    }

}

?>
