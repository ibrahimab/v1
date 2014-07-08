<?php
/**
 * File for class NewyseServiceServiceRegister
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceRegister originally named Register
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceRegister extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named registerPayment
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructPaymentCriteria $_criteria
     * @return NewyseServiceStructPayment
     */
    public function registerPayment($_sessionKey,NewyseServiceStructPaymentCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->registerPayment($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named registerKCDoorlockPincodeEvent
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructPincodeRegistration $_pincodeRegistration
     * @return NewyseServiceStructReservation
     */
    public function registerKCDoorlockPincodeEvent($_sessionKey,NewyseServiceStructPincodeRegistration $_pincodeRegistration)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->registerKCDoorlockPincodeEvent($_sessionKey,$_pincodeRegistration));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructPayment|NewyseServiceStructReservation
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
