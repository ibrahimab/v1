<?php
/**
 * File for class NewyseServiceServiceUpdate
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceUpdate originally named Update
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceUpdate extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named updateDebitCardItems
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructDebitCardTransactionContainer $_debitCardTransactionContainer
     * @return void
     */
    public function updateDebitCardItems($_sessionKey,NewyseServiceStructDebitCardTransactionContainer $_debitCardTransactionContainer)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->updateDebitCardItems($_sessionKey,$_debitCardTransactionContainer));
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
