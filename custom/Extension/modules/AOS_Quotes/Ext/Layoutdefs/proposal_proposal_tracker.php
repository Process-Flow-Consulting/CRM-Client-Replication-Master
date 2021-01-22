<?php
$layout_defs["AOS_Quotes"]["subpanel_setup"]["proposal_proposal_tracker"] =  array(
'order' => 90,
 'module' => 'oss_ProposalTracker',
 'subpanel_name' => 'default',
 'get_subpanel_data' => 'proposal_proposal_tracker',
 'title_key' => 'LBL_PROPOSAL_TRACKER_SUBPANEL_TITLE',
 'top_buttons' => array(
));

$layout_defs['AOS_Quotes']['subpanel_setup']['documents']['subpanel_name']='quotes_subpanel';
$layout_defs['AOS_Quotes']['subpanel_setup']['documents']['top_buttons'] = array(
		array('widget_class' => 'SubPanelTopCreateButton'),
		array (
				'widget_class' => 'SubPanelTopSelectProposalDocumentButton',
				'mode' => 'MultiSelect',
				'initial_filter_fields'=>array('proposal_docs'=>'proposal_docs')
		));
?>
