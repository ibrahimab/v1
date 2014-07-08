<?php
/**
 * File for class NewyseServiceStructAvailabilities
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAvailabilities originally named Availabilities
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAvailabilities extends NewyseServiceWsdlClass
{
    /**
     * The AvailabilityItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructAvailability
     */
    public $AvailabilityItem;
    /**
     * Constructor method for Availabilities
     * @see parent::__construct()
     * @param NewyseServiceStructAvailability $_availabilityItem
     * @return NewyseServiceStructAvailabilities
     */
    public function __construct($_availabilityItem = NULL)
    {
        parent::__construct(array('AvailabilityItem'=>$_availabilityItem),false);
    }
    /**
     * Get AvailabilityItem value
     * @return NewyseServiceStructAvailability|null
     */
    public function getAvailabilityItem()
    {
        return $this->AvailabilityItem;
    }
    /**
     * Set AvailabilityItem value
     * @param NewyseServiceStructAvailability $_availabilityItem the AvailabilityItem
     * @return NewyseServiceStructAvailability
     */
    public function setAvailabilityItem($_availabilityItem)
    {
        return ($this->AvailabilityItem = $_availabilityItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAvailabilities
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
