<?php
/**
 * File for class NewyseServiceServiceCreate
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceCreate originally named Create
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceCreate extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named createReservationProposal
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructReservationCriteria $_criteria
     * @return NewyseServiceStructReservation
     */
    public function createReservationProposal($_sessionKey,NewyseServiceStructReservationCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->createReservationProposal($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named createCustomer
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructCustomer $_customer
     * @return NewyseServiceStructCustomer
     */
    public function createCustomer($_sessionKey,NewyseServiceStructCustomer $_customer)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->createCustomer($_sessionKey,$_customer));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named createSession
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param NewyseServiceStructSessionCriteria $_newyseServiceStructSessionCriteria
     * @return string
     */
    public function createSession(NewyseServiceStructSessionCriteria $_newyseServiceStructSessionCriteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->createSession($_newyseServiceStructSessionCriteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructCustomer|NewyseServiceStructReservation|string
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
