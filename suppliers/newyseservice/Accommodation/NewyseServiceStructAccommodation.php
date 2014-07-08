<?php
/**
 * File for class NewyseServiceStructAccommodation
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodation originally named Accommodation
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodation extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * @var long
     */
    public $ResourceId;
    /**
     * The ObjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ObjectId;
    /**
     * The ArrivalDate
     * @var dateTime
     */
    public $ArrivalDate;
    /**
     * The Duration
     * @var int
     */
    public $Duration;
    /**
     * The SpecialCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SpecialCode;
    /**
     * Constructor method for Accommodation
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_objectId
     * @param dateTime $_arrivalDate
     * @param int $_duration
     * @param string $_specialCode
     * @return NewyseServiceStructAccommodation
     */
    public function __construct($_resourceId = NULL,$_objectId = NULL,$_arrivalDate = NULL,$_duration = NULL,$_specialCode = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ObjectId'=>$_objectId,'ArrivalDate'=>$_arrivalDate,'Duration'=>$_duration,'SpecialCode'=>$_specialCode),false);
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
     * Get Duration value
     * @return int|null
     */
    public function getDuration()
    {
        return $this->Duration;
    }
    /**
     * Set Duration value
     * @param int $_duration the Duration
     * @return int
     */
    public function setDuration($_duration)
    {
        return ($this->Duration = $_duration);
    }
    /**
     * Get SpecialCode value
     * @return string|null
     */
    public function getSpecialCode()
    {
        return $this->SpecialCode;
    }
    /**
     * Set SpecialCode value
     * @param string $_specialCode the SpecialCode
     * @return string
     */
    public function setSpecialCode($_specialCode)
    {
        return ($this->SpecialCode = $_specialCode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodation
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
