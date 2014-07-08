<?php
/**
 * File for class NewyseServiceServiceGet
 * @package NewyseService
 * @subpackage Services
 */
/**
 * This class stands for NewyseServiceServiceGet originally named Get
 * @package NewyseService
 * @subpackage Services
 */
class NewyseServiceServiceGet extends NewyseServiceWsdlClass
{
    /**
     * Method to call the operation originally named getCountries
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_string
     * @return NewyseServiceStructCountryContainer
     */
    public function getCountries($_string)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getCountries($_string));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getBrochures
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructBrochureCriteria $_criteria
     * @return NewyseServiceStructBrochureContainer
     */
    public function getBrochures($_sessionKey,NewyseServiceStructBrochureCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getBrochures($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getObjectAvailability
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructObjectAvailabilityCriteria $_criteria
     * @return NewyseServiceStructObjectAvailabilityContainer
     */
    public function getObjectAvailability($_sessionKey,NewyseServiceStructObjectAvailabilityCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getObjectAvailability($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getDebitCardCustomers
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param string $_debitCardNumber
     * @return NewyseServiceStructDebitCardCustomerContainer
     */
    public function getDebitCardCustomers($_sessionKey,$_debitCardNumber)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getDebitCardCustomers($_sessionKey,$_debitCardNumber));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getProperties
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructPropertyCriteria $_criteria
     * @return NewyseServiceStructPropertyContainer
     */
    public function getProperties($_sessionKey,NewyseServiceStructPropertyCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getProperties($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getReservation
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructGetReservation $_criteria
     * @return NewyseServiceStructReservationContainer
     */
    public function getReservation($_sessionKey,NewyseServiceStructGetReservation $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getReservation($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getAccommodationTypes
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructAccommodationTypeCriteria $_criteria
     * @return NewyseServiceStructAccommodationTypeContainer
     */
    public function getAccommodationTypes($_sessionKey,NewyseServiceStructAccommodationTypeCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getAccommodationTypes($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getKCActiveDoorlockPincodes
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param string $_resortCode
     * @return NewyseServiceStructKCPincodes
     */
    public function getKCActiveDoorlockPincodes($_sessionKey,$_startDate,$_endDate,$_resortCode)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getKCActiveDoorlockPincodes($_sessionKey,$_startDate,$_endDate,$_resortCode));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getDebitCardItems
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructDebitCardItemCriteria $_criteria
     * @return NewyseServiceStructDebitCardItemContainer
     */
    public function getDebitCardItems($_sessionKey,NewyseServiceStructDebitCardItemCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getDebitCardItems($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getObjects
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructObjectCriteria $_criteria
     * @return NewyseServiceStructObjectContainer
     */
    public function getObjects($_sessionKey,NewyseServiceStructObjectCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getObjects($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getResorts
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructResortCriteria $_criteria
     * @return NewyseServiceStructResortContainer
     */
    public function getResorts($_sessionKey,NewyseServiceStructResortCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getResorts($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getCustomerTitles
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructCustomerTitleCriteria $_criteria
     * @return NewyseServiceStructCustomerTitleContainer
     */
    public function getCustomerTitles($_sessionKey,NewyseServiceStructCustomerTitleCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getCustomerTitles($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getResourceAvailability
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructAvailabilityCriteria $_criteria
     * @return NewyseServiceStructAvailabilityContainer
     */
    public function getResourceAvailability($_sessionKey,NewyseServiceStructAvailabilityCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getResourceAvailability($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getResourceAdditions
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructResourceAdditionCriteria $_criteria
     * @return NewyseServiceStructResourceAdditionContainer
     */
    public function getResourceAdditions($_sessionKey,NewyseServiceStructResourceAdditionCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getResourceAdditions($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getImages
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructImageCriteria $_criteria
     * @return NewyseServiceStructImageContainer
     */
    public function getImages($_sessionKey,NewyseServiceStructImageCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getImages($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getSources
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructSourceCriteria $_criteria
     * @return NewyseServiceStructSourceContainer
     */
    public function getSources($_sessionKey,NewyseServiceStructSourceCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getSources($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getTipTrips
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructTipTripCriteria $_criteria
     * @return NewyseServiceStructTipTripContainer
     */
    public function getTipTrips($_sessionKey,NewyseServiceStructTipTripCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getTipTrips($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getInfo
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @return string
     */
    public function getInfo()
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getInfo());
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getTipTripCategories
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructTipTripCriteria $_criteria
     * @return NewyseServiceStructTipTripCategoryContainer
     */
    public function getTipTripCategories($_sessionKey,NewyseServiceStructTipTripCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getTipTripCategories($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getSubjects
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructSubjectCriteria $_criteria
     * @return NewyseServiceStructSubjectContainer
     */
    public function getSubjects($_sessionKey,NewyseServiceStructSubjectCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getSubjects($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getAddress
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructAddressCriteria $_criteria
     * @return NewyseServiceStructAddress
     */
    public function getAddress($_sessionKey,NewyseServiceStructAddressCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getAddress($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getKCDoorlockPincodes
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param string $_resortCode
     * @param string $_lockCode
     * @return NewyseServiceStructKCPincodes
     */
    public function getKCDoorlockPincodes($_sessionKey,$_startDate,$_endDate,$_resortCode,$_lockCode)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getKCDoorlockPincodes($_sessionKey,$_startDate,$_endDate,$_resortCode,$_lockCode));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getResortActivities
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructResortActivityCriteria $_criteria
     * @return NewyseServiceStructResortActivityContainer
     */
    public function getResortActivities($_sessionKey,NewyseServiceStructResortActivityCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getResortActivities($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getAccommodationKinds
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructAccommodationKindCriteria $_criteria
     * @return NewyseServiceStructAccommodationKindContainer
     */
    public function getAccommodationKinds($_sessionKey,NewyseServiceStructAccommodationKindCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getAccommodationKinds($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getFacilities
     * @uses NewyseServiceWsdlClass::getSoapClient()
     * @uses NewyseServiceWsdlClass::setResult()
     * @uses NewyseServiceWsdlClass::saveLastError()
     * @param string $_sessionKey
     * @param NewyseServiceStructFacilityCriteria $_criteria
     * @return NewyseServiceStructFacilityContainer
     */
    public function getFacilities($_sessionKey,NewyseServiceStructFacilityCriteria $_criteria)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->getFacilities($_sessionKey,$_criteria));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NewyseServiceWsdlClass::getResult()
     * @return NewyseServiceStructAccommodationKindContainer|NewyseServiceStructAccommodationTypeContainer|NewyseServiceStructAddress|NewyseServiceStructAvailabilityContainer|NewyseServiceStructBrochureContainer|NewyseServiceStructCountryContainer|NewyseServiceStructCustomerTitleContainer|NewyseServiceStructDebitCardCustomerContainer|NewyseServiceStructDebitCardItemContainer|NewyseServiceStructFacilityContainer|NewyseServiceStructImageContainer|NewyseServiceStructKCPincodes|NewyseServiceStructObjectAvailabilityContainer|NewyseServiceStructObjectContainer|NewyseServiceStructPropertyContainer|NewyseServiceStructReservationContainer|NewyseServiceStructResortActivityContainer|NewyseServiceStructResortContainer|NewyseServiceStructResourceAdditionContainer|NewyseServiceStructSourceContainer|NewyseServiceStructSubjectContainer|NewyseServiceStructTipTripCategoryContainer|NewyseServiceStructTipTripContainer|string
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
