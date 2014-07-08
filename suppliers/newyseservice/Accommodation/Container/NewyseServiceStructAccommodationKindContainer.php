<?php
/**
 * File for class NewyseServiceStructAccommodationKindContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationKindContainer originally named AccommodationKindContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationKindContainer extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationKinds
     * @var NewyseServiceStructAccommodationKinds
     */
    public $AccommodationKinds;
    /**
     * Constructor method for AccommodationKindContainer
     * @see parent::__construct()
     * @param NewyseServiceStructAccommodationKinds $_accommodationKinds
     * @return NewyseServiceStructAccommodationKindContainer
     */
    public function __construct($_accommodationKinds = NULL)
    {
        parent::__construct(array('AccommodationKinds'=>$_accommodationKinds),false);
    }
    /**
     * Get AccommodationKinds value
     * @return NewyseServiceStructAccommodationKinds|null
     */
    public function getAccommodationKinds()
    {
        return $this->AccommodationKinds;
    }
    /**
     * Set AccommodationKinds value
     * @param NewyseServiceStructAccommodationKinds $_accommodationKinds the AccommodationKinds
     * @return NewyseServiceStructAccommodationKinds
     */
    public function setAccommodationKinds($_accommodationKinds)
    {
        return ($this->AccommodationKinds = $_accommodationKinds);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationKindContainer
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
