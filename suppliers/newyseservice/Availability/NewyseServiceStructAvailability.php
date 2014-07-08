<?php
/**
 * File for class NewyseServiceStructAvailability
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAvailability originally named Availability
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAvailability extends NewyseServiceWsdlClass
{
    /**
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * The ResourceId
     * @var long
     */
    public $ResourceId;
    /**
     * The ArrivalDate
     * @var dateTime
     */
    public $ArrivalDate;
    /**
     * The DepartureDate
     * @var dateTime
     */
    public $DepartureDate;
    /**
     * The Prices
     * @var NewyseServiceStructPrices
     */
    public $Prices;
    /**
     * Constructor method for Availability
     * @see parent::__construct()
     * @param string $_resortCode
     * @param long $_resourceId
     * @param dateTime $_arrivalDate
     * @param dateTime $_departureDate
     * @param NewyseServiceStructPrices $_prices
     * @return NewyseServiceStructAvailability
     */
    public function __construct($_resortCode = NULL,$_resourceId = NULL,$_arrivalDate = NULL,$_departureDate = NULL,$_prices = NULL)
    {
        parent::__construct(array('ResortCode'=>$_resortCode,'ResourceId'=>$_resourceId,'ArrivalDate'=>$_arrivalDate,'DepartureDate'=>$_departureDate,'Prices'=>$_prices),false);
    }
    /**
     * Get ResortCode value
     * @return string|null
     */
    public function getResortCode()
    {
        return $this->ResortCode;
    }
    /**
     * Set ResortCode value
     * @param string $_resortCode the ResortCode
     * @return string
     */
    public function setResortCode($_resortCode)
    {
        return ($this->ResortCode = $_resortCode);
    }
    /**
     * Get ResourceId value
     * @return long|null
     */
    public function getResourceId()
    {
        return $this->ResourceId;
    }
    /**
     * Set ResourceId value
     * @param long $_resourceId the ResourceId
     * @return long
     */
    public function setResourceId($_resourceId)
    {
        return ($this->ResourceId = $_resourceId);
    }
    /**
     * Get ArrivalDate value
     * @return dateTime|null
     */
    public function getArrivalDate()
    {
        return $this->ArrivalDate;
    }
    /**
     * Set ArrivalDate value
     * @param dateTime $_arrivalDate the ArrivalDate
     * @return dateTime
     */
    public function setArrivalDate($_arrivalDate)
    {
        return ($this->ArrivalDate = $_arrivalDate);
    }
    /**
     * Get DepartureDate value
     * @return dateTime|null
     */
    public function getDepartureDate()
    {
        return $this->DepartureDate;
    }
    /**
     * Set DepartureDate value
     * @param dateTime $_departureDate the DepartureDate
     * @return dateTime
     */
    public function setDepartureDate($_departureDate)
    {
        return ($this->DepartureDate = $_departureDate);
    }
    /**
     * Get Prices value
     * @return NewyseServiceStructPrices|null
     */
    public function getPrices()
    {
        return $this->Prices;
    }
    /**
     * Set Prices value
     * @param NewyseServiceStructPrices $_prices the Prices
     * @return NewyseServiceStructPrices
     */
    public function setPrices($_prices)
    {
        return ($this->Prices = $_prices);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAvailability
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
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
