<?php

global $timedate;
$focus = new Administration();
$GLOBALS['log']->fatal('License update POST VARS');
$GLOBALS['log']->fatal($_POST);
$arRequestVars = $_POST;
foreach ($arRequestVars as $key => $val) {
    $prefix = $focus->get_config_prefix($key);
    if (in_array($prefix[0], $focus->config_categories)) {
        if ( $prefix[0] == "license" )
        {
            if ( $prefix[1] == "expire_date" )
            {
                global $timedate;
                $val = $timedate->swap_formats( $val, $timedate->get_date_format(), $timedate->dbDayFormat );
            }
            else
            if ( $prefix[1] == "key" )
            {
                $val = trim($val); // bug 16860 tyoung - trim whitespace from the start and end of the licence key value
            }
        }

        $focus->saveSetting($prefix[0], $prefix[1], $val);
    }
}


if(isset($arRequestVars['license_key'])){


    loadLicense(true);
    check_now(get_sugarbeat());

}