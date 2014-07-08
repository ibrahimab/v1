<?php
/**
 * File for class NewyseServiceStructReservedResources
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservedResources originally named ReservedResources
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservedResources extends NewyseServiceWsdlClass
{
    /**
     * The ReservedResourceItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructReservedResource
     */
    public $ReservedResourceItem;
    /**
     * Constructor method for ReservedResources
     * @see parent::__construct()
     * @param NewyseServiceStructReservedResource $_reservedResourceItem
     * @return NewyseServiceStructReservedResources
     */
    public function __construct($_reservedResourceItem = NULL)
    {
        parent::__construct(array('ReservedResourceItem'=>$_reservedResourceItem),false);
    }
    /**
     * Get ReservedResourceItem value
     * @return NewyseServiceStructReservedResource|null
     */
    public function getReservedResourceItem()
    {
        return $this->ReservedResourceItem;
    }
    /**
     * Set ReservedResourceItem value
     * @param NewyseServiceStructReservedResource $_reservedResourceItem the ReservedResourceItem
     * @return NewyseServiceStructReservedResource
     */
    public function setReservedResourceItem($_reservedResourceItem)
    {
        return ($this->ReservedResourceItem = $_reservedResourceItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservedResources
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
