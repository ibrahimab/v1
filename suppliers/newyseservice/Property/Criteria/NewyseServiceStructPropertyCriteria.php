<?php
/**
 * File for class NewyseServiceStructPropertyCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPropertyCriteria originally named PropertyCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPropertyCriteria extends NewyseServiceWsdlClass
{
    /**
     * The PropertyManagerId
     * @var long
     */
    public $PropertyManagerId;
    /**
     * The IncludePartial
     * Meta informations extracted from the WSDL
     * - default : false
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $IncludePartial;
    /**
     * Constructor method for PropertyCriteria
     * @see parent::__construct()
     * @param long $_propertyManagerId
     * @param boolean $_includePartial
     * @return NewyseServiceStructPropertyCriteria
     */
    public function __construct($_propertyManagerId = NULL,$_includePartial = false)
    {
        parent::__construct(array('PropertyManagerId'=>$_propertyManagerId,'IncludePartial'=>$_includePartial),false);
    }
    /**
     * Get PropertyManagerId value
     * @return long|null
     */
    public function getPropertyManagerId()
    {
        return $this->PropertyManagerId;
    }
    /**
     * Set PropertyManagerId value
     * @param long $_propertyManagerId the PropertyManagerId
     * @return long
     */
    public function setPropertyManagerId($_propertyManagerId)
    {
        return ($this->PropertyManagerId = $_propertyManagerId);
    }
    /**
     * Get IncludePartial value
     * @return boolean|null
     */
    public function getIncludePartial()
    {
        return $this->IncludePartial;
    }
    /**
     * Set IncludePartial value
     * @param boolean $_includePartial the IncludePartial
     * @return boolean
     */
    public function setIncludePartial($_includePartial)
    {
        return ($this->IncludePartial = $_includePartial);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPropertyCriteria
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
