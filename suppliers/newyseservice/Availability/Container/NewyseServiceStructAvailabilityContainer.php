<?php
/**
 * File for class NewyseServiceStructAvailabilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAvailabilityContainer originally named AvailabilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAvailabilityContainer extends NewyseServiceWsdlClass
{
    /**
     * The Availabilities
     * @var NewyseServiceStructAvailabilities
     */
    public $Availabilities;
    /**
     * Constructor method for AvailabilityContainer
     * @see parent::__construct()
     * @param NewyseServiceStructAvailabilities $_availabilities
     * @return NewyseServiceStructAvailabilityContainer
     */
    public function __construct($_availabilities = NULL)
    {
        parent::__construct(array('Availabilities'=>$_availabilities),false);
    }
    /**
     * Get Availabilities value
     * @return NewyseServiceStructAvailabilities|null
     */
    public function getAvailabilities()
    {
        return $this->Availabilities;
    }
    /**
     * Set Availabilities value
     * @param NewyseServiceStructAvailabilities $_availabilities the Availabilities
     * @return NewyseServiceStructAvailabilities
     */
    public function setAvailabilities($_availabilities)
    {
        return ($this->Availabilities = $_availabilities);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAvailabilityContainer
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
