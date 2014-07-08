<?php
/**
 * File for class NewyseServiceServiceCheckin
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceCheckin originally named Checkin
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceCheckin extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named checkinReservation
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructGetReservation $_criteria
     * @return NewyseServiceStructReservationContainer
     */
    public function checkinReservation($_sessionKey,NewyseServiceStructGetReservation $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->checkinReservation($_sessionKey,$_criteria));
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
