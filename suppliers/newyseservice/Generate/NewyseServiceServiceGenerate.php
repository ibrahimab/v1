<?php
/**
 * File for class NewyseServiceServiceGenerate
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceGenerate originally named Generate
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceGenerate extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named generateVouchers
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructVoucherCriteria $_criteria
     * @return NewyseServiceStructVoucherContainer
     */
    public function generateVouchers($_sessionKey,NewyseServiceStructVoucherCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->generateVouchers($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructVoucherContainer
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
