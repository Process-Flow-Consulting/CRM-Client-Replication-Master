<?php

require_once 'include/MVC/View/views/view.popup.php';

class OpportunitiesViewPopup extends ViewPopup {

    function OpportunitiesViewPopup() {
        parent::ViewPopup();
    }

    function display(){
    	  	
    	require_once 'custom/modules/Opportunities/OpportunityPopupSummary.php';
        $this->bean = new OpportunityPopupSummary();
    	
        parent::display();
    }
    

}

?>
