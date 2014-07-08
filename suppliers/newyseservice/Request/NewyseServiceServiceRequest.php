<?php
/**
 * File for class NewyseServiceServiceRequest
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceRequest originally named Request
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceRequest extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named requestBrochures
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructBrochureRequest $_brochureRequest
     * @return void
     */
    public function requestBrochures($_sessionKey,NewyseServiceStructBrochureRequest $_brochureRequest)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->requestBrochures($_sessionKey,$_brochureRequest));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return void
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
