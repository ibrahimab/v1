<?php
/**
 * File for class NewyseServiceServiceFind
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceFind originally named Find
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceFind extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named findAccommodationTypes
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructAccommodationTypeSearchCriteria $_criteria
     * @return NewyseServiceStructAccommodationTypeSearchContainer
     */
    public function findAccommodationTypes($_sessionKey,NewyseServiceStructAccommodationTypeSearchCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->findAccommodationTypes($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructAccommodationTypeSearchContainer
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
