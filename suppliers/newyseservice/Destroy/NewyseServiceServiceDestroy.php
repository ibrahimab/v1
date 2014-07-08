<?php
/**
 * File for class NewyseServiceServiceDestroy
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceDestroy originally named Destroy
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceDestroy extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named destroySession
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_string
     * @return void
     */
    public function destroySession($_string)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->destroySession($_string));
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
