<?php
/**
 * File for class NewyseServiceStructAccommodationTypeContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeContainer originally named AccommodationTypeContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeContainer extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationTypes
     * @var NewyseServiceStructAccommodationTypes
     */
    public $AccommodationTypes;
    /**
     * Constructor method for AccommodationTypeContainer
     * @see parent::__construct()
     * @param NewyseServiceStructAccommodationTypes $_accommodationTypes
     * @return NewyseServiceStructAccommodationTypeContainer
     */
    public function __construct($_accommodationTypes = NULL)
    {
        parent::__construct(array('AccommodationTypes'=>$_accommodationTypes),false);
    }
    /**
     * Get AccommodationTypes value
     * @return NewyseServiceStructAccommodationTypes|null
     */
    public function getAccommodationTypes()
    {
        return $this->AccommodationTypes;
    }
    /**
     * Set AccommodationTypes value
     * @param NewyseServiceStructAccommodationTypes $_accommodationTypes the AccommodationTypes
     * @return NewyseServiceStructAccommodationTypes
     */
    public function setAccommodationTypes($_accommodationTypes)
    {
        return ($this->AccommodationTypes = $_accommodationTypes);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeContainer
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
