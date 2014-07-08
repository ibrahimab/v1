<?php
/**
 * File for class NewyseServiceStructProperties
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructProperties originally named Properties
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructProperties extends NewyseServiceWsdlClass
{
    /**
     * The PropertyItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructProperty
     */
    public $PropertyItem;
    /**
     * The PropertyId
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var long
     */
    public $PropertyId;
    /**
     * Constructor method for Properties
     * @see parent::__construct()
     * @param NewyseServiceStructProperty $_propertyItem
     * @param long $_propertyId
     * @return NewyseServiceStructProperties
     */
    public function __construct($_propertyItem = NULL,$_propertyId = NULL)
    {
        parent::__construct(array('PropertyItem'=>$_propertyItem,'PropertyId'=>$_propertyId),false);
    }
    /**
     * Get PropertyItem value
     * @return NewyseServiceStructProperty|null
     */
    public function getPropertyItem()
    {
        return $this->PropertyItem;
    }
    /**
     * Set PropertyItem value
     * @param NewyseServiceStructProperty $_propertyItem the PropertyItem
     * @return NewyseServiceStructProperty
     */
    public function setPropertyItem($_propertyItem)
    {
        return ($this->PropertyItem = $_propertyItem);
    }
    /**
     * Get PropertyId value
     * @return long|null
     */
    public function getPropertyId()
    {
        return $this->PropertyId;
    }
    /**
     * Set PropertyId value
     * @param long $_propertyId the PropertyId
     * @return long
     */
    public function setPropertyId($_propertyId)
    {
        return ($this->PropertyId = $_propertyId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructProperties
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
