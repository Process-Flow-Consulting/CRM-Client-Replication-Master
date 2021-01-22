<?php
unset($layout_defs['Accounts']['subpanel_setup']['account_leadclientdetail']);
unset($layout_defs["Accounts"]["subpanel_setup"]["leads_accounts"]);
unset($layout_defs["Accounts"]["subpanel_setup"]["products_services_purchased"]);
$layout_defs['Accounts']['subpanel_setup']['opportunities']["top_buttons"]=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
		
);

$layout_defs['Accounts']['subpanel_setup']['contacts']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateCustomAccountNameButton',
		),
		1 =>
		array (
				'widget_class' => 'SubPanelTopSelectCustomContactsButton',
				'mode' => 'MultiSelect',
		),
);

$layout_defs["Accounts"]["subpanel_setup"]["account_aos_quotes"]["top_buttons"]=array();
