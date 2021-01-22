<?php

class save_url_field_hook {

    function save_field($bean, $event, $arguments) {

        if (isset($_REQUEST['project_url_l']) && isset($_REQUEST['project_url'])) {
            $bean->project_url_l = implode("^", $_REQUEST['project_url_l']);
            $bean->project_url = implode("^", $_REQUEST['project_url']);
        }
    }

}