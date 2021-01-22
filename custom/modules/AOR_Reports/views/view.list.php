<?php
require_once('include/MVC/View/views/view.list.php');
class AOR_ReportsViewList extends ViewList
{
    /**
     * @see ViewList::preDisplay()
     */
    public function preDisplay(){
        parent::preDisplay();
		if(isset($_REQUEST['view'])){
			$this->where=' aor_reports.report_module="'.$_REQUEST['view'].'" ';
		}
	}
}
