<?php
/**
 * File for class NewyseServiceServiceDecline
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceDecline originally named Decline
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceDecline extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named declineReservation
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructGetReservation $_criteria
     * @return NewyseServiceStructReservationContainer
     */
    public function declineReservation($_sessionKey,NewyseServiceStructGetReservation $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->declineReservation($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructReservationContainer
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
