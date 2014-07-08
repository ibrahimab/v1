<?php
/**
 * File for class NewyseServiceStructFacilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructFacilityContainer originally named FacilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructFacilityContainer extends NewyseServiceWsdlClass
{
    /**
     * The Facilities
     * @var NewyseServiceStructFacilities
     */
    public $Facilities;
    /**
     * Constructor method for FacilityContainer
     * @see parent::__construct()
     * @param NewyseServiceStructFacilities $_facilities
     * @return NewyseServiceStructFacilityContainer
     */
    public function __construct($_facilities = NULL)
    {
        parent::__construct(array('Facilities'=>$_facilities),false);
    }
    /**
     * Get Facilities value
     * @return NewyseServiceStructFacilities|null
     */
    public function getFacilities()
    {
        return $this->Facilities;
    }
    /**
     * Set Facilities value
     * @param NewyseServiceStructFacilities $_facilities the Facilities
     * @return NewyseServiceStructFacilities
     */
    public function setFacilities($_facilities)
    {
        return ($this->Facilities = $_facilities);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructFacilityContainer
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
