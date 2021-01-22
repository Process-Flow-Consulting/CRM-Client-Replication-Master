<?php
/*
 * if(!class_exists('nusoap_base')){
 * require_once('include/nusoap/class.nusoap_base.php'); }
 * if(!class_exists('soap_transport_http')){
 * require_once('include/nusoap/class.soap_transport_http.php'); }
 * if(!class_exists('soap_parser')){
 * require_once('include/nusoap/class.soap_parser.php'); }
 * if(!class_exists('nusoap_client')){
 * require_once('include/nusoap/class.soapclient.php'); }
 */

require_once ("include/nusoap/nusoap.php");
class instancePackage
{
    var $encryptedKey = '';
    var $soapClient, $siteUrl;
    
    /*
     * function __autoload($class) { include_once('include/nusoap/class.' .
     * $class . '.php'); // Check to see whether the include declared the class
     * if (!class_exists($class, false)) { trigger_error("Unable to load class:
     * $class", E_USER_WARNING); } }
     */
    function __construct()
    {
        global $sugar_config;
        // define the SOAP Client and point to the SOAP Server
        $soap_url = $sugar_config['soap_url'];
        $this->siteUrl = $sugar_config['site_url'];
        
        if (!empty($soap_url)) {
            $this->soapClient = new nusoapclient($soap_url, false);
        } else {
            sugar_die('Please configue SOAP URL');
        }
        
        // encrupt the instance key
        if (!$this->getCipherText($sugar_config['validation_key'])) {
            // send encryption error
            sugar_die("Encryption Error: Error with public key.");
        }
    }
    /*
     * function to get package details
     */
    function getPacakgeDetails()
    {
		
        if (!isset($_SESSION['inst_package_data'])) {
            // Prepare request params
            $arReqData = json_encode(array (
                    'client_url' => $this->siteUrl,
                    'KEY' => base64_encode($this->encryptedKey) 
            ));
            
            // Call the funtion to get projet leads from master instance
            $result_array = $this->soapClient->call('get_package_details', array (
                    $arReqData 
            ));
            
            // Decode the JSON encoded response
            $result_array = json_decode($result_array);
            foreach ($result_array->entry_list[0]->name_value_list as $key => $arValues) {
                $arClientInstance[$key] = $arValues[0];
            }
            
            $arReturnVal = array (
                    'no_of_users' => $arClientInstance['no_of_users'],
                    'no_of_opportunity' => $arClientInstance['no_of_opportunity'],
                    'expiry_date' => $arClientInstance['expiry_date'],
            		'upload_limit' => ($arClientInstance['upload_limit']*1048576),
            );
            /**
             * Do not allowed users to login an inactive instance
             */
            if(strtolower($arClientInstance['status']) == 'inactive'){
               session_destroy();
               header("Location: index.php?action=Login&module=Users&loginErrorMessage=LBL_INACTIVE_INSTANCE");
               $GLOBALS['log']->fatal('Access to inactive instance.');
               sugar_cleanup(true);
            }
            
            
            $_SESSION['inst_package_data'] = $arReturnVal;
        } else {
            
            $arReturnVal = $_SESSION['inst_package_data'];
        }
        
        return $arReturnVal;
    }
    
    /*
     * function to validate if more opportunity can be created
     */
	function validateOpportunities()
    {
        global $app_strings;
        $arPackage = self::getPacakgeDetails();

        $obOpportunities = loadBean('Opportunities');
      //  $arAllOpportunities = $obOpportunities->get_full_list('', ' opportunities.parent_opportunity_id is NULL ');
        //GET ALL PARENT OPPORTUNITIES
        $stGetParentOppSQL = 'SELECT COUNT(*) total_count FROM opportunities WHERE deleted =0 AND opportunities.parent_opportunity_id is NULL';
        $rsGetParentOppSQL = $GLOBALS['db']->query($stGetParentOppSQL);
        $arAllOpportunities = $GLOBALS['db']->fetchByAssoc($rsGetParentOppSQL);
        $iAllOpportunities = $arAllOpportunities['total_count'];

        // if package date expired then stop
        if (strtotime(date('Y-m-d')) > strtotime($arPackage['expiry_date'])) {
            sugar_die($app_strings['MSG_PACKAGE_EXPIRED_PULL_LEADS']);
        }
        // if number of opportunity is null then thats an unlimited one
        // if number of opp defined in pacakge then limit will be applicable
        return (trim($arPackage['no_of_opportunity']) != '' && ((int) $arPackage['no_of_opportunity'] <= (int) ($iAllOpportunities)));
    }
    
    /**
     * function to get the encrupted text
     */
    function getCipherText($stPlainText)
    {
        $publicKey = self::getPublicKey();
        
        $stEncryptedText = '';
        
        if (openssl_public_encrypt($stPlainText, $stEncryptedText, $publicKey)) {
            $this->encryptedKey = $stEncryptedText;
            $bReturn = true;
        } else {
            $this->encryptedKey = $stEncryptedText;
            $bReturn = false;
        }
        // free this public key
        openssl_free_key($publicKey);
        
        return $bReturn;
    }
    
    /**
     * get public key.
     */
    function getPublicKey()
    {
        global $sugar_config;
        
        $fp = fopen($sugar_config['public_key'], 'r');
        $fpRes = fread($fp, 8192);
        $publicKey = openssl_pkey_get_public($fpRes);
        
        return $publicKey;
    }
}
?>
