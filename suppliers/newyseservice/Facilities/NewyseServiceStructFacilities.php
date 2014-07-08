<?php
/**
 * File for class NewyseServiceStructFacilities
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructFacilities originally named Facilities
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructFacilities extends NewyseServiceWsdlClass
{
    /**
     * The FacilityItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructFacility
     */
    public $FacilityItem;
    /**
     * Constructor method for Facilities
     * @see parent::__construct()
     * @param NewyseServiceStructFacility $_facilityItem
     * @return NewyseServiceStructFacilities
     */
    public function __construct($_facilityItem = NULL)
    {
        parent::__construct(array('FacilityItem'=>$_facilityItem),false);
    }
    /**
     * Get FacilityItem value
     * @return NewyseServiceStructFacility|null
     */
    public function getFacilityItem()
    {
        return $this->FacilityItem;
    }
    /**
     * Set FacilityItem value
     * @param NewyseServiceStructFacility $_facilityItem the FacilityItem
     * @return NewyseServiceStructFacility
     */
    public function setFacilityItem($_facilityItem)
    {
        return ($this->FacilityItem = $_facilityItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructFacilities
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
