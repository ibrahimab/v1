<?php
/**
 * File for class NewyseServiceServiceConfirm
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceConfirm originally named Confirm
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceConfirm extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named confirmReservation
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructReservationCriteria $_criteria
     * @return NewyseServiceStructReservation
     */
    public function confirmReservation($_sessionKey,NewyseServiceStructReservationCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->confirmReservation($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructReservation
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
