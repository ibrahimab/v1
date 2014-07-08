<?php
/**
 * File for class NewyseServiceStructAccommodationTypeSearchObject
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeSearchObject originally named AccommodationTypeSearchObject
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeSearchObject extends NewyseServiceWsdlClass
{
    /**
     * The ObjectId
     * @var long
     */
    public $ObjectId;
    /**
     * The Code
     * @var string
     */
    public $Code;
    /**
     * Constructor method for AccommodationTypeSearchObject
     * @see parent::__construct()
     * @param long $_objectId
     * @param string $_code
     * @return NewyseServiceStructAccommodationTypeSearchObject
     */
    public function __construct($_objectId = NULL,$_code = NULL)
    {
        parent::__construct(array('ObjectId'=>$_objectId,'Code'=>$_code),false);
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
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeSearchObject
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
