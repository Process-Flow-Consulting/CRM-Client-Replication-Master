<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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

 * Description: view handler for last step of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/MVC/View/SugarView.php');

require_once('modules/Import/ImportCacheFiles.php');

              
class ViewFinishimport extends SugarView 
{	
    function ViewFinishimport(){
    	parent::SugarView();
    }
    
 	public function display()
    {
        global $mod_strings, $app_strings, $current_user, $sugar_config, $current_language,$app_list_strings;
        
        $this->ss->assign("HEADER", $app_strings['LBL_IMPORT']." ". $mod_strings['LBL_MODULE_NAME']);
		$this->ss->assign("JAVASCRIPT", $this->_getJS());
		
        // lookup this module's $mod_strings to get the correct module name
        $module_mod_strings = 
            return_module_language($current_language, 'Leads');
        $this->ss->assign("MODULENAME",'Leads');
        
        // read status file to get totals for records imported, errors, and duplicates
        $count        = 0;
        $errorCount   = 0;
        $dupeCount    = 0;
        $createdCount = 0;
        $updatedCount = 0;
        
        $fp = sugar_fopen(ImportCacheFiles::getStatusFileName(),'r');        
        if($fp){
	        while (( $row = fgetcsv($fp, 8192) ) !== FALSE) {
	            $count         += (int) $row[0];
	            $errorCount    += (int) $row[1];
	            $dupeCount     += (int) $row[2];
	            $createdCount  += (int) $row[3];
	            $updatedCount  += (int) $row[4];            
	        }
	        fclose($fp);
        }
        
        
        
        $this->ss->assign("noSuccess",FALSE);
              
        $this->ss->assign("errorCount",$errorCount);
        $this->ss->assign("dupeCount",$dupeCount);
        $this->ss->assign("createdCount",$createdCount);
        $this->ss->assign("updatedCount",$updatedCount);
        $this->ss->assign("errorFile",ImportCacheFiles::getErrorFileName());
        $this->ss->assign("errorrecordsFile",ImportCacheFiles::getErrorRecordsFileName());
        $this->ss->assign("dupeFile",ImportCacheFiles::getDuplicateFileName());
        
        $this->ss->assign("PROSPECTLISTBUTTON","");
        
        /*************************************************
         * CUSTOMIZATION FOR PROJECT LEADS
         * THIS WILL BE APPLICABLE FOR PROJECT LEADS ONLY
         * WE NEED TO DISPLAY IMPORTED COUNT SEPERATLLY 
         *************************************************/
        
        //get counts for new records
        $stCountSQL = "SELECT bean_type ,count(bean_type) total,case bean_type when 'Lead' then 0 when 'Account' then 1 else 2 end  ord FROM  users_last_import group by bean_type order by ord";
        $rsResult = $this->bean->db->query($stCountSQL);
        $arCountStatus = array();
        while($arCount = $this->bean->db->fetchByAssoc($rsResult))
        {   
        	if($arCount['bean_type'] != 'Contact' || $arCount['bean_type'] !='oss_LeadClientDetail')
        	$arCountStatus[$app_list_strings['moduleList'][$arCount['bean_type'].'s']] = $arCount['total']; 
        	          		
        }
        	
        $this->ss->assign('PL_IMPORT_STATUS',$arCountStatus);            	
        
        /*************** END OF CUSTOMIZATION **********/
        $this->ss->display('custom/modules/Leads/tpls/finish_import.tpl');
        
        $data = UsersLastImport::getBeansByImport('Leads');
        arsort($data);
        
        foreach ( $data as $beanname ) {
            
        	
        	
        	if($beanname == 'Contact' || $beanname =='oss_LeadClientDetail'){ 
        		//escape contacts in case its xml import from leads		
        		continue;
        	}
        	// load bean
            if ( !( $this->bean instanceof $beanname ) ) {
                $this->bean = new $beanname;
            }
            // build listview to show imported records
            require_once('include/ListView/ListViewFacade.php');
            
            if($beanname == 'Lead'){
            	require_once 'custom/modules/Leads/bbProjectLeads.php';
        		$this->bean = new bbProjectLeads(); 
            }
            
            $lvf = new ListViewFacade($this->bean, $this->bean->module_dir, 0);
        
            $params = array();
            if(!empty($_REQUEST['orderBy'])) {
                $params['orderBy'] = $_REQUEST['orderBy'];
                $params['overrideOrder'] = true;
                if(!empty($_REQUEST['sortOrder'])) $params['sortOrder'] = $_REQUEST['sortOrder'];
            }
            $beanname = ($this->bean->object_name == 'Case' ? 'aCase' : $this->bean->object_name);
            // add users_last_import joins so we only show records done in this import
            $params['custom_from']  = ', users_last_import';
            $params['custom_where'] = " AND users_last_import.assigned_user_id = '{$GLOBALS['current_user']->id}' 
                AND users_last_import.bean_type = '{$beanname}' 
                AND users_last_import.bean_id = {$this->bean->table_name}.id 
                AND users_last_import.deleted = 0 
                AND {$this->bean->table_name}.deleted = 0";
            $where = " {$this->bean->table_name}.id IN ( 
                        SELECT users_last_import.bean_id
                            FROM users_last_import
                            WHERE users_last_import.assigned_user_id = '{$GLOBALS['current_user']->id}' 
                                AND users_last_import.bean_type = '{$beanname}' 
                                AND users_last_import.deleted = 0 )";
                
            $lbl_last_imported = $mod_strings['LBL_LAST_IMPORTED'];
            $lvf->lv->mergeduplicates = false;
            $lvf->lv->showMassupdateFields = false;
            if ( $lvf->type == 2 ) {
                $lvf->template = 'include/ListView/ListViewNoMassUpdate.tpl';
            }
            
            
            $module_mod_strings = return_module_language($current_language, $this->bean->module_dir);
            $lvf->setup('', $where, $params, $module_mod_strings, 0, -1, '', strtoupper($beanname), array(), 'id');
            $lvf->display($lbl_last_imported.": ".$module_mod_strings['LBL_MODULE_NAME']);
        }
    }

    /**
     * Returns JS used in this view
     */
    private function _getJS()
    {
        return <<<EOJAVASCRIPT
<script type="text/javascript">
<!--
document.getElementById('importmore').onclick = function(){
	document.getElementById('importlast').module.value = 'Leads';
    document.getElementById('importlast').action.value = 'importxml';
    return true;
}

document.getElementById('finished').onclick = function(){
    document.getElementById('importlast').module.value = 'Leads';
    document.getElementById('importlast').action.value = 'index';
    return true;
}
-->
</script>

EOJAVASCRIPT;
    }
    /**
     * Returns a button to add this list of prospects to a Target List
     *
     * @return string html code to display button
     */
    private function _addToProspectListButton() 
    {
        global $app_strings, $sugar_version, $sugar_config, $current_user;
        
        $query = "SELECT distinct
				prospects.id,
				prospects.assigned_user_id,
				prospects.first_name,
				prospects.last_name,
				prospects.phone_work,
				prospects.title,
				email_addresses.email_address email1,
                                users.user_name as assigned_user_name
				FROM users_last_import,prospects
                                LEFT JOIN users
                                ON prospects.assigned_user_id=users.id
				LEFT JOIN email_addr_bean_rel on prospects.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module='Prospect' and email_addr_bean_rel.primary_address=1 and email_addr_bean_rel.deleted=0
				LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id 
										
				WHERE
				users_last_import.assigned_user_id=
					'{$current_user->id}'
				AND users_last_import.bean_type='Prospect'
				AND users_last_import.bean_id=prospects.id
				AND users_last_import.deleted=0
				AND prospects.deleted=0
			";
        
        $popup_request_data = array(
            'call_back_function' => 'set_return_and_save_background',
            'form_name' => 'DetailView',
            'field_to_name_array' => array(
                'id' => 'subpanel_id',
            ),
            'passthru_data' => array(
                'child_field' => 'notused',
                'return_url' => 'notused',
                'link_field_name' => 'notused',
                'module_name' => 'notused',
                'refresh_page'=>'1',
                'return_type'=>'addtoprospectlist',
                'parent_module'=>'ProspectLists',
                'parent_type'=>'ProspectList',
                'child_id'=>'id',
                'link_attribute'=>'prospects',
                'link_type'=>'default',	 //polymorphic or default
            )				
        );
    
        $popup_request_data['passthru_data']['query'] = urlencode($query);
    
        $json = getJSONobj();
        $encoded_popup_request_data = $json->encode($popup_request_data);	
    
        return <<<EOHTML
<script type="text/javascript" src="include/SubPanel/SubPanelTiles.js?s={$sugar_version}&c={$sugar_config['js_custom_version']}"></script>
<input align=right" type="button" name="select_button" id="select_button" class="button"
     title="{$app_strings['LBL_ADD_TO_PROSPECT_LIST_BUTTON_LABEL']}"
     accesskey="{$app_strings['LBL_ADD_TO_PROSPECT_LIST_BUTTON_KEY']}"
     value="{$app_strings['LBL_ADD_TO_PROSPECT_LIST_BUTTON_LABEL']}"
     onclick='open_popup("ProspectLists",600,400,"",true,true,$encoded_popup_request_data,"Single","true");' />
EOHTML;
    
    }
}
?>
