<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
class LeadsViewDedupe_details extends SugarView {

    function LeadsViewDedupe_details() {
        parent::SugarView();
    }
    
    function display(){

        if(!isset($this->bean->id) && trim($this->bean->id) != ''){
            sugar_die('Not A Valid Entry Point');
        }
        //get bbProjectLeads class
        require_once 'custom/modules/Leads/bbProjectLeads.php';
        $obProjectLeads = new bbProjectLeads();
        $obProjectLeads->retrieve($this->bean->id);
      //  $stChildPLSql = $obProjectLeads->lead_to_lead_sql();
      //  $arDupList = $obProjectLeads->process_full_list_query($stChildPLSql, '');
       $arDupList = $obProjectLeads->get_linked_beans('lead_to_lead_var','Leads');
//        echo '<pre>';print_r($arDupList); echo '</pre>';die;
        
        
        $this->ss->assign('AR_LEAD_DATA',$arDupList);
        $this->ss->assign('OB_LEAD_DATA',$this->bean);
        $this->ss->display('custom/modules/Leads/tpls/dedupe_details.tpl');
    }
}

?>
