<?php

require_once 'modules/Administration/Common.php';

function checkExistingValueFromDom($dom_name, $dom_value)
{
    global $app_list_strings;

    if ($dom_name == 'structure') {
        $dom = array_merge($app_list_strings['structure_residential'], $app_list_strings['structure_non_residential'], $app_list_strings['structure_non_building']);
    } else {
        $dom = $app_list_strings[$dom_name];
    }

    $dom_value = html_entity_decode($dom_value);
    $dom_value = str_replace(' ', '', $dom_value);

    foreach ($dom as $key => $value) {
        $new_key = str_replace(' ', '', $key);
        if ($new_key == $dom_value) {
            return $key;
        }
    }
}

function editDropdownList($dropdwon_name, $new_list_value)
{

    global $app_list_strings;

    $GLOBALS['log']->fatal('edit dropdown: ' . $dropdwon_name);

    //$new_list_value = addslashes($new_list_value);

    $arrPrevList = $app_list_strings[$dropdwon_name];

    foreach ($arrPrevList as $Key => $Value) {
        $list_value[] = '["' . $Key . '","' . $Value . '"]';
    }

    $list_value = implode(',', $list_value);

    if ($list_value)
        $list_value = '[' . $list_value . ',' . $new_list_value . ']';
    else
        $list_value = '[' . $new_list_value . ']';
    handleNewDropdownValue($dropdwon_name, $list_value);

    /*
	$arrParse['to_pdf'] = true;
	$arrParse['sugar_body_only'] = 1;
	$arrParse['action'] = 'savedropdown';
	$arrParse['view_package'] = 'studio';
	$arrParse['dropdown_name'] = $dropdwon_name;
	$arrParse['dropdown_lang'] = 'en_us';
	$arrParse['list_value'] = $list_value;
	$arrParse['module'] = 'ModuleBuilder';
	//$arrParse['use_push'] = 'true';
	$_REQUEST['view_package'] = 'studio';

	$GLOBALS['log']->info($arrParse);

	require_once 'modules/ModuleBuilder/MB/ModuleBuilder.php' ;
	//require_once 'modules/ModuleBuilder/parsers/parser.dropdown.php' ;
	require_once 'custom/include/customParserDropdown.php';
    $parser = new customParserDropdown(); 
	$parser->saveDropDown ( $arrParse ) ;*/
}


/**
 * @param $dropdownType
 * @param $newValues
 */
function handleNewDropdownValue($dropdownType, $newValues)
{

    $dropdown = array();
    $json = getJSONobj();
    $list_value = str_replace('&quot;&quot;:&quot;&quot;', '&quot;__empty__&quot;:&quot;&quot;', $newValues);
    $temp = $json->decode(html_entity_decode(rawurldecode($list_value), ENT_QUOTES));

    if (is_array($temp)) {
        foreach ($temp as $item) {

            $dropdown[SugarCleaner::stripTags(from_html($item [0]), false)] = SugarCleaner::stripTags(from_html($item [1]), false);
        }
    }

    $arr = array_merge($GLOBALS['app_list_strings'][$dropdownType], $dropdown);
    $contents = return_custom_app_list_strings_file_contents('en_us');

    $updateContents = replace_bb_dropdown_type($dropdownType, $arr, $contents);

    if (!empty($updateContents)) {
        save_custom_app_list_strings_contents($updateContents, 'en_us');
    } else {
        //lets log this errror
        $GLOBALS['log']->fatal('ALERT CRITICAL ERROR: There is Some issue with updating dropdown value', func_get_args());
    }

}

/**
 * Method to create Dropdown unique array lists
 *
 * @param $dropdown_type
 * @param $dropdown_array
 * @param $file_contents
 * @return string
 */
function replace_bb_dropdown_type($dropdown_type, &$dropdown_array,
                                  &$file_contents)
{
    $new_contents = $file_contents;

    if (!empty($dropdown_type) &&
        is_array($dropdown_array) &&
        !empty($file_contents)
    ) {
        //pattern for GLOBALS array
        $pattern = '/\$GLOBALS\[\'app_list_strings\'\]\[\'' . $dropdown_type .
            '\'\][\ ]*=[\ ]*array[\ ]*\([^\;]*\)[\ ]*;/';

        //pattern for without GLOBALS array
        $pattern_app_list_strings = '/\$app_list_strings\[\'' . $dropdown_type .
            '\'\][\ ]*=[\ ]*array[\ ]*\([^\;]*\)[\ ]*;/';

        //get all matches for $GLOBALS['app_list_strings']
        $numGlobalDups = preg_match_all($pattern, $file_contents, $matches, PREG_PATTERN_ORDER);

        //get all matches for $app_list_strings
        $numOfDups = preg_match_all($pattern_app_list_strings, $file_contents, $matches_app_list_strings, PREG_PATTERN_ORDER);

        if ($numOfDups) {
            $matches[0] = array_merge($matches[0], $matches_app_list_strings[0]);
        }

        $globalsAppListStrings = implode(',', $matches[0]);
        $globalsAppListStrings = preg_replace('/\$app_list_strings\[\'' . $dropdown_type . '\'\][\ ]*=/', '', $globalsAppListStrings);
        $globalsAppListStrings = preg_replace('/\$GLOBALS\[\'app_list_strings\'\]\[\'' . $dropdown_type . '\'\][\ ]*=/', '', $globalsAppListStrings);
        $globalsAppListStrings = preg_replace('/\;/', '', $globalsAppListStrings);


        $parstString = '$finalArray = array_merge(' .  $globalsAppListStrings . ');';

        (eval($parstString));

        $finalArray = array_merge($finalArray, $dropdown_array);
        asort($finalArray);

        $replacement = override_value_to_string('GLOBALS[\'app_list_strings\']',
            $dropdown_type, $finalArray);

        $unique_new_contents = preg_replace($pattern, '', $file_contents);
        $unique_new_contents = preg_replace($pattern_app_list_strings, '', $unique_new_contents);

        $new_contents = trim($unique_new_contents) .PHP_EOL. trim($replacement);
    }

    return $new_contents;
}

