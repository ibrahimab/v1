<?php
/**
 * File for class NewyseServiceStructObjectAvailability
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectAvailability originally named ObjectAvailability
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectAvailability extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * @var long
     */
    public $ResourceId;
    /**
     * The ObjectId
     * @var long
     */
    public $ObjectId;
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
     * Constructor method for ObjectAvailability
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_objectId
     * @param dateTime $_arrivalDate
     * @param dateTime $_departureDate
     * @return NewyseServiceStructObjectAvailability
     */
    public function __construct($_resourceId = NULL,$_objectId = NULL,$_arrivalDate = NULL,$_departureDate = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ObjectId'=>$_objectId,'ArrivalDate'=>$_arrivalDate,'DepartureDate'=>$_departureDate),false);
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
     * Get ObjectId value
     * @return long|null
     */
    public function getObjectId()
    {
        return $this->ObjectId;
    }
    /**
     * Set ObjectId value
     * @param long $_objectId the ObjectId
     * @return long
     */
    public function setObjectId($_objectId)
    {
        return ($this->ObjectId = $_objectId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectAvailability
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
