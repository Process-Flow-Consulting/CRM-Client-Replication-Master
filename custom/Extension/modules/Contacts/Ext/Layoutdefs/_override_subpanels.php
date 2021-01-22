<?php
unset($layout_defs['Contacts']['subpanel_setup']['contact_leadclientdetail']);
unset($layout_defs['Contacts']['subpanel_setup']['leads']);
$layout_defs['Contacts']['subpanel_setup']['opportunities']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
);
$layout_defs['Contacts']['subpanel_setup']['contact_aos_quotes']['top_buttons']=array();
$layout_defs['Contacts']['subpanel_setup']['documents']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
		1 =>
		array (
				'widget_class' => 'SubPanelTopSelectButton',
				'mode' => 'MultiSelect',
		),
);
$layout_defs['Contacts']['subpanel_setup']['history']['top_buttons']=array(
		array (
				'widget_class' => 'SubPanelTopCreateCustomNoteButton',
		),
		array (
				'widget_class' => 'SubPanelTopArchiveEmailButton',
		),
		array (
				'widget_class' => 'SubPanelTopSummaryButton',
		),
		
);