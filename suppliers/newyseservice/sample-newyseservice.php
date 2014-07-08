<?php
/**
 * Test with NewyseService for 'http://scws.newyse.com/nwsservice_europe_live/nwsws/newyseservice?wsdl'
 * @package NewyseService

 */
ini_set('memory_limit','512M');
ini_set('display_errors',true);
error_reporting(-1);
/**
 * Load autoload
 */
require_once dirname(__FILE__) . '/NewyseServiceAutoload.php';
/**
 * Wsdl instanciation infos. By default, nothing has to be set.
 * If you wish to override the SoapClient's options, please refer to the sample below.
 * 
 * This is an associative array as:
 * - the key must be a NewyseServiceWsdlClass constant beginning with WSDL_
 * - the value must be the corresponding key value
 * Each option matches the {@link http://www.php.net/manual/en/soapclient.soapclient.php} options
 * 
 * Here is below an example of how you can set the array:
    $wsdl = array();
 * $wsdl[NewyseServiceWsdlClass::WSDL_URL] = 'http://scws.newyse.com/nwsservice_europe_live/nwsws/newyseservice?wsdl';
 * $wsdl[NewyseServiceWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
 * $wsdl[NewyseServiceWsdlClass::WSDL_TRACE] = true;
 * $wsdl[NewyseServiceWsdlClass::WSDL_LOGIN] = 'myLogin';
 * $wsdl[NewyseServiceWsdlClass::WSDL_PASSWD] = '**********';
 * etc....
 * Then instantiate the Service class as: 
 * - $wsdlObject = new NewyseServiceWsdlClass($wsdl);
 */
/**
 * Examples
 */


/*************************************
 * Example for NewyseServiceServiceGet
 */

$wsdl = array();
$wsdl[NewyseServiceWsdlClass::WSDL_URL] = 'http://scws.newyse.com/nwsservice_europe_live/nwsws/newyseservice?wsdl';
$wsdl[NewyseServiceWsdlClass::WSDL_CACHE_WSDL] = WSDL_CACHE_NONE;
$wsdl[NewyseServiceWsdlClass::WSDL_TRACE] = true;
$wsdl[NewyseServiceWsdlClass::WSDL_LOGIN] = 'chaletnl';
$wsdl[NewyseServiceWsdlClass::WSDL_PASSWD] = 'chaletnl';

// sample call for NewyseServiceServiceCreate::createSession()
$newyseServiceServiceCreate = new NewyseServiceServiceCreate();

if($newyseServiceServiceCreate->createSession(new NewyseServiceStructSessionCriteria('normal', 'chaletnl', 'chaletnl', 'nl', 'TOCHA')))
    print_r($newyseServiceServiceCreate->getResult());
else
    print_r($newyseServiceServiceCreate->getLastError());

